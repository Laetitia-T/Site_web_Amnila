<?php
// db.php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amnila";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['IDproprietaire']) || empty($_SESSION['IDproprietaire'])) {
    die("Erreur : ID du propriétaire non défini.");
}

$IDproprietaire = intval($_SESSION['IDproprietaire']);

// Mettre à jour les informations si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nouveau_nom = htmlspecialchars($_POST['nom']);
    $nouveau_prenom = htmlspecialchars($_POST['prenom']);
    $nouveau_telephone = htmlspecialchars($_POST['telephone']);

    $sql_update = "UPDATE proprietaire SET NomP = ?, PrenomP = ?, TelephoneP = ? WHERE IDproprietaire = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssi", $nouveau_nom, $nouveau_prenom, $nouveau_telephone, $IDproprietaire);

    if ($stmt_update->execute()) {
        $message = "Mise à jour réussie !";
    } else {
        $message = "Erreur lors de la mise à jour.";
    }
    $stmt_update->close();
}

// Récupérer les informations actuelles
$sql = "SELECT * FROM proprietaire WHERE IDproprietaire = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $IDproprietaire);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nom = htmlspecialchars($row['NomP']);
    $prenom = htmlspecialchars($row['PrenomP']);
    $email = htmlspecialchars($row['Adresse_email_P']);
    $telephone = htmlspecialchars($row['TelephoneP']);
    $rib = htmlspecialchars($row['RIB']);
} else {
    die("Aucun propriétaire trouvé.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte - Neige&Soleil</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/iconly/bold.css">
    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon">
</head>
</head>
<body>
<div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <a href="index.php"><img src="assets/images/logo/logo-n&s.webp" alt="Logo" style="height:170px; width:170px;"></a>
                        </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item ">
                            <a href="index.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Accueil</span>
                            </a>
                        </li>
                        <li class="sidebar-item active ">
                            <a href="profil.php" class='sidebar-link'>
                                <i class="bi bi-person-circle"></i>
                                <span>Mon Profil</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
    <a href="#" class="sidebar-link" onclick="toggleAnnonceMenu(event)">
        <i class="bi bi-calendar-check"></i>
        <span>Déposer une annonce</span>
    </a>
    <ul id="annonceMenu" class="submenu" style="display: none; padding-left: 20px;">
        <li class="sidebar-item">
            <a href="annonce.php" class="sidebar-link">Déposer une annonce - Appartement</a>
        </li>
        <li class="sidebar-item">
            <a href="equipement.php" class="sidebar-link">Déposer une annonce - Équipement</a>
        </li>
    </ul>
</li>

                        <li class="sidebar-item ">
                            <a href="gerer-annonce.php" class='sidebar-link'>
                                <i class="bi bi-calendar-check"></i>
                                <span>Gerer les annonces</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="contrat.php" class="sidebar-link">
                                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                                <span>Mes Contrats</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="../../index.php" class="sidebar-link">
                                <i class="bi bi-arrow-left"></i>
                                <span>Retour</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>

        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
            <h3>Profil du Propriétaire</h3>
        </header>
        <div class="page-content">
            <section class="row">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5>Informations du propriétaire</h5>
                            <?php if (isset($message)) { echo "<p class='text-success'>$message</p>"; } ?>
                            <form method="post">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $nom; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo $prenom; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo $telephone; ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <footer>
            <p>2025 &copy; Neige&Soleil</p>
        </footer>
    </div>
</div>

<script>
    function toggleAnnonceMenu(event) {
        event.preventDefault(); // Empêche le lien de changer de page
        var menu = document.getElementById("annonceMenu");
        menu.style.display = (menu.style.display === "none" || menu.style.display === "") ? "block" : "none";
    }
</script>
</body>
</html>
