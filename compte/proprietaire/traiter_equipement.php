<?php
session_start();

// Paramètres de connexion à la base de données
$servername = "localhost";
$dbname = "amnila";
$dbusername = "root";
$dbpassword = "";

// Connexion à la base de données
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier que le propriétaire est connecté
if (!isset($_SESSION['IDproprietaire'])) {
    die("Erreur : Vous devez être connecté en tant que propriétaire.");
}
$id_proprietaire = $_SESSION['IDproprietaire'];

// Traiter la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et sécurisation des champs du formulaire
    $type_equipement   = htmlspecialchars(trim($_POST['type_equipement'] ?? ''));
    $detail_equipement = htmlspecialchars(trim($_POST['detail_equipement'] ?? ''));

    if (empty($type_equipement) || empty($detail_equipement)) {
        die("Erreur : Tous les champs sont obligatoires.");
    }

    // Vérifier si l'équipement existe déjà pour ce propriétaire
    $stmt = $conn->prepare("SELECT IDEquipement FROM equipement WHERE Type_d_equipement = ? AND IDproprietaire = ?");
    $stmt->bind_param("si", $type_equipement, $id_proprietaire);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Si l'équipement n'existe pas, l'insérer
        $stmt = $conn->prepare("INSERT INTO equipement (Type_d_equipement, Detail_equipement, IDproprietaire) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $type_equipement, $detail_equipement, $id_proprietaire);
        
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: equipement.php"); // Redirection après l'ajout
            exit();
        } else {
            die("Erreur lors de l'ajout de l'équipement.");
        }
    } else {
        echo "Cet équipement existe déjà pour ce propriétaire.";
    }

    $stmt->close();
}

$conn->close();
?>
