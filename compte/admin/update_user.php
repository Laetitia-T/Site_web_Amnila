<?php 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amnila";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Vérifier si les données sont envoyées
if (!isset($_POST['id'])) {
    echo "ID utilisateur manquant.";
    exit();
}

$id = $_POST['id'];

// Initialiser les variables
$name = isset($_POST['name']) ? $_POST['name'] : null;
$firstname = isset($_POST['firstname']) ? $_POST['firstname'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;

// Démarrer la transaction pour assurer l'intégrité des données
$conn->begin_transaction();

try {
    // Vérifier si l'ID existe dans chaque table avant de modifier
    $is_proprietaire = $conn->query("SELECT IDproprietaire FROM proprietaire WHERE IDproprietaire = $id")->num_rows > 0;
    $is_locataire = $conn->query("SELECT IDlocataire FROM locataire WHERE IDlocataire = $id")->num_rows > 0;
    $is_user = $conn->query("SELECT id FROM users WHERE id = $id")->num_rows > 0;

    // Mettre à jour les données seulement si elles existent
    if ($name !== null) {
        if ($is_proprietaire) {
            $stmt = $conn->prepare("UPDATE proprietaire SET NomP = ? WHERE IDproprietaire = ?");
            $stmt->bind_param("si", $name, $id);
            $stmt->execute();
            $stmt->close();
        }
        if ($is_locataire) {
            $stmt = $conn->prepare("UPDATE locataire SET NomL = ? WHERE IDlocataire = ?");
            $stmt->bind_param("si", $name, $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    if ($firstname !== null) {
        if ($is_proprietaire) {
            $stmt = $conn->prepare("UPDATE proprietaire SET PrenomP = ? WHERE IDproprietaire = ?");
            $stmt->bind_param("si", $firstname, $id);
            $stmt->execute();
            $stmt->close();
        }
        if ($is_locataire) {
            $stmt = $conn->prepare("UPDATE locataire SET PrenomL = ? WHERE IDlocataire = ?");
            $stmt->bind_param("si", $firstname, $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    if ($email !== null) {
        if ($is_proprietaire) {
            $stmt = $conn->prepare("UPDATE proprietaire SET Adresse_email_P = ? WHERE IDproprietaire = ?");
            $stmt->bind_param("si", $email, $id);
            $stmt->execute();
            $stmt->close();
        }
        if ($is_locataire) {
            $stmt = $conn->prepare("UPDATE locataire SET Adresse_email_L = ? WHERE IDlocataire = ?");
            $stmt->bind_param("si", $email, $id);
            $stmt->execute();
            $stmt->close();
        }
        if ($is_user) {
            $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->bind_param("si", $email, $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Valider la transaction
    $conn->commit();
    echo "Mise à jour réussie.";

} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    $conn->rollback();
    echo "Erreur lors de la mise à jour : " . $e->getMessage();
}

$conn->close();
?>
