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

## Rapports
Rapports > Ajouter : titre, année, type, et l'URL du PDF (Médiathèque > fichier > copier l'URL).
Afficher avec `[amidac_rapports]` ou `[amidac_rapports annee="2025"]`.

## Dans Elementor
Ajouter le widget **Shortcode** et y coller l'un des shortcodes ci-dessus.
Le widget « AMIDAC — Grille Projets » se trouve dans la catégorie « AMIDAC asbl ».

## Migration depuis l'ancienne installation
1. Téléverser et activer `amidac-toolkit.zip`.
2. Désactiver (puis supprimer) l'ancien plugin « AMIDAC — Widgets Elementor ».
3. Mettre à jour le child theme avec `amidac-kadence-child.zip`.
4. Réglages > Permaliens > Enregistrer (met à jour les URL).
Les contenus existants sont conservés : les noms de types de contenu et de champs n'ont pas changé.
