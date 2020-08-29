<?php

declare(strict_types=1);

namespace App\Exception;

class CurrencyIsoCodeShouldBeThreeCharsLongException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Currency Iso code must be equal to exactly 3 letters');
    }
}
