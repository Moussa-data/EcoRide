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
      <h1>Recherche de covoiturages</h1>
      <p>Simule des trajets pour préparer la future version dynamique.</p>

      <!-- Formulaire de recherche simple -->
      <form class="search-form">
          <input type="text" placeholder="Ville de départ">
          <input type="text" placeholder="Ville d'arrivée">
          <input type="date">
          <button type="button">Rechercher</button>
      </form>

      <!-- Section des covoiturages (maquette avec données en dur) -->
      <section class="rides-list">
            <article class="ride-card">
                <h2>Paris → Lyon</h2>
                <p><strong>Chauffeur :</strong> EcoDriver75 ⭐ 4.8</p>
                <p><strong>Date :</strong> 12/12/2025 - 08:00 → 12:00</p>
                <p><strong>Places restantes :</strong> 2</p>
                <p><strong>Prix :</strong> 25 €</p>
                <p><strong>Écologique :</strong> 🚗⚡ Voiture électrique</p>
                <a class="btn-link" href="covoiturage_detail.php?id=1">Voir le détail</a>
            </article>
           

           <article class="ride-card">
                <h2>Marseille → Nice</h2>
                    <p><strong>Chauffeur :</strong> EcoDriver75 ⭐ 4.8</p>
                    <p><strong>Date :</strong> 12/12/2025 - 08:00 → 12:00</p>
                    <p><strong>Places restantes :</strong> 2</p>
                    <p><strong>Prix :</strong> 25 €</p>
                    <p><strong>Écologique :</strong> 🚗⚡ Voiture électrique</p>
                <a class="btn-link" href="covoiturage_detail.php?id=2">Voir le détail</a>
            </article>


          <article class="ride-card">
                <h2>Toulouse → Bordeaux</h2>
                  <p><strong>Chauffeur :</strong> EcoDriver75 ⭐ 4.8</p>
                    <p><strong>Date :</strong> 12/12/2025 - 08:00 → 12:00</p>
                    <p><strong>Places restantes :</strong> 2</p>
                    <p><strong>Prix :</strong> 25 €</p>
                    <p><strong>Écologique :</strong> 🚗⚡ Voiture électrique</p>
                <a class="btn-link" href="covoiturage_detail.php?id=3">Voir le détail</a>
            </article>
          </article>
      </section>
  </main>

  <?php include 'includes/footer.php'; ?>

</body>
</html>
