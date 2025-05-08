# ✅ TODO / Roadmap - Blog & Portfolio Symfony

## 🔧 Priorités techniques

- [ ] Finaliser l'intégration de Mailer (Mailtrap OK, passer à une solution locale ou SMTP auto-hébergé ?)
- [ ] Ajouter la prise en charge de MongoDB pour :
  - [ ] Logs utilisateurs (connexion, erreurs)
  - [ ] Messages du formulaire de contact
- [ ] Configuration des "secrets" avec Symfony (`bin/console secrets:set`)
- [ ] Mettre en place la validation et le captcha sur le formulaire de contact

## ✨ Améliorations frontend

- [ ] Afficher les projets GitHub de manière plus visuelle
- [ ] Intégrer un thème sombre / clair toggle
- [ ] Proposer un site FR/EN (optionnel)

## 🗄 Gestion contenu admin

- [ ] CRUD pour :
  - [X] Articles
  - [ ] Catégories
  - [ ] Tags
  - [ ] Commentaires (modération)
- [ ] Interface d’administration sécurisée (rôle ADMIN)

## 📊 Statistiques & monitoring

- [ ] Compteur d'articles
- [ ] Compteur de vues par article
- [ ] Stats backend (requêtes, erreurs...)
- [ ] Ajout de Prometheus + Grafana pour le monitoring (optionnel)

## 🔐 Sécurité

- [ ] Ajouter un pare-feu (fail2ban, etc.)
- [ ] Scanner de vulnérabilité via `symfony security:check`
- [ ] Ajouter des tests de sécurité automatisés (PHPStan, Psalm...)

## 🚀 Déploiement futur

- [ ] Choisir une plateforme (Render ? Railway ? VPS personnel ?)
- [ ] Configuration HTTPS avec Traefik ou Certbot (selon plateforme)
- [ ] Automatiser CI/CD (GitHub Actions)
