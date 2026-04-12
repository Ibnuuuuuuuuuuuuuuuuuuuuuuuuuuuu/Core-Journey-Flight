<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Requests\FlightSearchRequest;
use App\Models\Airport;
use App\Models\FlightSchedule;
use App\Services\FlightSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class FlightSearchController extends Controller
{
    public function __construct(
        private readonly FlightSearchService $flightSearchService
    ) {
    }

    public function search(): View
    {
        $airports = Airport::query()->orderBy('airport_code')->get();

        $availableDepartureDates = FlightSchedule::query()
            ->whereDate('departure_date', '>=', now()->toDateString())
            ->orderBy('departure_date')
            ->distinct()
            ->pluck('departure_date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->values()
            ->all();

        return view('flights.search', [
            'airports' => $airports,
            'availableDepartureDates' => $availableDepartureDates,
        ]);
    }

    public function results(FlightSearchRequest $request): View
    {
        $validated = $request->validated();
        $flights = $this->flightSearchService->search($validated);

        return view('flights.results', [
            'flights' => $flights,
            'criteria' => $validated,
            'searchParams' => $validated,
        ]);
    }

    public function availableDates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'origin' => ['nullable', 'string', 'size:3'],
            'destination' => ['nullable', 'string', 'size:3', 'different:origin'],
        ]);

        $origin = strtoupper((string) ($validated['origin'] ?? ''));
        $destination = strtoupper((string) ($validated['destination'] ?? ''));

        $query = FlightSchedule::query()
            ->whereDate('departure_date', '>=', now()->toDateString());

        if ($origin !== '') {
            $query->where('origin', $origin);
        }

        if ($destination !== '') {
            $query->where('destination', $destination);
        }

        $dates = $query
            ->orderBy('departure_date')
            ->distinct()
            ->pluck('departure_date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->values();

        return response()->json([
            'data' => $dates,
        ]);
    }
}