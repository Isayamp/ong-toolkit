# Analyse du site amidacasbl.org

Date de l'analyse : 2026-10-27

## Constat

- Thème actif : `amidac-kadence-child` (parent `kadence`). Constructeur : Elementor + ElementsKit. Hébergement Hostinger.
- Pages : accueil, `nos-programmes`, `a-propos`, `nos-projets`, `contact`, `rapports-publications`.
- Types de contenu enregistrés par le child theme : `amidac_projet` (7 publiés), `amidac_membre`, `amidac_partenaire`, `amidac_rapport` (0 publié, via l'API publique).
- Taxonomies : `secteur`, `programme`, `statut_projet` (projets), `departement` (membres).
- Chaque projet possède bien une image mise en avant (`featured_media` renseigné).

## Problème 1 : fiche projet mal mise en page, image absente

Le child theme enregistre les CPT mais ne fournit **aucun gabarit** `single-amidac_projet.php` (le dossier `template-parts/` est vide).
Kadence applique son gabarit d'article générique, qui ne connaît pas ce CPT : pas d'image à la une, pas de métadonnées (zone, dates, bénéficiaires...), pas de mise en page dédiée.

Correctif prévu : gabarits dédiés fournis par le plugin (voir ROADMAP).

## Problème 2 : rapports, équipe, partenaires saisis à la main dans le code

Les CPT existent mais sont vides : les listes sont écrites en dur dans les pages Elementor. Il faut des widgets/shortcodes qui lisent les CPT, pour que l'équipe AMIDAC gère tout depuis l'admin WordPress.

## Problème 3 : architecture non réutilisable

CPT et métaboxes sont dans le thème : si le thème change, les contenus disparaissent. Pour un usage par plusieurs organisations, tout ce qui est « données » doit vivre dans un plugin.
