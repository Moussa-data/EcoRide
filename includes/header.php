<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = !empty($_SESSION['user']['pseudo']) ? $_SESSION['user']['pseudo'] : null;
?>

<header class="site-header">
  <div class="logo-bar">
    <a href="index.php" class="site-logo">EcoRide</a>
  </div>

  <?php if ($userName): ?>
    <div class="user-banner">
      <span class="user-label">Bonjour</span>
      <span class="user-name"><?= htmlspecialchars($userName) ?></span>
    </div>
  <?php endif; ?>

  <nav class="main-nav">
    <a href="index.php">Accueil</a>
    <a href="covoiturages_db.php">Covoiturages</a>
    <a href="contact.php">Contact</a>

    <?php if ($userName): ?>
      <a href="create_ride.php">Proposer un trajet</a>
      <a href="profile.php">Mon profil</a>
      <a href="logout.php">Déconnexion</a>
    <?php else: ?>
      <a href="login.php">Connexion</a>
      <a href="register.php">Inscription</a>
    <?php endif; ?>
  </nav>
</header>