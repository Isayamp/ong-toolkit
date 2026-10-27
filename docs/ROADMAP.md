# Feuille de route

- [x] Dépôt Git initialisé, sources importées
- [x] Nettoyage du child theme (dossier `elementor/` et include supprimés)
- [x] Plugin unique `amidac-toolkit` (CPT, métaboxes, gabarits, shortcodes, widget) — validé le 2026-10-27
- [x] Gabarit `single-amidac_projet` (écrit, **à tester sur le site**)
- [x] Shortcodes Équipe, Partenaires, Rapports (widgets Elementor dédiés : à faire)
- [ ] Fallback CPT dans le plugin si le thème est inactif
- [ ] Page de réglages du plugin (couleurs, libellés) pour usage multi-organisations
- [ ] Internationalisation (fichiers .pot), licence GPL, README public
- [x] Script de génération des zips (`scripts/build-zip.ps1`)

## À faire ensuite
- Tester sur le site, puis valider l'affichage de la fiche projet.
- Saisir équipe, partenaires et rapports dans l'admin et remplacer le code en dur des pages.
- Retirer du thème le code CPT/métaboxes devenu redondant (une fois le plugin validé en production).
- Généralisation : renommer le préfixe `amidac_` en préfixe neutre + page de réglages.
