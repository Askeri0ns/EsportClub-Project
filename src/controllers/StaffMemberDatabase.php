<?php

require_once __DIR__ . '/../classes/StaffMember.php';

class StaffMemberDatabase
{
    // Constructeur
    public function __construct(private PDO $pdo)
    {
    }

    // Transformer une ligne de la base en objet StaffMember
    private function hydrate(array $data): StaffMember
    {
        return new StaffMember(
            $data['firstname'],
            $data['lastname'],
            new DateTime($data['birthdate']),
            StaffRole::from($data['role']),
            $data['picture']
        );
    }

    // Obtenir un membre du staff via un identifiant
    public function getById(int $id): ?StaffMember
    {
        $query = $this->pdo->prepare('SELECT * FROM Staff_Member WHERE id = :id');
        $query->execute(['id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if (!$data)
        {
            return null;
        }
        return $this->hydrate($data);
    }

    // Obtenir tous les membres du staff
    public function getAll(): array
    {
        $query = $this->pdo->query('SELECT * FROM Staff_Member');
        $staffMembers = [];

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $data)
        {
            $staffMembers[] = $this->hydrate($data);
        }
        return $staffMembers;
    }

    // Ajouter un membre du staff à la base de données
    public function create(StaffMember $staffMember): void
    {
        $query = $this->pdo->prepare('INSERT INTO Staff_Member (firstname, lastname, birthdate, role, picture) VALUES (:firstname, :lastname, :birthdate, :role, :picture)');
        $query->execute([
            'firstname' => $staffMember->getFirstname(),
            'lastname' => $staffMember->getLastname(),
            'birthdate' => $staffMember->getBirthdate()->format('Y-m-d'),
            'role' => $staffMember->getRole(),
            'picture' => $staffMember->getPicture()
        ]);
    }

    // Modifier un membre du staff de la base de données
    public function update(int $id, StaffMember $staffMember): void
    {
        $query = $this->pdo->prepare('UPDATE Staff_Member SET firstname = :firstname, lastname = :lastname, birthdate = :birthdate, role = :role, picture = :picture WHERE id = :id');
        $query->execute([
            'id' => $id,
            'firstname' => $staffMember->getFirstname(),
            'lastname' => $staffMember->getLastname(),
            'birthdate' => $staffMember->getBirthdate()->format('Y-m-d'),
            'role' => $staffMember->getRole(),
            'picture' => $staffMember->getPicture()
        ]);
    }

    // Supprimer un membre du staff de la base de données
    public function delete(int $id): void
    {
        $query = $this->pdo->prepare('DELETE FROM Staff_Member WHERE id = :id');
        $query->execute(['id' => $id]);
    }
}
