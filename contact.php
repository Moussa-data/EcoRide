<?php
session_start();

$successMsg = "";
$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $errorMsg = "Merci de remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Email invalide.";
    } else {
        // Simulation (plus tard: envoi mail ou stockage DB)
        $successMsg = "Message envoyé ✅ (simulation). Nous vous répondrons bientôt.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide - Contact</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="hero">
  <h1>Contact</h1>
  <p>Une question ? Écris-nous.</p>

  <?php if ($successMsg): ?>
    <div style="color:green; margin:0.6rem 0;"><?= htmlspecialchars($successMsg) ?></div>
  <?php endif; ?>

  <?php if ($errorMsg): ?>
    <div style="color:red; margin:0.6rem 0;"><?= htmlspecialchars($errorMsg) ?></div>
  <?php endif; ?>

  <form class="form-register" method="POST" action="contact.php">
    <label for="name">Nom</label>
    <input id="name" name="name" type="text" required>

    <label for="email">Email</label>
    <input id="email" name="email" type="email" required>

    <label for="message">Message</label>
    <textarea id="message" name="message" rows="5" required style="padding:0.7rem 1rem; border:1px solid #ccc; border-radius:5px;"></textarea>

    <button type="submit">Envoyer</button>
  </form>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
