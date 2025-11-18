<?php

namespace App\Enums;
enum TicketStatusEnum: string
{
    case PENDING  = 'درانتظار';
    case IN_PROGRESS  = 'درحال پیگیری';
    case CLOSED  = 'بسته شده';
    case RESPONDED  = 'پاسخ داده شده';

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
