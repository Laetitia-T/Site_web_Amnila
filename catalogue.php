<?php
$servername = "localhost";
$dbname = "amnila";
$username = "root";
$password = "";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupération des annonces
$sql = "SELECT * FROM Appartement";
$result = $conn->query($sql);
$annonces = [];

// Récupération des équipements
$sql_equipements = "SELECT * FROM equipement";
$result_equipements = $conn->query($sql_equipements);
$equipements = [];

if ($result_equipements->num_rows > 0) {
    while ($row = $result_equipements->fetch_assoc()) {
        $equipements[] = $row;
    }
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['IDappartement'];

        // Récupération de la première photo associée
        $stmt_photos = $conn->prepare("SELECT Chemin FROM Photo WHERE IDappartement = ? LIMIT 1");
        $stmt_photos->bind_param("i", $id);
        $stmt_photos->execute();
        $result_photos = $stmt_photos->get_result();
        
        $photo = $result_photos->fetch_assoc();
        $row['photo'] = $photo ? $photo['Chemin'] : 'img/default.jpg'; // Image par défaut si aucune photo trouvée

        // Vérification de la disponibilité de l'appartement
        // On vérifie les réservations et on met à jour le statut
        $stmt_reservation = $conn->prepare("SELECT * FROM Reservation WHERE IDappartement = ? AND CURRENT_DATE BETWEEN Date_de_debutR AND Date_de_finR");
        $stmt_reservation->bind_param("i", $id);
        $stmt_reservation->execute();
        $result_reservation = $stmt_reservation->get_result();

        // Si une réservation est trouvée, l'appartement est occupé
        // if ($result_reservation->num_rows > 0) {
        //     $row['StatusR'] = 'Occupé';
        // } else {
        //     $row['StatusR'] = 'Disponible';
        // }

        $annonces[] = $row;
    }
}

// Suppression automatique des réservations expirées
$sql_delete_expired_reservations = "DELETE FROM Reservation WHERE Date_de_finR < CURRENT_DATE";
$conn->query($sql_delete_expired_reservations);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue des Appartements</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="lib/owlcarousel/owl.carousel.min.css">
    <link rel="stylesheet" href="lib/owlcarousel/owl.theme.default.min.css">
    <style>
        .blog-item {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
        }
        .blog-img img {
            border-radius: 10px;
            height: 250px;
            object-fit: cover;
        }
        .blog-title a {
            background: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        .custom-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
        }
        .custom-nav .owl-prev, .custom-nav .owl-next {
            background: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 10px;
            border-radius: 50%;
        }
        .occupied {
            background-color: red;
            color: white;
        }
        .available {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Catalogue</h4>
        <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
            <li class="breadcrumb-item active text-primary">Catalogue</li>
        </ol>    
    </div>
</div>
<!-- Header End -->

<!-- Catalogue Start -->
<div class="container-fluid blog py-5">
    <div class="container py-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
            <h4 class="text-primary">Notre Catalogue</h4>
            <h1 class="display-5 mb-4">Votre logement idéal, à prix juste</h1>
            <p class="mb-0">Des logements de qualité, accessibles à tous les budgets. Trouvez votre confort à prix juste, sans compromis. 
                Profitez d'une location simple et adaptée à vos besoins.
            </p>
        </div>
        
        <div class="position-relative">
        <div class="owl-dots.disabled"></div>
            <div class="owl-carousel blog-carousel wow fadeInUp" data-wow-delay="0.2s">
                <?php foreach ($annonces as $annonce): ?>
                    <div class="blog-item p-4">
                        <div class="blog-img mb-4">
                            <img src="<?= htmlspecialchars($annonce['photo']) ?>" class="img-fluid w-100 rounded" alt="">
                            <!-- <div class="blog-title">
                                <a href="#" class="btn <?= $annonce['StatusR'] == 'Occupé' ? 'occupied' : 'available' ?>">
                                    <?= $annonce['StatusR'] ?>
                                </a>
                            </div> -->
                        </div>
                        <a href="description-produit.php?id=<?= $annonce['IDappartement'] ?>" class="h4 d-inline-block mb-3"><?= htmlspecialchars($annonce['Type_d_appartementA']) ?> - <?= htmlspecialchars($annonce['RueA']) ?></a>
                        <!-- <p class="mb-4"><?= htmlspecialchars($annonce['Description']) ?></p> -->
                        <div class="d-flex align-items-center">
                            <div class="d-flex justify-content-center justify-content-md-end flex-shrink-0 mb-4">
                                <a class="btn btn-primary rounded-pill py-2 px-4 px-md-3 ms-2" href="description-appartement.php?id=<?= $annonce['IDappartement'] ?>">Découvrir ici</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>


<!-- Catalogue End -->

<?php include 'footer.php'; ?>

<!-- Back to Top -->
<a href="#" class="btn btn-primary btn-lg-square rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>

<!-- JavaScript Libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>

<!-- Script Owl Carousel -->
<script>
    $(document).ready(function(){
        $(".owl-carousel").owlCarousel({
            loop: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            nav: true,
            dots: false,
            margin: 20,
            responsive:{
                0: { items: 1 }, // Mobile
                600: { items: 2 }, // Tablette
                1000: { items: 3 } // Desktop
            },
            navText: ["<", ">"]
        });
    });
</script>

</body>
</html>
