<?php
session_start();
require __DIR__ . '/includes/db_connect.php';
require __DIR__ . '/includes/auth.php';

$user = current_user($pdo);

$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($bookingId <= 0) {
    header("Location: profile.php");
    exit;
}

$userId = (int)$_SESSION['user']['id'];

try {
    $pdo->beginTransaction();

    // Verrouiller la réservation
    $stmt = $pdo->prepare("SELECT id, user_id, ride_id, status FROM bookings WHERE id = ? FOR UPDATE");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();

    if (!$booking) {
        throw new Exception("Réservation introuvable.");
    }
    if ((int)$booking['user_id'] !== $userId) {
        throw new Exception("Accès interdit.");
    }
    if ($booking['status'] !== 'confirmed') {
        throw new Exception("Réservation déjà annulée.");
    }

    // Annuler
    $pdo->prepare("UPDATE bookings SET status='cancelled', cancelled_at=NOW() WHERE id=?")->execute([$bookingId]);

    // Rendre 1 place
    $pdo->prepare("UPDATE rides SET places_restantes = places_restantes + 1 WHERE id=?")
        ->execute([(int)$booking['ride_id']]);

    // Rendre 1 crédit
    $pdo->prepare("UPDATE users SET credits = credits + 1 WHERE id=?")->execute([$userId]);

    $pdo->commit();

    // Mettre à jour la session (pour affichage immédiat)
    $_SESSION['user']['credits'] = (int)$_SESSION['user']['credits'] + 1;

    header("Location: profile.php?cancel=1");
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header("Location: profile.php?error=" . urlencode($e->getMessage()));
    exit;
}
