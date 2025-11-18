<?php

namespace App\Enums;

enum ChargeStatusEnum: string
{
    case SENDING  = 'درحال ارسال';
    case UNPAID  = 'پرداخت نشده';
    case PAID  = 'پرداخت شده';

    case OVERDUE  = 'معوقه';
    case NOT_SENT  = 'ارسال نشد';
    case CANCELED  = 'لغوشده';

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
