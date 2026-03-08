<?php
session_start();
require __DIR__ . '/includes/db_connect.php';

$rideId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
  SELECT r.*, u.pseudo AS driver_pseudo
  FROM rides r
  LEFT JOIN users u ON u.id = r.driver_id
  WHERE r.id = ?
");
$stmt->execute([$rideId]);
$ride = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ride) {
  include 'includes/header.php';
  echo '<main style="max-width:900px;margin:2rem auto;padding:1rem;">';
  echo '<h2>Covoiturage introuvable</h2>';
  echo '<a href="covoiturages_db.php">Retour</a>';
  echo '</main>';
  include 'includes/footer.php';
  exit;
}

$userId = $_SESSION['user']['id'] ?? null;
$isLogged = !empty($_SESSION['user']);
$isOwner = $isLogged && ((int)$ride['driver_id'] === (int)$userId);

?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Détail covoiturage</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main style="max-width:900px;margin:2rem auto;padding:1rem;">
  <h1><?= htmlspecialchars($ride['depart']) ?> → <?= htmlspecialchars($ride['arrivee']) ?></h1>

  <p><strong>Chauffeur :</strong> <?= htmlspecialchars($ride['driver_pseudo'] ?? '—') ?></p>
  <p><strong>Date :</strong> <?= htmlspecialchars($ride['date_ride']) ?></p>
  <p><strong>Heure :</strong> <?= htmlspecialchars($ride['heure_depart']) ?> → <?= htmlspecialchars($ride['heure_arrivee']) ?></p>
  <p><strong>Prix :</strong> <?= htmlspecialchars($ride['prix']) ?> €</p>
  <p><strong>Places restantes :</strong> <?= (int)$ride['places_restantes'] ?></p>
  <p><strong>Écologique :</strong> <?= ((int)$ride['ecologique'] === 1) ? "🚗⚡ Oui" : "❌ Non" ?></p>

  <p><a href="covoiturages_db.php">← Retour</a></p>

  <?php if (!$isLogged): ?>
    <p>Connecte-toi pour participer.</p>
    <a href="login.php">Connexion</a>

  <?php elseif ($isOwner): ?>
    <p style="color:red;">Tu ne peux pas réserver ton propre trajet.</p>

  <?php elseif ((int)$ride['places_restantes'] <= 0): ?>
    <p style="color:red;">Plus de places disponibles.</p>

  <?php else: ?>
    <a href="participate.php?id=<?= (int)$ride['id'] ?>">Participer (1 crédit)</a>
  <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>