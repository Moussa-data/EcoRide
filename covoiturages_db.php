<?php
session_start();
require __DIR__ . '/includes/db_connect.php';

// ... session_start / require db_connect etc. avant


$where  = [];
$params = [];

// Récupération filtres
$depart  = isset($_GET['depart']) ? trim($_GET['depart']) : '';
$arrivee = isset($_GET['arrivee']) ? trim($_GET['arrivee']) : '';
$date    = isset($_GET['date_ride']) ? trim($_GET['date_ride']) : '';
$eco     = isset($_GET['ecologique']) ? trim($_GET['ecologique']) : ''; // "1" ou "" selon ton form
$prixMax = isset($_GET['prix_max']) ? trim($_GET['prix_max']) : '';     // optionnel

// ⚠️ IMPORTANT : recherche insensible à la casse + tolère espaces
if ($depart !== '') {
  $where[] = "LOWER(r.depart) LIKE ?";
  $params[] = '%' . mb_strtolower($depart, 'UTF-8') . '%';
}

if ($arrivee !== '') {
  $where[] = "LOWER(r.arrivee) LIKE ?";
  $params[] = '%' . mb_strtolower($arrivee, 'UTF-8') . '%';
}

// Date uniquement si fournie (format YYYY-MM-DD)
if ($date !== '') {
  $where[] = "r.date_ride = ?";
  $params[] = $date;
}

// Écologique : si le filtre est activé
if ($eco === '1') {
  $where[] = "r.ecologique = 1";
}

// Prix max : si fourni et numérique
if ($prixMax !== '' && is_numeric($prixMax)) {
  $where[] = "r.prix <= ?";
  $params[] = (float)$prixMax;
}

// Tri sécurisé (whitelist)
$allowedSort = [
  'date' => 'r.date_ride ASC, r.heure_depart ASC',
  'prix_asc' => 'r.prix ASC',
  'prix_desc' => 'r.prix DESC',
  'eco' => 'r.ecologique DESC, r.date_ride ASC'
];

$sortKey = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$orderBy = $allowedSort[$sortKey] ?? $allowedSort['date'];

// === Ton SQL (tu peux garder ton bloc) ===
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
$rides = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide - Covoiturages</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="hero">
  <h1>Covoiturages</h1>

  <!-- FORMULAIRE FILTRES (GET) -->
  <form class="search-form" method="GET" action="covoiturages_db.php">
    <input type="text" name="depart" placeholder="Départ" value="<?= htmlspecialchars($depart) ?>">
    <input type="text" name="arrivee" placeholder="Arrivée" value="<?= htmlspecialchars($arrivee) ?>">
    <input type="date" name="date_ride" value="<?= htmlspecialchars($date_ride) ?>">
    <input type="number" step="0.01" name="max_prix" placeholder="Prix max (€)" value="<?= htmlspecialchars($max_prix) ?>">

    <label style="display:flex; align-items:center; gap:0.4rem;">
      <input type="checkbox" name="eco" value="1" <?= $eco === 1 ? 'checked' : '' ?>>
      Écologique
    </label>

    <select name="sort">
      <option value="date" <?= $sort === 'date' ? 'selected' : '' ?>>Tri : Date</option>
      <option value="prix" <?= $sort === 'prix' ? 'selected' : '' ?>>Tri : Prix</option>
      <option value="places" <?= $sort === 'places' ? 'selected' : '' ?>>Tri : Places</option>
    </select>

    <button type="submit">Filtrer</button>
    <a class="btn-link" href="covoiturages_db.php" style="background:#777;">Réinitialiser</a>
  </form>

  <section class="rides-list">
    <?php if (empty($rides)): ?>
      <p style="margin-top:1rem;">Aucun covoiturage trouvé.</p>
    <?php else: ?>
      <?php foreach ($rides as $r): ?>
        <article class="ride-card">
          <h2><?= htmlspecialchars($r['depart']) ?> → <?= htmlspecialchars($r['arrivee']) ?></h2>
          <p><strong>Chauffeur :</strong> <?= htmlspecialchars($r['driver_pseudo'] ?? '—') ?></p>
          <p><strong>Date :</strong> <?= htmlspecialchars($r['date_ride']) ?></p>
          <p><strong>Heure :</strong> <?= htmlspecialchars($r['heure_depart']) ?> → <?= htmlspecialchars($r['heure_arrivee']) ?></p>
          <p><strong>Prix :</strong> <?= htmlspecialchars($r['prix']) ?> €</p>
          <p><strong>Places :</strong> <?= (int)$r['places_restantes'] ?></p>
          <p><strong>Écologique :</strong> <?= ((int)$r['ecologique']===1) ? "🚗⚡ Oui" : "❌ Non" ?></p>

          <a class="btn-link" href="/EcoRide/covoiturage_detail_db.php?id=<?= (int)$r['id'] ?>">Voir le détail</a>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
