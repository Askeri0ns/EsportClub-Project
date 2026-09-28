<?php

class Opposing_Club {
    // Attributs
    private int $id;
    private string $name;
    private string $address;
    private string $city;

    // Constructeur
    public function __construct(string $name, string $address, string $city) {
        $this->name = $name;
        $this->address = $address;
        $this->city = $city;
    }
}