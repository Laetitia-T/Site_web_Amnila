<?php
// Démarrer la session
session_start();

// Vérifier que l'ID administrateur est bien défini dans la session
if (!isset($_SESSION['IDadministrateur'])) {
    die("Erreur: ID administrateur non défini.");
}

// Récupérer l'ID administrateur depuis la session
$id_admin = $_SESSION['IDadministrateur'];

// Connexion à la base de données
$servername = "localhost";
$username = "root"; // remplace par ton utilisateur
$password = ""; // remplace par ton mot de passe
$dbname = "amnila"; // remplace par ton nom de base de données

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

// Vérifier que l'ID administrateur n'est pas vide
if (empty($id_admin)) {
    die("Erreur: ID administrateur invalide.");
}

// Requête préparée pour éviter les injections SQL
$stmt = $conn->prepare("SELECT * FROM Administrateur WHERE IDadministrateur = ?");
$stmt->bind_param("i", $id_admin); // "i" pour un entier
$stmt->execute();
$result = $stmt->get_result();

// Vérifier si des résultats ont été retournés
if ($result->num_rows > 0) {
    // Récupérer les données de l'administrateur
    $admin = $result->fetch_assoc();
} else {
    die("Aucun administrateur trouvé.");
}

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte - Neige&Soleil</title>
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
    <div id="app">
        <!-- Sidebar -->
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

        <!-- Main Content -->
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>Accueil</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-8">
                        <div class="row">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Administrateurs</h4>
                                </div>
                                <!-- Affichage des utilisateurs, dynamique -->
                                <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Prénom</th>
                                                <th>Username</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Connexion à la base de données pour récupérer tous les utilisateurs
                                            $conn = new mysqli($servername, $username, $password, $dbname);
                                            if ($conn->connect_error) {
                                                die("La connexion a échoué: " . $conn->connect_error);
                                            }
                                            
                                            // Récupérer les utilisateurs sauf l'administrateur
                                            $sql_users = "SELECT * FROM Administrateur WHERE IDadministrateur != $id_admin";
                                            $result_users = $conn->query($sql_users);
                                            
                                            if ($result_users->num_rows > 0) {
                                                while ($user = $result_users->fetch_assoc()) {
                                                    echo "<tr>
                                                        <td>{$user['NomA']}</td>
                                                        <td>{$user['PrenomA']}</td>
                                                        <td>{$user['UsernameA']}</td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='3'>Aucun utilisateur trouvé</td></tr>";
                                            }

                                            // Fermer la connexion
                                            $conn->close();
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profil utilisateur -->
                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="avatar avatar-xl">
                                    <img src="assets/images/faces/3.jpg" alt="User Avatar">
                                </div>
                                <h5 class="mt-3"><?php echo $admin['NomA'] . " " . $admin['PrenomA']; ?></h5>
                                <h6 class="text-muted"><?php echo $admin['UsernameA']; ?></h6>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Footer -->
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
</body>

</html>
