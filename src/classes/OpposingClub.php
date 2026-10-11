<?php

class OpposingClub
{
    // Constructeur
    public function __construct(private string $name, private string $address, private string $city)
    {
    }

    // Accesseurs

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