<?php

namespace src\enum;

use src\attributes\DisplayName;

trait HasDisplayName
{
    public function displayName(): string
    {
        $attributes = (new \ReflectionEnumBackedCase(self::class, $this->name))
            ->getAttributes(DisplayName::class);

        return $attributes[0]->newInstance()->value;
    }
}
