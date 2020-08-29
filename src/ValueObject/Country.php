<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Exception\CountryIsoCodeMustBeTwoCharactersLongException;

class Country
{
    private string $isoCode;

    public function __construct(
        string $isoCode
    ) {
        if (2 !== strlen($isoCode)) {
            throw new CountryIsoCodeMustBeTwoCharactersLongException();
        }

        $this->isoCode = $isoCode;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function __toString(): string
    {
        return $this->isoCode;
    }
}
