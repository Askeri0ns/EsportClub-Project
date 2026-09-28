<?php

class Matchs {
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
}