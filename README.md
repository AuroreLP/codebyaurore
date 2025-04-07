# Blog & Portfolio - Symfony Fullstack (Dockerized)

Bienvenue sur le dépôt de mon site personnel combinant un **blog** professionnel et un **portfolio** dynamique, développé avec **Symfony**, **Docker** et un environnement de développement sous **WSL2/Linux**.

---

## Présentation du projet

Ce projet a pour objectif de :
- Présenter mes articles (blog)
- Gérer des utilisateurs, commentaires, catégories, tags
- Afficher dynamiquement mes projets GitHub (section "About")
- Inclure un formulaire de contact (messages stockés en MongoDB)
- Offrir un design **mobile-first** et accessible

---

## Environnement technique

### Backend
- **Symfony** `7.2.*` (installé via Composer `webapp`) - vérifié dans composer.json
- **PHP** `8.3.6 (cli)` - vérifié en faisant: php --version
- **Architecture** : VMC (Vue / Modèle / Contrôleur)
- **ORM** : Doctrine (MySQL)
- **HttpClient** : intégration API GitHub
- **Formulaires** Symfony pour contact/commentaires
- **Mailer** : configuration avec Mailtrap (dev)

### Frontend
- **SASS** pour une organisation CSS modulaire (`abstracts/`, `base/`, `components/`, `main/`, `admin/` ) - à finir de configurer
- **HTML5 / JS Vanilla**
- **Mobile-first** design (responsive et clair)

### Docker services
- `php-apache` : serveur PHP + Apache
- `mysql` : base relationnelle (articles, users...)
- `phpmyadmin` : interface gestion MySQL
- `mongo` : base NoSQL pour logs/messages (à venir)
- `mailtrap` : test des emails envoyés (à finir de configurer)

### Base de données
- **MySQL** : articles, utilisateurs, commentaires, catégories, tags
- **MongoDB** (à venir) : logs d'activité & messages du formulaire

### Sécurité
- Variables sensibles via `.env.local`
- `.env` utilisé pour valeurs génériques
- Authentification Symfony (`security.yaml`)
- Connexions aux bases via utilisateurs dédiés

---

## Structure du projet

```
├── docker/                     # Fichiers de config Docker
├── src/                        # Contrôleurs, entités, services Symfony
├── templates/                  # Vues Twig
├── public/                     # Point d'entrée public (index.php)
├── assets/
│   ├── styles/                 # SASS structuré
│   └── js/                     # JS natif
├── migrations/
├── .env.local                  # Variables d'environnement sensibles
├── compose.yaml
└── README.md
```

---

## Installation (via Docker)

```zsh
composer create-project symfony/skeleton codebyaurore
cd codebyaurore
composer require webapp
docker-compose up -d --build
```

Créer les tables :
```zsh
docker exec -it www bin/console doctrine:migrations:migrate
```

---

## Tests & Email (dev)

Les e-mails sont interceptés via **Mailtrap** :
- Configuré dans `.env.local` via `MAILER_DSN`

---

## API GitHub intégrée

- Page About récupère dynamiquement les projets publics GitHub via l'API Github
- Affichage personnalisé dans la vue `about/index.html.twig`

---

## Prochaines étapes
Voir le fichier `TODO.md` (roadmap).