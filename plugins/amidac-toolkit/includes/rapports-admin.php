<?php
/**
 * Espace de gestion des rapports et documents.
 *
 * Page d'administration dédiée (menu « Rapports ») : liste filtrable, formulaire d'ajout,
 * actions groupées et import de la structure existante. Les documents restent stockés
 * dans le type de contenu amidac_rapport, mais sans passer par l'éditeur d'articles.
 */

defined( 'ABSPATH' ) || exit;

const AMIDAC_TOOLKIT_RAPPORTS_PAGE = 'amidac-rapports';

/**
 * Catégories de documents : clé => libellé.
 */
function amidac_toolkit_rapport_categories() {
    $td = 'amidac-toolkit';

    return array(
        'annuel'         => __( 'Rapports annuels', $td ),
        'publication'    => __( 'Autres publications', $td ),
        'reseau'         => __( 'Réseaux & coalitions', $td ),
        'institutionnel' => __( 'Documents institutionnels', $td ),
    );
}

function amidac_toolkit_rapports_capability() {
    return 'edit_others_posts';
}

function amidac_toolkit_rapports_url( $args = array() ) {
    return add_query_arg(
        array_merge( array( 'page' => AMIDAC_TOOLKIT_RAPPORTS_PAGE ), $args ),
        admin_url( 'admin.php' )
    );
}

function amidac_toolkit_rapports_redirect( $args = array() ) {
    wp_safe_redirect( amidac_toolkit_rapports_url( $args ) );
    exit;
}

function amidac_toolkit_rapports_check_access() {
    if ( ! current_user_can( amidac_toolkit_rapports_capability() ) ) {
        wp_die( esc_html__( 'Vous n’avez pas l’autorisation de gérer les rapports.', 'amidac-toolkit' ), '', array( 'response' => 403 ) );
    }
}

/**
 * Lit un document en garantissant qu'il s'agit bien d'un rapport.
 */
function amidac_toolkit_rapports_get( $id ) {
    $post = $id ? get_post( $id ) : null;
    return ( $post && 'amidac_rapport' === $post->post_type ) ? $post : null;
}

// ============================================================================
// MENU ET RESSOURCES
// ============================================================================

function amidac_toolkit_rapports_menu() {
    add_menu_page(
        __( 'Rapports et documents', 'amidac-toolkit' ),
        __( 'Rapports', 'amidac-toolkit' ),
        amidac_toolkit_rapports_capability(),
        AMIDAC_TOOLKIT_RAPPORTS_PAGE,
        'amidac_toolkit_rapports_page',
        'dashicons-media-document',
        8
    );
}
add_action( 'admin_menu', 'amidac_toolkit_rapports_menu' );

function amidac_toolkit_rapports_assets( $hook ) {
    if ( 'toplevel_page_' . AMIDAC_TOOLKIT_RAPPORTS_PAGE !== $hook ) {
        return;
    }

    wp_enqueue_media();

    $css = AMIDAC_TOOLKIT_DIR . 'assets/css/admin.css';
    $js  = AMIDAC_TOOLKIT_DIR . 'assets/js/rapports-admin.js';
    wp_enqueue_style( 'amidac-toolkit-admin', AMIDAC_TOOLKIT_URI . 'assets/css/admin.css', array(), AMIDAC_TOOLKIT_VERSION . '.' . filemtime( $css ) );
    wp_enqueue_script( 'amidac-toolkit-rapports', AMIDAC_TOOLKIT_URI . 'assets/js/rapports-admin.js', array( 'jquery' ), AMIDAC_TOOLKIT_VERSION . '.' . filemtime( $js ), true );
}
add_action( 'admin_enqueue_scripts', 'amidac_toolkit_rapports_assets' );

// ============================================================================
// PAGE : ROUTAGE ET MESSAGES
// ============================================================================

function amidac_toolkit_rapports_page() {
    amidac_toolkit_rapports_check_access();

    $action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- simple navigation.

    echo '<div class="wrap amidac-rapports">';
    amidac_toolkit_rapports_notice();

    if ( in_array( $action, array( 'new', 'edit' ), true ) ) {
        amidac_toolkit_rapports_form();
    } else {
        amidac_toolkit_rapports_list();
    }
    echo '</div>';
}

function amidac_toolkit_rapports_notice() {
    $td       = 'amidac-toolkit';
    $messages = array(
        'saved'    => array( 'success', __( 'Document enregistré.', $td ) ),
        'deleted'  => array( 'success', __( 'Document supprimé.', $td ) ),
        'bulk'     => array( 'success', __( 'Action groupée appliquée.', $td ) ),
        'imported' => array( 'success', __( 'Import terminé : les documents déjà présents ont été conservés.', $td ) ),
        'no_title' => array( 'error', __( 'Le titre est obligatoire.', $td ) ),
        'error'    => array( 'error', __( 'L’opération a échoué.', $td ) ),
    );

    $key = isset( $_GET['message'] ) ? sanitize_key( wp_unslash( $_GET['message'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
    if ( isset( $messages[ $key ] ) ) {
        printf(
            '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
            esc_attr( $messages[ $key ][0] ),
            esc_html( $messages[ $key ][1] )
        );
    }
}

// ============================================================================
// LISTE
// ============================================================================

/**
 * Tous les documents (publiés et brouillons), triés par catégorie puis année décroissante.
 */
function amidac_toolkit_rapports_all() {
    $posts = get_posts( array(
        'post_type'      => 'amidac_rapport',
        'post_status'    => array( 'publish', 'draft' ),
        'posts_per_page' => 500,
        'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
    ) );

    $order = array_flip( array_keys( amidac_toolkit_rapport_categories() ) );
    usort( $posts, function ( $a, $b ) use ( $order ) {
        $ca = $order[ amidac_toolkit_rapport_categorie( $a->ID ) ];
        $cb = $order[ amidac_toolkit_rapport_categorie( $b->ID ) ];
        if ( $ca !== $cb ) {
            return $ca <=> $cb;
        }
        $ya = (int) get_post_meta( $a->ID, '_amidac_annee', true );
        $yb = (int) get_post_meta( $b->ID, '_amidac_annee', true );
        if ( $ya !== $yb ) {
            return $yb <=> $ya;
        }
        return $a->menu_order <=> $b->menu_order;
    } );

    return $posts;
}

/**
 * Catégorie d'un document. Les anciens rapports sans catégorie comptent comme annuels.
 */
function amidac_toolkit_rapport_categorie( $post_id ) {
    $cat = get_post_meta( $post_id, '_amidac_categorie', true );
    return isset( amidac_toolkit_rapport_categories()[ $cat ] ) ? $cat : 'annuel';
}

function amidac_toolkit_rapports_list() {
    $td   = 'amidac-toolkit';
    $cats = amidac_toolkit_rapport_categories();

    $filtre = isset( $_GET['categorie'] ) ? sanitize_key( wp_unslash( $_GET['categorie'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
    $filtre = isset( $cats[ $filtre ] ) ? $filtre : '';
    $recherche = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

    $tous   = amidac_toolkit_rapports_all();
    $totaux = array_fill_keys( array_keys( $cats ), 0 );
    foreach ( $tous as $p ) {
        $totaux[ amidac_toolkit_rapport_categorie( $p->ID ) ]++;
    }

    $lignes = array_filter( $tous, function ( $p ) use ( $filtre, $recherche ) {
        if ( $filtre && amidac_toolkit_rapport_categorie( $p->ID ) !== $filtre ) {
            return false;
        }
        return '' === $recherche || false !== stripos( $p->post_title, $recherche );
    } );

    ?>
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Rapports et documents', $td ); ?></h1>
    <a href="<?php echo esc_url( amidac_toolkit_rapports_url( array( 'action' => 'new' ) ) ); ?>" class="page-title-action"><?php esc_html_e( 'Ajouter un document', $td ); ?></a>
    <hr class="wp-header-end">

    <ul class="subsubsub">
        <li>
            <a href="<?php echo esc_url( amidac_toolkit_rapports_url() ); ?>" class="<?php echo $filtre ? '' : 'current'; ?>">
                <?php esc_html_e( 'Tous', $td ); ?> <span class="count">(<?php echo (int) count( $tous ); ?>)</span>
            </a>
        </li>
        <?php foreach ( $cats as $key => $label ) : ?>
            <li>| <a href="<?php echo esc_url( amidac_toolkit_rapports_url( array( 'categorie' => $key ) ) ); ?>" class="<?php echo $filtre === $key ? 'current' : ''; ?>">
                <?php echo esc_html( $label ); ?> <span class="count">(<?php echo (int) $totaux[ $key ]; ?>)</span>
            </a></li>
        <?php endforeach; ?>
    </ul>

    <form method="get" class="search-box">
        <input type="hidden" name="page" value="<?php echo esc_attr( AMIDAC_TOOLKIT_RAPPORTS_PAGE ); ?>">
        <?php if ( $filtre ) : ?>
            <input type="hidden" name="categorie" value="<?php echo esc_attr( $filtre ); ?>">
        <?php endif; ?>
        <input type="search" name="s" value="<?php echo esc_attr( $recherche ); ?>" placeholder="<?php esc_attr_e( 'Rechercher un titre…', $td ); ?>">
        <button class="button"><?php esc_html_e( 'Rechercher', $td ); ?></button>
    </form>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="amidac-bulk" data-confirm="<?php esc_attr_e( 'Supprimer définitivement les documents cochés ?', $td ); ?>">
        <input type="hidden" name="action" value="amidac_rapport_bulk">
        <?php wp_nonce_field( 'amidac_rapport_bulk', 'amidac_rapport_nonce' ); ?>

        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <select name="bulk">
                    <option value=""><?php esc_html_e( 'Actions groupées', $td ); ?></option>
                    <option value="publish"><?php esc_html_e( 'Publier', $td ); ?></option>
                    <option value="draft"><?php esc_html_e( 'Masquer (brouillon)', $td ); ?></option>
                    <option value="delete"><?php esc_html_e( 'Supprimer', $td ); ?></option>
                </select>
                <button class="button"><?php esc_html_e( 'Appliquer', $td ); ?></button>
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <td class="check-column"><input type="checkbox" id="amidac-select-all"></td>
                    <th class="column-primary"><?php esc_html_e( 'Titre', $td ); ?></th>
                    <th><?php esc_html_e( 'Catégorie', $td ); ?></th>
                    <th><?php esc_html_e( 'Groupe / type', $td ); ?></th>
                    <th style="width:70px"><?php esc_html_e( 'Année', $td ); ?></th>
                    <th><?php esc_html_e( 'Fichier', $td ); ?></th>
                    <th style="width:90px"><?php esc_html_e( 'Statut', $td ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if ( ! $lignes ) : ?>
                <tr><td colspan="7"><?php esc_html_e( 'Aucun document pour le moment.', $td ); ?></td></tr>
            <?php endif; ?>
            <?php foreach ( $lignes as $p ) :
                $id      = $p->ID;
                $fichier = get_post_meta( $id, '_amidac_fichier_url', true );
                $detail  = get_post_meta( $id, '_amidac_groupe', true );
                $detail  = $detail ? $detail : get_post_meta( $id, '_amidac_type_rapport', true );
                $annee   = get_post_meta( $id, '_amidac_annee', true );
                $edit    = amidac_toolkit_rapports_url( array( 'action' => 'edit', 'id' => $id ) );
                $delete  = wp_nonce_url( admin_url( 'admin-post.php?action=amidac_rapport_delete&id=' . $id ), 'amidac_rapport_delete_' . $id );
                ?>
                <tr>
                    <th class="check-column"><input type="checkbox" name="ids[]" value="<?php echo (int) $id; ?>"></th>
                    <td class="column-primary">
                        <strong><a href="<?php echo esc_url( $edit ); ?>"><?php echo esc_html( $p->post_title ); ?></a></strong>
                        <div class="row-actions visible">
                            <a href="<?php echo esc_url( $edit ); ?>"><?php esc_html_e( 'Modifier', $td ); ?></a> |
                            <a href="<?php echo esc_url( $delete ); ?>" class="submitdelete" data-confirm="<?php esc_attr_e( 'Supprimer définitivement ce document ?', $td ); ?>"><?php esc_html_e( 'Supprimer', $td ); ?></a>
                        </div>
                    </td>
                    <td><?php echo esc_html( $cats[ amidac_toolkit_rapport_categorie( $id ) ] ); ?></td>
                    <td><?php echo esc_html( $detail ); ?></td>
                    <td><?php echo esc_html( $annee ); ?></td>
                    <td>
                        <?php if ( $fichier ) : ?>
                            <a href="<?php echo esc_url( $fichier ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Ouvrir', $td ); ?></a>
                        <?php else : ?>
                            <span class="amidac-manquant"><?php esc_html_e( 'À ajouter', $td ); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ( 'publish' === $p->post_status ) : ?>
                            <span class="amidac-statut amidac-statut--on"><?php esc_html_e( 'Publié', $td ); ?></span>
                        <?php else : ?>
                            <span class="amidac-statut"><?php esc_html_e( 'Masqué', $td ); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </form>

    <?php if ( ! $tous ) : ?>
        <div class="amidac-import">
            <h2><?php esc_html_e( 'Démarrer avec la structure actuelle du site', $td ); ?></h2>
            <p><?php esc_html_e( 'Crée en un clic les documents listés aujourd’hui sur la page Rapports & Publications (sans fichier). Il ne reste ensuite qu’à rattacher chaque PDF.', $td ); ?></p>
            <a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=amidac_rapport_import' ), 'amidac_rapport_import' ) ); ?>">
                <?php esc_html_e( 'Importer la structure existante', $td ); ?>
            </a>
        </div>
    <?php endif; ?>
    <?php
}

// ============================================================================
// FORMULAIRE
// ============================================================================

function amidac_toolkit_rapports_form() {
    $td   = 'amidac-toolkit';
    $cats = amidac_toolkit_rapport_categories();

    $id   = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
    $post = amidac_toolkit_rapports_get( $id );
    if ( $id && ! $post ) {
        wp_die( esc_html__( 'Document introuvable.', $td ) );
    }

    $m = function ( $key ) use ( $post ) {
        return $post ? get_post_meta( $post->ID, '_amidac_' . $key, true ) : '';
    };

    $categorie = $post ? amidac_toolkit_rapport_categorie( $post->ID ) : 'annuel';
    if ( ! $post && isset( $_GET['categorie'], $cats[ $_GET['categorie'] ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        $categorie = sanitize_key( wp_unslash( $_GET['categorie'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
    }

    // Groupes déjà utilisés, proposés en suggestion.
    $groupes = array();
    foreach ( amidac_toolkit_rapports_all() as $p ) {
        $g = get_post_meta( $p->ID, '_amidac_groupe', true );
        if ( $g ) {
            $groupes[ $g ] = true;
        }
    }
    ?>
    <h1><?php echo $post ? esc_html__( 'Modifier le document', $td ) : esc_html__( 'Ajouter un document', $td ); ?></h1>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="amidac-form">
        <input type="hidden" name="action" value="amidac_rapport_save">
        <input type="hidden" name="rapport_id" value="<?php echo (int) $id; ?>">
        <?php wp_nonce_field( 'amidac_rapport_save', 'amidac_rapport_nonce' ); ?>

        <table class="form-table" role="presentation"><tbody>
            <tr>
                <th><label for="amidac_titre"><?php esc_html_e( 'Titre', $td ); ?> *</label></th>
                <td><input type="text" id="amidac_titre" name="titre" class="large-text" required value="<?php echo esc_attr( $post ? $post->post_title : '' ); ?>"></td>
            </tr>
            <tr>
                <th><label for="amidac_categorie"><?php esc_html_e( 'Catégorie', $td ); ?></label></th>
                <td>
                    <select id="amidac_categorie" name="categorie">
                        <?php foreach ( $cats as $key => $label ) : ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $categorie, $key ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr data-cats="annuel">
                <th><label for="amidac_type"><?php esc_html_e( 'Type de rapport', $td ); ?></label></th>
                <td>
                    <input type="text" id="amidac_type" name="type" class="regular-text" value="<?php echo esc_attr( $m( 'type_rapport' ) ); ?>" placeholder="<?php esc_attr_e( 'Rapport d’Activités, Rapport Financier…', $td ); ?>">
                </td>
            </tr>
            <tr data-cats="annuel">
                <th><label for="amidac_annee"><?php esc_html_e( 'Année', $td ); ?></label></th>
                <td><input type="number" id="amidac_annee" name="annee" min="1990" max="2100" value="<?php echo esc_attr( $m( 'annee' ) ); ?>"></td>
            </tr>
            <tr data-cats="annuel publication">
                <th><label for="amidac_periode"><?php esc_html_e( 'Date affichée', $td ); ?></label></th>
                <td>
                    <input type="text" id="amidac_periode" name="periode" class="regular-text" value="<?php echo esc_attr( $m( 'periode' ) ); ?>" placeholder="<?php esc_attr_e( 'Décembre 2024', $td ); ?>">
                </td>
            </tr>
            <tr data-cats="institutionnel">
                <th><label for="amidac_groupe"><?php esc_html_e( 'Groupe', $td ); ?></label></th>
                <td>
                    <input type="text" id="amidac_groupe" name="groupe" class="regular-text" list="amidac-groupes" value="<?php echo esc_attr( $m( 'groupe' ) ); ?>" placeholder="<?php esc_attr_e( 'Statuts & Gouvernance', $td ); ?>">
                    <datalist id="amidac-groupes">
                        <?php foreach ( array_keys( $groupes ) as $g ) : ?>
                            <option value="<?php echo esc_attr( $g ); ?>">
                        <?php endforeach; ?>
                    </datalist>
                    <p class="description"><?php esc_html_e( 'Les documents d’un même groupe sont présentés ensemble.', $td ); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="amidac_description"><?php esc_html_e( 'Description', $td ); ?></label></th>
                <td><textarea id="amidac_description" name="description" rows="3" class="large-text"><?php echo esc_textarea( $post ? $post->post_excerpt : '' ); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="amidac_fichier_url"><?php esc_html_e( 'Fichier', $td ); ?></label></th>
                <td>
                    <input type="url" id="amidac_fichier_url" name="fichier_url" class="large-text" value="<?php echo esc_attr( $m( 'fichier_url' ) ); ?>" placeholder="https://…">
                    <input type="hidden" id="amidac_fichier_id" name="fichier_id" value="<?php echo esc_attr( $m( 'fichier_id' ) ); ?>">
                    <p>
                        <button type="button" class="button" id="amidac-choisir-fichier"
                                data-title="<?php esc_attr_e( 'Choisir le fichier du document', $td ); ?>"
                                data-button="<?php esc_attr_e( 'Utiliser ce fichier', $td ); ?>">
                            <?php esc_html_e( 'Choisir dans la médiathèque', $td ); ?>
                        </button>
                    </p>
                    <p class="description"><?php esc_html_e( 'Laissez vide si le fichier n’est pas encore prêt : le site affichera « Bientôt disponible ».', $td ); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="amidac_ordre"><?php esc_html_e( 'Ordre', $td ); ?></label></th>
                <td>
                    <input type="number" id="amidac_ordre" name="ordre" value="<?php echo esc_attr( $post ? $post->menu_order : 0 ); ?>" class="small-text">
                    <p class="description"><?php esc_html_e( 'Les plus petits nombres apparaissent en premier.', $td ); ?></p>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Visibilité', $td ); ?></th>
                <td>
                    <?php $statut = $post ? $post->post_status : 'publish'; ?>
                    <label><input type="radio" name="statut" value="publish" <?php checked( 'draft' !== $statut ); ?>> <?php esc_html_e( 'Publié sur le site', $td ); ?></label><br>
                    <label><input type="radio" name="statut" value="draft" <?php checked( 'draft' === $statut ); ?>> <?php esc_html_e( 'Masqué (brouillon)', $td ); ?></label>
                </td>
            </tr>
        </tbody></table>

        <p class="submit">
            <button class="button button-primary"><?php esc_html_e( 'Enregistrer', $td ); ?></button>
            <a class="button" href="<?php echo esc_url( amidac_toolkit_rapports_url() ); ?>"><?php esc_html_e( 'Retour à la liste', $td ); ?></a>
        </p>
    </form>
    <?php
}

// ============================================================================
// TRAITEMENTS (admin-post)
// ============================================================================

/**
 * Enregistre un document (création ou modification).
 */
function amidac_toolkit_rapport_save() {
    amidac_toolkit_rapports_check_access();
    check_admin_referer( 'amidac_rapport_save', 'amidac_rapport_nonce' );

    $id = isset( $_POST['rapport_id'] ) ? absint( $_POST['rapport_id'] ) : 0;
    if ( $id && ! amidac_toolkit_rapports_get( $id ) ) {
        amidac_toolkit_rapports_redirect( array( 'message' => 'error' ) );
    }

    $field = function ( $key ) {
        return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
    };

    $titre = $field( 'titre' );
    if ( '' === $titre ) {
        amidac_toolkit_rapports_redirect( $id ? array( 'action' => 'edit', 'id' => $id, 'message' => 'no_title' ) : array( 'action' => 'new', 'message' => 'no_title' ) );
    }

    $cats      = amidac_toolkit_rapport_categories();
    $categorie = isset( $cats[ $field( 'categorie' ) ] ) ? $field( 'categorie' ) : 'annuel';

    $args = array(
        'post_type'    => 'amidac_rapport',
        'post_title'   => $titre,
        'post_excerpt' => isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '',
        'post_status'  => 'draft' === $field( 'statut' ) ? 'draft' : 'publish',
        'menu_order'   => isset( $_POST['ordre'] ) ? intval( $_POST['ordre'] ) : 0,
    );

    $result = $id
        ? wp_update_post( array_merge( $args, array( 'ID' => $id ) ), true )
        : wp_insert_post( $args, true );

    if ( is_wp_error( $result ) || ! $result ) {
        amidac_toolkit_rapports_redirect( array( 'message' => 'error' ) );
    }

    // Un fichier choisi dans la médiathèque fait foi sur l'adresse saisie.
    $fichier_id  = isset( $_POST['fichier_id'] ) ? absint( $_POST['fichier_id'] ) : 0;
    $fichier_url = isset( $_POST['fichier_url'] ) ? esc_url_raw( wp_unslash( $_POST['fichier_url'] ) ) : '';
    if ( $fichier_id && 'attachment' === get_post_type( $fichier_id ) ) {
        $fichier_url = wp_get_attachment_url( $fichier_id );
    } else {
        $fichier_id = 0;
    }

    $annee = isset( $_POST['annee'] ) ? absint( $_POST['annee'] ) : 0;
    $meta  = array(
        '_amidac_categorie'    => $categorie,
        '_amidac_type_rapport' => $field( 'type' ),
        '_amidac_annee'        => $annee ? $annee : '',
        '_amidac_periode'      => $field( 'periode' ),
        '_amidac_groupe'       => $field( 'groupe' ),
        '_amidac_fichier_url'  => $fichier_url,
        '_amidac_fichier_id'   => $fichier_id ? $fichier_id : '',
    );
    foreach ( $meta as $key => $value ) {
        update_post_meta( $result, $key, $value );
    }

    amidac_toolkit_rapports_redirect( array( 'message' => 'saved' ) );
}
add_action( 'admin_post_amidac_rapport_save', 'amidac_toolkit_rapport_save' );

/**
 * Supprime un document (le fichier de la médiathèque est conservé).
 */
function amidac_toolkit_rapport_delete() {
    amidac_toolkit_rapports_check_access();

    $id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
    check_admin_referer( 'amidac_rapport_delete_' . $id );

    if ( amidac_toolkit_rapports_get( $id ) ) {
        wp_delete_post( $id, true );
        amidac_toolkit_rapports_redirect( array( 'message' => 'deleted' ) );
    }
    amidac_toolkit_rapports_redirect( array( 'message' => 'error' ) );
}
add_action( 'admin_post_amidac_rapport_delete', 'amidac_toolkit_rapport_delete' );

/**
 * Actions groupées : publier, masquer, supprimer.
 */
function amidac_toolkit_rapport_bulk() {
    amidac_toolkit_rapports_check_access();
    check_admin_referer( 'amidac_rapport_bulk', 'amidac_rapport_nonce' );

    $bulk = isset( $_POST['bulk'] ) ? sanitize_key( wp_unslash( $_POST['bulk'] ) ) : '';
    $ids  = isset( $_POST['ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['ids'] ) ) : array();

    if ( ! in_array( $bulk, array( 'publish', 'draft', 'delete' ), true ) || ! $ids ) {
        amidac_toolkit_rapports_redirect();
    }

    foreach ( $ids as $id ) {
        if ( ! amidac_toolkit_rapports_get( $id ) ) {
            continue;
        }
        if ( 'delete' === $bulk ) {
            wp_delete_post( $id, true );
        } else {
            wp_update_post( array( 'ID' => $id, 'post_status' => $bulk ) );
        }
    }

    amidac_toolkit_rapports_redirect( array( 'message' => 'bulk' ) );
}
add_action( 'admin_post_amidac_rapport_bulk', 'amidac_toolkit_rapport_bulk' );

// ============================================================================
// IMPORT DE LA STRUCTURE EXISTANTE
// ============================================================================

/**
 * Documents listés aujourd'hui sur la page Rapports & Publications (sans fichier).
 * Format : [ catégorie, titre, groupe, type, année, date affichée, description ].
 */
function amidac_toolkit_rapports_exemples() {
    return array(
        array( 'annuel', 'Rapport d’Activités 2024', '', 'Rapport d’Activités', 2024, 'Décembre 2024', 'Document complet couvrant l’année 2024 avec projets, réalisations et impact.' ),
        array( 'annuel', 'Rapport Financier 2024', '', 'Rapport Financier', 2024, 'Décembre 2024', 'Rapport complet du volet financier avec bilan comptable et gestion des ressources.' ),
        array( 'annuel', 'Rapport d’Activités 2023', '', 'Rapport d’Activités', 2023, 'Décembre 2023', 'Rapport d’activités 2023 documentant nos opérations et impacts territoriaux.' ),
        array( 'publication', 'Fiche de Présentation', '', '', 0, '', 'Document récapitulatif présentant AMIDAC, sa mission, ses valeurs et son cadre opérationnel.' ),
        array( 'publication', 'Activités Principales', '', '', 0, '', 'Description détaillée de nos 7 axes d’intervention et domaines d’action prioritaires.' ),
        array( 'reseau', 'ALHI', '', '', 0, '', 'Alliance Humanitaire Internationale - Rapports d’activités et contributions.' ),
        array( 'reseau', 'CCONAT', '', '', 0, '', 'Cadre de Concertation des Organisations Nationales du Sud-Kivu.' ),
        array( 'reseau', 'Forum Humanitaire', '', '', 0, '', 'Forum Humanitaire des ONG de la Zone de Santé de Nundu.' ),
        array( 'reseau', 'Fizi Amkeni', '', '', 0, '', 'Consortium Fizi Amkeni - Initiatives territoriales et collaborations.' ),
        array( 'institutionnel', 'Statuts d’AMIDAC', 'Statuts & Gouvernance', '', 0, '', '' ),
        array( 'institutionnel', 'Politique de Gouvernance', 'Statuts & Gouvernance', '', 0, '', '' ),
        array( 'institutionnel', 'Organigramme Institutionnel', 'Statuts & Gouvernance', '', 0, '', '' ),
        array( 'institutionnel', 'Politique de Protection Enfants', 'Politiques & Procédures', '', 0, '', '' ),
        array( 'institutionnel', 'Code de Conduite', 'Politiques & Procédures', '', 0, '', '' ),
        array( 'institutionnel', 'Politique de Gestion Financière', 'Politiques & Procédures', '', 0, '', '' ),
        array( 'institutionnel', 'Vision & Mission d’AMIDAC', 'Documents de Base', '', 0, '', '' ),
        array( 'institutionnel', 'Plan Stratégique 2024-2026', 'Documents de Base', '', 0, '', '' ),
        array( 'institutionnel', 'Infos Institutionnelles Complètes', 'Documents de Base', '', 0, '', '' ),
    );
}

/**
 * Importe les exemples ; un document de même titre et même catégorie n'est jamais recréé.
 */
function amidac_toolkit_rapport_import() {
    amidac_toolkit_rapports_check_access();
    check_admin_referer( 'amidac_rapport_import' );

    $existants = array();
    foreach ( amidac_toolkit_rapports_all() as $p ) {
        $existants[ amidac_toolkit_rapport_categorie( $p->ID ) . '|' . $p->post_title ] = true;
    }

    foreach ( amidac_toolkit_rapports_exemples() as $ordre => $e ) {
        list( $categorie, $titre, $groupe, $type, $annee, $periode, $description ) = $e;
        if ( isset( $existants[ $categorie . '|' . $titre ] ) ) {
            continue;
        }

        $id = wp_insert_post( array(
            'post_type'    => 'amidac_rapport',
            'post_title'   => $titre,
            'post_excerpt' => $description,
            'post_status'  => 'publish',
            'menu_order'   => $ordre,
        ) );
        if ( ! $id || is_wp_error( $id ) ) {
            continue;
        }

        update_post_meta( $id, '_amidac_categorie', $categorie );
        update_post_meta( $id, '_amidac_groupe', $groupe );
        update_post_meta( $id, '_amidac_type_rapport', $type );
        update_post_meta( $id, '_amidac_annee', $annee ? $annee : '' );
        update_post_meta( $id, '_amidac_periode', $periode );
    }

    amidac_toolkit_rapports_redirect( array( 'message' => 'imported' ) );
}
add_action( 'admin_post_amidac_rapport_import', 'amidac_toolkit_rapport_import' );
