<?php

require_once __DIR__ . '/../classes/Team.php';

class TeamDatabase
{
    // Constructeur
    public function __construct(private PDO $pdo)
    {
    }

    // Obtenir une équipe via un identifiant
    public function getById(int $id): ?Team
    {
        $query = $this->pdo->prepare('SELECT * FROM team WHERE id = :id');
        $query->execute(['id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if (!$data)
        {
            return null;
        }
        return new Team($data['name']);
    }

    // Obtenir toutes les équipes
    public function getAll(): array
    {
        $query = $this->pdo->query('SELECT * FROM team');
        $teams = [];
        while ($data = $query->fetch(PDO::FETCH_ASSOC))
        {
            $teams[] = new Team($data['name']);
        }
        return $teams;
    }

    // Ajouter une équipe à la base de données
    public function create(Team $team): void
    {
        $query = $this->pdo->prepare('INSERT INTO team (name) VALUES (:name)');
        $query->execute(['name' => $team->getName()]);
    }

    // Modifier une équipe de la base de données
    public function update(int $id, Team $team): void
    {
        $query = $this->pdo->prepare('UPDATE team SET name = :name WHERE id = :id');
        $query->execute(['id' => $id, 'name' => $team->getName()]);
    }

    // Supprimer une équipe de la base de données
    public function delete(int $id): void
    {
        $query = $this->pdo->prepare('DELETE FROM team WHERE id = :id');
        $query->execute(['id' => $id]);
    }
}