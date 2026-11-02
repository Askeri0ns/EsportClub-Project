<?php

namespace src\attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Entity
{
    public function __construct(public string $table)
    {
    }
}
