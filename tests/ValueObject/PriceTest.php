<?php

declare(strict_types=1);

namespace App\Tests\ValueObject;

use App\Exception\CurrencyIsoCodeShouldBeThreeCharsLongException;
use App\ValueObject\Price;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    public function testShouldThrowExceptionIfIsoCodeLongerThan3Chars()
    {
        $this->expectException(CurrencyIsoCodeShouldBeThreeCharsLongException::class);
        new Price(5, 'ABCD');
    }

    public function testShouldThrowExceptionIfIsoCodeShorterThan3Chars()
    {
        $this->expectException(CurrencyIsoCodeShouldBeThreeCharsLongException::class);
        new Price(5, 'AB');
    }

    public function testShouldHydrateObjectIfIsoCodeEquals3Chars()
    {
        $price = new Price(5, 'ABC');
        $this->assertEquals(5, $price->getAmount());
        $this->assertEquals('ABC', $price->getIsoCodeCurrency());
    }
}
