<?php

namespace App\Enums;

enum UnitOperationEnum: string
{
    case RENTED  = 'فروخته شده';
    case OWNER_OPERATED  = 'راه اندازی توسط مالک';
    case EMPTY  = 'فروخته نشده';

    public static function fromKey(string $key): ?self
    {
        $cases = (new \ReflectionEnum(self::class))->getCases();
        foreach ($cases as $case) {
            if ($case->getName() === strtoupper($key)) {
                return $case->getValue();
            }
        }
        return null;
    }

    public static function fromValue(string $value): ?self
    {
        $cases = (new \ReflectionEnum(self::class))->getCases();
        foreach ($cases as $case) {
            if ($case->getValue()->value === $value) {
                return $case->getValue();
            }
        }
        return null;
    }
}
