<?php
session_start();
require __DIR__ . '/includes/db_connect.php';

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errorMsg = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT id, pseudo, email, password_hash, credits FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $errorMsg = "Email ou mot de passe incorrect.";
        } elseif (!password_verify($password, $user['password_hash'])) {
            $errorMsg = "Email ou mot de passe incorrect.";
        } else {
            // Connexion OK : on stocke en session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'pseudo' => $user['pseudo'],
                'email' => $user['email'],
                'credits' => $user['credits']
            ];

            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide - Connexion</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

  <?php include 'includes/header.php'; ?>

  <main class="hero">
      <h1>Connexion</h1>

      <?php if (!empty($errorMsg)) : ?>
        <div style="color:red; margin: 0.6rem 0;">
          <?= htmlspecialchars($errorMsg) ?>
        </div>
      <?php endif; ?>

      <form class="form-register" action="login.php" method="POST">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required>

          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" required>

          <button type="submit">Se connecter</button>
      </form>
  </main>

  <?php include 'includes/footer.php'; ?>

</body>
</html>
