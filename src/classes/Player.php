<?php

namespace src\classes;

use DateTime;
use src\attributes\Entity;
use src\attributes\FieldConstraint;

#[Entity(table: 'Player')]
class Player
{
    // Constructeur
    public function __construct(
        #[FieldConstraint(minLength: 1, maxLength: 100)]
        private string $firstname,
        #[FieldConstraint(minLength: 1, maxLength: 100)]
        private string $lastname,
        #[FieldConstraint]
        private DateTime $birthdate,
        #[FieldConstraint(nullable: true, maxLength: 2048)]
        private ?string $picture = null
    ) {
        FieldConstraint::validate($this);
    }

    // Accesseurs

    // Firsname (Getter & Setter)
    public function getFirstname(): string
    {
        return $this->firstname;
    }
    public function setFirstname(string $newFirstname): static
    {
        FieldConstraint::validateProperty($this, 'firstname', $newFirstname);
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
        FieldConstraint::validateProperty($this, 'lastname', $newLastname);
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
        FieldConstraint::validateProperty($this, 'picture', $newPicture);
        $this->picture = $newPicture;
        return $this;
    }
}