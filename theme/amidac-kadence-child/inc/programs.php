<?php
/**
 * AMIDAC — Fonctions pour la page "Nos Programmes"
 * 
 * Gestion dynamique des projets associés aux 7 axes d'intervention.
 *
 * @package     AMIDAC_Kadence_Child
 * @subpackage  Programs
 * @author      Isaya M'lela Pombo — Emo Touch Group
 * @version     1.0.0
 */

defined( 'ABSPATH' ) || exit;


// ============================================================================
// 1. RÉCUPÉRER LES PROJETS ASSOCIÉS À UN PROGRAMME
// ============================================================================

/**
 * Récupère les 2 projets associés à un programme spécifique.
 *
 * @param string $program_id Slug du programme (ex: 'peace-non-violence')
 * @return WP_Query          Objet de requête WordPress
 *
 * Utilisation:
 *   $projects = amidac_get_projects_by_program( 'peace-non-violence' );
 *   if ( $projects->have_posts() ) {
 *       while ( $projects->have_posts() ) {
 *           $projects->the_post();
 *           // Afficher le projet
 *       }
 *   }
 */
function amidac_get_projects_by_program( $program_id ) {
    
    // Arguments de la requête
    $args = array(
        'post_type'      => 'amidac_projet',    // Type de contenu: projet
        'posts_per_page' => 2,                   // Limiter à 2 projets
        'orderby'        => 'date',              // Trier par date
        'order'          => 'DESC',              // Plus récent d'abord
        'post_status'    => 'publish',           // Seulement les publiés
        'tax_query'      => array(               // ← CHANGÉ
            array(
                'taxonomy' => 'programme',       // ← CHANGÉ
                'field'    => 'slug',            // ← CHANGÉ
                'terms'    => $program_id,       // ← CHANGÉ
            )
        )
    );
    // Exécuter la requête
    return new WP_Query( $args );
}


// ============================================================================
// 2. AFFICHER LES PROJETS D'UN PROGRAMME (HTML)
// ============================================================================

/**
 * Affiche les 2 projets d'un programme en format HTML.
 * 
 * Utilisation dans Elementor:
 *   <?php amidac_display_program_projects( 'peace-non-violence' ); ?>
 *
 * @param string $program_id Slug du programme
 */
function amidac_display_program_projects( $program_id ) {
    
    // Récupérer les projets
    $projects_query = amidac_get_projects_by_program( $program_id );
    
    // Vérifier s'il y a des résultats
    if ( $projects_query->have_posts() ) {
        
        echo '<div class="projects-grid-2">';
        
        // Boucle sur les projets
        while ( $projects_query->have_posts() ) {
            $projects_query->the_post();
            
            // Variables du projet
            $project_id    = get_the_ID();
            $project_title = get_the_title();
            $project_link  = get_permalink();
            $project_year  = get_the_date( 'Y' );
            
            // Image avec fallback
            $project_image = get_the_post_thumbnail_url( $project_id, 'amidac-card' );
            if ( ! $project_image ) {
                $project_image = AMIDAC_CHILD_URI . '/assets/images/placeholder.svg';
                if ( ! file_exists( AMIDAC_CHILD_DIR . '/assets/images/placeholder.svg' ) ) {
                    // Utiliser une image SVG inline si fichier n'existe pas
                    $project_image = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="600" height="400"%3E%3Crect width="600" height="400" fill="%23f0f0f0"/%3E%3Ctext x="50%25" y="50%25" font-size="18" fill="%23999" text-anchor="middle" dy=".3em"%3EImage non disponible%3C/text%3E%3C/svg%3E';
                }
            }
            
            ?>
            <!-- CARTE PROJET -->
            <div class="project-item">
                
                <!-- IMAGE -->
                <div class="project-image">
                    <a href="<?php echo esc_url( $project_link ); ?>">
                        <img 
                            src="<?php echo esc_url( $project_image ); ?>" 
                            alt="<?php echo esc_attr( $project_title ); ?>"
                            loading="lazy"
                            width="600"
                            height="400"
                        >
                    </a>
                </div>
                
                <!-- INFO -->
                <div class="project-info">
                    <a href="<?php echo esc_url( $project_link ); ?>" class="project-link">
                        <h5 class="project-title">
                            <?php echo esc_html( $project_title ); ?>
                        </h5>
                    </a>
                    <p class="project-meta">
                        <?php echo esc_html( $project_year ); ?>
                    </p>
                </div>
                
            </div><!-- FIN CARTE PROJET -->
            
            <?php
        }
        
        echo '</div><!-- FIN GRILLE PROJETS -->';
        
        // Réinitialiser la boucle WordPress
        wp_reset_postdata();
        
    } else {
        
        // Pas de projets trouvés
        echo '<p class="text-muted">' . 
             esc_html__( 'Aucun projet trouvé pour ce programme.', 'amidac-kadence-child' ) . 
             '</p>';
        
    }
}


// ============================================================================
// 3. API REST — ENDPOINTS POUR AJAX
// ============================================================================

/**
 * Enregistre les endpoints API REST pour charger les projets en AJAX.
 * 
 * Endpoints:
 *   /wp-json/amidac/v1/projects/[PROGRAM-ID]
 *
 * Exemple:
 *   /wp-json/amidac/v1/projects/peace-non-violence
 */
function amidac_register_api_endpoints() {
    
    // Endpoint: Récupérer les projets d'un programme
    register_rest_route( 'amidac/v1', '/projects/(?P<program_id>[a-zA-Z0-9\-]+)', array(
        'methods'             => 'GET',
        'callback'            => 'amidac_api_get_projects',
        'permission_callback' => '__return_true'
    ) );
}
add_action( 'rest_api_init', 'amidac_register_api_endpoints' );


/**
 * Callback de l'API REST — Retourner les projets en JSON.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function amidac_api_get_projects( $request ) {
    
    // Récupérer l'ID du programme depuis l'URL
    $program_id = $request->get_param( 'program_id' );
    $program_id = sanitize_text_field( $program_id );
    
    // Récupérer les projets
    $projects_query = amidac_get_projects_by_program( $program_id );
    
    // Tableau des résultats
    $projects = array();
    
    // Boucle sur les projets
    if ( $projects_query->have_posts() ) {
        while ( $projects_query->have_posts() ) {
            $projects_query->the_post();
            
            $project_id = get_the_ID();
            
            // Image avec fallback
            $project_image = get_the_post_thumbnail_url( $project_id, 'amidac-card' );
            if ( ! $project_image ) {
                $project_image = null;
            }
            
            // Ajouter au tableau
            $projects[] = array(
                'id'       => $project_id,
                'title'    => get_the_title(),
                'link'     => get_permalink(),
                'image'    => $project_image,
                'date'     => get_the_date( 'Y-m-d' ),
                'excerpt'  => get_the_excerpt()
            );
        }
        wp_reset_postdata();
    }
    
    // Retourner en JSON
    return new WP_REST_Response( $projects, 200 );
}


// ============================================================================
// 4. FILTRAGE DES PROJETS PAR PROGRAMME (URL)
// ============================================================================

/**
 * Modifie la requête principale si le paramètre ?programme=xxx est présent.
 * 
 * Permet de filtrer les projets par programme sur la page d'archive.
 * 
 * Utilisation:
 *   /nos-projets/?programme=peace-non-violence
 *   /projets/?programme=protection-enfants-femmes
 */
function amidac_filter_projects_by_program() {
    
    // Vérifier s'le paramètre ?programme= est présent
    if ( isset( $_GET['programme'] ) && ! is_admin() ) {
        
        // Récupérer et nettoyer le paramètre
        $program_filter = sanitize_text_field( $_GET['programme'] );
        
        // Ajouter un filtre à la requête principale de WordPress
        add_filter( 'pre_get_posts', function( $query ) use ( $program_filter ) {
            
            // S'assurer que c'est la requête principale
            if ( ! is_admin() && $query->is_main_query() ) {
                
                // Ajouter la requête meta
                $query->set( 'meta_query', array(
                    array(
                        'key'     => 'programs_associated',
                        'value'   => $program_filter,
                        'compare' => 'LIKE'
                    )
                ) );
            }
            
            return $query;
        } );
    }
}
add_action( 'wp', 'amidac_filter_projects_by_program' );


// ============================================================================
// 5. UTILITAIRES — LISTES DE PROGRAMMES
// ============================================================================

/**
 * Retourne la liste complète de tous les programmes avec leurs IDs.
 * 
 * Utilisation:
 *   $programs = amidac_get_all_programs();
 *   foreach ( $programs as $id => $label ) {
 *       echo $label;
 *   }
 *
 * @return array Tableau [id => label]
 */
function amidac_get_all_programs() {
    return array(
        'peace-non-violence'        => __( 'Promotion de la Paix & Non-violence',         'amidac-kadence-child' ),
        'protection-enfants-femmes' => __( 'Protection Enfants & Femmes',                  'amidac-kadence-child' ),
        'environnement-ecosystem'   => __( 'Environnement & Écosystème',                   'amidac-kadence-child' ),
        'sante-integree'            => __( 'Santé Intégrée',                               'amidac-kadence-child' ),
        'securite-alimentaire'      => __( 'Sécurité Alimentaire & Sociale',               'amidac-kadence-child' ),
        'developpement-integral'    => __( 'Développement Intégral',                       'amidac-kadence-child' ),
        'education'                 => __( 'Éducation Formelle & Non-formelle',            'amidac-kadence-child' ),
    );
}


/**
 * Convertit un ID de programme en label lisible.
 * 
 * Utilisation:
 *   echo amidac_get_program_label( 'peace-non-violence' );
 *   // Affiche: "Promotion de la Paix & Non-violence"
 *
 * @param string $program_id Slug du programme
 * @return string             Label du programme
 */
function amidac_get_program_label( $program_id ) {
    $programs = amidac_get_all_programs();
    return isset( $programs[ $program_id ] ) ? $programs[ $program_id ] : $program_id;
}


/**
 * Obtient les infos détaillées d'un programme (optionnel).
 * 
 * Peut être étendu pour inclure plus de détails:
 * - Description complète
 * - Icône
 * - Couleur accent
 * - etc.
 *
 * @param string $program_id Slug du programme
 * @return array             Données du programme
 */
function amidac_get_program_details( $program_id ) {
    
    // Détails des programmes (peuvent être stockés dans des options)
    $programs = array(
        'peace-non-violence' => array(
            'id'    => 'peace-non-violence',
            'label' => __( 'Promotion de la Paix & Non-violence', 'amidac-kadence-child' ),
            'color' => 'var(--amidac-secondary)',
            'icon'  => '🕊️',
        ),
        'protection-enfants-femmes' => array(
            'id'    => 'protection-enfants-femmes',
            'label' => __( 'Protection Enfants & Femmes', 'amidac-kadence-child' ),
            'color' => 'var(--amidac-secondary)',
            'icon'  => '👨‍👩‍👧‍👦',
        ),
        'environnement-ecosystem' => array(
            'id'    => 'environnement-ecosystem',
            'label' => __( 'Environnement & Écosystème', 'amidac-kadence-child' ),
            'color' => 'var(--amidac-secondary)',
            'icon'  => '🌱',
        ),
        'sante-integree' => array(
            'id'    => 'sante-integree',
            'label' => __( 'Santé Intégrée', 'amidac-kadence-child' ),
            'color' => 'var(--amidac-secondary)',
            'icon'  => '🏥',
        ),
        'securite-alimentaire' => array(
            'id'    => 'securite-alimentaire',
            'label' => __( 'Sécurité Alimentaire & Sociale', 'amidac-kadence-child' ),
            'color' => 'var(--amidac-accent-gold)',
            'icon'  => '🌾',
        ),
        'developpement-integral' => array(
            'id'    => 'developpement-integral',
            'label' => __( 'Développement Intégral', 'amidac-kadence-child' ),
            'color' => 'var(--amidac-secondary)',
            'icon'  => '💼',
        ),
        'education' => array(
            'id'    => 'education',
            'label' => __( 'Éducation Formelle & Non-formelle', 'amidac-kadence-child' ),
            'color' => 'var(--amidac-secondary)',
            'icon'  => '📚',
        ),
    );
    
    // Retourner les détails du programme ou un tableau vide
    return isset( $programs[ $program_id ] ) ? $programs[ $program_id ] : array();
}


// ============================================================================
// 6. FILTRES & ACTIONS GLOBALES (Optionnel)
// ============================================================================

/**
 * Hook personnalisé: avant d'afficher les projets
 * Permet aux développeurs d'ajouter du code avant les projets.
 * 
 * Utilisation:
 *   add_action( 'amidac_before_program_projects', function( $program_id ) {
 *       echo "Projets pour: " . $program_id;
 *   } );
 */
// do_action( 'amidac_before_program_projects', $program_id );


/**
 * Filtrer les arguments de la requête WP_Query des projets.
 * 
 * Utilisation:
 *   add_filter( 'amidac_projects_query_args', function( $args ) {
 *       $args['posts_per_page'] = 3; // Afficher 3 au lieu de 2
 *       return $args;
 *   } );
 */
// $args = apply_filters( 'amidac_projects_query_args', $args );


// ============================================================================
// 7. SHORTCODE (Optionnel)
// ============================================================================

/**
 * Shortcode pour afficher les projets d'un programme.
 * 
 * Utilisation:
 *   [amidac_program_projects id="peace-non-violence"]
 *
 * @param array $atts Attributs du shortcode
 */
function amidac_program_projects_shortcode( $atts ) {
    
    $atts = shortcode_atts( array(
        'id' => 'peace-non-violence',
    ), $atts );
    
    ob_start();
    amidac_display_program_projects( sanitize_text_field( $atts['id'] ) );
    return ob_get_clean();
}
add_shortcode( 'amidac_program_projects', 'amidac_program_projects_shortcode' );


// ============================================================================
// ✅ FIN — inc/programs.php
// ============================================================================

/**
 * Résumé des fonctions disponibles:
 * 
 * PUBLIC (à utiliser partout):
 * ─────────────────────────────
 * 1. amidac_get_projects_by_program( $program_id )
 *    → Récupère la WP_Query des 2 projets
 *
 * 2. amidac_display_program_projects( $program_id )
 *    → Affiche les projets en HTML (utilisable dans Elementor)
 *
 * 3. amidac_get_all_programs()
 *    → Retourne le tableau de tous les programmes
 *
 * 4. amidac_get_program_label( $program_id )
 *    → Convertit un ID en label lisible
 *
 * 5. amidac_get_program_details( $program_id )
 *    → Retourne les détails complets du programme
 *
 * API & FILTERS:
 * ──────────────
 * • REST Endpoint: /wp-json/amidac/v1/projects/[ID]
 * • Filtrage URL: ?programme=ID
 * • Shortcode: [amidac_program_projects id="ID"]
 */
 
 
 
 
 // ============================================================================
// ACTIVER LES SHORTCODES DANS LES WIDGETS HTML D'ELEMENTOR
// À ajouter à la FIN de inc/programs.php (avant rien, juste à la fin)
// ============================================================================

add_filter( 'elementor/widget/render_content', function( $content, $widget ) {
    if ( 'html' === $widget->get_name() ) {
        $content = do_shortcode( $content );
    }
    return $content;
}, 10, 2 );