<?php

require_once __DIR__ . '/../classes/PlayerHasTeam.php';
require_once __DIR__ . '/../classes/Player.php';
require_once __DIR__ . '/../classes/Team.php';

class PlayerHasTeamDatabase
{
    private PDO $pdo;

    public function __construct()
    {
        // Connexion à la base SQLite
        $this->pdo = new PDO('sqlite:' . __DIR__ . '/E-sportsClub.db');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Enregistre l'association entre un joueur et une équipe
     */
    public function insert(int $playerId, string $teamName): void
    {
        $sql = 'INSERT INTO Player_has_Team (player_id, team_name) VALUES (:player_id, :team_name)';
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':player_id' => $playerId,
            ':team_name' => $teamName,
        ]);
    }

    /**
     * Récupère tous les joueurs d'une équipe précise
     */
    public function getPlayersByTeam(string $teamName): array
    {
        // On fait une jointure (JOIN) pour récupérer les vraies infos du joueur
        $sql = 'SELECT p.id, p.firstname, p.lastname, p.birthdate, p.picture
                FROM Player p
                JOIN Player_has_Team pht ON p.id = pht.player_id
                WHERE pht.team_name = :team_name';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':team_name' => $teamName]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $players = [];
        foreach ($rows as $row) {
            $players[] = new Player(
                $row['firstname'],
                $row['lastname'],
                new DateTime($row['birthdate']),
                $row['picture']
            );
        }

        return $players;
    }
}