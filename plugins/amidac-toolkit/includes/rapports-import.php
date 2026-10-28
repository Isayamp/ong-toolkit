<?php
/**
 * Import en lot des rapports et documents.
 *
 * On envoie une archive ZIP contenant les PDF et un fichier rapports.csv. Chaque ligne du
 * tableau crée un document complet : fichier dans la médiathèque, titre, catégorie, type,
 * année, date affichée, groupe et description. Rien n'est jamais écrasé : un document
 * déjà présent (même titre, même catégorie) est ignoré.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Colonnes attendues du tableau (la première ligne contient ces noms).
 */
function amidac_toolkit_lot_colonnes() {
    return array( 'fichier', 'titre', 'categorie', 'type', 'annee', 'date_affichee', 'groupe', 'description' );
}

/**
 * Lit le CSV (séparateur « ; » ou « , », avec ou sans BOM) et renvoie une liste de lignes associatives.
 */
function amidac_toolkit_lot_lire_csv( $contenu ) {
    $contenu = preg_replace( '/^\xEF\xBB\xBF/', '', $contenu );
    $premiere = strtok( $contenu, "\n" );
    $sep      = substr_count( $premiere, ';' ) >= substr_count( $premiere, ',' ) ? ';' : ',';

    $flux = fopen( 'php://temp', 'r+' );
    fwrite( $flux, $contenu );
    rewind( $flux );

    $entetes = fgetcsv( $flux, 0, $sep );
    if ( ! $entetes ) {
        fclose( $flux );
        return array();
    }
    $entetes = array_map( function ( $e ) {
        return sanitize_key( trim( $e ) );
    }, $entetes );

    $lignes = array();
    while ( ( $row = fgetcsv( $flux, 0, $sep ) ) !== false ) {
        if ( ! array_filter( $row, 'strlen' ) ) {
            continue;
        }
        $row      = array_pad( $row, count( $entetes ), '' );
        $lignes[] = array_combine( $entetes, array_slice( $row, 0, count( $entetes ) ) );
    }
    fclose( $flux );

    return $lignes;
}

function amidac_toolkit_lot_form() {
    $td = 'amidac-toolkit';
    ?>
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Importer un lot de documents', $td ); ?></h1>
    <a href="<?php echo esc_url( amidac_toolkit_rapports_url() ); ?>" class="page-title-action"><?php esc_html_e( 'Retour à la liste', $td ); ?></a>
    <hr class="wp-header-end">

    <div class="amidac-import">
        <p><?php esc_html_e( 'Envoyez une archive ZIP contenant les PDF et le tableau rapports.csv. Chaque ligne du tableau crée un document complet, déjà rattaché à son fichier.', $td ); ?></p>
        <p class="description">
            <?php esc_html_e( 'Colonnes du tableau :', $td ); ?>
            <code><?php echo esc_html( implode( ' ; ', amidac_toolkit_lot_colonnes() ) ); ?></code><br>
            <?php esc_html_e( 'Catégories : annuel, publication, reseau, institutionnel. Un document déjà présent (même titre, même catégorie) est ignoré.', $td ); ?>
        </p>

        <form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="amidac_rapport_lot">
            <?php wp_nonce_field( 'amidac_rapport_lot', 'amidac_lot_nonce' ); ?>
            <p><input type="file" name="lot" accept=".zip,application/zip" required></p>
            <p>
                <label><input type="checkbox" name="brouillon" value="1"> <?php esc_html_e( 'Créer les documents en brouillon (à publier après vérification)', $td ); ?></label>
            </p>
            <p><button type="submit" class="button button-primary"><?php esc_html_e( 'Importer', $td ); ?></button></p>
        </form>
    </div>
    <?php
}

function amidac_toolkit_lot_import() {
    amidac_toolkit_rapports_check_access();
    check_admin_referer( 'amidac_rapport_lot', 'amidac_lot_nonce' );

    $retour = function ( $args ) {
        amidac_toolkit_rapports_redirect( array_merge( array( 'action' => 'lot' ), $args ) );
    };

    if ( ! class_exists( 'ZipArchive' ) ) {
        $retour( array( 'message' => 'lot_nozip' ) );
    }
    if ( empty( $_FILES['lot']['tmp_name'] ) || UPLOAD_ERR_OK !== (int) $_FILES['lot']['error'] || ! is_uploaded_file( $_FILES['lot']['tmp_name'] ) ) {
        $retour( array( 'message' => 'lot_upload' ) );
    }

    $zip = new ZipArchive();
    if ( true !== $zip->open( $_FILES['lot']['tmp_name'] ) ) {
        $retour( array( 'message' => 'lot_zip' ) );
    }

    // Le tableau peut se trouver à la racine ou dans un dossier de l'archive.
    $csv = false;
    for ( $i = 0; $i < $zip->numFiles; $i++ ) {
        if ( 'rapports.csv' === strtolower( basename( $zip->getNameIndex( $i ) ) ) ) {
            $csv = $zip->getFromIndex( $i );
            break;
        }
    }
    $lignes = $csv ? amidac_toolkit_lot_lire_csv( $csv ) : array();
    if ( ! $lignes || array_diff( array( 'fichier', 'titre' ), array_keys( $lignes[0] ) ) ) {
        $zip->close();
        $retour( array( 'message' => 'lot_csv' ) );
    }

    // Index des fichiers de l'archive par nom (jamais par chemin).
    $index = array();
    for ( $i = 0; $i < $zip->numFiles; $i++ ) {
        $nom = $zip->getNameIndex( $i );
        if ( '/' !== substr( $nom, -1 ) && 'pdf' === strtolower( pathinfo( $nom, PATHINFO_EXTENSION ) ) ) {
            $index[ strtolower( basename( $nom ) ) ] = $i;
        }
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    if ( function_exists( 'set_time_limit' ) ) {
        set_time_limit( 300 );
    }

    $cats      = amidac_toolkit_rapport_categories();
    $statut    = ! empty( $_POST['brouillon'] ) ? 'draft' : 'publish';
    $existants = array();
    foreach ( amidac_toolkit_rapports_all() as $p ) {
        $existants[ amidac_toolkit_rapport_categorie( $p->ID ) . '|' . $p->post_title ] = true;
    }

    $crees = $ignores = $erreurs = 0;

    foreach ( $lignes as $ordre => $l ) {
        $titre = sanitize_text_field( $l['titre'] );
        $cat   = isset( $cats[ sanitize_key( $l['categorie'] ?? '' ) ] ) ? sanitize_key( $l['categorie'] ) : 'annuel';
        if ( '' === $titre ) {
            $erreurs++;
            continue;
        }
        if ( isset( $existants[ $cat . '|' . $titre ] ) ) {
            $ignores++;
            continue;
        }

        $cle = strtolower( basename( $l['fichier'] ) );
        if ( ! isset( $index[ $cle ] ) ) {
            $erreurs++;
            continue;
        }

        // Le PDF est écrit dans un fichier temporaire puis confié à la médiathèque.
        $tmp = wp_tempnam( $cle );
        file_put_contents( $tmp, $zip->getFromIndex( $index[ $cle ] ) );

        $piece = media_handle_sideload(
            array( 'name' => sanitize_file_name( basename( $l['fichier'] ) ), 'tmp_name' => $tmp ),
            0,
            null,
            array( 'post_title' => $titre, 'post_excerpt' => '', 'post_content' => '' )
        );
        if ( is_wp_error( $piece ) ) {
            if ( file_exists( $tmp ) ) {
                wp_delete_file( $tmp );
            }
            $erreurs++;
            continue;
        }

        $id = wp_insert_post( array(
            'post_type'    => 'amidac_rapport',
            'post_title'   => $titre,
            'post_excerpt' => sanitize_textarea_field( $l['description'] ?? '' ),
            'post_status'  => $statut,
            'menu_order'   => $ordre,
        ) );
        if ( ! $id || is_wp_error( $id ) ) {
            $erreurs++;
            continue;
        }

        $annee = absint( $l['annee'] ?? 0 );
        update_post_meta( $id, '_amidac_categorie', $cat );
        update_post_meta( $id, '_amidac_type_rapport', sanitize_text_field( $l['type'] ?? '' ) );
        update_post_meta( $id, '_amidac_annee', $annee ? $annee : '' );
        update_post_meta( $id, '_amidac_periode', sanitize_text_field( $l['date_affichee'] ?? '' ) );
        update_post_meta( $id, '_amidac_groupe', sanitize_text_field( $l['groupe'] ?? '' ) );
        update_post_meta( $id, '_amidac_fichier_id', $piece );
        update_post_meta( $id, '_amidac_fichier_url', wp_get_attachment_url( $piece ) );

        $existants[ $cat . '|' . $titre ] = true;
        $crees++;
    }

    $zip->close();
    amidac_toolkit_rapports_redirect( array( 'message' => 'lot_ok', 'crees' => $crees, 'ignores' => $ignores, 'erreurs' => $erreurs ) );
}
add_action( 'admin_post_amidac_rapport_lot', 'amidac_toolkit_lot_import' );
