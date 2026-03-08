<?php
session_start();
require __DIR__ . '/includes/db_connect.php';
require __DIR__ . '/includes/auth.php';

require_login();

$userId = (int)$_SESSION['user']['id'];

// -----------------------------
// 1) GET = page de confirmation
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $rideId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($rideId <= 0) {
        header("Location: covoiturages_db.php");
        exit;
    }

    // Charger le trajet
    $stmt = $pdo->prepare("
        SELECT r.*, u.pseudo AS driver_pseudo
        FROM rides r
        LEFT JOIN users u ON u.id = r.driver_id
        WHERE r.id = ?
    ");
    $stmt->execute([$rideId]);
    $ride = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ride) {
        header("Location: covoiturages_db.php");
        exit;
    }

    // Charger les crédits utilisateur
    $stmtU = $pdo->prepare("SELECT credits FROM users WHERE id = ?");
    $stmtU->execute([$userId]);
    $user = $stmtU->fetch(PDO::FETCH_ASSOC);
    $credits = (int)($user['credits'] ?? 0);

    // Token anti double-clic / validation
    $_SESSION['participate_token'] = bin2hex(random_bytes(16));
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EcoRide - Confirmation</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>

    <?php include 'includes/header.php'; ?>

    <main class="hero">
        <h1>Confirmer la participation</h1>

        <section class="ride-detail">
            <p><strong>Trajet :</strong> <?= htmlspecialchars($ride['depart']) ?> → <?= htmlspecialchars($ride['arrivee']) ?></p>
            <p><strong>Date :</strong> <?= htmlspecialchars($ride['date_ride']) ?></p>
            <p><strong>Heure :</strong> <?= htmlspecialchars($ride['heure_depart']) ?> → <?= htmlspecialchars($ride['heure_arrivee']) ?></p>
            <p><strong>Chauffeur :</strong> <?= htmlspecialchars($ride['driver_pseudo'] ?? '—') ?></p>
            <p><strong>Prix :</strong> <?= htmlspecialchars($ride['prix']) ?> €</p>
            <p><strong>Places restantes :</strong> <?= (int)$ride['places_restantes'] ?></p>
            <p><strong>Écologique :</strong> <?= ((int)$ride['ecologique'] === 1) ? "🚗⚡ Oui" : "❌ Non" ?></p>
        </section>

        <section class="ride-detail">
            <p><strong>Coût :</strong> 1 crédit</p>
            <p><strong>Vos crédits :</strong> <?= $credits ?> crédit(s)</p>

            <?php if ($credits <= 0): ?>
                <p style="color:red;">Crédits insuffisants.</p>
                <a class="btn-link" href="covoiturage_detail.php?id=<?= (int)$ride['id'] ?>">Retour</a>

            <?php elseif ((int)$ride['driver_id'] === $userId): ?>
                <p style="color:red;">Vous ne pouvez pas réserver votre propre trajet.</p>
                <a class="btn-link" href="covoiturage_detail.php?id=<?= (int)$ride['id'] ?>">Retour</a>

            <?php elseif ((int)$ride['places_restantes'] <= 0): ?>
                <p style="color:red;">Plus de places disponibles.</p>
                <a class="btn-link" href="covoiturage_detail.php?id=<?= (int)$ride['id'] ?>">Retour</a>

            <?php else: ?>
                <form method="POST" action="participate.php">
                    <input type="hidden" name="id" value="<?= (int)$ride['id'] ?>">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['participate_token']) ?>">
                    <button type="submit">Confirmer (déduire 1 crédit)</button>
                </form>

                <a class="btn-link" href="covoiturage_detail.php?id=<?= (int)$ride['id'] ?>">Annuler</a>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    </body>
    </html>
    <?php
    exit;
}

// -----------------------------
// 2) POST = exécution réservation
// -----------------------------
$rideId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$token  = $_POST['token'] ?? '';

if ($rideId <= 0) {
    header("Location: covoiturages_db.php");
    exit;
}

if (empty($_SESSION['participate_token']) || !hash_equals($_SESSION['participate_token'], $token)) {
    header("Location: covoiturage_detail.php?id=" . $rideId . "&error=" . urlencode("Confirmation invalide. Réessaie."));
    exit;
}
unset($_SESSION['participate_token']);

try {
    $pdo->beginTransaction();

    // Verrou utilisateur + crédits
    $stmt = $pdo->prepare("SELECT id, credits FROM users WHERE id = ? FOR UPDATE");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Utilisateur introuvable.");
    }

    if ((int)$user['credits'] <= 0) {
        throw new Exception("Crédits insuffisants.");
    }

    // Verrou trajet + places
    $stmt = $pdo->prepare("SELECT id, places_restantes, driver_id FROM rides WHERE id = ? FOR UPDATE");
    $stmt->execute([$rideId]);
    $ride = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ride) {
        throw new Exception("Covoiturage introuvable.");
    }

    if ((int)$ride['driver_id'] === $userId) {
        throw new Exception("Tu ne peux pas réserver ton propre trajet.");
    }

    if ((int)$ride['places_restantes'] <= 0) {
        throw new Exception("Plus de places disponibles.");
    }

    // Vérifier si une réservation existe déjà
    $stmt = $pdo->prepare("
        SELECT id, status
        FROM bookings
        WHERE user_id = ? AND ride_id = ?
        FOR UPDATE
    ");
    $stmt->execute([$userId, $rideId]);
    $existingBooking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingBooking) {
        if ($existingBooking['status'] === 'confirmed') {
            throw new Exception("Tu participes déjà à ce trajet.");
        }

        if ($existingBooking['status'] === 'cancelled') {
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
            $stmt->execute([$existingBooking['id']]);
        }
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO bookings (user_id, ride_id, status)
            VALUES (?, ?, 'confirmed')
        ");
        $stmt->execute([$userId, $rideId]);
    }

    // Décrémenter crédit + place
    $pdo->prepare("UPDATE users SET credits = credits - 1 WHERE id = ?")->execute([$userId]);
    $pdo->prepare("UPDATE rides SET places_restantes = places_restantes - 1 WHERE id = ?")->execute([$rideId]);

    $pdo->commit();

    // Mise à jour session
    $_SESSION['user']['credits'] = (int)$user['credits'] - 1;

    header("Location: profile.php?success=1");
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header("Location: covoiturage_detail.php?id=" . $rideId . "&error=" . urlencode($e->getMessage()));
    exit;
}