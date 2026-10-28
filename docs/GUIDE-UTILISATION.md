# Guide d'utilisation (équipe AMIDAC)

Tout se gère depuis le menu WordPress, sans écrire de code.

## Projets
Projets > Ajouter. Renseigner titre, texte, **image mise en avant**, puis les cases « Détails », « Statistiques » et « Médias ».
La fiche s'affiche automatiquement avec l'image, les chiffres clés et l'encadré « En bref ».

## Équipe
Équipe > Ajouter un membre : nom (titre), photo (image mise en avant), poste, email, LinkedIn.
Le champ « Ordre » (attributs de page) classe l'affichage. Afficher avec `[amidac_equipe]` ou `[amidac_equipe departement="direction"]`.

## Partenaires
Partenaires > Ajouter : nom + logo (image mise en avant) + site web. Afficher avec `[amidac_partenaires]`.

## Rapports et documents
Menu **Rapports** (espace dédié, hors éditeur d'articles).
- **Liste** : filtre par catégorie (rapports annuels, autres publications, réseaux & coalitions, documents institutionnels), recherche, actions groupées (publier, masquer, supprimer).
- **Ajouter un document** : titre, catégorie, description, fichier (bouton « Choisir dans la médiathèque »), date affichée, ordre, visibilité. Les champs année/type/groupe n'apparaissent que pour les catégories concernées.
- **Premier démarrage** : bouton « Importer la structure existante » crée les 18 documents actuels de la page, sans fichier. Il reste à rattacher chaque PDF.
- Sans fichier, le site affiche « Bientôt disponible » au lieu d'un bouton mort.
- **Importer un lot** (bouton en haut de la liste) : envoyer une archive ZIP contenant les PDF et un tableau `rapports.csv` (colonnes `fichier ; titre ; categorie ; type ; annee ; date_affichee ; groupe ; description`). Chaque ligne crée un document complet, fichier compris, dans la médiathèque. Un document déjà présent (même titre, même catégorie) est ignoré, donc l'import peut être relancé sans doublon. Case « brouillon » pour vérifier avant de publier.

### Préparer un lot (sur le PC)
Les Word sont convertis en PDF, puis le PDF est allégé : images réduites à 130 dpi, polices réduites aux caractères utilisés, structure nettoyée. Le titre et l'auteur sont écrits dans le PDF. Les noms de fichier sont explicites, par exemple `forum-nundu-compte-rendu-n22-2025-10-07.pdf`. Le titre, la date, le numéro, le lieu et les participants sont lus dans le contenu du document. Les doublons exacts sont écartés. Les originaux ne sont jamais modifiés ; les PDF prêts sont écrits dans un dossier à part. La compression se fait sur le PC, pas sur l'hébergement.

Affichage : `[amidac_rapports]` (toutes les sections), avec barre de recherche et filtres catégorie et année (`filtres="non"` pour les masquer), `[amidac_rapports categorie="annuel"]`, `[amidac_rapports categorie="reseau" titres="oui"]`, `[amidac_rapports annee="2024"]`.
Pour la page Rapports & Publications : dans Elementor, remplacer le widget HTML par un widget Shortcode contenant `[amidac_rapports]` (garder le hero et la section de contact).

## Dans Elementor
Ajouter le widget **Shortcode** et y coller l'un des shortcodes ci-dessus.
Le widget « AMIDAC — Grille Projets » se trouve dans la catégorie « AMIDAC asbl ».

## Migration depuis l'ancienne installation
1. Téléverser et activer `amidac-toolkit.zip`.
2. Désactiver (puis supprimer) l'ancien plugin « AMIDAC — Widgets Elementor ».
3. Mettre à jour le child theme avec `amidac-kadence-child.zip`.
4. Réglages > Permaliens > Enregistrer (met à jour les URL).
Les contenus existants sont conservés : les noms de types de contenu et de champs n'ont pas changé.
