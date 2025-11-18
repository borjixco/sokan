<?php

namespace App\Enums;

enum OccupantStatusEnum: string
{
    case CURRENT  = 'جاری';
    case ARCHIVE  = 'آرشیو';

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
}
