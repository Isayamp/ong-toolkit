# Déploiement sur l'hébergement mutualisé (Hostinger)

Le plugin et le thème sont envoyés par SFTP depuis GitHub Actions.
Le déploiement n'est **jamais automatique à chaque commit** : il se lance à la main ou à la publication d'un tag `vX.Y.Z`.
Seuls les dossiers `amidac-toolkit` et `amidac-kadence-child` sont remplacés ; le reste du site n'est pas touché.

## 1. Créer un compte SFTP dédié (hPanel)

1. hPanel > **Fichiers** > **Comptes FTP** > créer un compte.
2. Dossier du compte : `public_html/wp-content` (ou la racine du site, selon le cas).
3. Noter l'hôte, le port (souvent `65002` en mutualisé), l'identifiant et le mot de passe.
4. Ce compte sert uniquement au déploiement : le supprimer s'il n'est plus utile.

## 2. Enregistrer les secrets dans GitHub

Dépôt > Settings > Secrets and variables > Actions > New repository secret, ou en ligne de commande
(la valeur est demandée au clavier, rien n'est écrit dans l'historique) :

```
gh secret set SFTP_HOST
gh secret set SFTP_PORT
gh secret set SFTP_USER
gh secret set SFTP_PASSWORD
gh secret set SFTP_WP_CONTENT
```

`SFTP_WP_CONTENT` est le chemin de `wp-content` **vu depuis le dossier du compte SFTP**
(par exemple `.` si le compte est déjà dans `wp-content`, sinon `public_html/wp-content`).

## 3. Lancer un déploiement

- Manuel : onglet **Actions** > « Déploiement sur le site » > **Run workflow** > choisir plugin, thème ou les deux.
- Par tag : `git tag v0.2.0 && git push --tags` (déploie plugin et thème).

## 4. Après le déploiement

Vider le cache (Hostinger / extension de cache) et ouvrir une fiche projet pour vérifier.

## Sécurité

- Ne jamais écrire d'identifiants dans le dépôt (il est public) ni dans les issues.
- Les secrets GitHub ne sont pas exposés dans les journaux ; changer le mot de passe SFTP si un doute existe.
