<?php 
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['IDadministrateur'])) {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amnila";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué: " . $conn->connect_error);
}

// Récupérer l'ID de l'administrateur depuis la session
$IDadministrateur = $_SESSION['IDadministrateur'];

// Récupérer les informations de l'administrateur
$sql = "SELECT * FROM Administrateur WHERE IDadministrateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $IDadministrateur);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Vérifier si l'administrateur existe
if (!$admin) {
    die("Administrateur non trouvé");
}

// Fermer la requête préparée
$stmt->close();

// Récupérer la liste des utilisateurs avec les informations en fonction du rôle
$sql_users = "
    SELECT u.id, u.email, u.role, 
           p.NomP, p.PrenomP, 
           l.NomL, l.PrenomL, 
           a.NomA, a.PrenomA
    FROM Users u
    LEFT JOIN Proprietaire p ON u.id = p.IDproprietaire
    LEFT JOIN Locataire l ON u.id = l.IDlocataire
    LEFT JOIN Administrateur a ON u.id = a.IDadministrateur
";
$result_users = $conn->query($sql_users);

// Récupérer la liste des annonces
$sql_annonces = "SELECT a.IDappartement, a.Type_d_appartementA, a.RueA, a.Prix_journalier, p.NomP, p.PrenomP 
                 FROM Appartement a 
                 JOIN Proprietaire p ON a.IDproprietaire = p.IDproprietaire";
$result_annonces = $conn->query($sql_annonces);

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Type_d_appartementA = $_POST['type'];
    $RueA = $_POST['rue'];
    $Prix_journalier = $_POST['prix'];

    // Mise à jour dans la base de données
    $sql_update = "UPDATE Appartement SET Type_d_appartementA = ?, RueA = ?, Prix_journalier = ? WHERE IDappartement = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssdi", $Type_d_appartementA, $RueA, $Prix_journalier, $IDappartement);

    if ($stmt_update->execute()) {
        header("Location: profil.php?success=modification");
        exit();
    } else {
        echo "Erreur lors de la mise à jour.";
    }

    $stmt_update->close();
}

if (isset($_GET['success']) && $_GET['success'] == 'modification') {
    echo '<div class="alert alert-success">Modification réussie !</div>';
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
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
<style>
    .btn-danger {
        background-color: #00b4d8;
        border: none;
    }
    .btn-danger:hover {
        background-color: rgb(3, 153, 183);
    }
    .bi-pencil-square {
        box-shadow: none;
        border-color: rgb(3, 119, 143);
    }
</style>
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
                    <li class="sidebar-item">
                        <a href="index.php" class='sidebar-link'>
                            <i class="bi bi-grid-fill"></i>
                            <span>Accueil</span>
                        </a>
                    </li>
                    <li class="sidebar-item active">
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
            <h3>Mon Profil</h3>
        </div>
        <div class="page-content">
            <section class="row">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="avatar avatar-xl">
                                        <img src="assets/images/faces/3.jpg" alt="User Avatar">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h5 class="mt-3"><?php echo htmlspecialchars($admin['NomA'] . ' ' . $admin['PrenomA']); ?></h5>
                                    <p class="text-muted"><?php echo htmlspecialchars($admin['UsernameA']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations Personnelles -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Informations Personnelles</h4>
                        </div>
                        <div class="card-body">
                            <h6><strong>Pseudo :</strong> <?php echo htmlspecialchars($admin['UsernameA']); ?></h6>
                        </div>
                    </div>

                    <!-- Liste des utilisateurs -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4>Liste des Utilisateurs</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Opérations</th> <!-- Colonne pour les boutons d'actions -->
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Rôle</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = $result_users->fetch_assoc()): ?>
                                        <tr>
                                            <td> <!-- Bouton Modifier déplacé à gauche -->
                                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil-square"></i> Modifier
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                                            <td>
                                                <?php 
                                                    if ($user['role'] == 'proprietaire') {
                                                        echo htmlspecialchars($user['NomP']);  // Nom du propriétaire
                                                    } elseif ($user['role'] == 'locataire') {
                                                        echo htmlspecialchars($user['NomL']);  // Nom du locataire
                                                    } else {
                                                        echo htmlspecialchars($user['NomA']);  // Nom de l'administrateur
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    if ($user['role'] == 'proprietaire') {
                                                        echo htmlspecialchars($user['PrenomP']);  // Prénom du propriétaire
                                                    } elseif ($user['role'] == 'locataire') {
                                                        echo htmlspecialchars($user['PrenomL']);  // Prénom du locataire
                                                    } else {
                                                        echo htmlspecialchars($user['PrenomA']);  // Prénom de l'administrateur
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                                                    <i class="bi bi-trash"></i> Supprimer
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Liste des annonces -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Liste des Annonces</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr><th>ID</th><th>Type</th><th>Localisation</th><th>Prix Journalier (€)</th><th>Propriétaire</th><th>Opérations</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($annonce = $result_annonces->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($annonce['IDappartement']); ?></td>
                                    <td><?php echo htmlspecialchars($annonce['Type_d_appartementA']); ?></td>
                                    <td><?php echo htmlspecialchars($annonce['RueA']); ?></td>
                                    <td><?php echo htmlspecialchars($annonce['Prix_journalier']); ?> €</td>
                                    <td><?php echo htmlspecialchars($annonce['NomP'] . ' ' . $annonce['PrenomP']); ?></td>
                                    <td>
    <a href="edit_annonce.php?id=<?php echo $annonce['IDappartement']; ?>" class="btn btn-warning btn-sm">
        <i class="bi bi-pencil-square"></i> Modifier
    </a>
    <a href="delete_annonce.php?id=<?php echo $annonce['IDappartement']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer cette annonce ?');">
        <i class="bi bi-trash"></i> Supprimer
    </a>
</td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <!-- Footer -->
        <footer>
            <div class="footer clearfix mb-0 text-muted">
                <p>2025 &copy; Neige&Soleil</p>
            </div>
        </footer>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.js"></script>
</body>
</html>


