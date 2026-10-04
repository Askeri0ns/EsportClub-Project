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

    // Id
    public function getID(): int{
        return $this->$id;
    }

    public function setID(int $id): void{
        return $this->id = $id;
    }

    // Firsname
    public function getDate(): DateTime {
        return $this->firstname;
    }

    public function setDate(string $firstname): void {
        return $this->firstname = $firstname;
    }

    // Lastname
    public function getLastname(): DateTime {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void {
        return $this->lastname = $lastname;
    }
}