<?php
// On simule des covoiturages en dur (plus tard ça viendra de la BDD)
$rides = [
    1 => [
        'titre'        => 'Paris → Lyon',
        'chauffeur'    => 'EcoDriver75',
        'note'         => 4.8,
        'photo'        => null, // futur : URL d'image
        'places'       => 2,
        'prix'         => 25,
        'date'         => '12/12/2025',
        'heure_depart' => '08:00',
        'heure_arrivee'=> '12:00',
        'ecologique'   => true,
        'vehicule'     => [
            'marque'   => 'Tesla',
            'modele'   => 'Model 3',
            'energie'  => 'Électrique',
            'couleur'  => 'Blanc'
        ],
        'preferences'  => [
            'Fumeur'      => 'Non',
            'Animaux'     => 'Oui',
            'Musique'     => 'Oui',
            'Discussion'  => 'Plutôt bavard'
        ],
        'avis' => [
            [
                'auteur' => 'Laura',
                'note'   => 5,
                'texte'  => 'Trajet très agréable, conducteur ponctuel.'
            ],
            [
                'auteur' => 'Samir',
                'note'   => 4.5,
                'texte'  => 'Voiture propre et confortable, je recommande.'
            ],
        ]
    ],
    2 => [
        'titre'        => 'Marseille → Nice',
        'chauffeur'    => 'SunRider13',
        'note'         => 4.5,
        'photo'        => null,
        'places'       => 1,
        'prix'         => 18,
        'date'         => '15/12/2025',
        'heure_depart' => '09:30',
        'heure_arrivee'=> '11:30',
        'ecologique'   => false,
        'vehicule'     => [
            'marque'   => 'Peugeot',
            'modele'   => '308',
            'energie'  => 'Essence',
            'couleur'  => 'Bleu'
        ],
        'preferences'  => [
            'Fumeur'      => 'Non',
            'Animaux'     => 'Non',
            'Musique'     => 'Oui',
            'Discussion'  => 'Calme'
        ],
        'avis' => [
            [
                'auteur' => 'Nina',
                'note'   => 4,
                'texte'  => 'Chauffeur sérieux, un peu de retard au départ.'
            ]
        ]
    ],
    3 => [
        'titre'        => 'Toulouse → Bordeaux',
        'chauffeur'    => 'GreenRide31',
        'note'         => 5.0,
        'photo'        => null,
        'places'       => 3,
        'prix'         => 22,
        'date'         => '20/12/2025',
        'heure_depart' => '07:00',
        'heure_arrivee'=> '10:00',
        'ecologique'   => true,
        'vehicule'     => [
            'marque'   => 'Renault',
            'modele'   => 'Zoé',
            'energie'  => 'Électrique',
            'couleur'  => 'Vert'
        ],
        'preferences'  => [
            'Fumeur'      => 'Non',
            'Animaux'     => 'Oui',
            'Musique'     => 'Oui',
            'Discussion'  => 'Au choix'
        ],
        'avis' => []
    ],
];

// Récupérer l'id passé dans l'URL : covoiturage_detail.php?id=1
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$ride = $rides[$id] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>EcoRide - Détail covoiturage</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php include 'includes/header.php'; ?>

<main class="hero">
    <?php if (!$ride): ?>
        <h1>Covoiturage introuvable</h1>
        <p>Le trajet demandé n’existe pas ou plus.</p>
        <a href="/EcoRide/covoiturages.php" class="btn-link">Retour aux covoiturages</a>
    <?php else: ?>
        <h1><?= htmlspecialchars($ride['titre']); ?></h1>
        <p><strong>Chauffeur :</strong> <?= htmlspecialchars($ride['chauffeur']); ?> ⭐ <?= $ride['note']; ?></p>
        <p><strong>Date :</strong> <?= $ride['date']; ?> – <?= $ride['heure_depart']; ?> → <?= $ride['heure_arrivee']; ?></p>
        <p><strong>Places restantes :</strong> <?= $ride['places']; ?></p>
        <p><strong>Prix :</strong> <?= $ride['prix']; ?> €</p>
        <p><strong>Aspect écologique :</strong>
            <?php if ($ride['ecologique']): ?>
                🚗⚡ Trajet écologique (voiture électrique)
            <?php else: ?>
                ❌ Trajet non écologique (véhicule thermique)
            <?php endif; ?>
        </p>

        <section class="ride-detail">
            <h2>Véhicule</h2>
            <p><strong>Marque :</strong> <?= $ride['vehicule']['marque']; ?></p>
            <p><strong>Modèle :</strong> <?= $ride['vehicule']['modele']; ?></p>
            <p><strong>Énergie :</strong> <?= $ride['vehicule']['energie']; ?></p>
            <p><strong>Couleur :</strong> <?= $ride['vehicule']['couleur']; ?></p>
        </section>

        <section class="ride-detail">
            <h2>Préférences du conducteur</h2>
            <?php foreach ($ride['preferences'] as $preference => $valeur): ?>
                <p><strong><?= $preference; ?> :</strong> <?= $valeur; ?></p>
            <?php endforeach; ?>
        </section>

        <section class="ride-detail">
            <h2>Avis des passagers</h2>
            <?php if (empty($ride['avis'])): ?>
                <p>Aucun avis pour le moment.</p>
            <?php else: ?>
                <?php foreach ($ride['avis'] as $avis): ?>
                    <div class="review">
                        <p><strong><?= $avis['auteur']; ?></strong> – ⭐ <?= $avis['note']; ?></p>
                        <p><?= $avis['texte']; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <a href="/EcoRide/covoiturages.php" class="btn-link">← Retour aux covoiturages</a>
        <button class="btn-primary" type="button">Participer (maquette)</button>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
