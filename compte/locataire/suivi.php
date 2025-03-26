<?php
// Démarre la session
session_start();

// Vérifiez si l'ID du locataire est dans la session
if (!isset($_SESSION['IDlocataire'])) {
    die("Utilisateur non authentifié.");
}

$IDlocataire = $_SESSION['IDlocataire'];// Récupérer l'ID du locataire depuis la session

// Connexion à la base de données
$host = 'localhost';
$dbname = 'amnila'; 
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection échouée : " . $e->getMessage();
    exit;
}

// Récupérer les paiements des locataires avec les informations des appartements
$query = "SELECT r.*, a.Type_d_appartementA, a.Prix_journalier 
          FROM reservation r
          JOIN appartement a ON r.IDappartement = a.IDappartement
          WHERE r.IDlocataire = :locataire_id"; // Filtrer par l'ID du locataire

$stmt = $pdo->prepare($query);
$stmt->bindParam(':locataire_id', $locataire_id, PDO::PARAM_INT); // Lier l'ID du locataire à la requête
$stmt->execute();
$paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi des Paiements - Locataire</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/app.css">
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
                        <li class="sidebar-item active">
                            <a href="suivi.php" class='sidebar-link'>
                                <i class="bi bi-calendar-check"></i>
                                <span>Suivi des Paiements Locataire</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
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
                            <h3>Suivi des Paiements Locataire</h3>
                            <p class="text-subtitle text-muted">Gestion des loyers et paiements des locataires</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Suivi des Paiements Locataire</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            Liste des Paiements Locataires
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" id="table1">
                                <thead>
                                    <tr>
                                        <th>Appartement</th>
                                        <th>Prix (€)</th>
                                        <th>Date de début</th>
                                        <th>Date de fin</th>
                                        <th>Statut</th>
                                        <th>Payer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($paiements as $paiement): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($paiement['Type_d_appartementA']); ?></td>
                                            <td><?php echo htmlspecialchars($paiement['Prix_journalier']); ?> €</td>
                                            <td><?php echo htmlspecialchars($paiement['Date_de_debutR']); ?></td>
                                            <td><?php echo htmlspecialchars($paiement['Date_de_finR']); ?></td>
                                            <td>
                                                <?php 
                                                    if ($paiement['StatutR'] == 'Payé') {
                                                        echo '<span class="badge bg-success">Payé</span>';
                                                    } elseif ($paiement['StatutR'] == 'Non payé') {
                                                        echo '<span class="badge bg-danger">Non payé</span>';
                                                    } else {
                                                        echo '<span class="badge bg-warning">En attente</span>';
                                                    }
                                                ?>
                                            </td>
                                            <td>paiement</td>
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
                        <p>Réalisé avec <span class="text-danger"><i class="bi bi-heart"></i></span> par Vous</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendors/simple-datatables/simple-datatables.js"></script>
    <script>
        let table1 = document.querySelector('#table1');
        let dataTable = new simpleDatatables.DataTable(table1);
    </script>
    <script src="assets/js/main.js"></script>
</body>

</html>
