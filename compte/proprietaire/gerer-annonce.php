<?php
session_start();

$servername = "localhost";
$dbname = "amnila";
$dbusername = "root";
$dbpassword = "";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

if (!isset($_SESSION['IDproprietaire']) || empty($_SESSION['IDproprietaire'])) {
    die("Erreur : ID du propriétaire non défini.");
}

$IDproprietaire = intval($_SESSION['IDproprietaire']);

$sql = "SELECT * FROM appartement WHERE IDproprietaire = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $IDproprietaire);
$stmt->execute();
$result = $stmt->get_result();
$appartements = [];
while ($row = $result->fetch_assoc()) {
    $appartements[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Appartements</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

                        <li class="sidebar-item active ">
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
    <h2>Gestion de mes appartements</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Adresse</th>
                <th>Prix (€)</th>
                <th>Surface</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($appartements as $appartement) { ?>
                <tr>
                    <td><?= $appartement['Type_d_appartementA'] ?></td>
                    <td><?= $appartement['RueA'] . ', ' . $appartement['VilleA'] . ', ' . $appartement['Code_PostalA'] ?></td>
                    <td><?= $appartement['Prix_journalier'] ?></td>
                    <td><?= $appartement['SurfaceA'] ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                            data-id="<?= $appartement['IDappartement'] ?>" 
                            data-type="<?= $appartement['Type_d_appartementA'] ?>"
                            data-rue="<?= $appartement['RueA'] ?>"
                            data-ville="<?= $appartement['VilleA'] ?>"
                            data-code="<?= $appartement['Code_PostalA'] ?>"
                            data-surface="<?= $appartement['SurfaceA'] ?>"
                            data-prix="<?= $appartement['Prix_journalier'] ?>">Modifier</button>
                        <a href="gerer-annonces.php?delete_id=<?= $appartement['IDappartement'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal de modification -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Modifier Appartement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" action="edit_annonce.php">
                    <input type="hidden" name="IDappartement" id="editID">
                    <div class="mb-3">
                        <label for="editType" class="form-label">Type d'Appartement</label>
                        <input type="text" class="form-control" id="editType" name="type_appartement">
                    </div>
                    <div class="mb-3">
                        <label for="editRue" class="form-label">Rue</label>
                        <input type="text" class="form-control" id="editRue" name="rue">
                    </div>
                    <div class="mb-3">
                        <label for="editVille" class="form-label">Ville</label>
                        <input type="text" class="form-control" id="editVille" name="ville">
                    </div>
                    <div class="mb-3">
                        <label for="editCode" class="form-label">Code Postal</label>
                        <input type="text" class="form-control" id="editCode" name="code_postal">
                    </div>
                    <div class="mb-3">
                        <label for="editSurface" class="form-label">Surface</label>
                        <input type="text" class="form-control" id="editSurface" name="surface">
                    </div>
                    <div class="mb-3">
                        <label for="editPrix" class="form-label">Prix Journalier</label>
                        <input type="text" class="form-control" id="editPrix" name="prix_journalier">
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $('#editID').val(button.data('id'));
        $('#editType').val(button.data('type'));
        $('#editRue').val(button.data('rue'));
        $('#editVille').val(button.data('ville'));
        $('#editCode').val(button.data('code'));
        $('#editSurface').val(button.data('surface'));
        $('#editPrix').val(button.data('prix'));
    });
});
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
