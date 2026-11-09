<?php

namespace src\classes;

use DateTime;
use src\attributes\Entity;
use src\attributes\FieldConstraint;

#[Entity(table: 'Game')]
class Game
{
    // Constructeur
    public function __construct(
        #[FieldConstraint(min: 0)]
        private int $teamScore,
        #[FieldConstraint(min: 0)]
        private int $opponentScore,
        #[FieldConstraint]
        private Team $team,
        #[FieldConstraint]
        private OpposingClub $opposingClub,
        #[FieldConstraint]
        private DateTime $date,
        #[FieldConstraint(minLength: 1, maxLength: 100)]
        private string $city,
    ) {
        FieldConstraint::validate($this);
    }

    // Accesseurs

    // TeamScore (Getter & Setter)
    public function getTeamScore(): int
    {
        return $this->teamScore;
    }
    public function setTeamScore(int $newTeamScore): static
    {
        FieldConstraint::validateProperty($this, 'teamScore', $newTeamScore);
        $this->teamScore = $newTeamScore;
        return $this;
    }

    // OpponentScore (Getter & Setter)
    public function getOpponentScore(): int
    {
        return $this->opponentScore;
    }
    public function setOpponentScore(int $newOpponentScore): static
    {
        FieldConstraint::validateProperty($this, 'opponentScore', $newOpponentScore);
        $this->opponentScore = $newOpponentScore;
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

    // OpposingClub (Getter & Setter)
    public function getOpposingClub(): OpposingClub
    {
        return $this->opposingClub;
    }
    public function setOpposingClub(OpposingClub $newOpposingClub): static
    {
        $this->opposingClub = $newOpposingClub;
        return $this;
    }

    // Date (Getter & Setter)
    public function getDate(): DateTime
    {
        return $this->date;
    }
    public function setDate(DateTime $newDate): static
    {
        $this->date = $newDate;
        return $this;
    }

    // City (Getter & Setter)
    public function getCity(): string
    {
        return $this->city;
    }
    public function setCity(string $newCity): static
    {
        FieldConstraint::validateProperty($this, 'city', $newCity);
        $this->city = $newCity;
        return $this;
    }
}
