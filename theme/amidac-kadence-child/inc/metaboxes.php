<?php
/**
 * AMIDAC — Metaboxes / Champs personnalisés
 * Pour le CPT amidac_projet (fonctionne SANS ACF)
 *
 * @package AMIDAC_Kadence_Child
 */

defined( 'ABSPATH' ) || exit;

// ============================================================================
// ENREGISTREMENT DES METABOXES
// ============================================================================

function amidac_register_metaboxes() {
    add_meta_box(
        'amidac_projet_details',
        __( '📋 Détails du Projet', 'amidac-kadence-child' ),
        'amidac_projet_details_callback',
        'amidac_projet',
        'normal',
        'high'
    );

    add_meta_box(
        'amidac_projet_stats',
        __( '📊 Statistiques & Impact', 'amidac-kadence-child' ),
        'amidac_projet_stats_callback',
        'amidac_projet',
        'normal',
        'default'
    );

    add_meta_box(
        'amidac_projet_media',
        __( '🖼️ Médias supplémentaires', 'amidac-kadence-child' ),
        'amidac_projet_media_callback',
        'amidac_projet',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'amidac_register_metaboxes' );

// ============================================================================
// CALLBACKS — RENDU DES METABOXES
// ============================================================================

/**
 * Metabox : Détails du projet
 */
function amidac_projet_details_callback( $post ) {
    wp_nonce_field( 'amidac_projet_details_nonce', 'amidac_projet_details_nonce_field' );

    $secteur_label = get_post_meta( $post->ID, '_amidac_secteur_label', true );
    $statut        = get_post_meta( $post->ID, '_amidac_statut',        true );
    $date_debut    = get_post_meta( $post->ID, '_amidac_date_debut',    true );
    $date_fin      = get_post_meta( $post->ID, '_amidac_date_fin',      true );
    $zone          = get_post_meta( $post->ID, '_amidac_zone',          true );
    $partenaires   = get_post_meta( $post->ID, '_amidac_partenaires',   true );
    $financement   = get_post_meta( $post->ID, '_amidac_financement',   true );
    $tag_couleur   = get_post_meta( $post->ID, '_amidac_tag_couleur',   true ) ?: 'vert';

    ?>
    <style>
        .amidac-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .amidac-meta-field { display: flex; flex-direction: column; gap: 5px; }
        .amidac-meta-field label { font-weight: 600; font-size: 12px; text-transform: uppercase; color: #555; }
        .amidac-meta-field input,
        .amidac-meta-field select,
        .amidac-meta-field textarea { width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; }
        .amidac-meta-field input:focus,
        .amidac-meta-field select:focus { border-color: #1A237E; outline: none; box-shadow: 0 0 0 2px rgba(26,35,126,0.15); }
        .amidac-meta-separator { grid-column: 1 / -1; border: none; border-top: 1px solid #eee; margin: 8px 0; }
        .amidac-meta-full { grid-column: 1 / -1; }
        .amidac-color-swatches { display: flex; gap: 8px; flex-wrap: wrap; }
        .amidac-color-swatch { display: flex; align-items: center; gap: 6px; cursor: pointer; }
        .amidac-color-swatch input { width: auto; }
        .amidac-color-dot { width: 16px; height: 16px; border-radius: 50%; display: inline-block; }
    </style>
    <div class="amidac-meta-grid">

        <!-- Étiquette secteur (texte libre) -->
        <div class="amidac-meta-field">
            <label for="amidac_secteur_label">🏷️ Étiquette du secteur</label>
            <input type="text" id="amidac_secteur_label" name="amidac_secteur_label"
                   value="<?php echo esc_attr( $secteur_label ); ?>"
                   placeholder="Ex: Autonomisation, Éducation…" />
        </div>

        <!-- Couleur de l'étiquette -->
        <div class="amidac-meta-field">
            <label>🎨 Couleur de l'étiquette</label>
            <div class="amidac-color-swatches">
                <?php
                $colors = array(
                    'vert'   => array( 'label' => 'Vert',   'hex' => '#2E7D32' ),
                    'bleu'   => array( 'label' => 'Bleu',   'hex' => '#1A237E' ),
                    'or'     => array( 'label' => 'Or',     'hex' => '#f9a825' ),
                    'rouge'  => array( 'label' => 'Rouge',  'hex' => '#c62828' ),
                    'violet' => array( 'label' => 'Violet', 'hex' => '#6a1b9a' ),
                );
                foreach ( $colors as $slug => $c ) :
                    $checked = checked( $tag_couleur, $slug, false );
                ?>
                <label class="amidac-color-swatch">
                    <input type="radio" name="amidac_tag_couleur" value="<?php echo esc_attr( $slug ); ?>" <?php echo $checked; ?> />
                    <span class="amidac-color-dot" style="background:<?php echo esc_attr( $c['hex'] ); ?>;border:2px solid <?php echo esc_attr( $tag_couleur === $slug ? '#333' : 'transparent' ); ?>"></span>
                    <span style="font-size:12px;"><?php echo esc_html( $c['label'] ); ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <hr class="amidac-meta-separator" />

        <!-- Statut -->
        <div class="amidac-meta-field">
            <label for="amidac_statut">📌 Statut du projet</label>
            <select id="amidac_statut" name="amidac_statut">
                <option value=""                 <?php selected( $statut, '' ); ?>><?php _e( '— Sélectionner —', 'amidac-kadence-child' ); ?></option>
                <option value="en_cours"         <?php selected( $statut, 'en_cours' ); ?>><?php _e( '🟢 En cours', 'amidac-kadence-child' ); ?></option>
                <option value="termine"          <?php selected( $statut, 'termine' ); ?>><?php _e( '✅ Terminé', 'amidac-kadence-child' ); ?></option>
                <option value="en_preparation"   <?php selected( $statut, 'en_preparation' ); ?>><?php _e( '🔵 En préparation', 'amidac-kadence-child' ); ?></option>
                <option value="suspendu"         <?php selected( $statut, 'suspendu' ); ?>><?php _e( '🔴 Suspendu', 'amidac-kadence-child' ); ?></option>
            </select>
        </div>

        <!-- Zone géographique -->
        <div class="amidac-meta-field">
            <label for="amidac_zone">📍 Zone / Localisation</label>
            <input type="text" id="amidac_zone" name="amidac_zone"
                   value="<?php echo esc_attr( $zone ); ?>"
                   placeholder="Ex: Zone de santé de Nundu" />
        </div>

        <!-- Dates -->
        <div class="amidac-meta-field">
            <label for="amidac_date_debut">📅 Date de début</label>
            <input type="date" id="amidac_date_debut" name="amidac_date_debut"
                   value="<?php echo esc_attr( $date_debut ); ?>" />
        </div>

        <div class="amidac-meta-field">
            <label for="amidac_date_fin">📅 Date de fin</label>
            <input type="date" id="amidac_date_fin" name="amidac_date_fin"
                   value="<?php echo esc_attr( $date_fin ); ?>" />
        </div>

        <!-- Partenaires -->
        <div class="amidac-meta-field amidac-meta-full">
            <label for="amidac_partenaires">🤝 Partenaires / Bailleurs</label>
            <input type="text" id="amidac_partenaires" name="amidac_partenaires"
                   value="<?php echo esc_attr( $partenaires ); ?>"
                   placeholder="Ex: UNICEF, ONU Femmes, USAID…" />
        </div>

        <!-- Financement -->
        <div class="amidac-meta-field amidac-meta-full">
            <label for="amidac_financement">💰 Source de financement</label>
            <input type="text" id="amidac_financement" name="amidac_financement"
                   value="<?php echo esc_attr( $financement ); ?>"
                   placeholder="Ex: Fonds propres, Subvention ECHO…" />
        </div>

    </div>
    <?php
}

/**
 * Metabox : Statistiques & Impact
 */
function amidac_projet_stats_callback( $post ) {
    wp_nonce_field( 'amidac_projet_stats_nonce', 'amidac_projet_stats_nonce_field' );

    $beneficiaires  = get_post_meta( $post->ID, '_amidac_beneficiaires',  true );
    $menages        = get_post_meta( $post->ID, '_amidac_menages',        true );
    $femmes_pct     = get_post_meta( $post->ID, '_amidac_femmes_pct',     true );
    $aires_sante    = get_post_meta( $post->ID, '_amidac_aires_sante',    true );
    $villages       = get_post_meta( $post->ID, '_amidac_villages',       true );
    $budget_usd     = get_post_meta( $post->ID, '_amidac_budget_usd',     true );
    ?>
    <div class="amidac-meta-grid">
        <div class="amidac-meta-field">
            <label for="amidac_beneficiaires">👥 Bénéficiaires directs</label>
            <input type="number" id="amidac_beneficiaires" name="amidac_beneficiaires"
                   value="<?php echo esc_attr( $beneficiaires ); ?>" min="0" placeholder="Ex: 1200" />
        </div>
        <div class="amidac-meta-field">
            <label for="amidac_menages">🏠 Ménages touchés</label>
            <input type="number" id="amidac_menages" name="amidac_menages"
                   value="<?php echo esc_attr( $menages ); ?>" min="0" placeholder="Ex: 240" />
        </div>
        <div class="amidac-meta-field">
            <label for="amidac_femmes_pct">♀️ % Femmes / Filles</label>
            <input type="number" id="amidac_femmes_pct" name="amidac_femmes_pct"
                   value="<?php echo esc_attr( $femmes_pct ); ?>" min="0" max="100" placeholder="Ex: 65" />
        </div>
        <div class="amidac-meta-field">
            <label for="amidac_aires_sante">🏥 Aires de santé couvertes</label>
            <input type="number" id="amidac_aires_sante" name="amidac_aires_sante"
                   value="<?php echo esc_attr( $aires_sante ); ?>" min="0" placeholder="Ex: 5" />
        </div>
        <div class="amidac-meta-field">
            <label for="amidac_villages">🏘️ Villages / Localités</label>
            <input type="number" id="amidac_villages" name="amidac_villages"
                   value="<?php echo esc_attr( $villages ); ?>" min="0" placeholder="Ex: 12" />
        </div>
        <div class="amidac-meta-field">
            <label for="amidac_budget_usd">💵 Budget (USD)</label>
            <input type="number" id="amidac_budget_usd" name="amidac_budget_usd"
                   value="<?php echo esc_attr( $budget_usd ); ?>" min="0" placeholder="Ex: 50000" />
        </div>
    </div>
    <?php
}

/**
 * Metabox : Médias (sidebar)
 */
function amidac_projet_media_callback( $post ) {
    wp_nonce_field( 'amidac_projet_media_nonce', 'amidac_projet_media_nonce_field' );

    $video_url    = get_post_meta( $post->ID, '_amidac_video_url',    true );
    $rapport_url  = get_post_meta( $post->ID, '_amidac_rapport_url',  true );
    ?>
    <div style="display:flex;flex-direction:column;gap:12px;">
        <div class="amidac-meta-field">
            <label for="amidac_video_url" style="font-weight:600;font-size:12px;text-transform:uppercase;color:#555;">🎬 URL Vidéo (YouTube/Vimeo)</label>
            <input type="url" id="amidac_video_url" name="amidac_video_url"
                   value="<?php echo esc_attr( $video_url ); ?>"
                   placeholder="https://youtube.com/watch?v=…"
                   style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;" />
        </div>
        <div class="amidac-meta-field">
            <label for="amidac_rapport_url" style="font-weight:600;font-size:12px;text-transform:uppercase;color:#555;">📄 URL Rapport PDF</label>
            <input type="url" id="amidac_rapport_url" name="amidac_rapport_url"
                   value="<?php echo esc_attr( $rapport_url ); ?>"
                   placeholder="https://…/rapport.pdf"
                   style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;" />
        </div>
    </div>
    <?php
}

// ============================================================================
// SAUVEGARDE DES METABOXES
// ============================================================================

function amidac_save_projet_meta( $post_id ) {
    // Sécurité : vérification du nonce
    $nonces = array(
        'amidac_projet_details_nonce_field' => 'amidac_projet_details_nonce',
        'amidac_projet_stats_nonce_field'   => 'amidac_projet_stats_nonce',
        'amidac_projet_media_nonce_field'   => 'amidac_projet_media_nonce',
    );

    $valid = false;
    foreach ( $nonces as $field => $action ) {
        if ( isset( $_POST[ $field ] ) && wp_verify_nonce( $_POST[ $field ], $action ) ) {
            $valid = true;
        }
    }
    if ( ! $valid ) return;

    // Pas d'auto-save
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // Permission
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Champs texte
    $text_fields = array(
        'amidac_secteur_label' => '_amidac_secteur_label',
        'amidac_statut'        => '_amidac_statut',
        'amidac_zone'          => '_amidac_zone',
        'amidac_partenaires'   => '_amidac_partenaires',
        'amidac_financement'   => '_amidac_financement',
        'amidac_tag_couleur'   => '_amidac_tag_couleur',
    );

    foreach ( $text_fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
        }
    }

    // Champs date
    $date_fields = array(
        'amidac_date_debut' => '_amidac_date_debut',
        'amidac_date_fin'   => '_amidac_date_fin',
    );

    foreach ( $date_fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            $date = sanitize_text_field( $_POST[ $post_key ] );
            // Valider format de date YYYY-MM-DD
            if ( empty( $date ) || preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
                update_post_meta( $post_id, $meta_key, $date );
            }
        }
    }

    // Champs numériques
    $number_fields = array(
        'amidac_beneficiaires' => '_amidac_beneficiaires',
        'amidac_menages'       => '_amidac_menages',
        'amidac_femmes_pct'    => '_amidac_femmes_pct',
        'amidac_aires_sante'   => '_amidac_aires_sante',
        'amidac_villages'      => '_amidac_villages',
        'amidac_budget_usd'    => '_amidac_budget_usd',
    );

    foreach ( $number_fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, absint( $_POST[ $post_key ] ) );
        }
    }

    // Champs URL
    $url_fields = array(
        'amidac_video_url'   => '_amidac_video_url',
        'amidac_rapport_url' => '_amidac_rapport_url',
    );

    foreach ( $url_fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, esc_url_raw( $_POST[ $post_key ] ) );
        }
    }
}
add_action( 'save_post_amidac_projet', 'amidac_save_projet_meta' );