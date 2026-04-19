<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\FlightSchedule;
use App\Services\BookingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function showFlight(Request $request, FlightSchedule $flightSchedule): View
    {
        $this->applyLocale($request);

        $seatClass = (string) $request->query('seat_class', '');
        $passengerCount = max(1, (int) $request->query('passenger_count', '1'));
        $timeFilters = $this->extractTimeFilters($request);
        $backToResultsUrl = $this->resolveInternalBackUrl(
            $request->query('back_to_results'),
            'flights.results',
            array_merge([
                'origin' => (string) $request->query('origin', (string) $flightSchedule->origin),
                'destination' => (string) $request->query('destination', (string) $flightSchedule->destination),
                'departure_date' => (string) $request->query('departure_date', optional($flightSchedule->departure_date)->toDateString()),
                'passenger_count' => $passengerCount,
                'seat_class' => $seatClass,
            ], $timeFilters)
        );

        $flightSchedule->load(['airline', 'seatClasses']);

        $selectedSeatClass = $flightSchedule->seatClasses
            ->firstWhere('seat_class', $seatClass);

        return view('flights.show', [
            'flight' => $flightSchedule,
            'passengerCount' => $passengerCount,
            'seatClass' => $seatClass,
            'selectedSeatPrice' => $selectedSeatClass?->class_price,
            'timeFilters' => $timeFilters,
            'backToResultsUrl' => $backToResultsUrl,
        ]);
    }

    public function create(Request $request, FlightSchedule $flightSchedule): View
    {
        $this->applyLocale($request);

        $seatClass = (string) $request->query('seat_class', '');
        $passengerCount = max(1, (int) $request->query('passenger_count', '1'));
        $timeFilters = $this->extractTimeFilters($request);
        $backToResultsUrl = $this->resolveInternalBackUrl(
            $request->query('back_to_results'),
            'flights.results',
            array_merge([
                'origin' => (string) $request->query('origin', (string) $flightSchedule->origin),
                'destination' => (string) $request->query('destination', (string) $flightSchedule->destination),
                'departure_date' => (string) $request->query('departure_date', optional($flightSchedule->departure_date)->toDateString()),
                'passenger_count' => $passengerCount,
                'seat_class' => $seatClass,
            ], $timeFilters)
        );
        $backToDetailUrl = $this->resolveInternalBackUrl(
            $request->query('back_to_detail'),
            'flights.show',
            array_merge([
                'flightSchedule' => $flightSchedule->id,
                'passenger_count' => $passengerCount,
                'seat_class' => $seatClass,
                'back_to_results' => $backToResultsUrl,
            ], $timeFilters)
        );

        $flightSchedule->load(['airline']);

        $selectedSeatClass = $flightSchedule->seatClasses()
            ->where('seat_class', $seatClass)
            ->first();

        return view('bookings.create', [
            'flight' => $flightSchedule,
            'passengerCount' => $passengerCount,
            'seatClass' => $seatClass,
            'seatPrice' => $selectedSeatClass?->class_price,
            'timeFilters' => $timeFilters,
            'backToDetailUrl' => $backToDetailUrl,
            'backToResultsUrl' => $backToResultsUrl,
            'currentCreateUrl' => $request->fullUrl(),
        ]);
    }

    public function store(StoreBookingRequest $request, FlightSchedule $flightSchedule): RedirectResponse
    {
        $this->applyLocale($request);

        $validated = $request->validated();
        $timeFilters = [
            'departure_slots' => $validated['departure_slots'] ?? [],
            'arrival_slots' => $validated['arrival_slots'] ?? [],
        ];

        // Create booking
        $bookingService = new BookingService();
        $booking = $bookingService->createBooking([
            'flight_schedule_id' => $flightSchedule->id,
            'full_name' => $validated['full_name'],
            'nik' => $validated['nik'],
            'seat_class' => $validated['seat_class'],
            'passenger_count' => $validated['passenger_count'],
            'ancillary_services' => $validated['ancillary_services'] ?? [],
        ]);

        return redirect()
            ->route('bookings.payment', [
                'flightSchedule' => $flightSchedule->id,
                'booking_id' => $booking->id,
                'seat_class' => $validated['seat_class'],
                'passenger_count' => $validated['passenger_count'],
                'back_to_detail' => $validated['back_to_detail'] ?? null,
                'back_to_results' => $validated['back_to_results'] ?? null,
                'back_to_form' => $validated['back_to_form'] ?? null,
                'departure_slots' => $timeFilters['departure_slots'],
                'arrival_slots' => $timeFilters['arrival_slots'],
            ])
            ->with('booking_form_success', 'Data penumpang berhasil divalidasi. Silakan lanjut ke tahap pembayaran.')
            ->with('booking_payload', $validated)
            ->with('booking_id', $booking->id);
    }

    public function payment(Request $request, FlightSchedule $flightSchedule): View
    {
        $this->applyLocale($request);

        $seatClass = (string) $request->query('seat_class', '');
        $passengerCount = max(1, (int) $request->query('passenger_count', '1'));
        $timeFilters = $this->extractTimeFilters($request);
        $backToResultsUrl = $this->resolveInternalBackUrl($request->query('back_to_results'), 'flights.search');
        $backToDetailUrl = $this->resolveInternalBackUrl(
            $request->query('back_to_detail'),
            'flights.show',
            array_merge([
                'flightSchedule' => $flightSchedule->id,
                'passenger_count' => $passengerCount,
                'seat_class' => $seatClass,
                'back_to_results' => $backToResultsUrl,
            ], $timeFilters)
        );
        $backToFormUrl = $this->resolveInternalBackUrl(
            $request->query('back_to_form'),
            'bookings.create',
            array_merge([
                'flightSchedule' => $flightSchedule->id,
                'passenger_count' => $passengerCount,
                'seat_class' => $seatClass,
                'back_to_detail' => $backToDetailUrl,
                'back_to_results' => $backToResultsUrl,
            ], $timeFilters)
        );

        return view('bookings.payment', [
            'flight' => $flightSchedule,
            'backToFormUrl' => $backToFormUrl,
        ]);
    }

    public function confirmPayment(Request $request): \Illuminate\View\View|bool
    {
        $this->applyLocale($request);

        $bookingId = $request->input('booking_id');
        $paymentStatus = $request->input('payment_status');
        $paymentMethod = $request->input('payment_method');

        // Validate input
        $request->validate([
            'booking_id' => 'required|integer|exists:bookings,id',
            'payment_status' => 'required|string|in:successful,failed',
            'payment_method' => 'required|string',
        ]);

        $bookingService = new BookingService();
        $success = $bookingService->confirmPayment($bookingId, $paymentStatus, $paymentMethod);

        if (!$success) {
            return false; // Or redirect to error page
        }

        $booking = \App\Models\Booking::with('flightSchedule.airline')->find($bookingId);

        return view('bookings.success', [
            'booking' => $booking,
        ]);
    }

    public function downloadEticket(Request $request, Booking $booking): \Illuminate\Http\Response
    {
        $this->applyLocale($request);

        // Ensure booking is paid and belongs to user (in real app, add auth check)
        if ($booking->status !== 'paid') {
            abort(403, 'Booking not paid');
        }

        $booking->load(['flightSchedule.airline', 'tickets']);

        $pdf = Pdf::loadView('pdf.eticket', [
            'booking' => $booking,
        ]);

        return $pdf->download('e-ticket-' . $booking->booking_code . '.pdf');
    }

    public function switchLanguage(Request $request, string $lang): RedirectResponse
    {
        $normalized = strtolower($lang);
        if (!in_array($normalized, ['id', 'en'], true)) {
            $normalized = 'id';
        }

        $request->session()->put('ui_lang', $normalized);
        App::setLocale($normalized);

        $previousUrl = url()->previous();

        return redirect()->to($previousUrl !== '' ? $previousUrl : route('flights.search'))->withInput();
    }

    private function extractTimeFilters(Request $request): array
    {
        return [
            'departure_slots' => $this->normalizeSlots($request->query('departure_slots', [])),
            'arrival_slots' => $this->normalizeSlots($request->query('arrival_slots', [])),
        ];
    }

    private function normalizeSlots(mixed $slots): array
    {
        if (!is_array($slots)) {
            return [];
        }

        $allowed = ['dawn', 'morning', 'afternoon', 'evening'];

        return array_values(array_intersect($allowed, array_map('strval', $slots)));
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

    private function resolveInternalBackUrl(mixed $candidate, string $fallbackRoute, array $fallbackParams = []): string
    {
        $candidateUrl = is_string($candidate) ? trim($candidate) : '';

        if ($candidateUrl !== '' && $this->isInternalUrl($candidateUrl)) {
            return $candidateUrl;
        }

        return route($fallbackRoute, $fallbackParams);
    }

    private function isInternalUrl(string $url): bool
    {
        if (Str::startsWith($url, '/')) {
            return true;
        }

        $appUrl = trim((string) config('app.url'));
        if ($appUrl !== '' && Str::startsWith($url, rtrim($appUrl, '/'))) {
            return true;
        }

        return false;
    }
}
