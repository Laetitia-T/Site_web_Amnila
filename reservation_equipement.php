<?php
// Connexion à la base de données
$host = 'localhost';
$user = 'root';  // Remplace par ton nom d'utilisateur
$password = '';  // Remplace par ton mot de passe
$dbname = 'amnila'; // Remplace par ton nom de base de données
$conn = new mysqli($host, $user, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'locataire') {
    die("Vous devez être connecté en tant que locataire.");
}
$id_locataire = $_SESSION['user_id'];

$erreur = '';
$message = '';
$reservation_id = '';
$equipements = null;

// Récupérer l'ID de la réservation active associée à ce locataire
$sql_get_reservation = "SELECT IDreservation FROM reservation 
                        WHERE IDlocataire = $id_locataire 
                        AND (StatutR = 'en cours' OR StatutR = 'En attente') 
                        LIMIT 1";
$result_reservation = $conn->query($sql_get_reservation);

if ($result_reservation->num_rows > 0) {
    $row_reservation = $result_reservation->fetch_assoc();
    $reservation_id = $row_reservation['IDreservation'];

    // Récupérer les équipements DISPONIBLES pour réservation
    $sql_get_equipements = "SELECT * FROM equipement 
                            WHERE IDEquipement NOT IN 
                            (SELECT IDEquipement FROM reserver WHERE IDreservation = $reservation_id)";
    $equipements = $conn->query($sql_get_equipements);

    // Récupérer les équipements DÉJÀ réservés
    $sql_get_equipements_reserves = "SELECT e.IDequipement, e.Type_d_equipement, e.Detail_equipement 
                                     FROM equipement e
                                     INNER JOIN reserver r ON e.IDequipement = r.IDequipement
                                     WHERE r.IDreservation = $reservation_id";
    $equipements_reserves = $conn->query($sql_get_equipements_reserves);
} else {
    $erreur = "Aucune réservation active trouvée pour votre compte.";
}

// ✅ Ajout des équipements sélectionnés
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['equipements']) && is_array($_POST['equipements'])) {
        foreach ($_POST['equipements'] as $equipement_id) {
            $sql_reserver = "INSERT INTO reserver (IDequipement, IDreservation) VALUES ($equipement_id, $reservation_id)";
            $conn->query($sql_reserver);
        }
        $message = 'Les équipements ont été réservés avec succès.';
        header("Refresh:0"); // Recharge la page pour afficher la mise à jour
    }
}

// ✅ Suppression d’un équipement réservé
if (isset($_POST['supprimer_equipement'])) {
    $equipement_id_supprimer = $_POST['supprimer_equipement'];
    $sql_supprimer = "DELETE FROM reserver WHERE IDequipement = $equipement_id_supprimer AND IDreservation = $reservation_id";
    if ($conn->query($sql_supprimer)) {
        $message = "L'équipement a été annulé avec succès.";
        header("Refresh:0"); // Recharge la page
    } else {
        $erreur = "Erreur lors de la suppression de l'équipement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation d'équipements</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Réserver des équipements</h1>

<?php if ($erreur) { echo "<p style='color: red;'>$erreur</p>"; } ?>
<?php if ($message) { echo "<p style='color: green;'>$message</p>"; } ?>

<!-- ✅ Section : Équipements DISPONIBLES -->
<?php if ($reservation_id && isset($equipements) && $equipements->num_rows > 0): ?>
    <h2>Sélectionnez les équipements à réserver :</h2>
    <form method="POST" action="">
        <input type="hidden" name="reservation_id" value="<?php echo $reservation_id; ?>">
        <?php while ($equipement = $equipements->fetch_assoc()): ?>
            <div>
                <input type="checkbox" name="equipements[]" value="<?php echo $equipement['IDequipement']; ?>">
                <?php echo $equipement['Type_d_equipement'] . " - " . $equipement['Detail_equipement']; ?>
            </div>
        <?php endwhile; ?>
        <input type="submit" value="Réserver">
    </form>
<?php endif; ?>

<!-- ✅ Section : Équipements DÉJÀ réservés avec possibilité d'annulation -->
<?php if (isset($equipements_reserves) && $equipements_reserves->num_rows > 0): ?>
    <h2>Équipements déjà réservés :</h2>
    <ul>
        <?php while ($equipement = $equipements_reserves->fetch_assoc()): ?>
            <li>
                <?php echo $equipement['Type_d_equipement'] . " - " . $equipement['Detail_equipement']; ?>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="supprimer_equipement" value="<?php echo $equipement['IDequipement']; ?>">
                    <button type="submit">Annuler</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>
<?php endif; ?>

</body>
</html>

<?php
// Fermeture de la connexion
$conn->close();
?>
