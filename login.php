<?php
session_start();

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Vérification de l'email
    $query = $pdo->prepare("SELECT id, name, firstname, role, email, password FROM users WHERE email = ?");
    $query->execute([$email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Stocker les informations de session pour chaque rôle
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
    
        // Stocker l'ID en fonction du rôle
        $_SESSION['IDlocataire'] = $user['role'] === 'locataire' ? $user['id'] : null;
        $_SESSION['IDproprietaire'] = $user['role'] === 'proprietaire' ? $user['id'] : null;
        $_SESSION['IDadministrateur'] = $user['role'] === 'admin' ? $user['id'] : null;
    
        // Redirection selon le rôle
        switch ($user['role']) {
            case 'admin':
            case 'proprietaire':
            case 'locataire':
                // Redirection vers index.php après connexion, peu importe le rôle
                header('Location: index.php');
                break;
            default:
                $error = "Rôle inconnu. Contactez l'administrateur.";
                break;
        }
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
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
        <h2 class="text-center mb-4">Connexion</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Adresse Email</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="Entrez votre email">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de Passe</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Entrez votre mot de passe">
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
        <div class="mt-3 text-center">
            <a href="register.php" class="text-decoration-none">Créer un compte</a>
        </div>
        <div class="mt-2 text-center">
    <a href="forgot_password.php" class="text-decoration-none">Mot de passe oublié ?</a>
</div>

        <?php if (!empty($error)) : ?>
            <div class="mt-3 text-danger text-center"><?= $error ?></div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
