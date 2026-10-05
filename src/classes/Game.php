<?php

class Game {
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
        $this->teamScore = $teamScore;
        $this->opponentScore = $opponentScore;
        $this->date = $date;
        $this->team = $team;
        $this->city = $city;
        $this->opposingClub = $opposingClub;
    }

    // Getter et Setter
    // Id
    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): static {
        $this->id = $id;
        return $this;
    }

    // Team_Score
    public function getTeamScore(): int {
        return $this->teamScore;
    }

    public function setTeamScore(string $teamScore): static {
        $this->teamScore = $teamScore;
        return $this;
    }

    // opponent_score
    public function getOpponentScore(): int {
        return $this->opponentScore;
    }

    public function setOpponentScore(int $opponentScore): static {
        $this->opponentScore = $newOpponentScore;
        return $this;
    }

    // DateTime
    public function getDate(): DateTime {
        return $this->date;
    }

    public function setDate(DateTime $date): static {
        $this->date = $newDate;
        return $this;
    }

    // Team
    public function getTeam(): Team {
        return $this->team;
    }

    public function setTeam(Team $team): static {
        $this->team = $Newteam;
        return $this;
    }

    // city
    public function getCity(): string {
        return $this->city;
    }

    public function setCity(string $city): static {
        $this->city = $newCity;
        return $this;
    }

    // Opposing_Club
    public function getOpposingClub(): OpposingClub {
        return $this->OpposingClub;
    }

    public function setOpposingClub(string $opposingClub): static {
        $this->OpposingClub = $opposingClub;
        return $this;
    }
}