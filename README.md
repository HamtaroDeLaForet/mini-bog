# mini-blog

Application de blog développée avec Symfony 8, Doctrine ORM et EasyAdmin. Elle permet la gestion d'articles catégorisés, de commentaires modérés et d'un espace d'administration complet.

---

## Stack technique

- **PHP** avec Symfony 8
- **Doctrine ORM** + Migrations
- **EasyAdmin 5** pour le back-office
- **Twig** + Bootstrap 5 pour le front
- **Symfony UX Turbo** pour la navigation sans rechargement
- **FakerPHP** + DoctrineFixturesBundle pour les données de test
- **PHPUnit** pour les tests

---

## Modèle de données

### User
Utilisateur authentifié via un formulaire de login custom (`AuthAuthenticator`). Les nouveaux comptes sont créés avec `isActive = false` et doivent être validés par un administrateur avant de pouvoir commenter.

| Champ | Type |
|---|---|
| email | string (unique) |
| username | string |
| firstName / lastName | string |
| password | string (hashé) |
| roles | array |
| isActive | bool |
| profilePicture | string (nullable) |
| createdAt / updatedAt | DateTimeImmutable |

### Post
Article de blog lié à un `User` (auteur) et une `Category`. Les images sont uploadées via EasyAdmin dans `public/uploads/posts/`.

| Champ | Type |
|---|---|
| title | string |
| content | text |
| picture | string |
| publishedAt / createdAt | DateTimeImmutable |
| user | ManyToOne → User |
| category | ManyToOne → Category |

### Category
Catégorie d'articles avec un nom et une description.

### Comment
Commentaire lié à un `Post` et un `User`. Chaque commentaire a un champ `isApprouved` : les admins voient leurs commentaires publiés directement, les autres passent par une modération.

---

## Installation

```bash
git clone <repo>
cd mini-blog
composer install
```

Copier `.env` en `.env.local` et configurer la base de données :

```dotenv
DATABASE_URL="mysql://user:password@127.0.0.1:3306/mini_blog"
```

Créer la base et appliquer les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Charger les fixtures (données de test) :

```bash
php bin/console doctrine:fixtures:load
```

---

## Lancer le projet

Avec WAMP/XAMPP, pointer le virtual host sur le dossier `public/`. Ou via le serveur intégré Symfony :

```bash
symfony server:start
```

---

## Routes principales

| Route | URL | Description |
|---|---|---|
| `app_home` | `/` | Page d'accueil, 3 derniers articles |
| `app_posts_index` | `/posts` | Liste de tous les articles |
| `app_post_show` | `/posts/{id}` | Détail d'un article + commentaires |
| `app_register` | `/register` | Inscription |
| `app_login` | `/login` | Connexion |
| `app_logout` | `/logout` | Déconnexion (redirige vers `/`) |
| `app_profile` | `/profile` | Profil utilisateur |
| `admin` | `/admin` | Dashboard EasyAdmin (admin uniquement) |

---

## Administration (EasyAdmin)

Accessible sur `/admin` pour les utilisateurs avec `ROLE_ADMIN`. Gestion complète de :
- **Users** — activation des comptes, gestion des rôles
- **Posts** — création avec upload d'image, assignation de catégorie
- **Categories** — nom et description
- **Comments** — modération (approbation / suppression)

---

## Fonctionnalités notables

- **Validation des comptes** : les nouveaux inscrits ont `isActive = false`. Un admin doit les activer depuis le back-office pour qu'ils puissent commenter.
- **Modération des commentaires** : les commentaires des utilisateurs normaux sont soumis à validation. Les admins publient directement.
- **Avatar en navbar** : photo de profil ou initiale générée si aucune photo n'est définie.
- **Navigation avec referer** : le bouton "Retour" sur un article ramène à la page de provenance (accueil ou liste).
- **Sauvegarde du scroll** : la position de scroll est mémorisée lors d'un clic "Lire la suite" et restaurée au retour.
- **Upload d'images** : les images de posts sont stockées dans `public/uploads/posts/` et nommées par hash aléatoire.

---

## Tests

```bash
php bin/phpunit
```

---

## Structure du projet

```
src/
├── Controller/
│   ├── Admin/          # CRUD EasyAdmin
│   ├── PostsController.php
│   ├── ProfileController.php
│   ├── RegistrationController.php
│   └── SecurityController.php
├── Entity/             # User, Post, Category, Comment
├── DataFixtures/       # AppFixtures (Faker)
├── Form/               # CommentType, ProfileType, RegistrationFormType
├── Repository/
└── Security/           # AuthAuthenticator
templates/
├── base.html.twig
├── home/
├── posts/
├── profile/
└── security/
```

## Login compte test 

Admin : admin@admin.blog | adminblog
User Test : lucas.martin@exemple.fr | User123.

