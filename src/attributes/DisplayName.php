<?php

namespace src\attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class DisplayName
{
    public function __construct(public string $value) {}
}
