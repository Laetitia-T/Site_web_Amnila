<?php
// Paramètres de connexion à la base de données
$servername = "localhost";
$dbname = "amnila";
$dbusername = "root";
$dbpassword = "";

// Connexion à la base
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier que l'ID de l'appartement est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID d'appartement invalide.");
}
$id_appartement = intval($_GET['id']);

// Récupérer les informations de l'appartement
$sql = "SELECT * FROM appartement WHERE IDappartement = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_appartement);
$stmt->execute();
$result = $stmt->get_result();
$appartement = $result->fetch_assoc();

if (!$appartement) {
    die("Appartement introuvable.");
}

// Récupérer les photos associées à cet appartement
$sql_photos = "SELECT * FROM photo WHERE IDappartement = ?";
$stmt_photos = $conn->prepare($sql_photos);
$stmt_photos->bind_param("i", $id_appartement);
$stmt_photos->execute();
$result_photos = $stmt_photos->get_result();

// Vous pouvez également récupérer d'autres informations (par exemple, le nombre de réservations)
// Ici, nous nous contenterons des informations de l'appartement et de ses photos.

$stmt->close();
$stmt_photos->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($appartement['Type_d_appartementA']); ?></title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Vous pouvez inclure votre header ici -->
  <?php include 'header.php'; ?>

  <!-- Header Start -->
  <div class="container-fluid bg-breadcrumb">
    <div class="container text-center py-5" style="max-width: 900px;">
      <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Appartement</h4>
      <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
        <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
        <li class="breadcrumb-item active text-primary">Appartement</li>
      </ol>
    </div>
  </div>
  <!-- Header End -->

  <div class="container my-5">
    <div class="card p-4 shadow-sm">
      <div class="row align-items-center">
        <div class="col-md-6">
          <!-- Carousel des photos -->
          <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <?php
              $active = 'active';
              while ($photo = $result_photos->fetch_assoc()):
              ?>
                <div class="carousel-item <?php echo $active; ?>">
                  <img src="<?php echo htmlspecialchars($photo['Chemin']); ?>" class="d-block w-100" alt="Photo Appartement">
                </div>
              <?php
                // Enlever 'active' pour les prochaines images
                $active = '';
              endwhile;
              ?>
            </div>
            <!-- Contrôles du carrousel -->
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
          </div>
        </div>
        <div class="col-md-6">
          <h1><?php echo htmlspecialchars($appartement['Type_d_appartementA']); ?></h1>
          <p class="h3 py-2">Prix : <?php echo number_format($appartement['Prix_journalier'], 2); ?> €/jour</p>
          <p><strong>Localisation :</strong> <?php echo htmlspecialchars($appartement['RueA']); ?></p>
          <p><strong>Ville :</strong> <?php echo htmlspecialchars($appartement['VilleA']); ?></p>
          <p><strong>Surface :</strong> <?php echo htmlspecialchars($appartement['SurfaceA']); ?> m²</p>
          <a href="reserver.php?id=<?php echo $id_appartement; ?>" class="btn btn-primary">Réserver</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <?php include 'footer.php'; ?>

  <!-- Scripts nécessaires pour Bootstrap 5 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
