<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidLatitudeCoordinateFormatException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('The latitude coordinate is invalid');
    }
}
