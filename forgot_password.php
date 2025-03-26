<?php
session_start();
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    // Vérification si l'email existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Sauvegarde du token
        $stmt = $pdo->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE email=?");
        $stmt->execute([$token, $expires, $email]);

        // Envoi de l'email avec le lien
        $reset_link = "http://localhost/reset_password.php?token=$token";
        mail($email, "Réinitialisation du mot de passe", "Cliquez ici : $reset_link");

        $_SESSION['message'] = "Un email de réinitialisation a été envoyé.";
    } else {
        $_SESSION['error'] = "Aucun compte trouvé avec cet email.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head> <style>
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
        .button {
            background-color: #00b4d8;
            border: none;
        }
        .button:hover {
            background-color: rgb(3, 153, 183);
        }
        .button:focus {
            box-shadow: none;
            border-color: #00b4d8;
        }
    </style>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4" style="max-width: 400px;">
        <h2 class="text-center">Mot de passe oublié</h2>
        <?php if (!empty($_SESSION['message'])) : ?>
            <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])) : ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Email</label>
                <br>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Envoyer</button>
        </form>
    </div>
</body>
</html>
