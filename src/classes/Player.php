<?php

class Player
{
    // Attributs
    private string $firstname;
    private string $lastname;
    private DateTime $birthdate;
    private string $picture;

    // Constructeur
    public function __construct(string $firstname, string $lastname, DateTime $birthdate, ?string $picture = null)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->birthdate = $birthdate;
        $this->picture = $picture;
    }

    // Accesseurs

    // Firsname (Getter & Setter)
    public function getFirstname(): string
    {
        return $this->firstname;
    }
    public function setFirstname(string $newFirstname): static
    {
        $this->firstname = $newFirstname;
        return $this;
    }

    // Lastname (Getter & Setter)
    public function getLastname(): string
    {
        return $this->lastname;
    }
    public function setLastname(string $newLastname): static
    {
        $this->lastname = $newLastname;
        return $this;
    }

    // Birthdate (Getter & Setter)
    public function getBirthdate(): DateTime
    {
        return $this->birthdate;
    }
    public function setBirthdate(DateTime $newBirthdate): static
    {
        $this->birthdate = $newBirthdate;
        return $this;
    }

    // Picture
    public function getPicture(): ?string
    {
        return $this->picture;
    }
    public function setPicture(string $newPicture): static
    {
        $this->picture = $newPicture;
        return $this;
    }
}