<?php

namespace src\attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class DatabaseController
{
    public function __construct(public string $entityClass)
    {
    }
}
