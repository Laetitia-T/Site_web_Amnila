<?php
// Connexion à la base de données
$host = '127.0.0.1';
$dbname = 'amnila';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("La connexion à la base de données a échoué : " . $e->getMessage());
}

// Démarrage de la session
session_start();

// Vérification que l'IDlocataire est bien défini
if (!isset($_SESSION['IDlocataire'])) {
    die("Utilisateur non authentifié.");
}

$IDlocataire = $_SESSION['IDlocataire'];

// Requête pour obtenir les réservations du locataire
$reservationQuery = $pdo->prepare("
    SELECT * FROM Reservation 
    WHERE IDlocataire = ? 
    ORDER BY Date_de_debutR DESC 
    LIMIT 5
");
$reservationQuery->execute([$IDlocataire]);
$reservations = $reservationQuery->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les informations du locataire
$query = $pdo->prepare("SELECT * FROM locataire WHERE IDlocataire = ?");
$query->execute([$IDlocataire]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Vérification si les données existent avant de les utiliser
$nom = $user['NomL'] ?? 'Nom non disponible';
$prenom = $user['PrenomL'] ?? 'Prénom non disponible';
$email = $user['Adresse_email_L'] ?? 'Email non disponible';
$telephone = $user['TelephoneL'] ?? 'Téléphone non disponible';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Locataire - Neige&Soleil</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/vendors/iconly/bold.css">
    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
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
                <div class="sidebar-menu active">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item active">
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
        <header>
            <h3>Bienvenue, <?php echo htmlspecialchars($nom) . ' ' . htmlspecialchars($prenom); ?></h3>
        </header>

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
                                            <h6 class="text-muted font-semibold">Nom du Locataire</h6>
                                            <h5 class="font-extrabold mb-0"><?php echo htmlspecialchars($nom) . ' ' . htmlspecialchars($prenom); ?></h5>
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
                                <img src="assets/images/faces/1.jpg" alt="User Avatar">
                            </div>
                            <h5 class="mt-3"><?php echo htmlspecialchars($nom) . ' ' . htmlspecialchars($prenom); ?></h5>
                            <h6 class="text-muted"><?php echo htmlspecialchars($email); ?></h6>
                            <p class="mt-3">Téléphone: <?php echo htmlspecialchars($telephone); ?></p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Réservations Récentes</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Chalet</th>
                                        <th>Date de Début</th>
                                        <th>Date de Fin</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($reservations)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Aucune réservation trouvée.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($reservations as $reservation): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($reservation['chalet'] ?? 'Non spécifié'); ?></td>
                                                <td><?php echo htmlspecialchars($reservation['Date_de_debutR']); ?></td>
                                                <td><?php echo htmlspecialchars($reservation['Date_de_finR']); ?></td>
                                                <td><span class="badge <?php echo ($reservation['StatutR'] == 'confirmée') ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?php echo htmlspecialchars($reservation['StatutR']); ?>
                                                </span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
