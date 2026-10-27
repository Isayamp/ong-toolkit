<?php
/**
 * AMIDAC — Widget Elementor : Grille de Projets
 *
 * Widget qui affiche les projets AMIDAC en grille
 * avec le design identique au HTML de référence (1 grande carte + petites).
 * Tous les paramètres sont éditables dans Elementor.
 *
 * @package AMIDAC_Kadence_Child
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;

class AMIDAC_Widget_Projets_Grid extends Widget_Base {

    // ========================================================================
    // IDENTITÉ DU WIDGET
    // ========================================================================

    public function get_name()  { return 'amidac-projets-grid'; }
    public function get_title() { return __( 'Projets — Grille AMIDAC', 'amidac-kadence-child' ); }
    public function get_icon()  { return 'eicon-gallery-grid'; }

    public function get_categories() { return array( 'amidac-widgets' ); }
    public function get_keywords()   { return array( 'projets', 'grille', 'amidac', 'portfolio' ); }

    public function get_style_depends()  { return array( 'amidac-elementor-widgets' ); }
    public function get_script_depends() { return array( 'amidac-main' ); }

    // ========================================================================
    // CONTROLS (PANNEAU ELEMENTOR)
    // ========================================================================

    protected function register_controls() {

        // ====================================================================
        // ONGLET CONTENU
        // ====================================================================

        // --- Section : En-tête ---
        $this->start_controls_section( 'section_header', array(
            'label' => __( '📝 En-tête de section', 'amidac-kadence-child' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'pretitle', array(
            'label'       => __( 'Pré-titre (étiquette)', 'amidac-kadence-child' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( '📁 Nos Projets', 'amidac-kadence-child' ),
            'placeholder' => __( 'Ex : Nos Projets', 'amidac-kadence-child' ),
            'dynamic'     => array( 'active' => true ),
        ) );

        $this->add_control( 'title', array(
            'label'       => __( 'Titre principal', 'amidac-kadence-child' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Des Actions Concrètes sur le Terrain', 'amidac-kadence-child' ),
            'label_block' => true,
            'dynamic'     => array( 'active' => true ),
        ) );

        $this->add_control( 'subtitle', array(
            'label'       => __( 'Sous-titre / Description', 'amidac-kadence-child' ),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => __( "Depuis 2015, AMIDAC a mené plus de 10 projets à fort impact dans la zone de santé de Nundu.", 'amidac-kadence-child' ),
            'rows'        => 3,
            'dynamic'     => array( 'active' => true ),
        ) );

        $this->add_control( 'show_header', array(
            'label'        => __( "Afficher l'en-tête", 'amidac-kadence-child' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Oui', 'amidac-kadence-child' ),
            'label_off'    => __( 'Non', 'amidac-kadence-child' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ) );

        $this->end_controls_section();

        // --- Section : Source des projets ---
        $this->start_controls_section( 'section_source', array(
            'label' => __( '⚙️ Source des projets', 'amidac-kadence-child' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'source', array(
            'label'   => __( 'Source des données', 'amidac-kadence-child' ),
            'type'    => Controls_Manager::SELECT,
            'options' => array(
                'cpt'    => __( 'CPT WordPress (amidac_projet)', 'amidac-kadence-child' ),
                'manual' => __( 'Saisie manuelle', 'amidac-kadence-child' ),
            ),
            'default' => 'cpt',
        ) );

        // CPT options
        $this->add_control( 'posts_per_page', array(
            'label'      => __( 'Nombre de projets', 'amidac-kadence-child' ),
            'type'       => Controls_Manager::NUMBER,
            'min'        => 1,
            'max'        => 12,
            'step'       => 1,
            'default'    => 5,
            'condition'  => array( 'source' => 'cpt' ),
        ) );

        $this->add_control( 'orderby', array(
            'label'     => __( 'Trier par', 'amidac-kadence-child' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => array(
                'date'       => __( 'Date', 'amidac-kadence-child' ),
                'title'      => __( 'Titre', 'amidac-kadence-child' ),
                'menu_order' => __( 'Ordre menu', 'amidac-kadence-child' ),
                'rand'       => __( 'Aléatoire', 'amidac-kadence-child' ),
            ),
            'default'  => 'date',
            'condition' => array( 'source' => 'cpt' ),
        ) );

        $this->add_control( 'order', array(
            'label'     => __( 'Ordre', 'amidac-kadence-child' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => array(
                'DESC' => __( 'Décroissant (récent en premier)', 'amidac-kadence-child' ),
                'ASC'  => __( 'Croissant (ancien en premier)',   'amidac-kadence-child' ),
            ),
            'default'   => 'DESC',
            'condition' => array( 'source' => 'cpt' ),
        ) );

        // Filtre par taxonomie secteur
        $sectors = array( '' => __( 'Tous les secteurs', 'amidac-kadence-child' ) );
        $terms   = get_terms( array( 'taxonomy' => 'secteur', 'hide_empty' => false ) );
        if ( ! is_wp_error( $terms ) && $terms ) {
            foreach ( $terms as $term ) {
                $sectors[ $term->slug ] = $term->name;
            }
        }
        $this->add_control( 'filter_secteur', array(
            'label'     => __( 'Filtrer par secteur', 'amidac-kadence-child' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => $sectors,
            'default'   => '',
            'condition' => array( 'source' => 'cpt' ),
        ) );

        $this->end_controls_section();

        // --- Section : Projets manuels ---
        $this->start_controls_section( 'section_manual', array(
            'label'     => __( '✏️ Projets manuels', 'amidac-kadence-child' ),
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => array( 'source' => 'manual' ),
        ) );

        $repeater = new Repeater();

        $repeater->add_control( 'projet_titre', array(
            'label'       => __( 'Titre du projet', 'amidac-kadence-child' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Nom du projet', 'amidac-kadence-child' ),
            'label_block' => true,
            'dynamic'     => array( 'active' => true ),
        ) );

        $repeater->add_control( 'projet_description', array(
            'label'   => __( 'Description courte', 'amidac-kadence-child' ),
            'type'    => Controls_Manager::TEXTAREA,
            'default' => __( 'Description du projet et de son impact sur les communautés.', 'amidac-kadence-child' ),
            'rows'    => 3,
            'dynamic' => array( 'active' => true ),
        ) );

        $repeater->add_control( 'projet_tag', array(
            'label'   => __( 'Étiquette / Secteur', 'amidac-kadence-child' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Éducation', 'amidac-kadence-child' ),
            'dynamic' => array( 'active' => true ),
        ) );

        $repeater->add_control( 'projet_image', array(
            'label'   => __( 'Image du projet', 'amidac-kadence-child' ),
            'type'    => Controls_Manager::MEDIA,
            'default' => array( 'url' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&q=80' ),
        ) );

        $repeater->add_control( 'projet_lien', array(
            'label'       => __( 'Lien', 'amidac-kadence-child' ),
            'type'        => Controls_Manager::URL,
            'placeholder' => __( 'https://...', 'amidac-kadence-child' ),
            'dynamic'     => array( 'active' => true ),
        ) );

        $repeater->add_control( 'projet_tag_couleur', array(
            'label'   => __( "Couleur de l'étiquette", 'amidac-kadence-child' ),
            'type'    => Controls_Manager::SELECT,
            'options' => array(
                'vert'    => __( 'Vert (défaut)', 'amidac-kadence-child' ),
                'bleu'    => __( 'Bleu',          'amidac-kadence-child' ),
                'or'      => __( 'Or / Orange',   'amidac-kadence-child' ),
                'rouge'   => __( 'Rouge',          'amidac-kadence-child' ),
                'violet'  => __( 'Violet',         'amidac-kadence-child' ),
            ),
            'default' => 'vert',
        ) );

        $this->add_control( 'projets_list', array(
            'label'       => __( 'Projets', 'amidac-kadence-child' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => array(
                array(
                    'projet_titre'       => 'Kikapo Changu ni Maisha Yangu',
                    'projet_description' => "Renforcement de l'autonomisation économique des femmes et filles déplacées dans 5 aires de santé.",
                    'projet_tag'         => 'Autonomisation',
                    'projet_image'       => array( 'url' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&q=80' ),
                ),
                array(
                    'projet_titre'       => 'Twajihudumiya Wenyewe',
                    'projet_description' => 'Construction de passerelles et latrines pour la sécurité et la santé communautaire.',
                    'projet_tag'         => 'Infrastructure',
                    'projet_image'       => array( 'url' => 'https://images.unsplash.com/photo-1542810634-71277d95dcbb?w=600&q=80' ),
                ),
                array(
                    'projet_titre'       => 'Projet SIMAMA',
                    'projet_description' => "Accompagnement scolaire des enfants déplacés, orphelins et enfants de la rue.",
                    'projet_tag'         => 'Éducation',
                    'projet_image'       => array( 'url' => 'https://images.unsplash.com/photo-1509099836639-18ba1795216d?w=600&q=80' ),
                ),
                array(
                    'projet_titre'       => 'Jeunes Arbres à Palabre',
                    'projet_description' => 'Reboisement et lutte contre le réchauffement climatique dans 5 aires de santé.',
                    'projet_tag'         => 'Environnement',
                    'projet_image'       => array( 'url' => 'https://images.unsplash.com/photo-1497486751825-1233686d5d80?w=600&q=80' ),
                ),
                array(
                    'projet_titre'       => 'Bâtissons la Paix',
                    'projet_description' => 'Promotion de la paix et de la non-violence dans les milieux scolaires et périscolaires.',
                    'projet_tag'         => 'Paix',
                    'projet_image'       => array( 'url' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=600&q=80' ),
                ),
            ),
            'title_field' => '{{{ projet_titre }}}',
        ) );

        $this->end_controls_section();

        // --- Section : Mise en page ---
        $this->start_controls_section( 'section_layout', array(
            'label' => __( '🎛️ Mise en page', 'amidac-kadence-child' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'layout', array(
            'label'   => __( 'Style de grille', 'amidac-kadence-child' ),
            'type'    => Controls_Manager::SELECT,
            'options' => array(
                'masonry'  => __( 'Masonry (1 grande + petites)', 'amidac-kadence-child' ),
                'grid'     => __( 'Grille uniforme',              'amidac-kadence-child' ),
                'carousel' => __( 'Carrousel (à venir)',          'amidac-kadence-child' ),
            ),
            'default' => 'masonry',
        ) );

        $this->add_control( 'grid_cols', array(
            'label'     => __( 'Colonnes (grille uniforme)', 'amidac-kadence-child' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => array(
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ),
            'default'   => '3',
            'condition' => array( 'layout' => 'grid' ),
        ) );

        $this->add_control( 'card_min_height', array(
            'label'      => __( 'Hauteur min. petites cartes (px)', 'amidac-kadence-child' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => array( 'px' ),
            'range'      => array( 'px' => array( 'min' => 150, 'max' => 500 ) ),
            'default'    => array( 'unit' => 'px', 'size' => 280 ),
            'selectors'  => array(
                '{{WRAPPER}} .amidac-project-card' => 'min-height: {{SIZE}}{{UNIT}};',
            ),
        ) );

        $this->add_control( 'card_main_height', array(
            'label'      => __( 'Hauteur carte principale (px)', 'amidac-kadence-child' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => array( 'px' ),
            'range'      => array( 'px' => array( 'min' => 300, 'max' => 800 ) ),
            'default'    => array( 'unit' => 'px', 'size' => 580 ),
            'selectors'  => array(
                '{{WRAPPER}} .amidac-project-card--main' => 'min-height: {{SIZE}}{{UNIT}};',
            ),
            'condition'  => array( 'layout' => 'masonry' ),
        ) );

        $this->add_control( 'show_link_btn', array(
            'label'        => __( "Afficher le bouton 'Voir le projet'", 'amidac-kadence-child' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Oui', 'amidac-kadence-child' ),
            'label_off'    => __( 'Non', 'amidac-kadence-child' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ) );

        $this->add_control( 'link_text', array(
            'label'     => __( 'Texte du lien', 'amidac-kadence-child' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Voir le projet', 'amidac-kadence-child' ),
            'condition' => array( 'show_link_btn' => 'yes' ),
        ) );

        $this->add_control( 'show_all_btn', array(
            'label'        => __( "Afficher le bouton 'Voir tous les projets'", 'amidac-kadence-child' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ) );

        $this->add_control( 'all_btn_text', array(
            'label'     => __( 'Texte du bouton', 'amidac-kadence-child' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Tous nos projets', 'amidac-kadence-child' ),
            'condition' => array( 'show_all_btn' => 'yes' ),
        ) );

        $this->add_control( 'all_btn_url', array(
            'label'     => __( 'URL du bouton', 'amidac-kadence-child' ),
            'type'      => Controls_Manager::URL,
            'default'   => array( 'url' => get_post_type_archive_link( 'amidac_projet' ) ?: '/projets' ),
            'condition' => array( 'show_all_btn' => 'yes' ),
        ) );

        $this->end_controls_section();

        // ====================================================================
        // ONGLET STYLE
        // ====================================================================

        // --- Style : Couleurs ---
        $this->start_controls_section( 'section_style_colors', array(
            'label' => __( '🎨 Couleurs', 'amidac-kadence-child' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'tag_color_vert', array(
            'label'   => __( 'Couleur étiquette Vert', 'amidac-kadence-child' ),
            'type'    => Controls_Manager::COLOR,
            'default' => '#2E7D32',
            'selectors' => array(
                '{{WRAPPER}} .amidac-project-tag--vert' => 'background-color: {{VALUE}};',
            ),
        ) );

        $this->add_control( 'tag_color_bleu', array(
            'label'   => __( 'Couleur étiquette Bleu', 'amidac-kadence-child' ),
            'type'    => Controls_Manager::COLOR,
            'default' => '#1A237E',
            'selectors' => array(
                '{{WRAPPER}} .amidac-project-tag--bleu' => 'background-color: {{VALUE}};',
            ),
        ) );

        $this->add_control( 'overlay_start_color', array(
            'label'   => __( 'Couleur overlay (fond)', 'amidac-kadence-child' ),
            'type'    => Controls_Manager::COLOR,
            'default' => '#0d1554',
            'selectors' => array(
                '{{WRAPPER}} .amidac-project-overlay' => 'background: linear-gradient(to top, {{VALUE}}ee 0%, transparent 60%);',
            ),
        ) );

        $this->end_controls_section();

        // --- Style : Typographie ---
        $this->start_controls_section( 'section_style_typo', array(
            'label' => __( '🔡 Typographie', 'amidac-kadence-child' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'heading_section_tag', array(
            'label'     => __( 'En-tête de section', 'amidac-kadence-child' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_group_control( Group_Control_Typography::get_type(), array(
            'name'     => 'title_typo',
            'label'    => __( 'Titre', 'amidac-kadence-child' ),
            'selector' => '{{WRAPPER}} .amidac-section-title h2',
        ) );

        $this->add_control( 'title_color', array(
            'label'     => __( 'Couleur titre', 'amidac-kadence-child' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .amidac-section-title h2' => 'color: {{VALUE}};',
            ),
        ) );

        $this->add_control( 'heading_card', array(
            'label'     => __( 'Cartes projet', 'amidac-kadence-child' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_group_control( Group_Control_Typography::get_type(), array(
            'name'     => 'card_title_typo',
            'label'    => __( 'Titre carte', 'amidac-kadence-child' ),
            'selector' => '{{WRAPPER}} .amidac-project-overlay h3',
        ) );

        $this->add_group_control( Group_Control_Typography::get_type(), array(
            'name'     => 'card_desc_typo',
            'label'    => __( 'Description carte', 'amidac-kadence-child' ),
            'selector' => '{{WRAPPER}} .amidac-project-overlay p',
        ) );

        $this->end_controls_section();

        // --- Style : Espacement ---
        $this->start_controls_section( 'section_style_spacing', array(
            'label' => __( '📐 Espacement', 'amidac-kadence-child' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'section_padding', array(
            'label'      => __( 'Padding section', 'amidac-kadence-child' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => array( 'px', '%', 'em' ),
            'default'    => array( 'top' => 80, 'right' => 0, 'bottom' => 80, 'left' => 0, 'unit' => 'px', 'isLinked' => false ),
            'selectors'  => array(
                '{{WRAPPER}} .amidac-projets-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ),
        ) );

        $this->add_control( 'gap', array(
            'label'      => __( 'Espacement entre cartes', 'amidac-kadence-child' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => array( 'px' ),
            'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
            'default'    => array( 'unit' => 'px', 'size' => 20 ),
            'selectors'  => array(
                '{{WRAPPER}} .amidac-projects-grid' => 'gap: {{SIZE}}{{UNIT}};',
            ),
        ) );

        $this->add_control( 'border_radius', array(
            'label'      => __( 'Arrondi des cartes', 'amidac-kadence-child' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => array( 'px' ),
            'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
            'default'    => array( 'unit' => 'px', 'size' => 14 ),
            'selectors'  => array(
                '{{WRAPPER}} .amidac-project-card' => 'border-radius: {{SIZE}}{{UNIT}};',
            ),
        ) );

        $this->end_controls_section();
    }

    // ========================================================================
    // RENDER (OUTPUT HTML)
    // ========================================================================

    protected function render() {
        $settings = $this->get_settings_for_display();

        // Démarrer le wrapper
        echo '<div class="amidac-projets-widget">';

        // --- En-tête ---
        if ( 'yes' === $settings['show_header'] ) {
            $this->render_header( $settings );
        }

        // --- Grille ---
        $layout = $settings['layout'];
        if ( 'masonry' === $layout ) {
            $this->render_masonry( $settings );
        } else {
            $this->render_grid( $settings );
        }

        // --- Bouton "Tous les projets" ---
        if ( 'yes' === $settings['show_all_btn'] && ! empty( $settings['all_btn_text'] ) ) {
            $this->render_all_button( $settings );
        }

        echo '</div><!-- .amidac-projets-widget -->';
    }

    // ========================================================================
    // SOUS-MÉTHODES DE RENDU
    // ========================================================================

    /**
     * En-tête de section.
     */
    private function render_header( $settings ) {
        echo '<div class="amidac-section-title">';
        if ( ! empty( $settings['pretitle'] ) ) {
            echo '<span class="pretitle">' . esc_html( $settings['pretitle'] ) . '</span>';
        }
        if ( ! empty( $settings['title'] ) ) {
            echo '<h2>' . esc_html( $settings['title'] ) . '</h2>';
        }
        if ( ! empty( $settings['subtitle'] ) ) {
            echo '<p>' . esc_html( $settings['subtitle'] ) . '</p>';
        }
        echo '</div>';
    }

    /**
     * Grille Masonry : première carte grande, reste petites.
     */
    private function render_masonry( $settings ) {
        $projets = $this->get_projects( $settings );
        if ( empty( $projets ) ) {
            echo '<p class="amidac-no-projects">' . esc_html__( 'Aucun projet à afficher.', 'amidac-kadence-child' ) . '</p>';
            return;
        }

        echo '<div class="amidac-projects-grid amidac-projects-grid--masonry">';

        foreach ( $projets as $i => $projet ) {
            $is_main = ( 0 === $i );
            $this->render_card( $projet, $is_main, $settings );
        }

        echo '</div>';
    }

    /**
     * Grille uniforme.
     */
    private function render_grid( $settings ) {
        $projets = $this->get_projects( $settings );
        if ( empty( $projets ) ) {
            echo '<p class="amidac-no-projects">' . esc_html__( 'Aucun projet à afficher.', 'amidac-kadence-child' ) . '</p>';
            return;
        }

        $cols = intval( $settings['grid_cols'] );
        echo '<div class="amidac-projects-grid amidac-projects-grid--grid amidac-projects-grid--cols-' . $cols . '">';

        foreach ( $projets as $projet ) {
            $this->render_card( $projet, false, $settings );
        }

        echo '</div>';
    }

    /**
     * Rendu d'une carte projet.
     */
    private function render_card( $projet, $is_main, $settings ) {
        $tag_color = $projet['tag_color'] ?? 'vert';
        $link_url  = $projet['link'] ?? '#';
        $target    = $projet['link_target'] ?? '_self';

        $classes = array( 'amidac-project-card' );
        if ( $is_main ) {
            $classes[] = 'amidac-project-card--main';
        }

        $wrapper_tag   = ! empty( $link_url ) && '#' !== $link_url ? 'a' : 'div';
        $wrapper_attrs = '';
        if ( 'a' === $wrapper_tag ) {
            $wrapper_attrs = ' href="' . esc_url( $link_url ) . '" target="' . esc_attr( $target ) . '"';
        }

        echo '<' . $wrapper_tag . ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $wrapper_attrs . '>';

        // Image de fond
        if ( ! empty( $projet['image'] ) ) {
            echo '<img src="' . esc_url( $projet['image'] ) . '" alt="' . esc_attr( $projet['title'] ) . '" loading="lazy" />';
        } else {
            echo '<div class="amidac-project-placeholder"></div>';
        }

        // Overlay avec les infos
        echo '<div class="amidac-project-overlay">';

        // Tag / étiquette
        if ( ! empty( $projet['tag'] ) ) {
            echo '<span class="amidac-project-tag amidac-project-tag--' . esc_attr( $tag_color ) . '">';
            echo esc_html( $projet['tag'] );
            echo '</span>';
        }

        echo '<h3>' . esc_html( $projet['title'] ) . '</h3>';

        if ( ! empty( $projet['description'] ) ) {
            echo '<p>' . esc_html( $projet['description'] ) . '</p>';
        }

        // Bouton "Voir le projet"
        if ( 'yes' === $settings['show_link_btn'] && ! empty( $link_url ) && '#' !== $link_url ) {
            echo '<span class="amidac-project-link">'
               . esc_html( $settings['link_text'] )
               . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>'
               . '</span>';
        }

        echo '</div><!-- .amidac-project-overlay -->';
        echo '</' . $wrapper_tag . '>';
    }

    /**
     * Bouton "Tous les projets".
     */
    private function render_all_button( $settings ) {
        $url    = $settings['all_btn_url']['url'] ?? '#';
        $target = ! empty( $settings['all_btn_url']['is_external'] ) ? '_blank' : '_self';
        $rel    = ! empty( $settings['all_btn_url']['nofollow'] ) ? ' rel="nofollow"' : '';

        echo '<div class="amidac-projects-footer">';
        echo '<a href="' . esc_url( $url ) . '" class="amidac-btn amidac-btn-outline" target="' . esc_attr( $target ) . '"' . $rel . '>';
        echo esc_html( $settings['all_btn_text'] );
        echo ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';
        echo '</a>';
        echo '</div>';
    }

    // ========================================================================
    // RÉCUPÉRATION DES PROJETS
    // ========================================================================

    /**
     * Récupère les projets depuis le CPT ou depuis les données manuelles.
     *
     * @param array $settings
     * @return array Liste normalisée de projets
     */
    private function get_projects( $settings ) {
        if ( 'manual' === $settings['source'] ) {
            return $this->get_manual_projects( $settings );
        }
        return $this->get_cpt_projects( $settings );
    }

    /**
     * Projets depuis le CPT WordPress.
     */
    private function get_cpt_projects( $settings ) {
        $args = array(
            'post_type'      => 'amidac_projet',
            'posts_per_page' => intval( $settings['posts_per_page'] ),
            'post_status'    => 'publish',
            'orderby'        => sanitize_key( $settings['orderby'] ),
            'order'          => ( 'ASC' === $settings['order'] ) ? 'ASC' : 'DESC',
        );

        // Filtre taxonomie
        if ( ! empty( $settings['filter_secteur'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'secteur',
                    'field'    => 'slug',
                    'terms'    => sanitize_key( $settings['filter_secteur'] ),
                ),
            );
        }

        $query   = new WP_Query( $args );
        $projets = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();

                $post_id = get_the_ID();

                // Image : featured image ou fallback champ ACF "image_projet"
                $image = '';
                if ( has_post_thumbnail( $post_id ) ) {
                    $image = get_the_post_thumbnail_url( $post_id, 'amidac-card' );
                } elseif ( function_exists( 'get_field' ) ) {
                    $acf_img = get_field( 'image_projet', $post_id );
                    $image   = is_array( $acf_img ) ? ( $acf_img['url'] ?? '' ) : $acf_img;
                }

                // Tag : premier terme du secteur ou champ ACF
                $tag = '';
                $terms = get_the_terms( $post_id, 'secteur' );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    $tag = $terms[0]->name;
                } elseif ( function_exists( 'get_field' ) ) {
                    $tag = get_field( 'secteur_label', $post_id ) ?: '';
                }

                $projets[] = array(
                    'title'       => get_the_title(),
                    'description' => get_the_excerpt() ?: wp_trim_words( get_the_content(), 20 ),
                    'image'       => $image,
                    'tag'         => $tag,
                    'tag_color'   => 'vert',
                    'link'        => get_permalink(),
                    'link_target' => '_self',
                );
            }
            wp_reset_postdata();
        }

        return $projets;
    }

    /**
     * Projets saisis manuellement dans le repeater Elementor.
     */
    private function get_manual_projects( $settings ) {
        $projets = array();

        foreach ( (array) $settings['projets_list'] as $item ) {
            $projets[] = array(
                'title'       => $item['projet_titre']       ?? '',
                'description' => $item['projet_description'] ?? '',
                'image'       => $item['projet_image']['url'] ?? '',
                'tag'         => $item['projet_tag']          ?? '',
                'tag_color'   => $item['projet_tag_couleur']  ?? 'vert',
                'link'        => $item['projet_lien']['url']  ?? '#',
                'link_target' => ! empty( $item['projet_lien']['is_external'] ) ? '_blank' : '_self',
            );
        }

        return $projets;
    }

    // ========================================================================
    // CONTENU ELEMENTOR (Aperçu live dans l'éditeur)
    // ========================================================================

    protected function content_template() {
        ?>
        <# var settings = settings; #>
        <div class="amidac-projets-widget">
            <# if ( settings.show_header === 'yes' ) { #>
            <div class="amidac-section-title">
                <# if ( settings.pretitle ) { #>
                <span class="pretitle">{{{ settings.pretitle }}}</span>
                <# } #>
                <# if ( settings.title ) { #>
                <h2>{{{ settings.title }}}</h2>
                <# } #>
                <# if ( settings.subtitle ) { #>
                <p>{{{ settings.subtitle }}}</p>
                <# } #>
            </div>
            <# } #>
            <div class="amidac-projects-grid amidac-projects-grid--masonry">
                <# _.each( settings.projets_list, function( item, i ) { #>
                <div class="amidac-project-card <# if (i===0) { #>amidac-project-card--main<# } #>">
                    <img src="{{{ item.projet_image.url }}}" alt="{{{ item.projet_titre }}}" />
                    <div class="amidac-project-overlay">
                        <span class="amidac-project-tag amidac-project-tag--{{{ item.projet_tag_couleur || 'vert' }}}">{{{ item.projet_tag }}}</span>
                        <h3>{{{ item.projet_titre }}}</h3>
                        <p>{{{ item.projet_description }}}</p>
                    </div>
                </div>
                <# }); #>
            </div>
        </div>
        <?php
    }
}
