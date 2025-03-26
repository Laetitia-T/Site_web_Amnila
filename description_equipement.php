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

// Vérifier que l'ID de l'équipement est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID d'équipement invalide.");
}
$id_equipement = intval($_GET['id']);

// Récupérer les informations de l'équipement
$sql_equipement = "SELECT * FROM equipement WHERE IDequipement = ?";
$stmt_equipement = $conn->prepare($sql_equipement);
$stmt_equipement->bind_param("i", $id_equipement);
$stmt_equipement->execute();
$result_equipement = $stmt_equipement->get_result();
$equipement = $result_equipement->fetch_assoc();

// Vérifier si l'équipement existe
if (!$equipement) {
    die("Équipement introuvable.");
}

// Fermer la connexion
$stmt_equipement->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'équipement</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="style.css">
</head>
<style>
/* Styles généraux */
body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    color: #333;
    margin: 0;
    padding: 0;
}

/* Conteneur principal */
.container {
    max-width: 900px;
    margin: auto;
    padding: 20px;
}

/* Style de la carte */
.card {
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

/* Titre */
h1 {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
}

/* Détails de l'équipement */
ul {
    list-style: none;
    padding: 0;
}

ul li {
    font-size: 1.2rem;
    padding: 8px 0;
    border-bottom: 1px solid #ddd;
}

ul li::before {
    content: "✔";
    color: #28a745;
    font-weight: bold;
    margin-right: 10px;
}

/* Message équipement introuvable */
.no-equipment {
    font-size: 1.2rem;
    font-weight: bold;
    color: #dc3545;
    text-align: center;
    padding: 10px;
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        padding: 10px;
    }
}
</style>
    <body>
  <!-- Vous pouvez inclure votre header ici -->
  <?php include 'header.php'; ?>
  <!-- Header Start -->
  <div class="container-fluid bg-breadcrumb">
        <div class="container text-center py-5" style="max-width: 900px;">
            <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Equipement</h4>
            <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
                <li class="breadcrumb-item active text-primary">Equipement</li>
            </ol>
        </div>
    </div>
    <!-- Header End -->
  <div class="container my-5">
    <div class="card p-4 shadow-sm">
      <div class="row align-items-center">
        <div class="col-md-6">
    <div class="container my-5">
        <div class="card p-4 shadow-sm">
            <h1>Détails de l'équipement</h1>
            <ul>
                <li><strong>Type d'équipement :</strong> <?= htmlspecialchars($equipement['Type_d_equipement']) ?></li>
                <li><strong>Détails de l'équipement :</strong> <?= htmlspecialchars($equipement['Detail_equipement']) ?></li>
            </ul>
        </div>
        
    </div>
    <!-- <a href="reserver.php?id=<?php echo $id_appartement; ?>" class="btn btn-primary">Réserver</a> -->
</div>

</div>
<a href="reservation_equipement.php?id=<?php echo $id_equipement; ?>" class="btn btn-primary">Réserver</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

    <?php include 'footer.php'; ?>
    <script src="assets/js/bootstrap.bundle.js"></script>
</body>
</html>
