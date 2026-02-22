# EcoRide — Plateforme de covoiturage (Projet ECF)

EcoRide est une application web de covoiturage permettant :
- de rechercher des trajets (filtres + tri)
- de consulter le détail d’un trajet
- de participer à un trajet (double confirmation)
- d’annuler une réservation
- de proposer un trajet (utilisateur connecté)
- de gérer un profil avec crédits

## Stack
- PHP (procédural)
- MySQL (PDO)
- HTML / CSS / JavaScript
- XAMPP (en local)

## Installation (local)
1. Cloner le projet
2. Placer le dossier dans `htdocs` (XAMPP)
3. Démarrer Apache + MySQL
4. Créer une base (ex: `ecoride_db`) dans phpMyAdmin
5. Importer les scripts SQL :
   - `sql/01_schema.sql`
   - `sql/02_seed.sql`
6. Vérifier la connexion DB dans `includes/db_connect.php`
7. Accéder au site :
   - http://localhost/EcoRide/index.php

## Scripts SQL
Les scripts se trouvent dans le dossier `sql/` :
- `01_schema.sql` : création des tables
- `02_seed.sql` : données de test

## Parcours de test (fonctionnel)
1. Inscription / Connexion
2. Recherche d’un trajet (casse/espaces tolérés)
3. Consultation détail
4. Participation avec double confirmation
5. Profil : affichage réservation
6. Annulation : restitution crédit + place

## Comptes de test
Si vous importez `02_seed.sql`, vous pouvez utiliser :
- Email : (à compléter si tu veux que je mette un compte seed précis)
- Mot de passe : (à compléter)

Sinon, vous pouvez créer un compte via `register.php`.

## Liens livrables
- GitHub (public) : À AJOUTER
- Déploiement : À AJOUTER
- Kanban : À AJOUTER

## Auteur
Moussa