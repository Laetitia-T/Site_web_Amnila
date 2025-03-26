<?php
session_start();

// Connexion à la base de données
$servername = "localhost";
$dbname = "amnila";
$dbusername = "root";
$dbpassword = "";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['IDproprietaire']) || empty($_SESSION['IDproprietaire'])) {
    die("Erreur : ID du propriétaire non défini.");
}

$IDproprietaire = intval($_SESSION['IDproprietaire']);

// Modifier les photos d'un appartement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modifier_photos'])) {
    $IDappartement = intval($_POST['IDappartement']);
    
    // Supprimer les anciennes photos
    $sql_delete_photos = "DELETE FROM photo WHERE IDappartement = ?";
    $stmt = $conn->prepare($sql_delete_photos);
    $stmt->bind_param("i", $IDappartement);
    $stmt->execute();
    $stmt->close();

    // Ajouter les nouvelles photos
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
            $filename = $_FILES['photos']['name'][$key];
            $destination = "uploads/" . $filename;
            if (move_uploaded_file($tmp_name, $destination)) {
                $sql_photo = "INSERT INTO photo (Chemin, IDappartement) VALUES (?, ?)";
                $stmt_photo = $conn->prepare($sql_photo);
                $stmt_photo->bind_param("si", $destination, $IDappartement);
                $stmt_photo->execute();
                $stmt_photo->close();
            }
        }
    }
    echo "Photos mises à jour avec succès.";
}

// Récupérer les appartements du propriétaire
$sql = "SELECT * FROM appartement WHERE IDproprietaire = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $IDproprietaire);
$stmt->execute();
$result = $stmt->get_result();
$appartements = [];
while ($row = $result->fetch_assoc()) {
    $appartements[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modification des Photos</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
</head>
<body>
    <div class="container">
        <h2>Modifier les Photos</h2>
        <?php foreach ($appartements as $appartement) { ?>
            <h4><?= $appartement['Type_d_appartementA'] ?></h4>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="IDappartement" value="<?= $appartement['IDappartement'] ?>">
                <input type="file" name="photos[]" multiple required>
                <button type="submit" name="modifier_photos" class="btn btn-primary">Mettre à jour</button>
            </form>
        <?php } ?>
    </div>
</body>
</html>
