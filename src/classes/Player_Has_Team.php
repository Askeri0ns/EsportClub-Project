<?php

class Player_Has_Team {
    // Attributs
    private Player $player;
    private Team $team;
    private string $role;

    // Constructeur
    public function __construct(string $role) {
        $this->role = $role;
    }
}