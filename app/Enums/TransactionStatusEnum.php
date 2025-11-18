<?php

namespace App\Enums;

enum TransactionStatusEnum: string
{
    case PENDING = 'درانتظار';
    case SUCCESSFUL = 'موفق';
    case FAILED  = 'ناموفق';
    case CANCELED  = 'لغو';
    case EXPIRED  = 'منقضی';

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
