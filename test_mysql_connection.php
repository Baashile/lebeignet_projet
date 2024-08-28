<?php

if ($argc !== 5) {
    echo "Usage: php test_mysql_connection.php <host> <username> <password> <database>\n";
    exit(1);
}
$host = $argv[1];
$user = $argv[2];
$pass = $argv[3];
$db = $argv[4];
$charset = 'utf8mb4'; 

$dsn = "mysql:host=$host;dbname=$db;"; // charset=$charset
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connexion réussie à la base de données MySQL." . PHP_EOL;
} catch (PDOException $e) {echo "Erreur de connexion à la base de données MySQL: " . $e->getMessage() . PHP_EOL;}
?>