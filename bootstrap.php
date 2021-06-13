<?php

// Active la session (utilisée par la classe Security/Firewall)
session_start();

// On créé une constante représentant la racine du projet
const PROJECT_ROOT = __DIR__;

// Auto-chargement de classes
spl_autoload_register(function ($class) {
    require __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';
});

// Configuration
const NOTIFY_EMAIL = 'notify@example.org';

// Connection à la BDD
$dsn  = 'mysql:dbname=php-oo-seacloud;host=localhost;port=12221';
$user = 'php-oo-seacloud';
$pwd  = 'php-oo-seacloud';

$connection = new PDO($dsn, $user, $pwd, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
]);

// Charge des fonctions d'aide
require PROJECT_ROOT . '/includes/helper.php';
