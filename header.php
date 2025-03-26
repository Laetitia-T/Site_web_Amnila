<?php
session_start(); // Démarrer la session

// Vérifiez si l'utilisateur est connecté
$isUserLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Neige & Soleil</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Chargement...</span>
        </div>
    </div> -->
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <div class="container-fluid topbar bg-light px-5 d-none d-lg-block">
        <div class="row gx-0 align-items-center">
            <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                <div class="d-flex flex-wrap">
                    <a href="contact.php" class="text-muted small me-4"><i class="fas fa-map-marker-alt text-primary me-2"></i>Notre Emplacement</a>
                    <a href="tel:+01234567890" class="text-muted small me-4"><i class="fas fa-phone-alt text-primary me-2"></i>+01234567890</a>
                    <a href="mailto:example@gmail.com" class="text-muted small me-0"><i class="fas fa-envelope text-primary me-2"></i>Example@gmail.com</a>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <?php if (!$isUserLoggedIn): ?>
                    <!-- L'utilisateur n'est pas connecté -->
                        <a href="register.php"><small class="me-3 text-dark"><i class="fa fa-user text-primary me-2"></i>Inscription</small></a>
                        <a href="login.php"><small class="me-3 text-dark"><i class="fa fa-sign-in-alt text-primary me-2"></i>Se connecter</small></a>
                    <?php else: ?>
                        <!-- L'utilisateur est connecté -->
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <a href="compte/admin/profil.php"><small class="me-3 text-dark"><i class="fa fa-user text-primary me-2"></i>Mon profil</small></a>
                        <?php elseif ($_SESSION['role'] == 'locataire'): ?>
                            <a href="compte/locataire/profil.php"><small class="me-3 text-dark"><i class="fa fa-user text-primary me-2"></i>Mon profil</small></a>
                        <?php elseif ($_SESSION['role'] == 'proprietaire'): ?>
                            <a href="compte/proprietaire/profil.php"><small class="me-3 text-dark"><i class="fa fa-user text-primary me-2"></i>Mon profil</small></a>
                        <?php endif; ?>
                        <a href="logout.php" class="text-dark"><small><i class="fas fa-power-off text-primary me-2"></i>Déconnexion</small></a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar & Hero Start -->
    <div class="container-fluid position-relative p-0">
    <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0" >
        <a href="" class="navbar-brand p-0">
            <h1 class="text-primary">
                <img src="img/logo-n&s.webp" alt="Logo">
            </h1>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="fa fa-bars" ></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto py-0">
                <a href="index.php" class="nav-item nav-link">Accueil</a>
                <a href="about.php" class="nav-item nav-link">À propos</a>
                <a href="service.php" class="nav-item nav-link">Services</a>
                <a href="catalogue.php" class="nav-item nav-link">Catalogue</a>
                <a href="contact.php" class="nav-item nav-link">Contact</a>
            </div>
        </div>
    </nav>
</div>
    <!-- Navbar & Hero End -->

    <!-- Ajouter votre contenu ici -->
</body>

</html>
