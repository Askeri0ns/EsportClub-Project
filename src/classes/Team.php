<?php

class Team
{
    // Constructeur
    public function __construct(private string $name)
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
}