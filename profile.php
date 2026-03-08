<?php
session_start();
require __DIR__ . '/includes/db_connect.php';
require __DIR__ . '/includes/auth.php';

$current = current_user($pdo);

// Recharger l'utilisateur (crédits à jour)
$stmtUser = $pdo->prepare("SELECT id, pseudo, email, credits FROM users WHERE id = ?");
$stmtUser->execute([$_SESSION['user']['id']]);
$user = $stmtUser->fetch();

if (!$user) {
  session_destroy();
  header("Location: login.php");
  exit;
}

// Mettre à jour la session (pour menu + pages)
$_SESSION['user'] = $user;

// Réservations
$stmt = $pdo->prepare("
  SELECT 
    b.id AS booking_id,
    b.status,
    r.id AS ride_id,
    r.depart, r.arrivee, r.date_ride, r.heure_depart, r.heure_arrivee,
    r.prix, r.ecologique
  FROM bookings b
  JOIN rides r ON r.id = b.ride_id
  WHERE b.user_id = ?
  ORDER BY b.created_at DESC
");
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EcoRide - Mon profil</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="hero">
  <h1>Mon espace EcoRide</h1>

  <?php if (!empty($_GET['success'])): ?>
    <div style="color:green; margin:0.6rem 0;">Participation enregistrée ✅ (-1 crédit)</div>
  <?php endif; ?>

  <?php if (!empty($_GET['cancel'])): ?>
    <div style="color:green; margin:0.6rem 0;">Réservation annulée ✅ (+1 crédit)</div>
  <?php endif; ?>

  <?php if (!empty($_GET['error'])): ?>
    <div style="color:red; margin:0.6rem 0;"><?= htmlspecialchars($_GET['error']) ?></div>
  <?php endif; ?>

  <p>Bienvenue <strong><?= htmlspecialchars($user['pseudo']) ?></strong> 👋</p>

  <section class="ride-detail">
    <h2>Informations</h2>
    <p><strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo']) ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Crédits :</strong> <?= (int)$user['credits'] ?> crédits</p>
  </section>

  <section class="ride-detail">
    <h2>Mes réservations</h2>

    <?php if (empty($bookings)): ?>
      <p>Aucune réservation pour le moment.</p>
    <?php else: ?>
      <?php foreach ($bookings as $b): ?>
        <div class="review">
          <p><strong><?= htmlspecialchars($b['depart']) ?> → <?= htmlspecialchars($b['arrivee']) ?></strong></p>
          <p><strong>Date :</strong> <?= htmlspecialchars($b['date_ride']) ?> (<?= htmlspecialchars($b['heure_depart']) ?> → <?= htmlspecialchars($b['heure_arrivee']) ?>)</p>
          <p><strong>Prix :</strong> <?= htmlspecialchars($b['prix']) ?> €</p>
          <p><strong>Écologique :</strong> <?= ((int)$b['ecologique'] === 1) ? "🚗⚡ Oui" : "❌ Non" ?></p>

          <a class="btn-link" href="covoiturage_detail.php?id=<?= (int)$b['ride_id'] ?>">Voir le trajet</a>

          <?php if ($b['status'] === 'confirmed'): ?>
            <a class="btn-link"
               href="cancel_booking.php?id=<?= (int)$b['booking_id'] ?>"
               onclick="return confirm('Annuler cette réservation ? (+1 crédit)');">
              Annuler
            </a>
          <?php else: ?>
            <p style="color:#777; margin-top:0.6rem;">Statut : annulée</p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>

  <section class="ride-detail">
    <h2>Actions</h2>
    <a class="btn-link" href="covoiturages_db.php">Trouver un covoiturage</a>
  </section>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>