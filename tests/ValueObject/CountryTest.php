<?php

declare(strict_types=1);

namespace App\Tests\ValueObject;

use App\Exception\CountryIsoCodeMustBeTwoCharactersLongException;
use App\ValueObject\Country;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
    public function testShouldThrowExceptionWhenCountryLongerThanTwoCharacters()
    {
        $this->expectException(CountryIsoCodeMustBeTwoCharactersLongException::class);
        new Country('ABC');
    }

    public function testShouldThrowExceptionWhenCountryShorterThanTwoCharacters()
    {
        $this->expectException(CountryIsoCodeMustBeTwoCharactersLongException::class);
        new Country('A');
    }

    public function testShouldHydrateObjectIfIsoCodeIsTwoChars()
    {
        $country = new Country('AB');
        $this->assertEquals('AB', $country->getIsoCode());
        $this->assertEquals('AB', "$country");
    }
}
