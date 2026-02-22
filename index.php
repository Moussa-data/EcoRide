<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EcoRide - Accueil</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="hero">
  <h1>Bienvenue sur EcoRide</h1>
  <p>Le covoiturage écologique et économique.</p>

  <form class="search-form" method="GET" action="covoiturages_db.php">
    <input type="text" name="depart" placeholder="Ville de depart" required>
    <input type="text" name="arrivee" placeholder="ville d'arrivé" required>
    <input type="date" name="date_ride">
    <button type="submit">Rechercher</button>
  </form>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>

