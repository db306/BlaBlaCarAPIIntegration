<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\Coordinate;
use App\ValueObject\Country;
use DateTime;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SearchTripService implements SearchServiceInterface
{
    private HttpClientInterface $client;
    private CacheItemPoolInterface $cache;
    private string $apiKey;
    private string $baseUrl;

    public function __construct(
        HttpClientInterface $client,
        CacheItemPoolInterface $cache,
        string $apiKey,
        string $baseUrl
    ) {
        $this->client = $client;
        $this->cache = $cache;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param DateTime    $from               Date from where to start looking for trips
     * @param DateTime    $to                 End date from where to stop looking for trips
     * @param Coordinate  $departure          Departure coordinates of the trip search
     * @param Coordinate  $destination        Destination coordinates of the trip search
     * @param Country     $originCountry      Country of departure in ISO 3166-1 aplha-2 Format
     * @param Country     $destinationCountry Country of destination in ISO 3166-1 aplha-2 Format
     * @param string|null $cursor             The next page pagination cursor
     *
     * @return array The expected result from BlaBlaCar Search API
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function searchTrip(
        DateTime $from,
        DateTime $to,
        Coordinate $departure,
        Coordinate $destination,
        Country $originCountry,
        Country $destinationCountry,
        ?string $cursor
    ): array {
        $query = [
            'key' => $this->apiKey,
            'start_date_local' => $from->format('yy-m-d\TH:i:s'),
            'end_date_local' => $to->format('yy-m-d\TH:i:s'),
            'locale' => 'fr-FR',
            'currency' => 'EUR',
            'from_coordinate' => "$departure",
            'to_coordinate' => "$destination",
            'from_country' => "$originCountry",
            'to_country' => "$destinationCountry",
        ];

        if (null !== $cursor) {
            $query['from_cursor'] = $cursor;
        }

        $hash = strval(crc32(join('', $query)));
        $cacheItem = $this->cache->getItem($hash);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $response = $this->client->request(
            'GET',
            $this->baseUrl,
            [
                'query' => $query,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        )->toArray();

        // As suggested by https://support.blablacar.com/hc/en-us/articles/360014200220--How-to-use-BlaBlaCar-search-API-
        $cacheItem->expiresAfter(600); // you shouldn't cache the results you retrieve from the API, or for a very small time (10 min max)
        $cacheItem->set($response);
        $this->cache->save($cacheItem);

        return $response;
    }
}
