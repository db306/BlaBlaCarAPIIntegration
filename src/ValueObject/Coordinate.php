<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Exception\InvalidLatitudeCoordinateFormatException;
use App\Exception\InvalidLongitudeCoordinateFormatException;

class Coordinate
{
    private string $latitude;
    private string $longitude;

    public function __construct(
        string $latitude,
        string $longitude
    ) {
        // Both Regex borrowed from : https://stackoverflow.com/a/22007205/3262907
        if (!preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', $latitude)) {
            throw new InvalidLatitudeCoordinateFormatException();
        }

        if (!preg_match('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $longitude)) {
            throw new InvalidLongitudeCoordinateFormatException();
        }

        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function __toString(): string
    {
        return $this->latitude.','.$this->longitude;
    }
}
