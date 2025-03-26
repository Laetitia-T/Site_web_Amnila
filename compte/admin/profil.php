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

if (isset($_POST['update_annonce'])) {
    $IDappartement = $_POST['IDappartement'];
    $Type_d_appartementA = $_POST['type'];
    $RueA = $_POST['rue'];
    $Prix_journalier = $_POST['prix'];

    // Mise à jour de l'appartement
    $sql_update = "UPDATE Appartement SET Type_d_appartementA = ?, RueA = ?, Prix_journalier = ? WHERE IDappartement = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssdi", $Type_d_appartementA, $RueA, $Prix_journalier, $IDappartement);

    if ($stmt_update->execute()) {
        $stmt_update->close();
        $conn->close();
        header("Location: profil.php"); // Recharge la page après mise à jour
        exit();
    } else {
        echo '<div class="alert alert-danger">Erreur lors de la mise à jour de l\'annonce.</div>';
    }

    $stmt_update->close();
}

// Fermer la connexion ici, à la fin du script
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Gestion des Annonces</title>
    <!-- Inclure Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte - Neige&Soleil</title>
    <!-- Fonts and Icons -->
    <<!DOCTYPE html>
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
            border-color:rgb(3, 119, 143);
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
                        <li class="sidebar-item ">
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

                        <!-- Liste des userss -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h4>Liste des Utilisateurs</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Rôle</th>
                                            <th>Email</th>
                                            <th>Opérations</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php while ($user = $result_users->fetch_assoc()): ?>
        <td><?php echo htmlspecialchars($user['id']); ?></td>
        <!-- Nom de l'utilisateur (rendu éditable selon le rôle) -->
        <td contenteditable="true" class="editable" data-id="<?php echo $user['id']; ?>" data-column="<?php echo $user['role'] == 'proprietaire' ? 'NomP' : ($user['role'] == 'locataire' ? 'NomL' : 'NomA'); ?>">
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

        <!-- Prénom de l'utilisateur (rendu éditable selon le rôle) -->
        <td contenteditable="true" class="editable" data-id="<?php echo $user['id']; ?>" data-column="<?php echo $user['role'] == 'proprietaire' ? 'PrenomP' : ($user['role'] == 'locataire' ? 'PrenomL' : 'PrenomA'); ?>">
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

        <!-- Rôle de l'utilisateur (rendu éditable) -->
        <td contenteditable="true" class="editable" data-id="<?php echo $user['id']; ?>" data-column="role">
            <?php echo htmlspecialchars($user['role']); ?>
        </td>

        <!-- Email de l'utilisateur (rendu éditable) -->
        <td contenteditable="true" class="editable" data-id="<?php echo $user['id']; ?>" data-column="email">
            <?php echo htmlspecialchars($user['email']); ?>
        </td>

                                                <td>

                                                    <!-- Bouton Modifier -->
                                                    <button class="btn btn-warning btn-sm edit-btn">
                                                        <i class="bi bi-pencil-square"></i> Modifier
                                                    </button>

                                                    <!-- Bouton Enregistrer -->
                                                    <button class="btn btn-primary btn-sm save-btn" 
                                                            data-id="<?php echo $user['id']; ?>" 
                                                            style="display: none;">
                                                        Enregistrer
                                                    </button>

                                                    <!-- Bouton Supprimer -->
                                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" 
                                                       class="btn btn-danger btn-sm delete-btn" 
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
            </div>


            <!-- Liste des annonces -->
            <div class="card mt-4">
                <div class="card-header">
    <h4>Gestion des Annonces</h4>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Localisation</th>
                <th>Prix Journalier (€)</th>
                <th>Propriétaire</th>
                <th>Opérations</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop pour afficher les annonces -->
            <?php while ($annonce = $result_annonces->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($annonce['IDappartement']); ?></td>
                    <td><?php echo htmlspecialchars($annonce['Type_d_appartementA']); ?></td>
                    <td><?php echo htmlspecialchars($annonce['RueA']); ?></td>
                    <td><?php echo htmlspecialchars($annonce['Prix_journalier']); ?> €</td>
                    <td><?php echo htmlspecialchars($annonce['NomP']) . ' ' . htmlspecialchars($annonce['PrenomP']); ?></td>
                    <td>
                        <!-- Bouton pour afficher la modale -->
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?php echo $annonce['IDappartement']; ?>" class="btn btn-warning btn-sm" ><i class="bi bi-pencil-square"></i>Modifier</button>
                
                        <a href="delete_annonce.php?id=<?php echo $annonce['IDappartement']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer cette annonce ?');">
                        <i class="bi bi-trash"></i> Supprimer
                                                </a>
            </td>
                </tr>

                <!-- Fenêtre modale -->
                <div class="modal fade" id="editModal<?php echo $annonce['IDappartement']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Modifier l'Annonce</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="profil.php">
                                    <input type="hidden" name="IDappartement" value="<?php echo $annonce['IDappartement']; ?>">
                                    <div class="form-group">
                                        <label for="type">Type d'appartement</label>
                                        <input type="text" class="form-control" name="type" value="<?php echo $annonce['Type_d_appartementA']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rue">Localisation</label>
                                        <input type="text" class="form-control" name="rue" value="<?php echo $annonce['RueA']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="prix">Prix Journalier (€)</label>
                                        <input type="number" class="form-control" name="prix" value="<?php echo $annonce['Prix_journalier']; ?>" required>
                                    </div>
                                    <button type="submit" name="update_annonce" class="btn btn-success" style="background-color:rgb(2, 83, 99);!important">Mettre à jour</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Activer l'édition de la cellule
    $(".edit-btn").click(function() {
        var row = $(this).closest("tr");
        row.find(".editable").attr("contenteditable", "true").focus();
        row.find(".edit-btn").hide();
        row.find(".save-btn").show();
    });

    // Sauvegarder les modifications
    $(".save-btn").click(function() {
        var row = $(this).closest("tr");
        var id = row.find(".editable").data("id");  // Récupérer l'ID de l'utilisateur à partir de la ligne
        var name = row.find(".editable[data-column^='Nom']").text().trim(); // Récupère le nom (NomP, NomL, etc.)
        var firstname = row.find(".editable[data-column^='Prenom']").text().trim(); // Récupère le prénom (PrenomP, PrenomL, etc.)
        var email = row.find(".editable[data-column='email']").text().trim(); // Email

        // Envoi des données via AJAX
        $.ajax({
            url: "update_user.php",
            type: "POST",
            data: {
                id: id,
                name: name,
                firstname: firstname,
                email: email
            },
            success: function(response) {
                alert(response);
                row.find(".editable").attr("contenteditable", "false");
                row.find(".save-btn").hide();
                row.find(".edit-btn").show();
            },
            error: function() {
                alert("Erreur lors de la mise à jour.");
            }
        });
    });
});

    </script>
</body>
</html>
