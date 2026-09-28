<?php

class Staff_Member {
    // Attributs
    private int $id;
    private string $firstname;
    private string $lastname;
    private DateTime $birthdate;
    private string $picture;
    private string $role;

    // Constructeur
    public function __construct(string $firstname, string $lastname, DateTime $birthdate, string $role, 
string $picture) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->birthdate = $birthdate;
        $this->role = $role;
        $this->picture = $picture;
    }
}