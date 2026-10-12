<?php

namespace src\classes;

class Team
{
    // Constructeur
    public function __construct(private string $name, private ?int $id = null)
    {
    }

    // Accesseurs

    // Name (Getter & Setter)
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $newName): static
    {
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
        $this->id = $id;
        return $this;
    }
}