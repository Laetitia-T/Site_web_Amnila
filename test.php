<?php
$servername = "localhost";
$dbname = "amnila";
$dbusername = "root";
$dbpassword = "";

// Connexion à la base
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier que l'ID de l'appartement est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID d'appartement invalide.");
}

$id_appartement = intval($_GET['id']);

// Vérifier que l'utilisateur est connecté (locataire uniquement)
session_start();
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'locataire') {
//     die("Vous devez être connecté en tant que locataire pour réserver.");
// }
//$id_locataire = $_SESSION['user_id'];
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'locataire') {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('loginWarning'));
            myModal.show();
        });
    </script>";
}


// Récupération des réservations pour cet appartement
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) && $_GET['month'] >= 1 && $_GET['month'] <= 12 ? intval($_GET['month']) : date('m');

$start_date = "$year-$month-01";
$end_date = date("Y-m-t", strtotime($start_date));

$sql_reservations = "SELECT Date_de_debutR, Date_de_finR FROM reservation WHERE IDappartement = ? AND (
                        (Date_de_debutR BETWEEN ? AND ?) OR (Date_de_finR BETWEEN ? AND ?)
                     )";
$stmt = $conn->prepare($sql_reservations);
$stmt->bind_param("issss", $id_appartement, $start_date, $end_date, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$reserved_dates = [];
while ($row = $result->fetch_assoc()) {
    $start = strtotime($row['Date_de_debutR']);
    $end = strtotime($row['Date_de_finR']);
    for ($i = $start; $i <= $end; $i += 86400) {
        $reserved_dates[date('Y-m-d', $i)] = true;
    }
}

$stmt->close();
$conn->close();

// Tableau des mois en français
$mois_francais = [
    1 => "Janvier", 2 => "Février", 3 => "Mars", 4 => "Avril", 
    5 => "Mai", 6 => "Juin", 7 => "Juillet", 8 => "Août", 
    9 => "Septembre", 10 => "Octobre", 11 => "Novembre", 12 => "Décembre"
];

$mois_actuel = $mois_francais[$month] ?? "Mois invalide";

// Fonction pour générer le calendrier
function generate_calendar($month, $year, $reserved_dates) {
    $days_in_month = date('t', strtotime("$year-$month-01"));
    $first_day_of_month = date('N', strtotime("$year-$month-01"));

    echo "<table class='calendar table table-bordered'>";
    echo "<thead class='table-dark'><tr><th>Lun</th><th>Mar</th><th>Mer</th><th>Jeu</th><th>Ven</th><th>Sam</th><th>Dim</th></tr></thead><tbody><tr>";

    $day_count = 1;

    for ($i = 1; $i < $first_day_of_month; $i++) {
        echo "<td></td>";
        $day_count++;
    }

    for ($day = 1; $day <= $days_in_month; $day++) {
        $date_str = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $class = isset($reserved_dates[$date_str]) ? 'table-danger' : 'table-success';
        echo "<td class='$class'>$day</td>";

        if ($day_count % 7 == 0) {
            echo "</tr><tr>";
        }
        $day_count++;
    }

    while ($day_count % 7 != 1) {
        echo "<td></td>";
        $day_count++;
    }

    echo "</tr></tbody></table>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation de l'appartement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/iconly/bold.css">
    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f6f9; 
            text-align: center; 
            padding: 20px;
        }
        .container {
            max-width: 700px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px #00171f;
        }
        .calendar {
            margin: auto;
            border-collapse: collapse;
        }
        .calendar th, .calendar td {
            text-align: center;
            font-size: 18px;
            padding: 15px;
        }
        .table-danger { background-color: #00b4d8!important; } /* Rouge pour réservations */
        .table-success { background-color: #ffffff!important; } /* Blanc pour dispo */
        .navigation {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .navigation a {
            text-decoration: none;
            padding: 10px 15px;
            background-color:#a6a6a6;
            color: white;
            border-radius: 5px;
            transition: 0.3s;
        }
        .navigation a:hover {
            background-color:#3e3e3e;
        }
        .form-group {
            margin-bottom: 15px;
        }
        
    </style>
</head>
<!-- Modal d'avertissement -->
<div class="modal fade" id="loginWarning" tabindex="-1" aria-labelledby="warningLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="warningLabel">Attention</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Vous devez être connecté en tant que locataire pour faire une réservation.
            </div>
            <div class="modal-footer">
                <a href="login.php" class="btn btn-primary">Se connecter</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<body>

    <div class="container">
        
    <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
        <h3>Réserver l'appartement</h3>
        </header>

        <!-- Navigation mois et année -->
        <div class="navigation">
            <a href="?id=<?= $id_appartement ?>&year=<?= $year - 1 ?>&month=<?= $month ?>">« Année Préc.</a>
            <a href="?id=<?= $id_appartement ?>&year=<?= $year ?>&month=<?= ($month == 1) ? 12 : ($month - 1) ?>">‹ Mois Préc.</a>
            <span class="fw-bold"><?= $mois_actuel . " " . $year ?></span>
            <a href="?id=<?= $id_appartement ?>&year=<?= $year ?>&month=<?= ($month == 12) ? 1 : ($month + 1) ?>">Mois Suiv. ›</a>
            <a href="?id=<?= $id_appartement ?>&year=<?= $year + 1 ?>&month=<?= $month ?>">Année Suiv. »</a>
        </div>

        <!-- Affichage du calendrier -->
        <?php generate_calendar($month, $year, $reserved_dates); ?>

        <!-- Formulaire de réservation -->
        <div class="mt-4">
            <h3>Effectuer une réservation</h3>
            <form method="POST" action="reserver.php?id=<?= $id_appartement ?>">
                <div class="form-group">
                    <label class="fw-bold">Date de début :</label>
                    <input type="date" name="date_debut" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="fw-bold">Date de fin :</label>
                    <input type="date" name="date_fin" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Réserver</button>
                
            </form>
            
        </div>
    </div>

</body>
</html>
