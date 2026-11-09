<?php

namespace src\classes;

use src\attributes\Entity;
use src\attributes\FieldConstraint;

#[Entity(table: 'Opposing_Club')]
class OpposingClub
{
    // Constructeur
    public function __construct(
        #[FieldConstraint(minLength: 1, maxLength: 100)]
        private string $name,
        #[FieldConstraint(minLength: 1, maxLength: 150)]
        private string $address,
        #[FieldConstraint(minLength: 1, maxLength: 100)]
        private string $city,
    ) {
        FieldConstraint::validate($this);
    }

    // Accesseurs

    // Name (Getter & Setter)
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $newName): static
    {
        FieldConstraint::validateProperty($this, 'name', $newName);
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
        FieldConstraint::validateProperty($this, 'address', $newAddress);
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
        FieldConstraint::validateProperty($this, 'city', $newCity);
        $this->city = $newCity;
        return $this;
    }
}
