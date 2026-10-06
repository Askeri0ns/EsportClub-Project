<?php

require_once __DIR__ . '/../classes/Player.php';

class PlayerDatabase
{
    // Constructeur
    public function __construct(private PDO $pdo)
    {
    }

    // Obtenir un joueur via un identifiant
    public function getById(int $id): ?Player
    {
        $query = $this->pdo->prepare('SELECT * FROM Player WHERE id = :id');
        $query->execute(['id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if (!$data)
        {
            return null;
        }
        return new Player($data['firstname'], $data['lastname'], $data['birthdate'], $data['picture']);
    }

    // Obtenir tous les joueurs
    public function getAll(): array
    {
        $query = $this->pdo->query('SELECT * FROM Player');
        $players = [];

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $data)
        {
            $players[] = new Player(
                $data['firstname'],
                $data['lastname'],
                $data['birthdate'],
                $data['picture']
            );
        }
        return $players;
    }

    // Ajouter un joueur à la base de données
    public function create(Player $player): void
    {
        $query = $this->pdo->prepare('INSERT INTO Player (firstname, lastname, birthdate, picture) VALUES (:firstname, :lastname, :birthdate, :picture)');
        $query->execute([
            'firstname' => $player->getFirstname(),
            'lastname' => $player->getLastname(),
            'birthdate' => $player->getBirthdate(),
            'picture' => $player->getPicture()
        ]);
    }

    // Modifier un joueur de la base de données
    public function update(int $id, Player $player): void
    {
        $query = $this->pdo->prepare('UPDATE Player SET firstname = :firstname, lastname = :lastname, birthdate = :birthdate, picture = :picture WHERE id = :id');
        $query->execute([
            'id' => $id,
            'firstname' => $player->getFirstname(),
            'lastname' => $player->getLastname(),
            'birthdate' => $player->getBirthdate(),
            'picture' => $player->getPicture()
        ]);
    }

    // Supprimer un joueur de la base de données
    public function delete(int $id): void
    {
        $query = $this->pdo->prepare('DELETE FROM Player WHERE id = :id');
        $query->execute(['id' => $id]);
    }
}