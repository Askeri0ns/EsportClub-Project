<?php

class Opposing_Club
{
    // Attributs
    private int $id;
    private string $name;
    private string $address;
    private string $city;

    // Constructeur
    public function __construct(string $name, string $address, string $city)
    {
        $this->name = $name;
        $this->address = $address;
        $this->city = $city;
    }

    // Accesseurs
    
    // Id (Getter & Setter)
    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $newId): static
    {
        $this->id = $newId;
        return $this;
    }

    // Name (Getter & Setter)
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $newName): static
    {
        $this->name = $newName;
        return $this;
    }

    // Address (Getter & Setter)
    public function getAddress(): string
    {
        return $this->address;
    }
    public function setAddress(string $newAddress): static
    {
        $this->address = $newAddress;
        return $this;
    }

    // City (Getter & Setter)
    public function getCity(): string
    {
        return $this->city;
    }
    public function setCity(string $newCity): static
    {
        $this->city = $newCity;
        return $this;
    }
}