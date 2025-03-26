<?php
session_start();
if (!isset($_SESSION['IDadministrateur'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amnila";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id_appartement = intval($_GET['id']);

    // Début de la transaction
    $conn->begin_transaction();

    try {
        // Supprimer les photos liées à l'appartement
        $sql_photos = "DELETE FROM photo WHERE IDappartement = ?";
        $stmt_photos = $conn->prepare($sql_photos);
        $stmt_photos->bind_param("i", $id_appartement);
        $stmt_photos->execute();

        // Supprimer les équipements liés à l'appartement
        $sql_equipements = "DELETE FROM equipement WHERE IDappartement = ?";
        $stmt_equipements = $conn->prepare($sql_equipements);
        $stmt_equipements->bind_param("i", $id_appartement);
        $stmt_equipements->execute();

        // Supprimer les contrats liés aux réservations de cet appartement
        $sql_contrats = "DELETE FROM contrat WHERE IDreservation IN 
                        (SELECT IDreservation FROM reservation WHERE IDappartement = ?)";
        $stmt_contrats = $conn->prepare($sql_contrats);
        $stmt_contrats->bind_param("i", $id_appartement);
        $stmt_contrats->execute();

        // Supprimer les réservations liées à l'appartement
        $sql_reservations = "DELETE FROM reservation WHERE IDappartement = ?";
        $stmt_reservations = $conn->prepare($sql_reservations);
        $stmt_reservations->bind_param("i", $id_appartement);
        $stmt_reservations->execute();

        // Supprimer l'annonce de l'appartement
        $sql_annonce = "DELETE FROM appartement WHERE IDappartement = ?";
        $stmt_annonce = $conn->prepare($sql_annonce);
        $stmt_annonce->bind_param("i", $id_appartement);
        $stmt_annonce->execute();

        // Valider la transaction
        $conn->commit();

        // Rediriger avec un message de succès
        header("Location: profil.php?success=Annonce supprimée");
        exit();
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $conn->rollback();
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
}

$conn->close();
?>
