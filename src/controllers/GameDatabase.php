<?php

require_once __DIR__ . '/../classes/Team.php';
require_once __DIR__ . '/../classes/OpposingClub.php';
require_once __DIR__ . '/../classes/Matchs.php';

class GameDatabase
{
    // 1. On garde la connexion PDO dans un attribut privé
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('sqlite:' . __DIR__ . '/E-sportsClub.db');

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // 4. "getter"
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}