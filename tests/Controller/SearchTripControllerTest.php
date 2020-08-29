<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Service\SearchTripService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchTripControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShouldThrowExceptionWhenEmptyFromDate()
    {
        $client = static::createClient();
        $client->request('GET', '/search');
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals('from_date cannot be an empty value', $response->detail);
    }

    public function testShouldThrowExceptionWhenEmptyToDate()
    {
        $client = static::createClient();

        $query = [
            'from_date' => '2020-08-28',
        ];

        $client->request('GET', '/search?'.http_build_query($query, '', '&amp;'));
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals('to_date cannot be an empty value', $response->detail);
    }

    public function testShouldThrowExceptionWhenFromDateIsLaterThanFromDate()
    {
        $client = static::createClient();

        $query = [
            'from_date' => '2020-08-28',
            'to_date' => '2020-08-27',
            'dep_lat' => '43.608292',
            'dep_lng' => '3.879600',
            'dest_lat' => '45.764042',
            'dest_lng' => '4.835659',
            'dep_country' => 'FR',
            'dest_country' => 'FR',
        ];

        $client->request('GET', '/search?'.http_build_query($query, '', '&amp;'));
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals($response->detail, 'to_date has to be at a latter date than from_date');
    }

    public function testShouldThrowExceptionWhenDepartureLatitudeValueIsWrong()
    {
        $client = static::createClient();

        $query = [
            'from_date' => '2020-08-28',
            'to_date' => '2020-08-29',
            'dep_lat' => '43.wefwef92',
            'dep_lng' => '3.879600',
            'dest_lat' => '45.764042',
            'dest_lng' => '4.835659',
            'dep_country' => 'FR',
            'dest_country' => 'FR',
        ];

        $client->request('GET', '/search?'.http_build_query($query, '', '&amp;'));
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals($response->detail, 'The latitude coordinate is invalid');
    }

    public function testShouldThrowExceptionWhenDepartureLongitudeValueIsWrong()
    {
        $client = static::createClient();

        $query = [
            'from_date' => '2020-08-28',
            'to_date' => '2020-08-29',
            'dep_lat' => '43.608292',
            'dep_lng' => 'perojf',
            'dest_lat' => '45.764042',
            'dest_lng' => '4.835659',
            'dep_country' => 'FR',
            'dest_country' => 'FR',
        ];

        $client->request('GET', '/search?'.http_build_query($query, '', '&amp;'));
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals($response->detail, 'The longitude coordinate is invalid');
    }

    public function testShouldThrowExceptionWhenDestinationLatitudeValueIsWrong()
    {
        $client = static::createClient();

        $query = [
            'from_date' => '2020-08-28',
            'to_date' => '2020-08-29',
            'dep_lat' => '43.608292',
            'dep_lng' => '3.879600',
            'dest_lat' => 'erferfrefref',
            'dest_lng' => '4.835659',
            'dep_country' => 'FR',
            'dest_country' => 'FR',
        ];

        $client->request('GET', '/search?'.http_build_query($query, '', '&amp;'));
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals($response->detail, 'The latitude coordinate is invalid');
    }

    public function testShouldThrowExceptionWhenDestinationLongitudeValueIsWrong()
    {
        $client = static::createClient();

        $query = [
            'from_date' => '2020-08-28',
            'to_date' => '2020-08-29',
            'dep_lat' => '43.608292',
            'dep_lng' => '3.879600',
            'dest_lat' => '45.764042',
            'dest_lng' => 'erfreferf',
            'dep_country' => 'FR',
            'dest_country' => 'FR',
        ];

        $client->request('GET', '/search?'.http_build_query($query, '', '&amp;'));
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals($response->detail, 'The longitude coordinate is invalid');
    }

    public function testShouldThrowExceptionWhenOriginCountryIsWrong()
    {
        $client = static::createClient();

        $query = [
            'from_date' => '2020-08-28',
            'to_date' => '2020-08-29',
            'dep_lat' => '43.608292',
            'dep_lng' => '3.879600',
            'dest_lat' => '45.764042',
            'dest_lng' => '4.835659',
            'dep_country' => 'FRA',
            'dest_country' => 'FR',
        ];

        $client->request('GET', '/search?'.http_build_query($query, '', '&amp;'));
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals($response->detail, 'Country Iso Code Should Be 2 Letters only');
    }

    public function testShouldThrowExceptionWhenDestinationCountryIsWrong()
    {
        $client = static::createClient();

        $query = [
            'from_date' => '2020-08-28',
            'to_date' => '2020-08-29',
            'dep_lat' => '43.608292',
            'dep_lng' => '3.879600',
            'dest_lat' => '45.764042',
            'dest_lng' => '4.835659',
            'dep_country' => 'FR',
            'dest_country' => 'F',
        ];

        $client->request('GET', '/search?'.http_build_query($query, '', '&amp;'));
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals($response->detail, 'Country Iso Code Should Be 2 Letters only');
    }

    public function testShouldThrowExceptionIfApiThrowsAnException()
    {
        $client = self::createClient();
        $mockService = $this->getMockBuilder(SearchTripService::class)->disableOriginalConstructor()->getMock();
        $mockService->expects($this->once())->method('searchTrip')->willThrowException(new \Exception('Random unknown Exception thrown by BlaBlaCar Service'));
        $client->getContainer()->set('App\Service\SearchTripService', $mockService);

        $query = [
            'from_date' => '2020-08-30',
            'to_date' => '2020-08-31',
            'dep_lat' => '43.608292',
            'dep_lng' => '3.879600',
            'dest_lat' => '45.764042',
            'dest_lng' => '4.835659',
            'dep_country' => 'FR',
            'dest_country' => 'FR',
        ];

        $client->request('GET', '/search?'.http_build_query($query, '', '&amp;'));
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertEquals($response->detail, 'We encountered and issue with BlaBlaCar Search API service');
    }

    public function testShouldReturnJsonWithResults()
    {
        $expectedResults = ['apple', 'banana'];
        $client = self::createClient();
        $mockService = $this->getMockBuilder(SearchTripService::class)->disableOriginalConstructor()->getMock();
        $mockService->expects($this->once())->method('searchTrip')->willReturn($expectedResults);
        $client->getContainer()->set('App\Service\SearchTripService', $mockService);

        $query = [
            'from_date' => '2020-08-30',
            'to_date' => '2020-08-31',
            'dep_lat' => '43.608292',
            'dep_lng' => '3.879600',
            'dest_lat' => '45.764042',
            'dest_lng' => '4.835659',
            'dep_country' => 'FR',
            'dest_country' => 'FR',
        ];

        $client->request('GET', '/search?'.http_build_query($query, '', '&amp;'));
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($response, $expectedResults);
    }
}
