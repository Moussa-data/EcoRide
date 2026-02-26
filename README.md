# EcoRide — Plateforme de covoiturage écologique

Projet réalisé dans le cadre de l’ECF Développeur Web et Web Mobile.

## 🚀 Fonctionnalités

- Inscription / Connexion sécurisée
- Recherche de covoiturages (filtres + tri)
- Détail d’un trajet
- Participation avec double confirmation
- Anti-doublon réservation
- Annulation avec restitution crédit et place
- Création d’un trajet (utilisateur connecté)
- Profil utilisateur avec historique

---

## 🛠️ Technologies utilisées

- PHP (PDO)
- MySQL
- HTML / CSS
- JavaScript
- XAMPP (local)

---

## ⚙️ Installation locale

1. Cloner le projet
2. Placer le dossier dans `htdocs`
3. Démarrer Apache et MySQL (XAMPP)
4. Créer une base de données `ecoride_db`
5. Importer :
   - `sql/01_schema.sql`
   - `sql/02_seed.sql`
6. Vérifier `includes/db_connect.php`
7. Accéder à :
   http://localhost/EcoRide/index.php

---

## 🧪 Parcours de test

1. Créer un compte
2. Se connecter
3. Rechercher un trajet
4. Consulter détail
5. Participer (double confirmation)
6. Vérifier crédit et places
7. Annuler réservation

---

## 📂 Structure

- `includes/` → Connexion DB, Auth, Header/Footer
- `sql/` → Scripts base de données
- `assets/` → CSS

## 📋 Gestion de projet

Suivi du projet sur Trello :  
🔗 https://trello.com/invite/b/699ac9be3c2967bdf8935592/ATTIa84a2afb9d946e1b3627baa61502a45e904AC793/ecoride

Ce board contient :
- Backlog des fonctionnalités
- Cartes “À faire”, “En cours”, “Terminé”
- Organisation des US du projet
- Suivi des corrections et tests

---

## 📌 Auteur

Moussa