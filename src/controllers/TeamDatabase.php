<?php

namespace src\controllers;

use PDO;
use src\classes\Team;

class TeamDatabase
{
    // Constructeur
    public function __construct(private PDO $pdo)
    {
    }

    // Transformer une ligne de la base en objet Team
    private function hydrate(array $data): Team
    {
        return new Team($data['name']);
    }

    // Obtenir une équipe via un identifiant
    public function getById(int $id): ?Team
    {
        $query = $this->pdo->prepare('SELECT * FROM Team WHERE id = :id');
        $query->execute(['id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if (!$data)
        {
            return null;
        }
        return $this->hydrate($data);
    }

    // Obtenir toutes les équipes
    public function getAll(): array
    {
        $query = $this->pdo->query('SELECT * FROM Team');
        $teams = [];

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $data)
        {
            $teams[] = $this->hydrate($data);
        }
        return $teams;
    }

    // Ajouter une équipe à la base de données
    public function create(Team $team): void
    {
        $query = $this->pdo->prepare('INSERT INTO Team (name) VALUES (:name)');
        $query->execute([
            'name' => $team->getName()
        ]);
    }

    // Modifier une équipe de la base de données
    public function update(int $id, Team $team): void
    {
        $query = $this->pdo->prepare('UPDATE Team SET name = :name WHERE id = :id');
        $query->execute([
            'id' => $id,
            'name' => $team->getName()
        ]);
    }

    // Supprimer une équipe de la base de données
    public function delete(int $id): void
    {
        $query = $this->pdo->prepare('DELETE FROM Team WHERE id = :id');
        $query->execute(['id' => $id]);
    }
}
