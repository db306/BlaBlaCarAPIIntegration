<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidLongitudeCoordinateFormatException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('The longitude coordinate is invalid');
    }
}
