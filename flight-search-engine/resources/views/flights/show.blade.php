@extends('layouts.app')

@section('title', __('flight_detail_page_title'))

@section('content')
    @php
        $departureDate = optional($flight->departure_date)->format('d M Y') ?? '-';
        $departureTime = \Illuminate\Support\Str::substr((string) $flight->departure_time, 0, 5);
        $arrivalTime = \Illuminate\Support\Str::substr((string) $flight->arrival_time, 0, 5);
        $selectedSeatClass = strtolower((string) $seatClass);
        $selectedSeatClassLabel = \Illuminate\Support\Str::of($selectedSeatClass)->replace('_', ' ')->title();
        $timeFilters = $timeFilters ?? ['departure_slots' => [], 'arrival_slots' => []];
        $routeText = trim((string) $flight->origin) . ' → ' . trim((string) $flight->destination);
        $currentDetailUrl = request()->fullUrl();
        $backToResultsUrl = request()->query('back_to_results');
        if (!is_string($backToResultsUrl) || trim($backToResultsUrl) === '') {
            $backToResultsUrl = route('flights.results', [
                'origin' => $flight->origin,
                'destination' => $flight->destination,
                'departure_date' => optional($flight->departure_date)->toDateString(),
                'passenger_count' => $passengerCount,
                'seat_class' => $selectedSeatClass,
                'departure_slots' => $timeFilters['departure_slots'],
                'arrival_slots' => $timeFilters['arrival_slots'],
            ]);
        }
        $facilities = [
            __('facility_cabin_baggage'),
            __('facility_checked_baggage'),
            __('facility_seat'),
            __('facility_entertainment'),
            __('facility_snack'),
            __('facility_reschedule'),
        ];
        $seatClassCards = $flight->seatClasses->map(function ($item) use ($flight, $passengerCount, $currentDetailUrl, $timeFilters, $selectedSeatClass, $backToResultsUrl) {
            $seatClassValue = strtolower((string) $item->seat_class);

            return [
                'value' => $seatClassValue,
                'label' => \Illuminate\Support\Str::of($seatClassValue)->replace('_', ' ')->title(),
                'price' => (float) $item->class_price,
                'seats' => (int) $item->available_seats,
                'selected' => $seatClassValue === $selectedSeatClass,
                'bookingUrl' => route('bookings.create', array_merge([
                    'flightSchedule' => $flight->id,
                    'origin' => $flight->origin,
                    'destination' => $flight->destination,
                    'departure_date' => optional($flight->departure_date)->toDateString(),
                    'passenger_count' => $passengerCount,
                    'seat_class' => $seatClassValue,
                    'back_to_results' => $backToResultsUrl,
                    'back_to_detail' => $currentDetailUrl,
                ], $timeFilters)),
            ];
        });
    @endphp

    <div class="mx-auto max-w-6xl space-y-8">
        <header class="space-y-4 text-center">
            <div class="flex flex-wrap items-center justify-center gap-2">
                <span class="rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-xs font-medium text-sky-200">{{ __('verify_schedule') }}</span>
                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">{{ $flight->flight_number }}</span>
                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">{{ $flight->airline?->airline_name ?? __('airline_unavailable') }}</span>
            </div>
            <div class="space-y-2">
                <h1 class="text-2xl font-bold tracking-tight text-white sm:text-4xl">{{ __('flight_detail_heading') }}</h1>
                <p class="mx-auto max-w-3xl text-sm text-slate-400 sm:text-base">{{ __('flight_detail_subheading') }}</p>
            </div>
        </header>

        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/60 shadow-2xl shadow-black/30 backdrop-blur-sm">
            <div class="border-b border-white/10 bg-gradient-to-r from-sky-500/15 via-indigo-500/10 to-emerald-500/15 px-6 py-6 sm:px-8">
                <div class="flex flex-col items-center gap-4 text-center sm:gap-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-300">{{ __('travel_route') }}</p>
                    <p class="text-2xl font-bold text-white sm:text-3xl">{{ $routeText }}</p>
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <span class="rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-xs font-medium text-sky-200">{{ $departureDate }}</span>
                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">{{ $departureTime }} - {{ $arrivalTime }}</span>
                        <span class="rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-200">{{ $selectedSeatClassLabel ?: __('class') }}</span>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 px-6 py-6 sm:px-8 xl:grid-cols-[1.15fr_0.85fr]">
                <div class="space-y-6">
                    <article class="rounded-2xl border border-white/10 bg-slate-950/45 p-5 shadow-lg shadow-black/10">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('schedule_overview') }}</p>
                                <p class="mt-1 text-sm text-slate-400">{{ __('flight_facility_note') }}</p>
                            </div>
                            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">{{ $flight->airline?->airline_name ?? __('airline_unavailable') }}</span>
                        </div>

                        <div class="mt-5 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-xs uppercase tracking-wider text-slate-500">{{ __('departure') }}</p>
                                <p class="mt-2 text-2xl font-bold text-white">{{ $departureTime }}</p>
                                <p class="mt-1 text-sm text-slate-300">{{ $flight->origin }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-xs uppercase tracking-wider text-slate-500">{{ __('arrival') }}</p>
                                <p class="mt-2 text-2xl font-bold text-white">{{ $arrivalTime }}</p>
                                <p class="mt-1 text-sm text-slate-300">{{ $flight->destination }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-xs uppercase tracking-wider text-slate-500">{{ __('date') }}</p>
                                <p class="mt-2 text-lg font-semibold text-white">{{ $departureDate }}</p>
                                <p class="mt-1 text-sm text-slate-300">{{ __('flight_detail_page_title') }}</p>
                            </div>
                        </div>
                    </article>

                    <article class="rounded-2xl border border-white/10 bg-slate-950/45 p-5 shadow-lg shadow-black/10">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('aircraft_facilities') }}</p>
                                <p class="mt-1 text-sm text-slate-400">{{ __('flight_facility_note') }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($facilities as $facility)
                                <span class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-slate-200">
                                    {{ $facility }}
                                </span>
                            @endforeach
                        </div>
                    </article>
                </div>

                <aside class="space-y-6">
                    <article class="rounded-2xl border border-white/10 bg-slate-950/45 p-5 shadow-lg shadow-black/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('selected_summary') }}</p>
                        <div class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                                <span class="text-slate-300">{{ __('class') }}</span>
                                <span class="font-semibold text-white">{{ $selectedSeatClassLabel ?: '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                                <span class="text-slate-300">{{ __('label_passengers') }}</span>
                                <span class="font-semibold text-white">{{ $passengerCount }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-sky-500/20 bg-sky-500/10 px-4 py-3">
                                <span class="text-sky-100">{{ __('selected_seat_class_hint') }}</span>
                                <span class="font-semibold text-white">{{ $selectedSeatClassLabel ?: __('choose_class') }}</span>
                            </div>
                        </div>
                    </article>

                    <article class="rounded-2xl border border-white/10 bg-slate-950/45 p-5 shadow-lg shadow-black/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('route_detail_heading') }}</p>
                        <div class="mt-4 space-y-3 text-sm">
                            <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                                <p class="text-xs uppercase tracking-wider text-slate-500">{{ __('travel_route') }}</p>
                                <p class="mt-1 font-medium text-white">{{ $routeText }}</p>
                            </div>
                            <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                                <p class="text-xs uppercase tracking-wider text-slate-500">{{ __('distance') }}</p>
                                <p class="mt-1 font-medium text-white">{{ $flight->route?->distance_km ? number_format((float) $flight->route->distance_km) . ' km' : __('route_distance_unavailable') }}</p>
                            </div>
                        </div>
                    </article>
                </aside>
            </div>
        </section>

        <section class="space-y-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('choose_class') }}</h2>
                    <p class="mt-1 text-sm text-slate-400">{{ __('choose_class_desc') }}</p>
                </div>
                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">{{ __('seat_class_fixed_note') }}</span>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                @forelse ($seatClassCards as $seatClassCard)
                    <article class="relative overflow-hidden rounded-3xl border {{ $seatClassCard['selected'] ? 'border-sky-400/40 bg-sky-500/10' : 'border-white/10 bg-slate-900/60' }} p-5 shadow-xl shadow-black/20 backdrop-blur-sm transition hover:-translate-y-0.5 hover:border-sky-400/30">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-xl font-bold text-white">{{ $seatClassCard['label'] }}</h3>
                                    @if ($seatClassCard['selected'])
                                        <span class="rounded-full border border-sky-400/30 bg-sky-400/10 px-2.5 py-1 text-xs font-medium text-sky-200">{{ __('selected_seat_class_badge') }}</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm text-slate-400">{{ __('seat_class_card_desc', ['class' => $seatClassCard['label']]) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs uppercase tracking-wider text-slate-500">{{ __('price_per_ticket') }}</p>
                                <p class="mt-1 text-2xl font-bold text-white">Rp{{ number_format($seatClassCard['price'], 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                <p class="text-xs uppercase tracking-wider text-slate-500">{{ __('available_seats') }}</p>
                                <p class="mt-1 text-lg font-semibold text-white">{{ $seatClassCard['seats'] }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 sm:col-span-2">
                                <p class="text-xs uppercase tracking-wider text-slate-500">{{ __('selected_summary') }}</p>
                                <p class="mt-1 text-sm text-slate-300">{{ __('seat_class_price_hint') }}</p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-sm text-slate-300">
                                <span class="font-medium text-white">{{ __('route') }}:</span>
                                {{ $flight->origin }} → {{ $flight->destination }}
                            </div>
                            <a
                                href="{{ $seatClassCard['bookingUrl'] }}"
                                class="inline-flex items-center justify-center rounded-xl {{ $seatClassCard['selected'] ? 'bg-gradient-to-r from-sky-500 to-indigo-600 text-white shadow-lg shadow-sky-500/20 hover:from-sky-400 hover:to-indigo-500' : 'border border-white/10 bg-white/5 text-slate-200 hover:border-sky-500/40 hover:bg-sky-500/10 hover:text-white' }} px-4 py-2.5 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
                            >
                                {{ $seatClassCard['selected'] ? __('continue_to_payment') : __('choose_this_class') }}
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/15 bg-slate-900/40 px-6 py-10 text-center text-sm text-slate-400">
                        {{ __('class_price_unavailable') }}
                    </div>
                @endforelse
            </div>
        </section>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <a
                href="{{ $backToResultsUrl }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:border-sky-500/40 hover:bg-sky-500/10 hover:text-white"
            >
                {{ __('back_to_results') }}
            </a>
            <p class="text-sm text-slate-400">{{ __('seat_class_fixed_note') }}</p>
        </div>
    </div>
@endsection
