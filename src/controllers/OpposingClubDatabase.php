<?php

require_once __DIR__ . '/../classes/OpposingClub.php';

class OpposingClubDatabase
{
    // Constructeur
    public function __construct(private PDO $pdo)
    {
    }

    // Transformer une ligne de la base en objet OpposingClub
    private function hydrate(array $data): OpposingClub
    {
        return new OpposingClub(
            $data['name'],
            $data['address'],
            $data['city']
        );
    }

    // Obtenir un club adverse via un identifiant
    public function getById(int $id): ?OpposingClub
    {
        $query = $this->pdo->prepare('SELECT * FROM Opposing_Club WHERE id = :id');
        $query->execute(['id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if (!$data)
        {
            return null;
        }
        return $this->hydrate($data);
    }

    // Obtenir tous les clubs adverses
    public function getAll(): array
    {
        $query = $this->pdo->query('SELECT * FROM Opposing_Club');
        $clubs = [];

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $data)
        {
            $clubs[] = $this->hydrate($data);
        }
        return $clubs;
    }

    // Ajouter un club adverse à la base de données
    public function create(OpposingClub $club): void
    {
        $query = $this->pdo->prepare('INSERT INTO Opposing_Club (name, address, city) VALUES (:name, :address, :city)');
        $query->execute([
            'name' => $club->getName(),
            'address' => $club->getAddress(),
            'city' => $club->getCity()
        ]);
    }

    // Modifier un club adverse de la base de données
    public function update(int $id, OpposingClub $club): void
    {
        $query = $this->pdo->prepare('UPDATE Opposing_Club SET name = :name, address = :address, city = :city WHERE id = :id');
        $query->execute([
            'id' => $id,
            'name' => $club->getName(),
            'address' => $club->getAddress(),
            'city' => $club->getCity()
        ]);
    }

    // Supprimer un club adverse de la base de données
    public function delete(int $id): void
    {
        $query = $this->pdo->prepare('DELETE FROM Opposing_Club WHERE id = :id');
        $query->execute(['id' => $id]);
    }
}
