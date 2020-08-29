<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Exception\CurrencyIsoCodeShouldBeThreeCharsLongException;

class Price
{
    private float $amount;
    private string $isoCodeCurrency;

    public function __construct($amount, $isoCodeCurrency)
    {
        if (3 !== strlen($isoCodeCurrency)) {
            throw new CurrencyIsoCodeShouldBeThreeCharsLongException();
        }

        $this->amount = $amount;
        $this->isoCodeCurrency = $isoCodeCurrency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getIsoCodeCurrency(): string
    {
        return $this->isoCodeCurrency;
    }
}
