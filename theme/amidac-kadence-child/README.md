# AMIDAC — Kadence Child Theme

> Child theme officiel du site **AMIDAC asbl**  
> Développé par **Msc. Isaya M'lela Pombo** — [Emo Touch Group](https://emotouchgroup.com)

---

## 📋 Informations

| Champ             | Valeur                          |
|-------------------|---------------------------------|
| **Version**       | 1.0.0                           |
| **Thème parent**  | Kadence (kadence)               |
| **Auteur**        | Isaya M'lela Pombo              |
| **Agence**        | Emo Touch Group                 |
| **Organisation**  | AMIDAC asbl                     |
| **Licence**       | GPL v2+                         |
| **Text Domain**   | amidac-kadence-child            |

---

## 📂 Structure du projet

```
amidac-kadence-child/
├── style.css               ← Identification du child theme + variables CSS
├── functions.php           ← Fonctions principales, CPT, hooks Kadence
├── inc/
│   ├── customizer.php      ← Panneaux du personnaliseur WP
│   ├── helpers.php         ← Fonctions utilitaires réutilisables
│   ├── shortcodes.php      ← Shortcodes personnalisés
│   ├── metaboxes.php       ← Champs personnalisés (à créer)
│   └── admin.php           ← Personnalisations admin (à créer)
├── assets/
│   ├── css/
│   │   ├── custom.css      ← Composants additionnels
│   │   ├── admin.css       ← Styles pour l'admin WP (à créer)
│   │   └── editor-style.css← Styles pour l'éditeur Gutenberg (à créer)
│   ├── js/
│   │   └── main.js         ← JavaScript principal
│   └── images/             ← Images du thème
├── template-parts/         ← Partials de templates (à créer)
└── README.md               ← Ce fichier
```

---

## 🚀 Installation

### 1. Prérequis
- WordPress 6.0+
- Thème **Kadence** installé et activé (version 3.x recommandée)
- PHP 7.4+ (8.0+ recommandé)

### 2. Installation du child theme

**Via FTP / cPanel :**
1. Compresser le dossier `amidac-kadence-child/` en `.zip`
2. Tableau de bord WP → **Apparence → Thèmes → Ajouter**
3. **Téléverser un thème** → choisir le fichier `.zip`
4. **Activer** le thème

**Via FTP direct :**
1. Uploader le dossier `amidac-kadence-child/` dans `/wp-content/themes/`
2. Tableau de bord WP → **Apparence → Thèmes**
3. Activer **AMIDAC - Kadence Child Theme**

---

## ⚙️ Configuration initiale

Après activation, aller dans **Apparence → Personnaliser → 🏢 AMIDAC asbl** pour configurer :

- **Informations Organisation** : nom, mission, numéro d'enregistrement
- **Coordonnées** : téléphone, email, adresse, Google Maps
- **Réseaux Sociaux** : Facebook, Twitter, Instagram, LinkedIn, YouTube, TikTok
- **Barre d'annonce** : message en haut du site
- **CTA** : texte et URL du bouton d'appel à l'action
- **Couleurs** : personnalisation des couleurs principales

---

## 🎨 Types de posts personnalisés (CPT)

| Slug              | Nom         | Archive |
|-------------------|-------------|---------|
| `amidac_projet`   | Projets     | `/projets/` |
| `amidac_membre`   | Équipe      | Non     |
| `amidac_partenaire`| Partenaires| Non     |
| `amidac_rapport`  | Rapports    | `/rapports/` |

---

## 🔧 Shortcodes disponibles

| Shortcode                    | Description                        |
|------------------------------|------------------------------------|
| `[amidac_phone]`             | Numéro de téléphone (lien tel:)    |
| `[amidac_email]`             | Email de contact (lien mailto:)    |
| `[amidac_address]`           | Adresse complète                   |
| `[amidac_cta]`               | Bouton call-to-action              |
| `[amidac_social_icons]`      | Icônes des réseaux sociaux         |
| `[amidac_year]`              | Année courante                     |
| `[amidac_org_name]`          | Nom de l'organisation              |
| `[amidac_stat number="500" label="Bénéficiaires"]` | Statistique |
| `[amidac_alert type="info"]Texte[/amidac_alert]` | Boîte alerte |
| `[amidac_breadcrumbs]`       | Fil d'ariane                       |

---

## 🎯 Classes CSS utilitaires

### Couleurs
```css
.amidac-text-primary    /* Texte bleu */
.amidac-text-secondary  /* Texte or */
.amidac-bg-primary      /* Fond bleu */
.amidac-bg-light        /* Fond clair */
.amidac-bg-dark         /* Fond sombre */
```

### Boutons
```html
<a href="#" class="amidac-btn amidac-btn-primary">Bouton bleu</a>
<a href="#" class="amidac-btn amidac-btn-outline">Bouton outline</a>
<a href="#" class="amidac-btn amidac-btn-accent">Bouton or</a>
```

### Sections
```html
<section class="amidac-section">          <!-- padding normal -->
<section class="amidac-section-lg">       <!-- padding large -->
<section class="amidac-section amidac-bg-light"> <!-- fond gris -->
```

### Cards
```html
<div class="amidac-card">
    <div class="amidac-card-icon">...</div>
    <h3>Titre</h3>
    <p>Contenu</p>
</div>
```

### Titres de section
```html
<div class="amidac-section-title">
    <span class="pretitle">Notre mission</span>
    <h2>Ce que nous faisons</h2>
    <p>Description courte</p>
</div>
```

---

## 🌐 Hooks Kadence utilisés

```php
// Top bar
add_action( 'kadence_top_bar', 'amidac_kadence_top_bar' );

// Filtrer les options Kadence
add_filter( 'kadence_options_defaults', 'amidac_kadence_options' );
```

---

## 📦 Plugins recommandés

| Plugin | Usage |
|--------|-------|
| **Advanced Custom Fields (ACF)** | Champs personnalisés pour les CPT |
| **WooCommerce Donations** | Gestion des dons en ligne |
| **Contact Form 7** | Formulaires de contact |
| **Yoast SEO** | Optimisation SEO |
| **WP Rocket** | Cache et performance |
| **Smush** | Optimisation des images |
| **WPML** | Multilingue (FR/EN/…) |

---

## 🔒 Sécurité intégrée

- Version WordPress masquée
- XML-RPC désactivé
- Emojis WP désactivés (performance)
- Liens inutiles supprimés du `<head>`

---

## 📝 Changelog

### v1.0.0 (2025)
- Création initiale du child theme
- Variables CSS (couleurs, typographie, espacements)
- Types de posts : Projets, Équipe, Partenaires, Rapports
- Taxonomies : Secteurs, Statuts, Départements
- Panneau Customizer complet
- Shortcodes utilitaires
- JavaScript : menu mobile, scroll, animations, compteurs
- Sécurité et optimisations de base

---

## 👨‍💻 Développeur

**Msc. Isaya M'lela Pombo**  
Développeur Web — Emo Touch Group  
[emotouchgroup.com](https://emotouchgroup.com)

---

*© 2025 AMIDAC asbl — Tous droits réservés*
