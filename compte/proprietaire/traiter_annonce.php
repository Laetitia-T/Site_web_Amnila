<?php
session_start();

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

// Vérifier que le propriétaire est connecté
if (!isset($_SESSION['IDproprietaire'])) {
    die("Erreur : Vous devez être connecté en tant que propriétaire pour déposer une annonce.");
}
$id_proprietaire = $_SESSION['IDproprietaire'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si toutes les valeurs requises existent
    $titre        = $conn->real_escape_string(trim($_POST['titre'] ?? ''));
    $adresse      = $conn->real_escape_string(trim($_POST['adresse'] ?? ''));
    $description  = $conn->real_escape_string(trim($_POST['description'] ?? ''));
    $prix         = $conn->real_escape_string(trim($_POST['prix'] ?? ''));
    $capacite     = $conn->real_escape_string(trim($_POST['capacite'] ?? ''));
    $code_postal  = $conn->real_escape_string(trim($_POST['code_postal'] ?? ''));
    $ville        = $conn->real_escape_string(trim($_POST['ville'] ?? ''));
    $surface      = $conn->real_escape_string(trim($_POST['surface'] ?? ''));
    $region       = $conn->real_escape_string(trim($_POST['region'] ?? ''));
    
    // Vérifier si la région existe déjà dans la base de données
    $stmt = $conn->prepare("SELECT IDregion FROM region WHERE NomR = ?");
    $stmt->bind_param("s", $region);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Si la région n'existe pas, on l'insère dans la table
        $stmt = $conn->prepare("INSERT INTO region (NomR) VALUES (?)");
        $stmt->bind_param("s", $region);
        if ($stmt->execute()) {
            // Récupérer l'ID de la nouvelle région insérée
            $id_region = $stmt->insert_id;
        } else {
            die("Erreur lors de l'insertion de la région.");
        }
    } else {
        // Si la région existe, on récupère son ID
        $region_data = $result->fetch_assoc();
        $id_region = $region_data['IDregion'];
    }
    $stmt->close();

    // Insertion dans la table appartement avec une requête préparée
    $sql = "INSERT INTO appartement (Type_d_appartementA, RueA, Code_PostalA, VilleA, SurfaceA, Prix_journalier, IDproprietaire, IDregion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssdii", $titre, $adresse, $code_postal, $ville, $surface, $prix, $id_proprietaire, $id_region);

    if ($stmt->execute()) {
        $id_appartement = $stmt->insert_id;
        $stmt->close();

        // Gestion des fichiers photos
        if (!empty($_FILES['photos']['name'][0])) {
            $upload_dir = '../../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            foreach ($_FILES['photos']['name'] as $key => $file_name) {
                $file_tmp  = $_FILES['photos']['tmp_name'][$key];
                $file_ext  = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_name  = $id_appartement . "_" . time() . "_" . $key . "." . $file_ext;
                $file_path = $upload_dir . $new_name;

                if (move_uploaded_file($file_tmp, $file_path)) {
                    $sqlPhoto = "INSERT INTO photo (Chemin, IDappartement) VALUES (?, ?)";
                    $stmt = $conn->prepare($sqlPhoto);
                    $file_path = 'uploads/'.$new_name;

                    $stmt->bind_param("si", $file_path, $id_appartement);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }

        // Redirection vers la page de description de l'appartement
        header("Location: ../../description_appartement.php?id=" . $id_appartement);
        exit();
    } else {
        echo "Erreur lors de l'insertion : " . $conn->error;
    }
}

$conn->close();
?>
