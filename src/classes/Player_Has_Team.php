<?php

class Player_Has_Team {
    // Attributs
    private int $player_id;
    private int $team_id;
    private string $role;

    // Constructeur
    public function __construct(string $role) {
        $this->role = $role;
    }
}