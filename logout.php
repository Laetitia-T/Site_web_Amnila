<?php
session_start();

// Supprime toutes les variables de session
$_SESSION = [];

// Détruit la session
session_destroy();

// Redirige vers la page de connexion ou d'accueil
header("Location: index.php"); // Remplace "login.php" par la page de ton choix
exit();
?>