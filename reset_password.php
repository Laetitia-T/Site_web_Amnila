<?php
session_start();
require "config.php"; // Connexion à la base

if (isset($_GET["token"])) {
    $token = $_GET["token"];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token=? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = password_hash($_POST["password"], PASSWORD_DEFAULT);
            
            // Mise à jour du mot de passe et suppression du token
            $stmt = $pdo->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
            $stmt->execute([$new_password, $user["id"]]);

            $_SESSION['message'] = "Votre mot de passe a été mis à jour.";
            header("Location: connexion.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Lien invalide ou expiré.";
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Token manquant.";
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation du mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4" style="max-width: 400px;">
        <h2 class="text-center">Nouveau mot de passe</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Nouveau mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Réinitialiser</button>
        </form>
    </div>
</body>
</html>
