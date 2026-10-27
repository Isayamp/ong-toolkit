# ONG Toolkit

Boîte à outils WordPress pour les ONG, développée d'abord pour **AMIDAC asbl** (RDC).

## Contenu du dépôt

| Dossier | Rôle |
| --- | --- |
| `plugins/amidac-toolkit/` | Plugin : projets, équipe, partenaires, rapports, gabarits, shortcodes, widget Elementor |
| `theme/amidac-kadence-child/` | Child theme Kadence : identité visuelle, Customizer |
| `docs/` | Analyse, workflow, feuille de route, journal, guide d'utilisation |
| `scripts/` | Génération des archives `.zip` à installer |

## Fonctionnalités du plugin

- Types de contenu : **Projets**, **Équipe**, **Partenaires**, **Rapports** (+ taxonomies secteur, programme, statut, département).
- Champs personnalisés natifs, sans ACF.
- Gabarits de fiche projet (image à la une, statistiques, informations clés) et de liste, surchargeables par le thème.
- Shortcodes `[amidac_equipe]`, `[amidac_partenaires]`, `[amidac_rapports]`.
- Widget Elementor « Grille Projets » (si Elementor est actif).

## Installation

1. `scripts/build-zip.ps1` génère `dist/amidac-toolkit.zip` et `dist/amidac-kadence-child.zip`.
2. WordPress > Extensions > Ajouter > Téléverser `amidac-toolkit.zip`, puis activer.
3. Voir `docs/GUIDE-UTILISATION.md`.

## Contribuer

Voir `docs/WORKFLOW.md` (commits en français, atomiques).

## Licence

GPL v2 ou ultérieure.
