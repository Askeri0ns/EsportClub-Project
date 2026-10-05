<?php

class PlayerHasTeam
{
    // Constructeur
    public function __construct(private Player $player, private Team $team, private PlayerRole $role)
    {
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
        return $this->role->value;
    }
    public function setRole(string $newRole): static
    {
        $this->role = PlayerRole::tryFrom($newRole);
        return $this;
    }
}

enum PlayerRole: string
{
    case DPS = 'dps';
    case TANK = 'tank';
    case SUPPORT = 'support';
    case FLEX = 'flex';
    case SNIPER = 'sniper';
    case ENTRY = 'entry';
}