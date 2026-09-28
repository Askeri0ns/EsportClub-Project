<?php

class Team {
    // Attributs
    private int $id;
    private string $name;

    // Constructeur
    public function __construct(string $name) {
        $this->name = $name;
    }
}