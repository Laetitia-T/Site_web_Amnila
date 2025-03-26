<?php
// db.php
session_start(); // Démarrer la session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amnila";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si l'ID du propriétaire est défini dans la session
if (!isset($_SESSION['IDproprietaire']) || empty($_SESSION['IDproprietaire'])) {
    die("Erreur : ID du propriétaire non défini.");
}

$IDproprietaire = intval($_SESSION['IDproprietaire']); // Sécuriser l'ID en tant qu'entier

// Requête SQL sécurisée
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
<html lang="en">

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

                        <li class="sidebar-item active ">
                            <a href="index.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Accueil</span>
                            </a>
                        </li>
                        <li class="sidebar-item ">
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
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-9">
                        <div class="row">
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-3 py-4-5">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="stats-icon purple">
                                                    <i class="iconly-boldShow"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <br>
                                                <h10 class="text-muted font-semibold">Nom du Propriétaire</h10>
                                                <h5 class="font-extrabold mb-0">
                                                    <?php 
                                                    // Vérification si les données existent avant de les afficher
                                                    if (isset($nom) && isset($prenom)) {
                                                        echo $nom . ' ' . $prenom;
                                                    } else {
                                                        echo 'Nom et Prénom non disponibles';
                                                    }
                                                    ?>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="avatar avatar-xl">
                                    <img src="assets/images/faces/2.jpg" alt="User Avatar">
                                </div>
                                <h5 class="mt-3">
                                    <?php 
                                    if (isset($nom) && isset($prenom)) {
                                        echo $nom . ' ' . $prenom;
                                    } else {
                                        echo 'Nom non disponible';
                                    }
                                    ?>
                                </h5>
                                <h6 class="text-muted">
                                    <?php 
                                    if (isset($email)) {
                                        echo $email;
                                    } else {
                                        echo 'Email non disponible';
                                    }
                                    ?>
                                </h6>
                                <p class="mt-3">
                                    Téléphone: 
                                    <?php 
                                    if (isset($telephone)) {
                                        echo $telephone;
                                    } else {
                                        echo 'Téléphone non disponible';
                                    }
                                    ?>
                                </p>
                                <p>RIB: 
                                    <?php 
                                    if (isset($rib)) {
                                        echo $rib;
                                    } else {
                                        echo 'RIB non disponible';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2025 &copy; Neige&Soleil</p>
                    </div>
                    <div class="float-end">
                        <p>Réalisé avec <span class="text-danger"><i class="bi bi-heart"></i></span> par Vous</p>
                    </div>
                </div>
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
