<?php

namespace App\Controller;

use App\Service\SearchServiceInterface;
use App\ValueObject\Coordinate;
use App\ValueObject\Country;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class SearchTripController extends AbstractController
{
    private SearchServiceInterface $searchService;
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        SearchServiceInterface $searchService
    ) {
        $this->logger = $logger;
        $this->searchService = $searchService;
    }

    /**
     * @Route("/", name="bla_bla")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/search", name="search", defaults={"_format"="json"})
     */
    public function searchTrips(Request $request)
    {
        if (empty($request->get('from_date'))) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'from_date cannot be an empty value');
        }

        $from = new DateTime($request->get('from_date'));

        if (empty($request->get('to_date'))) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'to_date cannot be an empty value');
        }

        $to = new DateTime($request->get('to_date'));

        try {
            $departureCoordinate = new Coordinate($request->get('dep_lat'), $request->get('dep_lng'));
            $destinationCoordinate = new Coordinate($request->get('dest_lat'), $request->get('dest_lng'));
            $originCountry = new Country($request->get('dep_country'));
            $destinationCountry = new Country($request->get('dest_country'));
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        $cursor = !empty($request->get('cursor')) ? $request->get('cursor') : null;

        if ($to < $from) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'to_date has to be at a latter date than from_date');
        }

        try {
            $trips = $this->searchService->searchTrip(
                $from,
                $to,
                $departureCoordinate,
                $destinationCoordinate,
                $originCountry,
                $destinationCountry,
                $cursor
            );
        } catch (\Exception $e) {
            $this->logger->critical('Error with BlaBlaCar Search API: '.$e->getMessage());
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'We encountered and issue with BlaBlaCar Search API service');
        }

        return $this->json($trips, 200);
    }
}
