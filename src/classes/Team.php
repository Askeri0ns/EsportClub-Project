<?php

class Team
{
    // Attributs
    private string $name;

    // Constructeur
    public function __construct(string $name)
    {
        $this->name = $name;
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
}