<?php
session_start();
require __DIR__ . '/includes/db_connect.php';

$where = [];
$params = [];

$depart = isset($_GET['depart']) ? trim($_GET['depart']) : '';
$arrivee = isset($_GET['arrivee']) ? trim($_GET['arrivee']) : '';
$date = isset($_GET['date_ride']) ? trim($_GET['date_ride']) : '';
$eco = isset($_GET['ecologique']) ? trim($_GET['ecologique']) : '';
$prixMax = isset($_GET['prix_max']) ? trim($_GET['prix_max']) : '';

if ($depart !== '') {
    $where[] = "LOWER(r.depart) LIKE ?";
    $params[] = '%' . mb_strtolower($depart, 'UTF-8') . '%';
}

if ($arrivee !== '') {
    $where[] = "LOWER(r.arrivee) LIKE ?";
    $params[] = '%' . mb_strtolower($arrivee, 'UTF-8') . '%';
}

if ($date !== '') {
    $where[] = "r.date_ride = ?";
    $params[] = $date;
}

if ($eco === '1') {
    $where[] = "r.ecologique = 1";
}

if ($prixMax !== '' && is_numeric($prixMax)) {
    $where[] = "r.prix <= ?";
    $params[] = (float)$prixMax;
}

$allowedSort = [
    'date' => 'r.date_ride ASC, r.heure_depart ASC',
    'prix_asc' => 'r.prix ASC',
    'prix_desc' => 'r.prix DESC'
];

$sortKey = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$orderBy = $allowedSort[$sortKey] ?? $allowedSort['date'];

$sql = "
    SELECT r.*, u.pseudo AS driver_pseudo
    FROM rides r
    LEFT JOIN users u ON u.id = r.driver_id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY $orderBy";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rides = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Covoiturages</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="hero">
    <h1>Liste des covoiturages</h1>

    <form method="GET" action="covoiturages_db.php" class="search-form">
        <input type="text" name="depart" placeholder="Ville de départ" value="<?= htmlspecialchars($depart) ?>">
        <input type="text" name="arrivee" placeholder="Ville d'arrivée" value="<?= htmlspecialchars($arrivee) ?>">
        <input type="date" name="date_ride" value="<?= htmlspecialchars($date) ?>">

        <select name="sort">
            <option value="date" <?= $sortKey === 'date' ? 'selected' : '' ?>>Trier par date</option>
            <option value="prix_asc" <?= $sortKey === 'prix_asc' ? 'selected' : '' ?>>Prix croissant</option>
            <option value="prix_desc" <?= $sortKey === 'prix_desc' ? 'selected' : '' ?>>Prix décroissant</option>
        </select>

        <button type="submit">Rechercher</button>
    </form>

    <section class="rides-list">
        <?php if (empty($rides)): ?>
            <p style="margin-top:1rem;">Aucun covoiturage trouvé.</p>
        <?php else: ?>
            <?php foreach ($rides as $ride): ?>
                <article class="ride-card" style="margin:1rem 0; padding:1rem; border:1px solid #ddd; border-radius:8px;">
                    <h2><?= htmlspecialchars($ride['depart']) ?> → <?= htmlspecialchars($ride['arrivee']) ?></h2>
                    <p><strong>Chauffeur :</strong> <?= htmlspecialchars($ride['driver_pseudo'] ?? '—') ?></p>
                    <p><strong>Date :</strong> <?= htmlspecialchars($ride['date_ride']) ?></p>
                    <p><strong>Heure :</strong> <?= htmlspecialchars($ride['heure_depart']) ?> → <?= htmlspecialchars($ride['heure_arrivee']) ?></p>
                    <p><strong>Prix :</strong> <?= htmlspecialchars($ride['prix']) ?> €</p>
                    <p><strong>Places restantes :</strong> <?= (int)$ride['places_restantes'] ?></p>
                    <p><strong>Écologique :</strong> <?= ((int)$ride['ecologique'] === 1) ? '🚗⚡ Oui' : '❌ Non' ?></p>

                    <a class="btn-link" href="covoiturage_detail.php?id=<?= (int)$ride['id'] ?>">Voir le détail</a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>