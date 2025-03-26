<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['IDadministrateur'])) {
    header("Location: gere-annonce.php");
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