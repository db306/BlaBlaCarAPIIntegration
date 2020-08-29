<?php

declare(strict_types=1);

namespace App\Tests\ValueObject;

use App\Exception\InvalidLatitudeCoordinateFormatException;
use App\Exception\InvalidLongitudeCoordinateFormatException;
use App\ValueObject\Coordinate;
use PHPUnit\Framework\TestCase;

class CoordinateTest extends TestCase
{
    public function testWhenCreatingCoordinateItShouldSetAllProperties()
    {
        $latitude = '43.608292';
        $longitude = '3.879600';
        $coordinate = new Coordinate($latitude, $longitude);
        $this->assertEquals($latitude, $coordinate->getLatitude());
        $this->assertEquals($longitude, $coordinate->getLongitude());
        $this->assertEquals($latitude.','.$longitude, "$coordinate");
    }

    public function testCoordinateShouldThrowExceptionWhenLatitudeIsInvalid()
    {
        $this->expectException(InvalidLatitudeCoordinateFormatException::class);
        $latitude = '43.aaa34t34t3t4';
        $longitude = '3.879600';
        new Coordinate($latitude, $longitude);
    }

    public function testCoordinateShouldThrowExceptionWhenLongitudeIsInvalid()
    {
        $this->expectException(InvalidLongitudeCoordinateFormatException::class);
        $latitude = '43.608292';
        $longitude = '3.z79600';
        new Coordinate($latitude, $longitude);
    }
}
