<?php

require_once __DIR__ . '/../classes/OpposingClub.php';

class OpposingClubDatabase
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('sqlite:' . __DIR__ . '/E-sportsClub.db');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAll(): array
    {
        $statement = $this->pdo->query('SELECT name, address, city FROM Opposing_Club');
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $clubs = [];
        foreach ($rows as $row)
        {
            $clubs[] = new OpposingClub(
                $row['name'],
                $row['address'],
                $row['city']
            );
        }

        return $clubs;
    }

    public function insert(OpposingClub $club): void
    {
        $sql = 'INSERT INTO Opposing_Club (name, address, city) VALUES (:name, :address, :city)';
        $stmt = $this->pdo->prepare($sql);

        // On protège les données avec des paramètres préparés
        $stmt->execute([
            ':name'    => $club->getName(),
            ':address' => $club->getAddress(),
            ':city'    => $club->getCity()
        ]);
    }
}