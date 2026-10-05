<?php

class Game
{
    // Attributs
    private int $id;
    private int $teamScore;
    private int $opponentScore;
    private DateTime $date;
    private Team $team;
    private string $city;
    private OpposingClub $opposingClub;

    // Constructeur
    public function __construct(int $teamScore, int $opponentScore, DateTime $date, Team $team, string $city,Opposing_Club $opposingClub) {
        $this->team_score = $teamScore;
        $this->opponent_score = $opponentScore;
        $this->date = $date;
        $this->team = $team;
        $this->city = $city;
        $this->opposingClub = $opposingClub;
    }

    // Accesseurs

    // Id (Getter & Setter)
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

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
}