<?php

// Récupération du chemin de la base de données :
$databasePath = isset($_ENV['DB_PATH']) 
    ? __DIR__ . '/../' . $_ENV['DB_PATH'] 
    : __DIR__ . '/EsportsClub.db';

// Création de l'objet PDO (la connexion à SQLite) :
$pdo = new PDO('sqlite:' . $databasePath, options: [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Activation des clés étrangères sous SQLite (désactivées par défaut sur ce moteur)
$pdo->exec('PRAGMA foreign_keys = ON');

return $pdo;