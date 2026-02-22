<?php
function require_login(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user']['id'])) {
        header("Location: login.php");
        exit;
    }
}

function current_user(PDO $pdo): array {
    require_login();
    $stmt = $pdo->prepare("SELECT id, pseudo, email, credits FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user']['id']]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit;
    }

    $_SESSION['user'] = $user; // synchro crédits/pseudo/email
    return $user;
}