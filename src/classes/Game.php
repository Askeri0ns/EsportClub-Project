<?php

class Game
{
    // Constructeur
    public function __construct(private int $teamScore, private int $opponentScore, private Team $team, private OpposingClub $opposingClub, private DateTime $date, private string $city)
    {
    }

    // Accesseurs

    // TeamScore (Getter & Setter)
    public function getTeamScore(): int
    {
        return $this->teamScore;
    }
    public function setTeamScore(int $newTeamScore): static
    {
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
        $this->city = $newCity;
        return $this;
    }
}