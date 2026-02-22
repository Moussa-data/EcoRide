<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide - Mentions légales</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="hero">
  <h1>Mentions légales</h1>

  <section class="ride-detail">
    <h2>Éditeur du site</h2>
    <p>EcoRide (projet pédagogique)</p>
    <p>Email : contact@ecoride.fr</p>
  </section>

  <section class="ride-detail">
    <h2>Données personnelles</h2>
    <p>Les données saisies (compte, réservations) sont utilisées uniquement pour le fonctionnement du service.</p>
    <p>Les mots de passe sont stockés sous forme chiffrée (hash).</p>
  </section>

  <section class="ride-detail">
    <h2>Hébergement</h2>
    <p>Local (XAMPP) – environnement de développement.</p>
  </section>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
