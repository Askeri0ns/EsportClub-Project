<?php

namespace src\classes;

use src\attributes\Entity;
use src\attributes\FieldConstraint;

#[Entity(table: 'Team')]
class Team
{
    // Constructeur
    public function __construct(
        #[FieldConstraint(minLength: 1, maxLength: 100)]
        private string $name,
        #[FieldConstraint(nullable: true, min: 1)]
        private ?int $id = null,
    ) {
        FieldConstraint::validate($this);
    }

    // Accesseurs

    // Name (Getter & Setter)
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $newName): static
    {
        FieldConstraint::validateProperty($this, 'name', $newName);
        $this->name = $newName;
        return $this;
    }

    // id (Getter & Setter)
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        FieldConstraint::validateProperty($this, 'id', $id);
        $this->id = $id;
        return $this;
    }
}
