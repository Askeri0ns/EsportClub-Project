<?php

class Matchs {
    // Attributs
    private int $id;
    private int $team_score;
    private int $opponent_score;
    private DateTime $date;
    private int $team_id;
    private string $city;
    private int $opposing_club_id;

    // Constructeur
    public function __construct(int $team_score, int $opponent_score, DateTime $date, int $team_id, string $city,int $opposing_club_id) {
        $this->team_score = $team_score;
        $this->opponent_score = $opponent_score;
        $this->date = $date;
        $this->team_id = $team_id;
        $this->city = $city;
        $this->opposing_club_id = $opposing_club_id;
    }
}