<?php

$databasePath = getenv('ESPORTCLUB_DB_PATH') ?: __DIR__ . '/EsportsClub.db';

$pdo = new PDO('sqlite:' . $databasePath, options: [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$pdo->exec('PRAGMA foreign_keys = ON');

return $pdo;
