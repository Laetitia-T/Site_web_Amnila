<?php
// Connexion à la base de données
$host = '127.0.0.1';
$dbNomL = 'amnila';  // Nom de la base de données
$userNomL = 'root';  // Utilisateur de la base de données
$password = '';      // Mot de passe (vide)

// Connexion à la base de données avec les bonnes variables
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbNomL;charset=utf8", $userNomL, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("La connexion à la base de données a échoué : " . $e->getMessage());
}

session_start();

// Supposons que l'ID de l'utilisateur est stocké dans la session
$IDlocataire = $_SESSION['IDlocataire'] ?? null;  // Ajout de la gestion d'une valeur nulle pour éviter les erreurs

// Vérification si l'IDlocataire est défini
if ($IDlocataire) {
    // Récupération des informations de l'utilisateur
    $query = $pdo->prepare("SELECT * FROM Locataire WHERE IDlocataire = ?");
    $query->execute([$IDlocataire]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // Traitement de la mise à jour des informations
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $telephone = trim($_POST["telephone"]);

    if (!empty($nom) && !empty($prenom) && !empty($telephone)) {
        $updateQuery = $pdo->prepare("UPDATE Locataire SET NomL = ?, PrenomL = ?, TelephoneL = ? WHERE IDlocataire = ?");
        $updateQuery->execute([$nom, $prenom, $telephone, $IDlocataire]);

        $message = "Informations mises à jour avec succès.";
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
    if (!$user) {
        die("Utilisateur non trouvé.");
    }

    // Récupération des réservations de l'utilisateur
    $reservationQuery = $pdo->prepare("SELECT * FROM Reservation WHERE IDlocataire = ? ORDER BY Date_de_debutR DESC LIMIT 5");
    $reservationQuery->execute([$IDlocataire]);
    $reservations = $reservationQuery->fetchAll(PDO::FETCH_ASSOC);
} else {
    die("ID locataire non valide.");
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Neige&Soleil</title>
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
                <div class="sidebar-menu active">
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

<body>
    <div id="app">
        <div id="main">
            <div class="page-heading">
                <h3>Mon Profil</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Modifier mes Informations</h4>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($message)): ?>
                                    <div class="alert alert-success"><?php echo $message; ?></div>
                                <?php elseif (!empty($error)): ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endif; ?>

                                <form method="post">
                                    <div class="mb-3">
                                        <label for="nom" class="form-label">Nom</label>
                                        <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['NomL']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="prenom" class="form-label">Prénom</label>
                                        <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['PrenomL']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="telephone" class="form-label">Téléphone</label>
                                        <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['TelephoneL']); ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                                </form>
                            </div>
                        </div>

                        <!-- Réservations -->
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
                                            <td colspan="4" class="text-center">Aucune</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($reservations as $reservation): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($reservation['chalet'] ?? 'Aucune'); ?></td>
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

    <!-- Scripts -->
    <script src="assets/js/bootstrap.bundle.js"></script>
</body>

</html> 