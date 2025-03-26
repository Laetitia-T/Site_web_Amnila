<?php
// Paramètres de la base de données
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

$error = "";
$success = "";

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des valeurs du formulaire
    $name = trim($_POST['name']);
    $firstname = trim($_POST['firstname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone = $_POST['phone'];  // Ajout de la récupération du téléphone
    $role = $_POST['role'];
    $rib = isset($_POST['rib']) ? trim($_POST['rib']) : '';
 

    // Vérification si l'email existe déjà dans la table users
    $query = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $query->execute([$email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $error = "Cet email est déjà utilisé.";
    } else {
        // Hachage du mot de passe
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Insertion dans la table users
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $password_hashed, $role]);

        // Récupérer l'ID de l'utilisateur nouvellement inséré
        $userId = $pdo->lastInsertId();

        // Insertion dans la table spécifique en fonction du rôle
        if ($role == "proprietaire") {
            if (empty($rib)) {
                $error = "Le RIB est obligatoire pour un propriétaire.";
            } else {
                // Préparation de la requête pour le propriétaire
                $stmt = $pdo->prepare("INSERT INTO Proprietaire (IDproprietaire, NomP, PrenomP, Adresse_email_P, PasswordP , TelephoneP, RIB) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $name, $firstname, $email, $password_hashed, $phone, $rib]);
                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            }
        } elseif ($role == "locataire") {
            // Préparation de la requête pour le locataire
            $stmt = $pdo->prepare("INSERT INTO Locataire (IDlocataire, NomL, PrenomL, Adresse_email_L, PasswordL, TelephoneL) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $name, $firstname, $email, $password_hashed, $phone]);
            $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        } else {
            $error = "Rôle invalide.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #00b4d8;
            border: none;
        }
        .btn-primary:hover {
            background-color: rgb(3, 153, 183);
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #00b4d8;
        }
    </style>
</head>
<body>

    <div class="card p-4">
        <h2 class="text-center mb-4">Inscription</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="Votre nom">
            </div>
            <div class="mb-3">
                <label for="firstname" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="firstname" name="firstname" required placeholder="Votre prénom">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="Votre email">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Votre mot de passe">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Téléphone</label>
                <input type="text" class="form-control mb-3" id="phone" name="phone" required placeholder="Votre téléphone">
            </div>
            <div id="ribField" style="display: none;">
                <label for="rib" class="form-label">RIB (obligatoire pour les propriétaires)</label>
                <input type="text" name="rib" id="rib" class="form-control mb-3" placeholder="RIB (obligatoire pour les propriétaires)">
            </div>
           
            <div class="mb-3">
                <label for="role" class="form-label">Rôle</label>
                <select class="form-select" id="role" name="role" required onchange="toggleRIBField()">
                    <option value="locataire">Locataire</option>
                    <option value="proprietaire">Propriétaire</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
        </form>

        <?php if (!empty($error)) : ?>
            <div class="mt-3 text-danger text-center"><?= $error ?></div>
        <?php elseif (!empty($success)) : ?>
            <div class="mt-3 text-success text-center"><?= $success ?></div>
        <?php endif; ?>
        
        <div class="mt-3 text-center">
            <a href="login.php" class="text-decoration-none">Déjà un compte ? Connectez-vous</a>
        </div>
    </div>

    <script>
        function toggleRIBField() {
            var role = document.getElementById("role").value;
            var ribField = document.getElementById("ribField");
            if (role === "proprietaire") {
                ribField.style.display = "block";
            } else {
                ribField.style.display = "none";
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
