<?php
// filepath: app/Services/FlightSearchService.php

namespace App\Services;

use App\Repositories\Contracts\FlightRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FlightSearchService
{
    public function __construct(
        private readonly FlightRepositoryInterface $flightRepository
    ) {
    }

    public function search(array $filters): Collection
    {
        $origin = strtoupper($filters['origin']);
        $destination = strtoupper($filters['destination']);
        $departureDate = Carbon::parse($filters['departure_date'])->toDateString();
        $passengerCount = (int) $filters['passenger_count'];
        $seatClass = $filters['seat_class'];

        return $this->flightRepository->searchAvailableFlights(
            origin: $origin,
            destination: $destination,
            departureDate: $departureDate,
            passengerCount: $passengerCount,
            seatClass: $seatClass
        );
    }
}