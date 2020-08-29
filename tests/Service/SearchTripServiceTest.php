<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\SearchTripService;
use App\ValueObject\Coordinate;
use App\ValueObject\Country;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SearchTripServiceTest extends TestCase
{
    private MockObject $httpClient;
    private MockObject $cache;

    public function setUp(): void
    {
        $this->httpClient = $this->getMockBuilder(HttpClientInterface::class)->disableOriginalConstructor()->getMock();
        $this->cache = $this->getMockBuilder(CacheItemPoolInterface::class)->disableOriginalConstructor()->getMock();
    }

    public function testSearchTripDoesNotAddsCursorToQueryIfNull()
    {
        $itemCache = $this->getMockBuilder(CacheItemInterface::class)->disableOriginalConstructor()->getMock();
        $this->cache->method('getItem')->willReturn($itemCache);

        $baseUrl = 'https://blablaexample.com';
        $apikey = 'ofiherofiherofhieriohf';
        $searchTripService = new SearchTripService($this->httpClient, $this->cache, $apikey, $baseUrl);

        $from = new \DateTime('now');
        $to = new \DateTime('now');
        $departure = new Coordinate('43.608292', '3.879600');
        $destination = new Coordinate('43.608292', '3.879600');
        $countryA = new Country('DE');
        $countryB = new Country('BE');

        $expectedQuery = [
            'key' => $apikey,
            'start_date_local' => $from->format('yy-m-d\TH:i:s'),
            'end_date_local' => $to->format('yy-m-d\TH:i:s'),
            'locale' => 'fr-FR',
            'currency' => 'EUR',
            'from_coordinate' => "$departure",
            'to_coordinate' => "$destination",
            'from_country' => "$countryA",
            'to_country' => "$countryB",
        ];

        $this->httpClient->expects($this->once())->method('request')->with('GET', $baseUrl, [
            'query' => $expectedQuery,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $searchTripService->searchTrip($from, $to, $departure, $destination, $countryA, $countryB, null);
    }

    public function testSearchTripAddsCursorToQueryIfNotNull()
    {
        $itemCache = $this->getMockBuilder(CacheItemInterface::class)->disableOriginalConstructor()->getMock();
        $this->cache->method('getItem')->willReturn($itemCache);

        $baseUrl = 'https://blablaexample.com';
        $apikey = 'ofiherofiherofhieriohf';
        $searchTripService = new SearchTripService($this->httpClient, $this->cache, $apikey, $baseUrl);

        $from = new \DateTime('now');
        $to = new \DateTime('now');
        $departure = new Coordinate('43.608292', '3.879600');
        $destination = new Coordinate('43.608292', '3.879600');
        $countryA = new Country('DE');
        $countryB = new Country('BE');
        $cursor = 'CGzAStx';

        $expectedQuery = [
            'key' => $apikey,
            'start_date_local' => $from->format('yy-m-d\TH:i:s'),
            'end_date_local' => $to->format('yy-m-d\TH:i:s'),
            'locale' => 'fr-FR',
            'currency' => 'EUR',
            'from_coordinate' => "$departure",
            'to_coordinate' => "$destination",
            'from_country' => "$countryA",
            'to_country' => "$countryB",
            'from_cursor' => $cursor,
        ];

        $this->httpClient->expects($this->once())->method('request')->with('GET', $baseUrl, [
            'query' => $expectedQuery,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $searchTripService->searchTrip($from, $to, $departure, $destination, $countryA, $countryB, $cursor);
    }

    public function testSearchTripShouldReturnResponseToArray()
    {
        $itemCache = $this->getMockBuilder(CacheItemInterface::class)->disableOriginalConstructor()->getMock();
        $this->cache->method('getItem')->willReturn($itemCache);

        $baseUrl = 'https://blablaexample.com';
        $apikey = 'ofiherofiherofhieriohf';
        $searchTripService = new SearchTripService($this->httpClient, $this->cache, $apikey, $baseUrl);

        $from = new \DateTime('now');
        $to = new \DateTime('now');
        $departure = new Coordinate('43.608292', '3.879600');
        $destination = new Coordinate('43.608292', '3.879600');
        $countryA = new Country('DE');
        $countryB = new Country('BE');
        $cursor = 'CGzAStx';

        $response = $this->getMockBuilder(ResponseInterface::class)->disableOriginalConstructor()->getMock();
        $response->expects($this->once())->method('toArray');

        $this->httpClient->method('request')->willReturn($response);

        $searchTripService->searchTrip($from, $to, $departure, $destination, $countryA, $countryB, $cursor);
    }

    public function testSearchTripShouldReturnCacheIfValid()
    {
        $expectedResult = ['apple', 'bananas'];
        $itemCache = $this->getMockBuilder(CacheItemInterface::class)->disableOriginalConstructor()->getMock();
        $itemCache->expects($this->once())->method('isHit')->willReturn(true);
        $itemCache->expects($this->once())->method('get')->willReturn($expectedResult);
        $this->cache->method('getItem')->willReturn($itemCache);

        $baseUrl = 'https://blablaexample.com';
        $apikey = 'ofiherofiherofhieriohf';
        $searchTripService = new SearchTripService($this->httpClient, $this->cache, $apikey, $baseUrl);

        $from = new \DateTime('now');
        $to = new \DateTime('now');
        $departure = new Coordinate('43.608292', '3.879600');
        $destination = new Coordinate('43.608292', '3.879600');
        $countryA = new Country('DE');
        $countryB = new Country('BE');
        $cursor = 'CGzAStx';

        $this->httpClient->expects($this->never())->method('request');
        $searchTripService->searchTrip($from, $to, $departure, $destination, $countryA, $countryB, $cursor);
    }

    public function testSearchTripShouldSaveIntoCacheIfItDoesNotExist()
    {
        $result = ['apple', 'bananas'];
        $response = $this->getMockBuilder(ResponseInterface::class)->disableOriginalConstructor()->getMock();
        $response->expects($this->once())->method('toArray')->willReturn($result);

        $itemCache = $this->getMockBuilder(CacheItemInterface::class)->disableOriginalConstructor()->getMock();
        $itemCache->expects($this->once())->method('isHit')->willReturn(false);
        $itemCache->expects($this->never())->method('get');
        $itemCache->expects($this->once())->method('expiresAfter')->with(600);
        $itemCache->expects($this->once())->method('set')->with($result);
        $this->cache->expects($this->once())->method('save')->with($itemCache);
        $this->cache->method('getItem')->willReturn($itemCache);

        $baseUrl = 'https://blablaexample.com';
        $apikey = 'ofiherofiherofhieriohf';
        $searchTripService = new SearchTripService($this->httpClient, $this->cache, $apikey, $baseUrl);

        $from = new \DateTime('now');
        $to = new \DateTime('now');
        $departure = new Coordinate('43.608292', '3.879600');
        $destination = new Coordinate('43.608292', '3.879600');
        $countryA = new Country('DE');
        $countryB = new Country('BE');
        $cursor = 'CGzAStx';

        $this->httpClient->method('request')->willReturn($response);
        $searchTripService->searchTrip($from, $to, $departure, $destination, $countryA, $countryB, $cursor);
    }
}
