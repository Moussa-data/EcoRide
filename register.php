<?php
require __DIR__ . '/includes/db_connect.php';

$successMsg = "";
$errorMsg = "";

// Traitement PHP (quand le formulaire est soumis)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validation côté serveur (indispensable même si JS existe)
    if ($pseudo === '' || $email === '' || $password === '' || $confirm === '') {
        $errorMsg = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Email invalide.";
    } elseif ($password !== $confirm) {
        $errorMsg = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 8) {
        $errorMsg = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        try {
            // Vérifier si email déjà utilisé
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errorMsg = "Cet email est déjà utilisé.";
            } else {
                // Hash du mot de passe
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // Insertion
                $stmt = $pdo->prepare("
                    INSERT INTO users (pseudo, email, password_hash, credits)
                    VALUES (?, ?, ?, 20)
                ");
                $stmt->execute([$pseudo, $email, $passwordHash]);

                $successMsg = "Compte créé avec succès ✅ (20 crédits offerts)";
            }
        } catch (PDOException $e) {
            $errorMsg = "Erreur lors de l’inscription : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>EcoRide - Inscription</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

  <?php include 'includes/header.php'; ?>

  <main class="hero">
      <h1>Créer un compte EcoRide</h1>
      <p>Rejoignez la communauté de covoiturage écologique 🌿</p>

      <?php if (!empty($successMsg)) : ?>
        <div style="color: green; margin: 0.6rem 0;">
          <?= htmlspecialchars($successMsg) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($errorMsg)) : ?>
        <div style="color: red; margin: 0.6rem 0;">
          <?= htmlspecialchars($errorMsg) ?>
        </div>
      <?php endif; ?>

      <!-- Zone d'erreurs JS -->
      <div id="errorBox" style="color:red; margin-top:0.5rem; font-size:0.9rem;"></div>

      <form class="form-register" action="register.php" method="POST">
          <label for="pseudo">Pseudo</label>
          <input type="text" id="pseudo" name="pseudo" required value="<?= htmlspecialchars($_POST['pseudo'] ?? '') ?>">

          <label for="email">Email</label>
          <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" required>

          <label for="confirm_password">Confirmer le mot de passe</label>
          <input type="password" id="confirm_password" name="confirm_password" required>

          <button type="submit">Créer mon compte</button>
      </form>
  </main>

  <?php include 'includes/footer.php'; ?>

  <!-- Validation JS (si tu l’avais déjà et qu’elle marche) -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const form = document.querySelector(".form-register");
      const passwordInput = document.getElementById("password");
      const confirmInput = document.getElementById("confirm_password");
      const errorBox = document.getElementById("errorBox");
      if (!form || !passwordInput || !confirmInput || !errorBox) return;

      form.addEventListener("submit", function (e) {
        const errors = [];
        const password = passwordInput.value.trim();
        const confirm = confirmInput.value.trim();
        errorBox.innerHTML = "";

        if (password !== confirm) errors.push("Les mots de passe ne correspondent pas.");
        if (password.length < 8) errors.push("Le mot de passe doit contenir au moins 8 caractères.");
        if (!/[0-9]/.test(password)) errors.push("Le mot de passe doit contenir au moins un chiffre.");
        if (!/[A-Z]/.test(password)) errors.push("Le mot de passe doit contenir au moins une majuscule.");
        if (!/[a-z]/.test(password)) errors.push("Le mot de passe doit contenir au moins une minuscule.");

        if (errors.length > 0) {
          e.preventDefault();
          errorBox.innerHTML = errors.join("<br>");
        }
      });
    });
  </script>

</body>
</html>
