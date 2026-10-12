<?php

namespace src\controllers;

use DateTime, PDO;
use src\classes\Game;
use src\classes\Team;
use src\classes\OpposingClub;

class GameDatabase
{
    // Requête de base : un match avec les informations de son club adverse
    private const SELECT = 'SELECT g.team_score, g.opponent_score, g.team_name, g.date, g.city,
                                   oc.name AS club_name, oc.address AS club_address, oc.city AS club_city
                            FROM Game g
                            JOIN Opposing_Club oc ON oc.name = g.opposing_club_name';

    // Constructeur
    public function __construct(private PDO $pdo)
    {
    }

    // Transformer une ligne de la base en objet Game
    private function hydrate(array $data): Game
    {
        return new Game(
            $data['team_score'],
            $data['opponent_score'],
            new Team($data['team_name']),
            new OpposingClub($data['club_name'], $data['club_address'], $data['club_city']),
            new DateTime($data['date']),
            $data['city']
        );
    }

    // Obtenir un match via un identifiant
    public function getById(int $id): ?Game
    {
        $query = $this->pdo->prepare(self::SELECT . ' WHERE g.id = :id');
        $query->execute(['id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if (!$data)
        {
            return null;
        }
        return $this->hydrate($data);
    }

    // Obtenir tous les matchs
    public function getAll(): array
    {
        $query = $this->pdo->query(self::SELECT);
        $games = [];

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $data)
        {
            $games[] = $this->hydrate($data);
        }
        return $games;
    }

    // Ajouter un match à la base de données
    public function create(Game $game): void
    {
        $query = $this->pdo->prepare('INSERT INTO Game (team_score, opponent_score, team_name, opposing_club_name, date, city) VALUES (:team_score, :opponent_score, :team_name, :opposing_club_name, :date, :city)');
        $query->execute([
            'team_score' => $game->getTeamScore(),
            'opponent_score' => $game->getOpponentScore(),
            'team_name' => $game->getTeam()->getName(),
            'opposing_club_name' => $game->getOpposingClub()->getName(),
            'date' => $game->getDate()->format('Y-m-d H:i:s'),
            'city' => $game->getCity()
        ]);
    }

    // Modifier un match de la base de données
    public function update(int $id, Game $game): void
    {
        $query = $this->pdo->prepare('UPDATE Game SET team_score = :team_score, opponent_score = :opponent_score, team_name = :team_name, opposing_club_name = :opposing_club_name, date = :date, city = :city WHERE id = :id');
        $query->execute([
            'id' => $id,
            'team_score' => $game->getTeamScore(),
            'opponent_score' => $game->getOpponentScore(),
            'team_name' => $game->getTeam()->getName(),
            'opposing_club_name' => $game->getOpposingClub()->getName(),
            'date' => $game->getDate()->format('Y-m-d H:i:s'),
            'city' => $game->getCity()
        ]);
    }

    // Supprimer un match de la base de données
    public function delete(int $id): void
    {
        $query = $this->pdo->prepare('DELETE FROM Game WHERE id = :id');
        $query->execute(['id' => $id]);
    }
}
