<?php
// Connexion à la base de données et traitement
$servername = "localhost";
$dbname = "amnila";
$dbusername = "root";
$dbpassword = "";

// Connexion à la base
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID d'appartement invalide.");
}

$id_appartement = intval($_GET['id']);

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'locataire') {
    die("Vous devez être connecté en tant que locataire.");
}
$id_locataire = $_SESSION['user_id'];

// Traitement de la réservation et récupération des dates réservées
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    if (strtotime($date_debut) > strtotime($date_fin)) {
        $message = "<div class='alert alert-danger'>La date de début doit être antérieure à la date de fin.</div>";
    } else {
        $sql_reservations = "SELECT Date_de_debutR, Date_de_finR FROM reservation WHERE IDappartement = ? AND (
                                (Date_de_debutR BETWEEN ? AND ?) OR (Date_de_finR BETWEEN ? AND ?)
                             )";
        $stmt = $conn->prepare($sql_reservations);
        $stmt->bind_param("issss", $id_appartement, $date_debut, $date_fin, $date_debut, $date_fin);
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

        $available = true;
        for ($i = strtotime($date_debut); $i <= strtotime($date_fin); $i += 86400) {
            if (isset($reserved_dates[date('Y-m-d', $i)])) {
                $available = false;
                break;
            }
        }

        if (!$available) {
            $message = "<div class='alert alert-danger'>Désolé, certaines dates de votre sélection sont déjà réservées.</div>";
        } else {
            $sql_insert = "INSERT INTO reservation (Date_de_debutR, Date_de_finR, StatutR, IDappartement, IDlocataire) 
                           VALUES (?, ?, 'En attente', ?, ?)";
            $stmt = $conn->prepare($sql_insert);
            $stmt->bind_param("ssii", $date_debut, $date_fin, $id_appartement, $id_locataire);

            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>Votre réservation a été effectuée avec succès.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Erreur lors de la réservation. Veuillez réessayer.</div>";
            }

            $stmt->close();
        }
    }
}

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

// Fonction pour générer le calendrier avec un design moderne
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
        $disabled = isset($reserved_dates[$date_str]) ? 'disabled' : '';
        echo "<td class='$class'><button class='btn btn-link' $disabled>$day</button></td>";

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

$mois_francais = [
    1 => "Janvier", 2 => "Février", 3 => "Mars", 4 => "Avril", 
    5 => "Mai", 6 => "Juin", 7 => "Juillet", 8 => "Août", 
    9 => "Septembre", 10 => "Octobre", 11 => "Novembre", 12 => "Décembre"
];

$mois_actuel = $mois_francais[$month] ?? "Mois invalide";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation de l'appartement</title>
    <link href="https://fonts.googleapis.com/css2?family=wght@400;500;600&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Quicksand', sans-serif; 
            background-color: #f0f2f5; 
            text-align: center; 
            padding: 30px;
        }
        .container {
            max-width: 900px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .calendar {
            margin: 20px auto;
            border-collapse: collapse;
        }
        .calendar th, .calendar td {
            text-align: center;
            font-size: 18px;
            padding: 20px;
        }
        .table-danger { background-color: #ffdddd!important; } 
        .table-success { background-color:rgb(255, 255, 255)!important; } 
        .table-dark { background-color: #343a40!important; color: #fff; } 
        .navigation a {
            text-decoration: none;
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            transition: 0.3s;
        }
        .navigation a:hover {
            background-color: #218838;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .btn-return {
            margin-top: 20px;
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .btn-return:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }
        .form-control {
            border-radius: 12px;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .btn-link {
            text-decoration: none;
            color: inherit;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="container">
        <h3 class="mb-4">Réservation de l'appartement</h3>

        <div class="navigation mb-4">
            <a href="?id=<?= $id_appartement ?>&year=<?= $year - 1 ?>&month=<?= $month ?>">« Année Préc.</a>
            <a href="?id=<?= $id_appartement ?>&year=<?= $year ?>&month=<?= ($month == 1) ? 12 : ($month - 1) ?>">‹ Mois Préc.</a>
            <span class="fw-bold"><?= $mois_actuel . " " . $year ?></span>
            <a href="?id=<?= $id_appartement ?>&year=<?= $year ?>&month=<?= ($month == 12) ? 1 : ($month + 1) ?>">Mois Suiv. ›</a>
            <a href="?id=<?= $id_appartement ?>&year=<?= $year + 1 ?>&month=<?= $month ?>">Année Suiv. »</a>
        </div>

        <!-- Affichage du calendrier -->
        <?php generate_calendar($month, $year, $reserved_dates); ?>

        <!-- Formulaire de réservation -->
        <h4>Effectuer une réservation</h4>
        <form method="POST" action="?id=<?= $id_appartement ?>">
            <div class="form-group mb-3">
                <label for="date_debut" class="fw-bold">Date de début :</label>
                <input type="date" name="date_debut" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="date_fin" class="fw-bold">Date de fin :</label>
                <input type="date" name="date_fin" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Réserver</button>
        </form>

        <!-- Message de succès ou d'erreur -->
        <?= isset($message) ? $message : '' ?>

        <!-- Bouton retour vers l'index -->
        <a href="index.php" class="btn btn-return w-100 mt-4">Retour à l'index</a>
    </div>

</body>
</html>
