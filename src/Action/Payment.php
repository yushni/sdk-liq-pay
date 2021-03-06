<?php
declare(strict_types=1);

namespace LiqPay\Action;

class Payment extends Action
{
    public const
        ACTION_PAY = 'pay';

    public static function pay (float $amount, string $orderId, string $currency, string $description)
    {
        return new static($amount, $orderId, $currency, self::ACTION_PAY, $description);
    }
}