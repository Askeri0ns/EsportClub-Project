<?php

namespace src\attributes;

use Attribute;
use InvalidArgumentException;
use ReflectionObject;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class FieldConstraint
{
    public function __construct(
        public bool $nullable = false,
        public int|float|null $min = null,
        public int|float|null $max = null,
        public ?int $minLength = null,
        public ?int $maxLength = null
    ) {
        if ($min !== null && $max !== null && $min > $max) {
            throw new InvalidArgumentException('La valeur minimale doit être inférieure ou égale à la valeur maximale.');
        }
        if ($minLength !== null && $minLength < 0 || $maxLength !== null && $maxLength < 0) {
            throw new InvalidArgumentException('La longueur ne peut pas être négative.');
        }
        if ($minLength !== null && $maxLength !== null && $minLength > $maxLength) {
            throw new InvalidArgumentException('La longueur minimale doit être inférieure ou égale à la longueur maximale.');
        }
    }

    public static function validate(object $object): void
    {
        $reflection = new ReflectionObject($object);
        foreach ($reflection->getProperties() as $property) {
            self::validateValue(
                $property->getName(),
                $property->getValue($object),
                $property->getAttributes(self::class)
            );
        }
    }

    public static function validateProperty(object $object, string $propertyName, mixed $value): void
    {
        $property = (new ReflectionObject($object))->getProperty($propertyName);
        self::validateValue(
            $propertyName,
            $value,
            $property->getAttributes(self::class)
        );
    }

    private static function validateValue(string $propertyName, mixed $value, array $attributes): void
    {
        foreach ($attributes as $attribute) {
            $constraint = $attribute->newInstance();

            if ($value === null) {
                if (!$constraint->nullable) {
                    throw new InvalidArgumentException("La propriété « {$propertyName} » ne peut pas être nulle.");
                }
                continue;
            }

            if ($constraint->min !== null || $constraint->max !== null) {
                if (!is_int($value) && !is_float($value)) {
                    throw new InvalidArgumentException("La propriété « {$propertyName} » doit être numérique.");
                }
                if ($constraint->min !== null && $value < $constraint->min) {
                    throw new InvalidArgumentException("La propriété « {$propertyName} » est inférieure au minimum autorisé.");
                }
                if ($constraint->max !== null && $value > $constraint->max) {
                    throw new InvalidArgumentException("La propriété « {$propertyName} » dépasse le maximum autorisé.");
                }
            }

            if ($constraint->minLength !== null || $constraint->maxLength !== null) {
                if (!is_string($value)) {
                    throw new InvalidArgumentException("La propriété « {$propertyName} » doit être une chaîne de caractères.");
                }

                $length = function_exists('mb_strlen')
                    ? mb_strlen($value, 'UTF-8')
                    : preg_match_all('/./us', $value);
                if ($length === false) {
                    throw new InvalidArgumentException("La propriété « {$propertyName} » n'est pas encodée en UTF-8 valide.");
                }
                if ($constraint->minLength !== null && $length < $constraint->minLength) {
                    throw new InvalidArgumentException("La propriété « {$propertyName} » est trop courte.");
                }
                if ($constraint->maxLength !== null && $length > $constraint->maxLength) {
                    throw new InvalidArgumentException("La propriété « {$propertyName} » est trop longue.");
                }
            }
        }
    }
}
