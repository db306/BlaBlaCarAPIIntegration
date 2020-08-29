<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\Coordinate;
use App\ValueObject\Country;
use DateTime;

interface SearchServiceInterface
{
    public function searchTrip(
        DateTime $from,
        DateTime $to,
        Coordinate $departure,
        Coordinate $destination,
        Country $originCountry,
        Country $destinationCountry,
        ?string $cursor
    ): array;
}
