<?php

class Team
{
    // Attributs
    private int $id;
    private string $name;

    // Constructeur
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    // Accesseurs

    //Id (Getter & Setter)
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $newId): static
    {
        $this->id = $newId;
        return $this;
    }

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
}