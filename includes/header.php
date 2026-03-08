<?php
// Démarrage de la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header>
  <div class="logo">EcoRide</div>

<nav>
  <a href="index.php">Accueil</a>
  <a href="covoiturages_db.php">Covoiturages</a>
  <a href="contact.php">Contact</a>

 <?php if (!empty($_SESSION['user'])): ?>
  <div class="user-box">
    <span class="user-label">Connecté :</span>
    <span class="user-name"><?= htmlspecialchars($_SESSION['user']['pseudo']); ?></span>
  </div>

  <a href="create_ride.php">Proposer un trajet</a>
  <a href="profile.php">Mon profil</a>
  <a href="logout.php">Déconnexion</a>
<?php else: ?>
  <a href="login.php">Connexion</a>
  <a href="register.php">Inscription</a>
<?php endif; ?>
    <a href="create_ride.php">Proposer un trajet</a>
    <a href="profile.php">Mon profil</a>
    <a href="logout.php">Déconnexion</a>
  <?php else: ?>
    <a href="login.php">Connexion</a>
    <a href="register.php">Inscription</a>
  <?php endif; ?>
</nav>
</header>
