<?php

class StaffMember
{
    // Attributs
    private int $id;
    private string $firstname;
    private string $lastname;
    private DateTime $birthdate;
    private string $role;
    private string $picture;

    // Constructeur
    public function __construct(string $firstname, string $lastname, DateTime $birthdate, string $role, ?string $picture = null) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->birthdate = $birthdate;
        $this->role = $role;
        $this->picture = $picture;
    }

    // Accesseurs
    
    // Id (Getter & Setter)
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $newId): static
    {
        $this->id = $newId;
        return $this;
    }
    
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
        return $this->role;
    }
    public function setRole(string $newRole): static
    {
        $this->role = $newRole;
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