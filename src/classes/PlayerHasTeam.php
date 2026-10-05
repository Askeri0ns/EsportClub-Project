<?php

class PlayerHasTeame
{
    // Attributs
    private Player $player;
    private Team $team;
    private string $role;

    // Constructeur
    public function __construct(Player $player, Team $team, string $role)
    {
        $this->player = $player;
        $this->team = $team;
        $this->role = $role;
    }

    // Accesseurs

    // Player (Getter & Setter)
    public function getPlayer(): Player
    {
        return $this->player;
    }
    public function setPlayer(Player $newPlayer): static
    {
        $this->player = $newPlayer;
        return $this;
    }

    // Team (Getter & Setter)
    public function getTeam(): Team
    {
        return $this->team;
    }
    public function setTeam(Team $newTeam): static
    {
        $this->team = $newTeam;
        return $this;
    }

    // Role (Getter & Setter)
    public function getRole(): string
    {
        return $this->role;
    }
    public function setRole(string $newRole): static
    {
        $this->role = $newRole;
        return $this;
    }
}