<?php
session_start();

// Vérifiez si l'ID du locataire est dans la session
if (!isset($_SESSION['IDlocataire'])) {
    die("Utilisateur non authentifié.");
}

$IDlocataire = $_SESSION['IDlocataire']; // Récupérer l'ID du locataire depuis la session

// Connexion à la base de données
$host = 'localhost';
$dbname = 'amnila'; // Nom de la base de données
$username = 'root'; // Nom d'utilisateur pour se connecter à la base
$password = ''; // Mot de passe pour se connecter à la base

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection échouée : " . $e->getMessage();
    exit;
}

// Vérification si le locataire a des réservations
$query = "SELECT COUNT(*) FROM reservation WHERE IDlocataire = :locataire_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':locataire_id', $IDlocataire, PDO::PARAM_INT);
$stmt->execute();
$reservationCount = $stmt->fetchColumn();

// if ($reservationCount == 0) {
//     die("Vous n'avez aucune réservation. Vous ne pouvez pas consulter cette page.");
// }

// Récupérer les réservations du locataire
$query = "SELECT r.IDreservation, r.Date_de_debutR, r.StatutR, r.Date_de_finR, a.Prix_journalier, a.Type_d_appartementA 
          FROM reservation r
          JOIN appartement a ON r.IDappartement = a.IDappartement
          WHERE r.IDlocataire = :locataire_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['locataire_id' => $IDlocataire]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation des Réservations</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
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
                        <li class="sidebar-item">
                            <a href="index.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Accueil</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="profil.php" class='sidebar-link'>
                                <i class="bi bi-person-circle"></i>
                                <span>Mon Profil</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="suivi.php" class='sidebar-link'>
                                <i class="bi bi-calendar-check"></i>
                                <span>Suivi des Paiements Locataire</span>
                            </a>
                        </li>
                        <li class="sidebar-item active">
                            <a href="consultation.php" class="sidebar-link">
                                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                                <span>Réservations</span>
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
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Consultation des Réservations</h3>
                            <p class="text-subtitle text-muted">Liste des réservations passées ou en cours</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Réservations</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            Liste des Réservations
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" id="reservationsTable">
                                <thead>
                                    <tr>
                                        <th>Type d'Appartement</th>
                                        <th>Date de Début</th>
                                        <th>Date de Fin</th>
                                        <th>Statut</th>
                                        <th>Equipement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservations as $reservation): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reservation['Type_d_appartementA']); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['Date_de_debutR']); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['Date_de_finR']); ?></td>
                                            <td>
                                                <?php 
                                                    if ($reservation['StatutR'] == 'Terminée') {
                                                        echo '<span class="badge bg-success">Terminée</span>';
                                                    } elseif ($reservation['StatutR'] == 'En cours') {
                                                        echo '<span class="badge bg-primary">En cours</span>';
                                                    } else {
                                                        echo '<span class="badge bg-danger">à venir</span>';
                                                    }
                                                ?>
                                            </td>
                                           <td>
                <a href="../../reservation_equipement.php" class="btn btn-primary">
                    Réserver un équipement
                </a>
            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
                        <p>Créé avec <span class="text-danger"><i class="bi bi-heart"></i></span> par <a href="http://ahmadsaugi.com">Laetitia, Nicolas & Amélien</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendors/simple-datatables/simple-datatables.js"></script>
    <script>
        let reservationsTable = document.querySelector('#reservationsTable');
        let dataTable = new simpleDatatables.DataTable(reservationsTable);
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>
