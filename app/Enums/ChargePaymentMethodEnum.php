<?php

namespace App\Enums;

enum ChargePaymentMethodEnum: string
{
    case CASH    = 'نقدی';
    case CARD    = 'کارت به کارت';
    case POS     = 'کارت خوان';
    case WALLET  = 'کیف پول';
    case GATEWAY = 'درگاه پرداخت';

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
