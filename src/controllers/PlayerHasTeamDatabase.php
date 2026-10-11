<?php

require_once __DIR__ . '/../classes/PlayerHasTeam.php';
require_once __DIR__ . '/../classes/Player.php';
require_once __DIR__ . '/../classes/Team.php';

class PlayerHasTeamDatabase
{
    // Requête de base : une association avec les informations du joueur
    private const SELECT = 'SELECT p.firstname, p.lastname, p.birthdate, p.picture, pht.team_name, pht.role
                            FROM Player_has_Team pht
                            JOIN Player p ON p.id = pht.player_id';

    // Constructeur
    public function __construct(private PDO $pdo)
    {
    }

    // Transformer une ligne de la base en objet PlayerHasTeam
    private function hydrate(array $data): PlayerHasTeam
    {
        return new PlayerHasTeam(
            new Player($data['firstname'], $data['lastname'], new DateTime($data['birthdate']), $data['picture']),
            new Team($data['team_name']),
            PlayerRole::from($data['role'])
        );
    }

    // Obtenir toutes les associations d'un joueur (via son identifiant)
    public function getByPlayer(int $playerId): array
    {
        $query = $this->pdo->prepare(self::SELECT . ' WHERE pht.player_id = :player_id');
        $query->execute(['player_id' => $playerId]);
        $associations = [];

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $data)
        {
            $associations[] = $this->hydrate($data);
        }
        return $associations;
    }

    // Obtenir toutes les associations d'une équipe (via son nom)
    public function getByTeam(string $teamName): array
    {
        $query = $this->pdo->prepare(self::SELECT . ' WHERE pht.team_name = :team_name');
        $query->execute(['team_name' => $teamName]);
        $associations = [];

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $data)
        {
            $associations[] = $this->hydrate($data);
        }
        return $associations;
    }

    // Obtenir toutes les associations
    public function getAll(): array
    {
        $query = $this->pdo->query(self::SELECT);
        $associations = [];

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $data)
        {
            $associations[] = $this->hydrate($data);
        }
        return $associations;
    }

    // Ajouter un joueur à une équipe
    public function create(int $playerId, PlayerHasTeam $association): void
    {
        $query = $this->pdo->prepare('INSERT INTO Player_has_Team (player_id, team_name, role) VALUES (:player_id, :team_name, :role)');
        $query->execute([
            'player_id' => $playerId,
            'team_name' => $association->getTeam()->getName(),
            'role' => $association->getRole()
        ]);
    }

    // Modifier l'association d'un joueur avec une équipe
    public function update(int $playerId, string $teamName, PlayerHasTeam $association): void
    {
        $query = $this->pdo->prepare('UPDATE Player_has_Team SET team_name = :new_team_name, role = :role WHERE player_id = :player_id AND team_name = :team_name');
        $query->execute([
            'player_id' => $playerId,
            'team_name' => $teamName,
            'new_team_name' => $association->getTeam()->getName(),
            'role' => $association->getRole()
        ]);
    }

    // Retirer un joueur d'une équipe
    public function delete(int $playerId, string $teamName): void
    {
        $query = $this->pdo->prepare('DELETE FROM Player_has_Team WHERE player_id = :player_id AND team_name = :team_name');
        $query->execute([
            'player_id' => $playerId,
            'team_name' => $teamName
        ]);
    }
}
