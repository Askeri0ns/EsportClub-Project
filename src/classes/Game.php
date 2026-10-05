<?php

class Game {
    // Attributs
    private int $id;
    private int $team_score;
    private int $opponent_score;
    private DateTime $date;
    private Team $team;
    private string $city;
    private Opposing_Club $opposing_club;

    // Constructeur
    public function __construct(int $team_score, int $opponent_score, DateTime $date, Team $team, string $city,Opposing_Club $opposing_club) {
        $this->team_score = $team_score;
        $this->opponent_score = $opponent_score;
        $this->date = $date;
        $this->team = $team;
        $this->city = $city;
        $this->opposing_club = $opposing_club;
    }

    // Getter et Setter
    // Id
    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    // Team_Score
    public function getTeamScore(): int {
        return $this->team_score;
    }

    public function setTeamScore(string $team_score): void {
        $this->team_score = $team_score;
    }

    // Opposing_Club
    public function getOpposingClub(): string {
        return $this->opponent_score;
    }

    public function setOpposingClub(string $opponent_score): void {
        $this->opponent_score = $opponent_score;
    }
}