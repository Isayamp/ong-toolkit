<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

class AMIDAC_Widget_Projets_Grid extends Widget_Base {

    public function get_name()        { return 'amidac-projets-grid'; }
    public function get_title()       { return 'AMIDAC — Grille Projets'; }
    public function get_icon()        { return 'eicon-gallery-grid'; }
    public function get_categories()  { return array( 'amidac-widgets' ); }
    public function get_keywords()    { return array( 'projets', 'amidac', 'grille', 'grid' ); }

    // =========================================================================
    // CONTROLS
    // =========================================================================

    protected function register_controls() {

        // ----- Section : En-tête -----
        $this->start_controls_section( 'sec_header', array(
            'label' => '📝 En-tête',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ));

        $this->add_control( 'show_header', array(
            'label'        => 'Afficher l\'en-tête',
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
        ));

        $this->add_control( 'pretitle', array(
            'label'     => 'Étiquette',
            'type'      => Controls_Manager::TEXT,
            'default'   => '📁 Nos Projets',
            'condition' => array( 'show_header' => 'yes' ),
        ));

        $this->add_control( 'title', array(
            'label'       => 'Titre',
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Des Actions Concrètes sur le Terrain',
            'label_block' => true,
            'condition'   => array( 'show_header' => 'yes' ),
        ));

        $this->add_control( 'subtitle', array(
            'label'     => 'Sous-titre',
            'type'      => Controls_Manager::TEXTAREA,
            'default'   => 'Depuis 2015, AMIDAC a mené plus de 10 projets à fort impact dans la zone de santé de Nundu.',
            'rows'      => 2,
            'condition' => array( 'show_header' => 'yes' ),
        ));

        $this->end_controls_section();

        // ----- Section : Source -----
        $this->start_controls_section( 'sec_source', array(
            'label' => '⚙️ Source des projets',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ));

        $this->add_control( 'source', array(
            'label'   => 'Source',
            'type'    => Controls_Manager::SELECT,
            'options' => array(
                'cpt'    => 'CPT WordPress (amidac_projet)',
                'manual' => 'Saisie manuelle',
            ),
            'default' => 'cpt',
        ));

        $this->add_control( 'posts_per_page', array(
            'label'     => 'Nombre de projets',
            'type'      => Controls_Manager::NUMBER,
            'default'   => 5,
            'min'       => 1,
            'max'       => 12,
            'condition' => array( 'source' => 'cpt' ),
        ));

        $this->add_control( 'orderby', array(
            'label'     => 'Trier par',
            'type'      => Controls_Manager::SELECT,
            'options'   => array(
                'date'       => 'Date',
                'title'      => 'Titre',
                'menu_order' => 'Ordre menu',
                'rand'       => 'Aléatoire',
            ),
            'default'   => 'date',
            'condition' => array( 'source' => 'cpt' ),
        ));

        $this->add_control( 'order', array(
            'label'     => 'Ordre',
            'type'      => Controls_Manager::SELECT,
            'options'   => array(
                'DESC' => 'Récent en premier',
                'ASC'  => 'Ancien en premier',
            ),
            'default'   => 'DESC',
            'condition' => array( 'source' => 'cpt' ),
        ));

        $this->end_controls_section();

        // ----- Section : Projets manuels -----
        $this->start_controls_section( 'sec_manual', array(
            'label'     => '✏️ Projets manuels',
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => array( 'source' => 'manual' ),
        ));

        $repeater = new Repeater();

        $repeater->add_control( 'titre', array(
            'label'       => 'Titre',
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Nom du projet',
            'label_block' => true,
        ));

        $repeater->add_control( 'description', array(
            'label'   => 'Description',
            'type'    => Controls_Manager::TEXTAREA,
            'default' => 'Description courte du projet et de son impact.',
            'rows'    => 2,
        ));

        $repeater->add_control( 'tag', array(
            'label'   => 'Étiquette',
            'type'    => Controls_Manager::TEXT,
            'default' => 'Éducation',
        ));

        $repeater->add_control( 'tag_couleur', array(
            'label'   => 'Couleur étiquette',
            'type'    => Controls_Manager::SELECT,
            'options' => array(
                'vert'   => 'Vert',
                'bleu'   => 'Bleu',
                'or'     => 'Or',
                'rouge'  => 'Rouge',
                'violet' => 'Violet',
            ),
            'default' => 'vert',
        ));

        $repeater->add_control( 'image', array(
            'label' => 'Image',
            'type'  => Controls_Manager::MEDIA,
            'default' => array( 'url' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&q=80' ),
        ));

        $repeater->add_control( 'lien', array(
            'label' => 'Lien',
            'type'  => Controls_Manager::URL,
        ));

        $this->add_control( 'projets_list', array(
            'label'       => 'Projets',
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => array(
                array( 'titre' => 'Kikapo Changu ni Maisha Yangu',  'tag' => 'Autonomisation', 'tag_couleur' => 'vert',   'description' => 'Renforcement de l\'autonomisation économique des femmes déplacées dans 5 aires de santé.', 'image' => array( 'url' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&q=80' ) ),
                array( 'titre' => 'Twajihudumiya Wenyewe',           'tag' => 'Infrastructure',  'tag_couleur' => 'bleu',  'description' => 'Construction de passerelles et latrines pour la sécurité communautaire.',                'image' => array( 'url' => 'https://images.unsplash.com/photo-1542810634-71277d95dcbb?w=600&q=80' ) ),
                array( 'titre' => 'Projet SIMAMA',                   'tag' => 'Éducation',       'tag_couleur' => 'or',    'description' => 'Accompagnement scolaire des enfants déplacés, orphelins et enfants de la rue.',        'image' => array( 'url' => 'https://images.unsplash.com/photo-1509099836639-18ba1795216d?w=600&q=80' ) ),
                array( 'titre' => 'Jeunes Arbres à Palabre',         'tag' => 'Environnement',   'tag_couleur' => 'vert',  'description' => 'Reboisement et lutte contre le réchauffement climatique dans 5 aires de santé.',       'image' => array( 'url' => 'https://images.unsplash.com/photo-1497486751825-1233686d5d80?w=600&q=80' ) ),
                array( 'titre' => 'Bâtissons la Paix',               'tag' => 'Paix',            'tag_couleur' => 'violet','description' => 'Promotion de la paix et de la non-violence dans les milieux scolaires.',               'image' => array( 'url' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=600&q=80' ) ),
            ),
            'title_field' => '{{{ titre }}}',
        ));

        $this->end_controls_section();

        // ----- Section : Mise en page -----
        $this->start_controls_section( 'sec_layout', array(
            'label' => '🎛️ Mise en page',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ));

        $this->add_control( 'layout', array(
            'label'   => 'Style de grille',
            'type'    => Controls_Manager::SELECT,
            'options' => array(
                'masonry' => 'Masonry (1 grande + petites)',
                'grid'    => 'Grille uniforme',
            ),
            'default' => 'masonry',
        ));

        $this->add_control( 'grid_cols', array(
            'label'     => 'Colonnes',
            'type'      => Controls_Manager::SELECT,
            'options'   => array( '2' => '2', '3' => '3', '4' => '4' ),
            'default'   => '3',
            'condition' => array( 'layout' => 'grid' ),
        ));

        $this->add_control( 'show_all_btn', array(
            'label'        => 'Bouton "Tous les projets"',
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
        ));

        $this->add_control( 'all_btn_text', array(
            'label'     => 'Texte du bouton',
            'type'      => Controls_Manager::TEXT,
            'default'   => 'Tous nos projets',
            'condition' => array( 'show_all_btn' => 'yes' ),
        ));

        $this->add_control( 'all_btn_url', array(
            'label'     => 'URL du bouton',
            'type'      => Controls_Manager::URL,
            'default'   => array( 'url' => '/projets' ),
            'condition' => array( 'show_all_btn' => 'yes' ),
        ));

        $this->end_controls_section();

        // ----- Style : Couleurs -----
        $this->start_controls_section( 'sec_style', array(
            'label' => '🎨 Couleurs',
            'tab'   => Controls_Manager::TAB_STYLE,
        ));

        $this->add_control( 'overlay_color', array(
            'label'     => 'Couleur overlay',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#0d1554',
            'selectors' => array(
                '{{WRAPPER}} .amidac-project-overlay' => 'background: linear-gradient(to top, {{VALUE}}ee 0%, transparent 65%);',
            ),
        ));

        $this->add_control( 'title_color', array(
            'label'     => 'Couleur titre section',
            'type'      => Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .amidac-sec-title h2' => 'color: {{VALUE}};',
            ),
        ));

        $this->end_controls_section();

        // ----- Style : Espacement -----
        $this->start_controls_section( 'sec_spacing', array(
            'label' => '📐 Espacement',
            'tab'   => Controls_Manager::TAB_STYLE,
        ));

        $this->add_control( 'gap', array(
            'label'      => 'Espacement entre cartes',
            'type'       => Controls_Manager::SLIDER,
            'size_units' => array( 'px' ),
            'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
            'default'    => array( 'unit' => 'px', 'size' => 16 ),
            'selectors'  => array(
                '{{WRAPPER}} .amidac-projects-grid' => 'gap: {{SIZE}}{{UNIT}};',
            ),
        ));

        $this->add_control( 'border_radius', array(
            'label'      => 'Arrondi des cartes',
            'type'       => Controls_Manager::SLIDER,
            'size_units' => array( 'px' ),
            'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
            'default'    => array( 'unit' => 'px', 'size' => 14 ),
            'selectors'  => array(
                '{{WRAPPER}} .amidac-project-card' => 'border-radius: {{SIZE}}{{UNIT}};',
            ),
        ));

        $this->end_controls_section();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    protected function render() {
        $s = $this->get_settings_for_display();
        echo '<div class="amidac-projets-wrap">';

        // En-tête
        if ( 'yes' === $s['show_header'] ) {
            echo '<div class="amidac-sec-title">';
            if ( $s['pretitle'] ) echo '<span class="amidac-pretag">' . esc_html( $s['pretitle'] ) . '</span>';
            if ( $s['title'] )    echo '<h2>' . esc_html( $s['title'] ) . '</h2>';
            if ( $s['subtitle'] ) echo '<p>'  . esc_html( $s['subtitle'] ) . '</p>';
            echo '</div>';
        }

        // Grille
        $projets = ( 'manual' === $s['source'] )
            ? $this->get_manual_projects( $s )
            : $this->get_cpt_projects( $s );

        if ( empty( $projets ) ) {
            echo '<p class="amidac-empty">Aucun projet à afficher. Créez des projets dans <strong>Projets</strong> dans l\'admin, ou passez en mode "Saisie manuelle".</p>';
        } else {
            $layout = $s['layout'];
            $cols   = intval( $s['grid_cols'] ?? 3 );
            $class  = 'masonry' === $layout
                ? 'amidac-projects-grid amidac-grid-masonry'
                : 'amidac-projects-grid amidac-grid-uniform amidac-grid-cols-' . $cols;

            echo '<div class="' . esc_attr( $class ) . '">';
            foreach ( $projets as $i => $p ) {
                $this->render_card( $p, ( 'masonry' === $layout && 0 === $i ) );
            }
            echo '</div>';
        }

        // Bouton
        if ( 'yes' === $s['show_all_btn'] && ! empty( $s['all_btn_text'] ) ) {
            $url    = $s['all_btn_url']['url'] ?? '#';
            $target = ! empty( $s['all_btn_url']['is_external'] ) ? '_blank' : '_self';
            echo '<div class="amidac-grid-footer">';
            echo '<a href="' . esc_url( $url ) . '" class="amidac-btn-voir" target="' . esc_attr( $target ) . '">';
            echo esc_html( $s['all_btn_text'] ) . ' →';
            echo '</a>';
            echo '</div>';
        }

        echo '</div>';
    }

    private function render_card( $p, $is_main ) {
        $tag_color = $p['tag_color'] ?? 'vert';
        $url       = $p['link'] ?? '';
        $tag       = $wrapper_open = $wrapper_close = '';

        $classes = 'amidac-project-card' . ( $is_main ? ' amidac-card-main' : '' );

        if ( $url && '#' !== $url ) {
            $target        = $p['link_target'] ?? '_self';
            $wrapper_open  = '<a href="' . esc_url( $url ) . '" class="' . $classes . '" target="' . esc_attr( $target ) . '">';
            $wrapper_close = '</a>';
        } else {
            $wrapper_open  = '<div class="' . $classes . '">';
            $wrapper_close = '</div>';
        }

        echo $wrapper_open;

        if ( ! empty( $p['image'] ) ) {
            echo '<img src="' . esc_url( $p['image'] ) . '" alt="' . esc_attr( $p['title'] ) . '" loading="lazy" />';
        } else {
            echo '<div class="amidac-card-placeholder"></div>';
        }

        echo '<div class="amidac-project-overlay">';

        if ( ! empty( $p['tag'] ) ) {
            echo '<span class="amidac-tag amidac-tag-' . esc_attr( $tag_color ) . '">' . esc_html( $p['tag'] ) . '</span>';
        }

        echo '<h3>' . esc_html( $p['title'] ) . '</h3>';

        if ( ! empty( $p['description'] ) ) {
            echo '<p>' . esc_html( $p['description'] ) . '</p>';
        }

        echo '</div>';
        echo $wrapper_close;
    }

    // =========================================================================
    // DONNÉES
    // =========================================================================

    private function get_cpt_projects( $s ) {
        $args = array(
            'post_type'      => 'amidac_projet',
            'posts_per_page' => intval( $s['posts_per_page'] ?? 5 ),
            'post_status'    => 'publish',
            'orderby'        => sanitize_key( $s['orderby'] ?? 'date' ),
            'order'          => ( 'ASC' === ( $s['order'] ?? 'DESC' ) ) ? 'ASC' : 'DESC',
        );

        $query   = new WP_Query( $args );
        $projets = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $id = get_the_ID();

                $image = has_post_thumbnail( $id )
                    ? get_the_post_thumbnail_url( $id, 'large' )
                    : '';

                $tag   = '';
                $terms = get_the_terms( $id, 'secteur' );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    $tag = $terms[0]->name;
                }
                if ( ! $tag ) {
                    $tag = get_post_meta( $id, '_amidac_secteur_label', true ) ?: '';
                }

                $projets[] = array(
                    'title'       => get_the_title(),
                    'description' => get_the_excerpt() ?: wp_trim_words( get_the_content(), 18 ),
                    'image'       => $image,
                    'tag'         => $tag,
                    'tag_color'   => get_post_meta( $id, '_amidac_tag_couleur', true ) ?: 'vert',
                    'link'        => get_permalink(),
                    'link_target' => '_self',
                );
            }
            wp_reset_postdata();
        }

        return $projets;
    }

    private function get_manual_projects( $s ) {
        $projets = array();
        foreach ( (array) ( $s['projets_list'] ?? array() ) as $item ) {
            $projets[] = array(
                'title'       => $item['titre']       ?? '',
                'description' => $item['description'] ?? '',
                'image'       => $item['image']['url'] ?? '',
                'tag'         => $item['tag']          ?? '',
                'tag_color'   => $item['tag_couleur']  ?? 'vert',
                'link'        => $item['lien']['url']  ?? '#',
                'link_target' => ! empty( $item['lien']['is_external'] ) ? '_blank' : '_self',
            );
        }
        return $projets;
    }

    // Aperçu dans l'éditeur
    protected function content_template() { ?>
        <# var s = settings; #>
        <div class="amidac-projets-wrap">
            <# if ( s.show_header === 'yes' ) { #>
            <div class="amidac-sec-title">
                <# if ( s.pretitle ) { #><span class="amidac-pretag">{{{ s.pretitle }}}</span><# } #>
                <# if ( s.title )    { #><h2>{{{ s.title }}}</h2><# } #>
                <# if ( s.subtitle ) { #><p>{{{ s.subtitle }}}</p><# } #>
            </div>
            <# } #>
            <div class="amidac-projects-grid amidac-grid-masonry">
                <# _.each( s.projets_list, function( p, i ) { #>
                <div class="amidac-project-card <# if(i===0){#>amidac-card-main<#}#>">
                    <img src="{{{ p.image.url }}}" alt="{{{ p.titre }}}" />
                    <div class="amidac-project-overlay">
                        <span class="amidac-tag amidac-tag-{{{ p.tag_couleur || 'vert' }}}">{{{ p.tag }}}</span>
                        <h3>{{{ p.titre }}}</h3>
                        <p>{{{ p.description }}}</p>
                    </div>
                </div>
                <# }); #>
            </div>
        </div>
    <?php }
}
