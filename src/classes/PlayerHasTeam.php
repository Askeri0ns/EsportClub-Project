<?php

class PlayerHasTeam {
    // Attributs
    private Player $player;
    private Team $team;
    private string $role;

    // Constructeur
    public function __construct(string $role) {
        $this->role = $role;
    }
}