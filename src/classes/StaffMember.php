<?php

class StaffMember
{
    // Constructeur
    public function __construct(private string $firstname, private string $lastname, private DateTime $birthdate, private StaffRole $role, private ?string $picture = null)
    {
    }

    // Accesseurs
    
    // Firstname (Getter & Setter)
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
    
    // Role (Getter & Setter)
    public function getRole(): string
    {
        return $this->role->value;
    }
    public function setRole(string $newRole): static
    {
        $this->role = StaffRole::tryFrom($newRole);
        return $this;
    }
    
    // Picture (Getter & Setter)
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

enum StaffRole: string
{
    case Manager = "manager";
    case Coach = "coach";
    case Analyste = "analyste";
    case Commentateur = "commentateur";
};