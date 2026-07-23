# Site INSP — Institut National de Santé Publique du Burkina Faso

Reconstruction du site à partir des 41 pages HTML fournies, sous forme d'un
**modèle de page unique** (HTML + Tailwind CSS) qui affiche le contenu
stocké dans `siteData.json`.

## 📁 Contenu du dossier

| Fichier            | Rôle                                                             |
|---------------------|-------------------------------------------------------------------|
| `index.html`        | Le site public : en-tête, 7 menus déroulants, pages, actualités.  |
| `admin.html`        | Espace admin protégé pour gérer les annonces/actualités.          |
| `siteData.json`     | Toutes les données du site (menus, pages, actualités, contact).    |
| `save.php`          | *(optionnel)* permet à l'admin d'enregistrer directement sur le serveur. |
| `README.md`         | Ce document.                                                       |

## 🚀 Mise en ligne

1. Copiez les **5 fichiers** dans le même dossier sur votre hébergement
   (via FTP, cPanel File Manager, etc.). Aucune base de données n'est requise.
2. Ouvrez `index.html` — c'est la page d'accueil du site.
3. `siteData.json` doit rester à côté de `index.html` et `admin.html` (même dossier).

Le site fonctionne sur **n'importe quel hébergement statique** (y compris
sans PHP). `save.php` est optionnel — voir plus bas.

## 🗂️ Comment le site est structuré

- **1 seul modèle de page** (`index.html`) affiche tout le contenu. La page
  affichée dépend de l'adresse après le `#`, par exemple :
  `index.html#/covid-19` affiche la page « Maladie à coronavirus COVID-19 ».
- Les **7 menus principaux** et leurs sous-menus déroulants (jusqu'à 3
  niveaux) sont générés automatiquement depuis `siteData.json` → `menu`.
- Chaque page du site (39 au total, sur les 41 fichiers fournis — 2 fichiers
  étaient des pages d'index techniques sans contenu propre) est stockée
  dans `siteData.json` → `pages`, avec son titre, sa date, son image, son
  contenu, ses documents PDF et ses vidéos.
- Les **13 actualités/évènements** repérés dans le fichier « Actualité-
  Évènement » sont stockés séparément dans `siteData.json` → `news` : ce
  sont eux que l'espace Admin permet de gérer.

## 🖼️ Images et vidéos

Les chemins des médias sont conservés **exactement tels qu'ils apparaissaient
dans les pages fournies**. Dans les fichiers HTML originaux, ces chemins
étaient déjà des adresses complètes hébergées sur `insp.bf` /
`i0.wp.com` (ex : `https://i0.wp.com/insp.bf/wp-content/uploads/...`), il n'y
avait pas de dossier local `assets/images/…` dans l'archive reçue. Ces
adresses ont donc été reprises telles quelles pour que les images et vidéos
continuent de s'afficher sans modification.

Si vous rapatriez un jour ces médias en local (ex. dossier `assets/images/`
sur votre propre hébergement), il suffit de remplacer les valeurs `image`,
`heroImage` et `src` correspondantes dans `siteData.json` — aucune autre
modification n'est nécessaire, le site s'adapte automatiquement.

## 🔐 Espace Admin (`admin.html`)

Accessible via le lien « Espace admin » en pied de page, ou directement à
l'adresse `admin.html`.

**Mot de passe par défaut : `INSP2026!`**

Depuis l'espace admin, le service Communication peut :
- Modifier le **bandeau d'annonce** affiché en haut de la page d'accueil.
- **Ajouter, modifier ou supprimer** des actualités/annonces (titre, extrait,
  catégorie, image, lien).

### Comment fonctionne l'enregistrement

Deux cas de figure, gérés automatiquement par `admin.html` :

1. **Avec `save.php` actif** (hébergement PHP) : en cliquant sur
   « Enregistrer », les modifications sont écrites directement dans
   `siteData.json` sur le serveur et sont **immédiatement visibles par tous
   les visiteurs**.
2. **Sans PHP** (hébergement statique simple) : les modifications sont
   enregistrées dans le navigateur de l'administrateur (aperçu immédiat sur
   cet appareil). Pour les publier pour tout le monde, cliquez sur
   **« Télécharger le JSON »**, puis remplacez le fichier `siteData.json`
   sur votre hébergement par celui qui vient d'être téléchargé.

> ⚠️ **Important** : `admin.html` protège l'accès par mot de passe côté
> navigateur — c'est une protection simple, adaptée à un usage interne, mais
> ce n'est pas une authentification de niveau serveur. Pour un site
> institutionnel exposé publiquement avec plusieurs comptes administrateurs,
> il est recommandé à terme de mettre en place une vraie authentification
> côté serveur (session PHP, .htaccess, etc.).

### Changer le mot de passe admin

1. Ouvrez `admin.html` dans un navigateur, ouvrez la console développeur
   (touche F12), et exécutez :
   ```js
   const buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode('VotreNouveauMotDePasse'));
   console.log(Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2,'0')).join(''));
   ```
2. Copiez la valeur affichée (une longue chaîne de 64 caractères).
3. Dans `admin.html`, remplacez la valeur de `PASSWORD_HASH` par cette
   nouvelle chaîne (près du début de la balise `<script>` en bas du fichier).

## 🔍 Recherche

Le site public inclut une recherche simple (icône loupe dans l'en-tête) qui
filtre les pages et actualités par titre.

## 🛠️ Notes techniques

- Tailwind CSS est chargé via CDN (`cdn.tailwindcss.com`) — aucune étape de
  build n'est nécessaire.
- La navigation utilise des ancres (`#/slug-de-la-page`), ce qui permet de
  partager un lien direct vers n'importe quelle page.
- Le contenu textuel a été nettoyé des classes techniques propres à
  l'ancien site WordPress (thème Hestia) pour s'afficher proprement avec
  Tailwind, sans rien retirer du texte, des liens, images ou documents.
- 2 fichiers de l'archive d'origine étaient des pages d'index de catégorie
  sans contenu propre (« BIOEXPRESS » et « Actualité-Évènement » — cette
  dernière étant la page-liste qui a servi à extraire les 13 actualités) ;
  ils n'ont donc pas de fiche `pages` dédiée mais leur contenu utile est bien
  repris ailleurs (menu et actualités).
