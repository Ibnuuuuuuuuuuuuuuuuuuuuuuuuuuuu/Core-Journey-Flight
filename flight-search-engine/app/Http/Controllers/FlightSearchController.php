<?php
declare(strict_types=1);

// filepath: app/Http/Controllers/FlightSearchController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Requests\FlightSearchRequest;
use App\Models\Airport;
use App\Models\FlightSchedule;
use App\Services\FlightSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class FlightSearchController extends Controller
{
    public function __construct(
        private readonly FlightSearchService $flightSearchService
    ) {
    }

    public function search(Request $request): View
    {
        $this->applyLocale($request);

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
        $this->applyLocale($request);

        $validated = $request->validated();

        Log::info('Flight search requested', [
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'departure_date' => $validated['departure_date'],
            'passenger_count' => $validated['passenger_count'],
            'seat_class' => $validated['seat_class'],
        ]);

        try {
            $flights = $this->flightSearchService->search($validated);
        } catch (Throwable $throwable) {
            report($throwable);

            throw $throwable;
        }

        $selectedDepartureSlots = $this->sanitizeSlots($request->input('departure_slots', []));
        $selectedArrivalSlots = $this->sanitizeSlots($request->input('arrival_slots', []));
        $flights = $this->filterFlightsByTimeSlots($flights, $selectedDepartureSlots, $selectedArrivalSlots);

        return view('flights.results', [
            'flights' => $flights,
            'criteria' => $validated,
            'searchParams' => $validated,
            'selectedDepartureSlots' => $selectedDepartureSlots,
            'selectedArrivalSlots' => $selectedArrivalSlots,
            'timeSlotOptions' => $this->timeSlotOptions(),
        ]);
    }

    public function availableDates(Request $request): JsonResponse
    {
        $this->applyLocale($request);

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

    private function applyLocale(Request $request): void
    {
        $locale = strtolower((string) $request->session()->get('ui_lang', 'id'));
        if (!in_array($locale, ['id', 'en'], true)) {
            $locale = 'id';
            $request->session()->put('ui_lang', $locale);
        }

        App::setLocale($locale);
    }

    private function sanitizeSlots(mixed $slots): array
    {
        if (!is_array($slots)) {
            return [];
        }

        $allowed = array_keys($this->timeSlotOptions());
        return array_values(array_intersect($allowed, array_map('strval', $slots)));
    }

    private function filterFlightsByTimeSlots(Collection $flights, array $departureSlots, array $arrivalSlots): Collection
    {
        $slotOptions = $this->timeSlotOptions();

        return $flights->filter(function ($flight) use ($departureSlots, $arrivalSlots, $slotOptions): bool {
            $departure = substr((string) $flight->departure_time, 0, 5);
            $arrival = substr((string) $flight->arrival_time, 0, 5);

            $isDepartureMatch = $departureSlots === [] || $this->matchesAnySlot($departure, $departureSlots, $slotOptions);
            $isArrivalMatch = $arrivalSlots === [] || $this->matchesAnySlot($arrival, $arrivalSlots, $slotOptions);

            return $isDepartureMatch && $isArrivalMatch;
        })->values();
    }

    private function matchesAnySlot(string $time, array $slots, array $slotOptions): bool
    {
        foreach ($slots as $slot) {
            $slotConfig = $slotOptions[$slot] ?? null;
            if ($slotConfig === null) {
                continue;
            }

            if ($time >= $slotConfig['from'] && $time < $slotConfig['to']) {
                return true;
            }
        }

        return false;
    }

    private function timeSlotOptions(): array
    {
        return [
            'dawn' => ['from' => '00:00', 'to' => '06:00'],
            'morning' => ['from' => '06:00', 'to' => '12:00'],
            'afternoon' => ['from' => '12:00', 'to' => '18:00'],
            'evening' => ['from' => '18:00', 'to' => '24:00'],
        ];
    }
}