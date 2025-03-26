<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['IDadministrateur'])) {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amnila";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier si l'ID de l'annonce est bien fourni
if (isset($_GET['id'])) {
    $id_annonce = intval($_GET['id']);

    // Récupérer les informations actuelles de l'annonce
    $sql = "SELECT * FROM Appartement WHERE IDappartement = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_annonce);
    $stmt->execute();
    $result = $stmt->get_result();
    $annonce = $result->fetch_assoc();

    if (!$annonce) {
        die("Annonce non trouvée");
    }
}

// Mise à jour des informations de l'annonce
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = mysqli_real_escape_string($conn, $_POST['titre']);
    $localisation = mysqli_real_escape_string($conn, $_POST['localisation']);
    $prix = mysqli_real_escape_string($conn, $_POST['prix']);

    $sql_update = "UPDATE Appartement SET Type_d_appartementA = ?, RueA = ?, Prix_journalier = ? WHERE IDappartement = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssdi", $titre, $localisation, $prix, $id_annonce);

    if ($stmt_update->execute()) {
        header("Location: index.php?message=Annonce mise à jour avec succès");
        exit();
    } else {
        echo "Erreur lors de la mise à jour : " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte modifier annonce - Neige&Soleil</title>
    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/iconly/bold.css">
    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon">
</head>
<body>
    <div class="container">
        <br>
        <h2>Modifier l'Annonce</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" name="titre" class="form-control" value="<?php echo htmlspecialchars($annonce['Type_d_appartementA']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="localisation" class="form-label">Localisation</label>
                <input type="text" name="localisation" class="form-control" value="<?php echo htmlspecialchars($annonce['RueA']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="prix" class="form-label">Prix Journalier (€)</label>
                <input type="number" name="prix" class="form-control" value="<?php echo htmlspecialchars($annonce['Prix_journalier']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
</body>
</html>
