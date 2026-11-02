<?php

namespace src\classes;

use src\attributes\Entity;
use src\attributes\FieldConstraint;
use src\enum\PlayerRole;

#[Entity(table: 'Player_has_Team')]
class PlayerHasTeam
{
    // Constructeur
    public function __construct(
        #[FieldConstraint]
        private Player $player,
        #[FieldConstraint]
        private Team $team,
        #[FieldConstraint]
        private PlayerRole $role
    ) {
        FieldConstraint::validate($this);
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
    public function getRole(): PlayerRole
    {
        return $this->role;
    }

    public function setRole(PlayerRole $newRole): static
    {
        $this->role = $newRole;
        return $this;
    }
}