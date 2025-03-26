<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amnila";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Requête pour récupérer les contrats
$sql = "SELECT IDcontrat , Date_de_signatureC, Date_de_debutC, Date_de_finC, Arrhes_payees, Solde_payee, Caution_versee FROM Contrat";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrats - Gestion des contrats</title>

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
                        <li class="sidebar-item"><a href="index.php" class='sidebar-link'><i class="bi bi-grid-fill"></i><span>Accueil</span></a></li>
                        <li class="sidebar-item"><a href="profil.php" class='sidebar-link'><i class="bi bi-person-circle"></i><span>Mon Profil</span></a></li>
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
                        <li class="sidebar-item active"><a href="contrat.php" class="sidebar-link"><i class="bi bi-file-earmark-spreadsheet-fill"></i><span>Mes Contrats</span></a></li>
                        <li class="sidebar-item">
                            <a href="../../index.php" class="sidebar-link">
                                <i class="bi bi-arrow-left"></i>
                                <span>Retour</span>
                            </a>
                        </li>
                    </ul>
                </div>
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
                            <h3>Mes Contrats</h3>
                            <p class="text-subtitle text-muted">Consultez vos contrats actifs ou inactifs</p>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <span>Liste des Contrats</span>
                            <div>
                                <button id="export" class="btn btn-primary">Exporter CSV</button>
                                <select id="filter-status" class="form-select d-inline-block w-auto">
                                    <option value="">Tous</option>
                                    <option value="Actif">Actif</option>
                                    <option value="Inactif">Inactif</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" id="table1">
                                <thead>
                                    <tr>
                                        <!-- <th>Nom de l'appartement</th> -->
                                        <th>Date de signature</th>
                                        <th>Date de Début</th>
                                        <th>Date de Fin</th>
                                        <th>Solde payée</th>
                                        <th>Caution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            //echo "<td>" . $row["Type_d_appartement"] . "</td>";
                                            echo "<td>" . $row["Date_de_signatureC"] . "</td>";
                                            echo "<td>" . $row["date_de_debutC"] . "</td>";
                                            echo "<td>" . $row["date_de_finC"] . "</td>";
                                            echo "<td>" . $row["Solde_payee"] . "</td>";
                                            echo "<td>" . $row["Caution_versee"] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>Aucun contrat trouvé</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2025 &copy; Votre Entreprise</p>
                    </div>
                    <div class="float-end">
                        <p>Conçu avec <span class="text-danger"><i class="bi bi-heart"></i></span> par Vous</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <script src="assets/vendors/simple-datatables/simple-datatables.js"></script>
    <script>
        // Initialisation du tableau
        const table1 = document.querySelector('#table1');
        const dataTable = new simpleDatatables.DataTable(table1);

        // Exportation des données en CSV
        document.getElementById('export').addEventListener('click', () => {
            dataTable.export({
                type: "csv",
                filename: "mes_contrats",
            });
        });

        // Filtrer les contrats par statut
        document.getElementById('filter-status').addEventListener('change', (event) => {
            const filter = event.target.value;
            dataTable.rows().remove(); // Vider le tableau

            // Ajouter les lignes filtrées
            dataTable.rows().add([...table1.querySelectorAll('tbody tr')].filter(row => 
                !filter || row.cells[4].textContent.trim() === filter
            ).map(row => row.outerHTML));
        });
    </script>

<script>
    function toggleAnnonceMenu(event) {
        event.preventDefault(); // Empêche le lien de changer de page
        var menu = document.getElementById("annonceMenu");
        menu.style.display = (menu.style.display === "none" || menu.style.display === "") ? "block" : "none";
    }
</script>
    <script src="assets/js/main.js"></script>
</body>

</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
