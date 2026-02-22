<?php
session_start();
require __DIR__ . '/includes/db_connect.php';
require __DIR__ . '/includes/auth.php';

$user = current_user($pdo);

$successMsg = "";
$errorMsg = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $depart = trim($_POST['depart'] ?? '');
    $arrivee = trim($_POST['arrivee'] ?? '');
    $date_ride = $_POST['date_ride'] ?? '';
    $heure_depart = $_POST['heure_depart'] ?? '';
    $heure_arrivee = $_POST['heure_arrivee'] ?? '';
    $prix = $_POST['prix'] ?? '';
    $places_restantes = $_POST['places_restantes'] ?? '';
    $ecologique = isset($_POST['ecologique']) ? 1 : 0;

    if ($depart === '' || $arrivee === '' || $date_ride === '' || $heure_depart === '' || $heure_arrivee === '' || $prix === '' || $places_restantes === '') {
        $errorMsg = "Tous les champs sont obligatoires (sauf écologique).";
    } elseif (!is_numeric($prix) || (float)$prix <= 0) {
        $errorMsg = "Le prix doit être un nombre positif.";
    } elseif (!ctype_digit((string)$places_restantes) || (int)$places_restantes <= 0) {
        $errorMsg = "Les places restantes doivent être un entier positif.";
    } else {
        try {
    $stmt = $pdo->prepare("
    INSERT INTO rides (driver_id, depart, arrivee, date_ride, heure_depart, heure_arrivee, prix, places_restantes, ecologique)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
    $_SESSION['user']['id'],
    $depart,
    $arrivee,
    $date_ride,
    $heure_depart,
    $heure_arrivee,
    $prix,
    $places_restantes,
    $ecologique
    ]);


            header("Location: covoiturages_db.php");
            exit;
        } catch (PDOException $e) {
            $errorMsg = "Erreur lors de la création du trajet : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide - Proposer un trajet</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="hero">
  <h1>Proposer un covoiturage</h1>
  <p>Crée un trajet qui apparaîtra dans la liste des covoiturages.</p>

  <?php if (!empty($errorMsg)) : ?>
    <div style="color:red; margin:0.6rem 0;"><?= htmlspecialchars($errorMsg) ?></div>
  <?php endif; ?>

  <form class="form-register" action="create_ride.php" method="POST">
    <label for="depart">Ville de départ</label>
    <input type="text" id="depart" name="depart" required>

    <label for="arrivee">Ville d'arrivée</label>
    <input type="text" id="arrivee" name="arrivee" required>

    <label for="date_ride">Date</label>
    <input type="date" id="date_ride" name="date_ride" required>

    <label for="heure_depart">Heure de départ</label>
    <input type="time" id="heure_depart" name="heure_depart" required>

    <label for="heure_arrivee">Heure d'arrivée</label>
    <input type="time" id="heure_arrivee" name="heure_arrivee" required>

    <label for="prix">Prix (€)</label>
    <input type="number" step="0.01" id="prix" name="prix" required>

    <label for="places_restantes">Places restantes</label>
    <input type="number" id="places_restantes" name="places_restantes" required>

    <label style="display:flex; gap:0.5rem; align-items:center; margin-top:0.5rem;">
      <input type="checkbox" name="ecologique" value="1">
      Trajet écologique (électrique)
    </label>

    <button type="submit">Créer le trajet</button>
  </form>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
