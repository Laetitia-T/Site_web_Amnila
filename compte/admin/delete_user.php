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
    $id_user = intval($_GET['id']);

    // Début de la transaction pour assurer l'intégrité des données
    $conn->begin_transaction();

    try {
        // Supprimer les réservations liées à l'appartement (si locataire)
        $sql_reservations = "DELETE FROM reservation WHERE IDlocataire IN (SELECT IDlocataire FROM Locataire WHERE IDlocataire = ?)";
        $stmt_reservations = $conn->prepare($sql_reservations);
        $stmt_reservations->bind_param("i", $id_user);
        $stmt_reservations->execute();

        // Supprimer les contrats associés aux réservations
        $sql_contrats = "DELETE FROM contrat WHERE IDreservation IN (SELECT IDreservation FROM reservation WHERE IDlocataire = ?)";
        $stmt_contrats = $conn->prepare($sql_contrats);
        $stmt_contrats->bind_param("i", $id_user);
        $stmt_contrats->execute();

        // Supprimer les photos et équipements liés à l'appartement (si propriétaire)
        $sql_photos = "DELETE FROM photo WHERE IDappartement IN (SELECT IDappartement FROM appartement WHERE IDproprietaire IN (SELECT IDproprietaire FROM proprietaire WHERE IDproprietaire = ?))";
        $stmt_photos = $conn->prepare($sql_photos);
        $stmt_photos->bind_param("i", $id_user);
        $stmt_photos->execute();

        $sql_equipements = "DELETE FROM equipement WHERE IDappartement IN (SELECT IDappartement FROM appartement WHERE IDproprietaire IN (SELECT IDproprietaire FROM proprietaire WHERE IDproprietaire = ?))";
        $stmt_equipements = $conn->prepare($sql_equipements);
        $stmt_equipements->bind_param("i", $id_user);
        $stmt_equipements->execute();

        // Supprimer les annonces d'appartement liées à l'utilisateur (propriétaire)
        $sql_annonces = "DELETE FROM appartement WHERE IDproprietaire IN (SELECT IDproprietaire FROM proprietaire WHERE IDproprietaire = ?)";
        $stmt_annonces = $conn->prepare($sql_annonces);
        $stmt_annonces->bind_param("i", $id_user);
        $stmt_annonces->execute();

        // Supprimer l'utilisateur des tables Proprietaire et Locataire
        $sql_proprietaire = "DELETE FROM Proprietaire WHERE IDproprietaire = ?";
        $stmt_proprietaire = $conn->prepare($sql_proprietaire);
        $stmt_proprietaire->bind_param("i", $id_user);
        $stmt_proprietaire->execute();

        $sql_locataire = "DELETE FROM Locataire WHERE IDlocataire = ?";
        $stmt_locataire = $conn->prepare($sql_locataire);
        $stmt_locataire->bind_param("i", $id_user);
        $stmt_locataire->execute();

        // Supprimer l'utilisateur de la table Users
        $sql_user = "DELETE FROM Users WHERE id = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("i", $id_user);
        $stmt_user->execute();

        // Valider la transaction
        $conn->commit();

        // Rediriger avec un message de succès
        header("Location: profil.php?success=Utilisateur supprimé");
        exit();
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $conn->rollback();
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
}

$conn->close();
?>
