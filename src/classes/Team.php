<?php

class Team {
    // Attributs
    private int $id;
    private string $name;

    // Constructeur
    public function __construct(string $name) {
        $this->name = $name;
    }

    // Getter et Setter
    //Id
    public function getId(): int{
        return $this->$id;
    }

    public function setId(int $id): static{
        $this->id = $id;
        return $this
    }

    // Name
    public function getName(): string{
        return $this->$name;
    }

    public function setId(string $name): static{
        $this->name = $name;
        return $this
    }
}