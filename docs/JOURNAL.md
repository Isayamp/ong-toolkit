# Journal

## 2026-10-27
- Analyse du site en ligne (voir ANALYSE.md).
- Création du dépôt, import du thème et du plugin, suppression du vestige `elementor/` du thème.
- Dépôt public créé : https://github.com/Isayamp/ong-toolkit
- Création du plugin `amidac-toolkit` : CPT, métaboxes (+ membres/partenaires/rapports), gabarits projet, shortcodes, widget Elementor migré.
- Thème : désactive ses propres CPT/métaboxes si le plugin est actif (aucune perte de données, mêmes slugs).
- Ancien plugin `amidac-elementor-widgets` supprimé du dépôt.

## 2026-10-28
- Fiche projet : image entière, résumé, statistiques au choix, autres projets, cartes arrondies à dégradé.
- Déploiement SFTP via GitHub Actions (accès SSH Hostinger, port 65002) ; CSS versionné par date de modification pour éviter le cache.
- Rapports : espace de gestion dédié dans l'admin (liste, formulaire, médiathèque, actions groupées, import), type de contenu masqué de l'éditeur d'articles.
- Rapports : nouveau shortcode `[amidac_rapports]` (cartes par catégorie, panneaux groupés pour les documents institutionnels).
- Rapports : écran « Importer un lot » (ZIP de PDF + rapports.csv). Premier lot préparé : 70 Word et 6 PDF reçus, 55 documents retenus après retrait des doublons, 28,6 Mo ramenés à 19,4 Mo.

