<?php

declare(strict_types=1);

namespace App\Exception;

class CountryIsoCodeMustBeTwoCharactersLongException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Country Iso Code Should Be 2 Letters only');
    }
}
