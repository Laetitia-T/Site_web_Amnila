<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Déposer une annonce</title>
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

                        <li class="sidebar-item ">
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
    <a href="#" class="sidebar-link" onclick="toggleAnnonceMenu(event)">
        <i class="bi bi-calendar-check"></i>
        <span>Déposer une annonce</span>
    </a>
    <ul id="annonceMenu" class="submenu" style="display: none; padding-left: 20px;">
        <li class="sidebar-item">
            <a href="annonce.php" class="sidebar-link">Déposer une annonce - Appartement</a>
        </li>
        <li class="sidebar-item">
            <a href="equipement.php" class="sidebar-link">Déposer une annonce - Équipement</a>
        </li>
    </ul>
</li>

                        <li class="sidebar-item ">
                            <a href="gerer-annonce.php" class='sidebar-link'>
                                <i class="bi bi-calendar-check"></i>
                                <span>Gerer les annonces</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="contrat.php" class="sidebar-link">
                                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                                <span>Mes Contrats</span>
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
            <h2>Déposer une annonce</h2>
            <form action="traiter_annonce.php" method="POST" enctype="multipart/form-data" onsubmit="return validerFormulaire()">
    <div class="mb-3">
        <label for="titre" class="form-label">Titre de l'annonce :</label>
        <input type="text" class="form-control" id="titre" name="titre" placeholder="Charmant appartement en montagne" required>
    </div>
    <div class="mb-3">
        <label for="adresse" class="form-label">Adresse complète :</label>
        <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Rue, Quartier, Numéro..." required>
    </div>
    <div class="mb-3">
        <label for="code_postal" class="form-label">Code Postal :</label>
        <input type="text" class="form-control" id="code_postal" name="code_postal" placeholder="75000" required>
    </div>
    <div class="mb-3">
        <label for="ville" class="form-label">Ville :</label>
        <input type="text" class="form-control" id="ville" name="ville" placeholder="Paris" required>
    </div>
    <div class="mb-3">
        <label for="region" class="form-label">Région :</label>
        <input type="text" class="form-control" id="region" name="region" placeholder="Île-de-France" required>
    </div>
    <div class="mb-3">
        <label for="surface" class="form-label">Surface (m²) :</label>
        <input type="number" class="form-control" id="surface" name="surface" min="1" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description :</label>
        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Grand appartement avec cuisine américaine, 3 chambres..." required></textarea>
    </div>
    <div class="mb-3">
        <label for="prix" class="form-label">Prix par nuit (€) :</label>
        <input type="number" step="0.01" class="form-control" id="prix" name="prix" min="1" required>
    </div>
    <div class="mb-3">
        <label for="capacite" class="form-label">Capacité maximale :</label>
        <input type="number" class="form-control" id="capacite" name="capacite" min="1" required>
    </div>
    <!-- <div class="mb-3">
        <label class="form-label">Équipements :</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="equipements[]" value="WiFi" id="wifi">
            <label class="form-check-label" for="wifi">Wi-Fi</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="equipements[]" value="Parking" id="parking">
            <label class="form-check-label" for="parking">Parking</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="equipements[]" value="Terrasse" id="terrasse">
            <label class="form-check-label" for="terrasse">Terrasse</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="equipements[]" value="Piscine" id="piscine">
            <label class="form-check-label" for="piscine">Piscine</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="equipements[]" value="Cuisine équipée" id="cuisine">
            <label class="form-check-label" for="cuisine">Cuisine équipée</label>
        </div>
    </div> -->
    <div class="mb-3">
        <label for="photos" class="form-label">Ajouter des photos :</label>
        <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
        <small class="text-muted">Formats acceptés : JPG, PNG, GIF</small>
    </div>
    <button type="submit" class="btn btn-primary">Poster l'Annonce</button>
</form>

        </div>
    </div>
</div>

<script>
    function validerFormulaire() {
        let fichiers = document.getElementById('photos').files;
        let extensionsPermises = ['jpg', 'jpeg', 'png', 'gif'];

        for (let i = 0; i < fichiers.length; i++) {
            let ext = fichiers[i].name.split('.').pop().toLowerCase();
            if (!extensionsPermises.includes(ext)) {
                alert("Seuls les fichiers JPG, PNG et GIF sont acceptés.");
                return false;
            }
        }
        return true;
    }
</script>

<script>
    function toggleAnnonceMenu(event) {
        event.preventDefault(); // Empêche le lien de changer de page
        var menu = document.getElementById("annonceMenu");
        menu.style.display = (menu.style.display === "none" || menu.style.display === "") ? "block" : "none";
    }
</script>
</body>
</html>
