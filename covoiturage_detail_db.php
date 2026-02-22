<?php
session_start();
require __DIR__ . '/includes/db_connect.php';

$rideId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
  SELECT 
    r.*,
    u.pseudo AS driver_pseudo,
    (SELECT COUNT(*) FROM bookings b WHERE b.ride_id = r.id AND b.status = 'confirmed') AS nb_reservations
  FROM rides r
  LEFT JOIN users u ON u.id = r.driver_id
  WHERE r.id = ?
");
$stmt->execute([$rideId]);
$ride = $stmt->fetch();

$errorMsg = $_GET['error'] ?? '';
$userId = $_SESSION['user']['id'] ?? null;

$connected = !empty($_SESSION['user']);
$isOwner = $connected && $ride && ((int)$ride['driver_id'] === (int)$userId);
$hasPlaces = $ride && ((int)$ride['places_restantes'] > 0);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EcoRide - Détail</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="hero">
  <?php if (!$ride): ?>
    <h1>Covoiturage introuvable</h1>
    <a class="btn-link" href="covoiturages_db.php">Retour</a>

  <?php else: ?>
    <h1><?= htmlspecialchars($ride['depart']) ?> → <?= htmlspecialchars($ride['arrivee']) ?></h1>

    <?php if (!empty($errorMsg)): ?>
      <div style="color:red; margin:0.6rem 0;"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <p><strong>Chauffeur :</strong> <?= htmlspecialchars($ride['driver_pseudo'] ?? '—') ?></p>
    <p><strong>Réservations :</strong> <?= (int)$ride['nb_reservations'] ?></p>
    <p><strong>Date :</strong> <?= htmlspecialchars($ride['date_ride']) ?></p>
    <p><strong>Heure :</strong> <?= htmlspecialchars($ride['heure_depart']) ?> → <?= htmlspecialchars($ride['heure_arrivee']) ?></p>
    <p><strong>Prix :</strong> <?= htmlspecialchars($ride['prix']) ?> €</p>
    <p><strong>Places restantes :</strong> <?= (int)$ride['places_restantes'] ?></p>
    <p><strong>Écologique :</strong> <?= ((int)$ride['ecologique'] === 1) ? "🚗⚡ Oui" : "❌ Non" ?></p>

    <a class="btn-link" href="covoiturages_db.php">← Retour</a>

    <?php if (!$connected): ?>
      <p style="margin-top:1rem; color:#777;">Connecte-toi pour participer.</p>
      <a class="btn-link" href="login.php">Se connecter</a>

    <?php elseif ($isOwner): ?>
      <p style="margin-top:1rem; color:red;">Tu ne peux pas réserver ton propre trajet.</p>

    <?php elseif (!$hasPlaces): ?>
      <p style="margin-top:1rem; color:red;">Plus de places disponibles.</p>

    <?php else: ?>
      <a class="btn-link" href="participate.php?id=<?= (int)$ride['id'] ?>">
        Participer (1 crédit)
      </a>
    <?php endif; ?>
  <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>