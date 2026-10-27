<?php
/**
 * AMIDAC — Personnalisations de l'Administration WordPress
 *
 * @package AMIDAC_Kadence_Child
 */

defined( 'ABSPATH' ) || exit;

// ============================================================================
// COLONNES PERSONNALISÉES — CPT Projets
// ============================================================================

/**
 * Ajouter des colonnes dans la liste admin des projets.
 */
function amidac_projet_columns( $columns ) {
    $new = array();
    foreach ( $columns as $key => $value ) {
        $new[ $key ] = $value;
        if ( 'title' === $key ) {
            $new['amidac_statut']       = __( 'Statut',       'amidac-kadence-child' );
            $new['amidac_secteur']      = __( 'Secteur',      'amidac-kadence-child' );
            $new['amidac_beneficiaires']= __( 'Bénéficiaires','amidac-kadence-child' );
            $new['amidac_zone']         = __( 'Zone',         'amidac-kadence-child' );
        }
    }
    return $new;
}
add_filter( 'manage_amidac_projet_posts_columns', 'amidac_projet_columns' );

/**
 * Remplir les colonnes.
 */
function amidac_projet_column_content( $column, $post_id ) {
    switch ( $column ) {
        case 'amidac_statut':
            $statut = get_post_meta( $post_id, '_amidac_statut', true );
            $labels = array(
                'en_cours'       => '<span style="color:#2E7D32;font-weight:600;">🟢 En cours</span>',
                'termine'        => '<span style="color:#555;font-weight:600;">✅ Terminé</span>',
                'en_preparation' => '<span style="color:#1A237E;font-weight:600;">🔵 En préparation</span>',
                'suspendu'       => '<span style="color:#c62828;font-weight:600;">🔴 Suspendu</span>',
            );
            echo isset( $labels[ $statut ] ) ? $labels[ $statut ] : '<span style="color:#aaa;">—</span>';
            break;

        case 'amidac_secteur':
            $terms = get_the_terms( $post_id, 'secteur' );
            if ( $terms && ! is_wp_error( $terms ) ) {
                $names = wp_list_pluck( $terms, 'name' );
                echo esc_html( implode( ', ', $names ) );
            } else {
                $label = get_post_meta( $post_id, '_amidac_secteur_label', true );
                echo $label ? esc_html( $label ) : '<span style="color:#aaa;">—</span>';
            }
            break;

        case 'amidac_beneficiaires':
            $n = get_post_meta( $post_id, '_amidac_beneficiaires', true );
            echo $n ? '<strong>' . number_format( intval( $n ), 0, ',', ' ' ) . '</strong>' : '<span style="color:#aaa;">—</span>';
            break;

        case 'amidac_zone':
            $zone = get_post_meta( $post_id, '_amidac_zone', true );
            echo $zone ? esc_html( $zone ) : '<span style="color:#aaa;">—</span>';
            break;
    }
}
add_action( 'manage_amidac_projet_posts_custom_column', 'amidac_projet_column_content', 10, 2 );

// ============================================================================
// SUPPRESSION DE COLONNES INUTILES EN ADMIN
// ============================================================================

function amidac_remove_dashboard_widgets() {
    // remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    // Décommentez selon vos besoins
}
add_action( 'wp_dashboard_setup', 'amidac_remove_dashboard_widgets' );

// ============================================================================
// PIED DE PAGE ADMIN
// ============================================================================

function amidac_admin_footer_text() {
    echo sprintf(
        '<span>%s — Développé par <a href="https://emotouchgroup.com" target="_blank"><strong>Emo Touch Group</strong></a> · <em>Isaya M\'lela Pombo</em></span>',
        esc_html( AMIDAC_THEME_NAME )
    );
}
add_filter( 'admin_footer_text', 'amidac_admin_footer_text' );
