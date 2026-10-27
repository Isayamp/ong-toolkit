# Workflow de développement

## Règles de dépôt

- Branche principale : `main`. Une branche par fonctionnalité (`fonctionnalite/...`, `correctif/...`).
- Commits **en français**, à l'impératif ou au présent, courts et atomiques, un sujet par commit
  (ex. « Ajout du gabarit single-projet », « Correction du nonce des métaboxes »).
- Beaucoup de petits commits plutôt qu'un gros.
- Aucun fichier généré ni archive `.zip` versionné.

## Cycle d'une tâche

1. Ouvrir/mettre à jour l'entrée dans `docs/ROADMAP.md`.
2. Développer dans le dépôt, commits atomiques.
3. Tester sur le site (staging si possible), noter le résultat dans `docs/JOURNAL.md`.
4. Générer le zip (`dist/`) et l'installer via Extensions > Ajouter.

## Conventions de nommage

- Text domains : thème `amidac-kadence-child`, plugin `amidac-elementor-widgets`.
- Meta keys : `_amidac_*`. CPT : `amidac_projet|membre|partenaire|rapport`.
- Taxonomies : `secteur`, `programme`, `statut_projet`, `departement`.
