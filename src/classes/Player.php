<?php

class Player {
    // Attributs
    private int $id;
    private string $firstname;
    private string $lastname;
    private DateTime $birthdate;
    private string $picture;

    // Constructeur
    public function __construct(string $firstname, string $lastname, DateTime $birthdate, ?string $picture = null) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->birthdate = $birthdate;
        $this->picture = $picture;
    }
}