@extends('layouts.app')

@section('title', __('results_page_title'))

@section('content')
    @php
        $departureLabel = \Illuminate\Support\Carbon::parse($criteria['departure_date'])->format('d/m/Y');
        $passengerCount = (int) ($searchParams['passenger_count'] ?? $criteria['passenger_count'] ?? 1);
        $selectedSeatClass = (string) ($searchParams['seat_class'] ?? $criteria['seat_class'] ?? '');
        $seatClassLabel = str_replace('_', ' ', $selectedSeatClass);
        $selectedDepartureSlots = $selectedDepartureSlots ?? [];
        $selectedArrivalSlots = $selectedArrivalSlots ?? [];
        $timeSlotOptions = $timeSlotOptions ?? [];
        $filterQuery = [
            'origin' => $criteria['origin'],
            'destination' => $criteria['destination'],
            'departure_date' => $criteria['departure_date'],
            'passenger_count' => $passengerCount,
            'seat_class' => $selectedSeatClass,
            'departure_slots' => $selectedDepartureSlots,
            'arrival_slots' => $selectedArrivalSlots,
        ];
        $slotLabels = [
            'dawn' => __('slot_dawn'),
            'morning' => __('slot_morning'),
            'afternoon' => __('slot_afternoon'),
            'evening' => __('slot_evening'),
        ];
    @endphp

    <div class="space-y-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-medium text-sky-400">{{ __('search_results') }}</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                    {{ $criteria['origin'] }}
                    <span class="mx-2 text-slate-500">→</span>
                    {{ $criteria['destination'] }}
                </h1>
                <p class="mt-2 text-slate-400">{{ $departureLabel }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center rounded-lg bg-sky-500/15 px-2.5 py-1 text-xs font-medium text-sky-200 ring-1 ring-sky-500/25">
                        {{ __('label_passengers') }}: {{ $passengerCount }}
                    </span>
                </div>
            </div>
            <a
                href="{{ route('flights.search') }}"
                class="inline-flex items-center justify-center gap-2 self-start rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:border-sky-500/40 hover:bg-sky-500/10 hover:text-white"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                {{ __('change_search') }}
            </a>
        </div>

        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-900/60 px-4 py-4 shadow-lg shadow-black/20 backdrop-blur-sm sm:px-5">
                <div>
                    <p class="text-sm font-semibold text-white">{{ __('schedule_filter') }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ __('schedule_filter_desc') }}</p>
                </div>
                <button
                    type="button"
                    id="open-filter-dialog"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:border-sky-500/40 hover:bg-sky-500/10 hover:text-white"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M4 6h16M7 12h10M10 18h4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('filter_button') }}
                </button>
            </div>

            <dialog
                id="filter-dialog"
                class="w-full max-w-2xl rounded-3xl border border-white/10 bg-slate-950 p-0 text-slate-100 shadow-2xl shadow-black/50 backdrop:bg-slate-950/70"
            >
                <form method="GET" action="{{ route('flights.results') }}" class="space-y-6 p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-4 border-b border-white/10 pb-4">
                        <div>
                            <p class="text-sm font-semibold text-white">{{ __('schedule_filter') }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ __('schedule_filter_desc') }}</p>
                        </div>
                        <button type="button" id="close-filter-dialog" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-medium text-slate-200 transition hover:bg-white/10">{{ __('close_filter') }}</button>
                    </div>

                    <input type="hidden" name="origin" value="{{ $criteria['origin'] }}">
                    <input type="hidden" name="destination" value="{{ $criteria['destination'] }}">
                    <input type="hidden" name="departure_date" value="{{ $criteria['departure_date'] }}">
                    <input type="hidden" name="passenger_count" value="{{ $passengerCount }}">
                    <input type="hidden" name="seat_class" value="{{ $selectedSeatClass }}">

                    <div class="grid gap-4 md:grid-cols-2">
                        <section class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">{{ __('departure_time_category') }}</p>
                            <div class="mt-3 space-y-2">
                                @foreach ($timeSlotOptions as $slotKey => $slotRange)
                                    <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-300">
                                        <input
                                            type="checkbox"
                                            name="departure_slots[]"
                                            value="{{ $slotKey }}"
                                            @checked(in_array($slotKey, $selectedDepartureSlots, true))
                                            class="h-4 w-4 rounded border-white/20 bg-slate-900 text-sky-500 focus:ring-sky-500/50"
                                        >
                                        <span>{{ $slotLabels[$slotKey] ?? $slotKey }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </section>

                        <section class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">{{ __('arrival_time_category') }}</p>
                            <div class="mt-3 space-y-2">
                                @foreach ($timeSlotOptions as $slotKey => $slotRange)
                                    <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-300">
                                        <input
                                            type="checkbox"
                                            name="arrival_slots[]"
                                            value="{{ $slotKey }}"
                                            @checked(in_array($slotKey, $selectedArrivalSlots, true))
                                            class="h-4 w-4 rounded border-white/20 bg-slate-900 text-sky-500 focus:ring-sky-500/50"
                                        >
                                        <span>{{ $slotLabels[$slotKey] ?? $slotKey }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </section>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-white/10 pt-4 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('flights.results', $filterQuery) }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:border-sky-500/40 hover:bg-sky-500/10 hover:text-white">
                            {{ __('reset_filter') }}
                        </a>
                        <div class="flex gap-3">
                            <button type="button" id="cancel-filter-dialog" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:border-white/20 hover:bg-white/10 hover:text-white">
                                {{ __('cancel_filter') }}
                            </button>
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:from-sky-400 hover:to-indigo-500">
                                {{ __('apply_filter') }}
                            </button>
                        </div>
                    </div>
                </form>
            </dialog>

            <div>
        @if ($flights->isEmpty())
            <div
                class="rounded-2xl border border-dashed border-white/15 bg-slate-900/40 px-6 py-16 text-center backdrop-blur-sm"
            >
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-800 ring-1 ring-white/10">
                    <svg class="h-7 w-7 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                        <path d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12" />
                    </svg>
                </div>
                <h2 class="mt-6 text-lg font-semibold text-white">{{ __('no_flights_title') }}</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-slate-400">
                    {{ __('no_flights_desc') }}
                </p>
            </div>
        @else
            <ul class="space-y-4">
                @foreach ($flights as $flight)
                    @php
                        $pricePerPax = (float) ($flight->price ?? 0);
                        $totalPrice = $pricePerPax * max($passengerCount, 1);
                    @endphp
                    <li class="group rounded-2xl border border-white/10 bg-slate-900/50 p-5 shadow-lg shadow-black/20 backdrop-blur-sm transition hover:border-sky-500/25 hover:bg-slate-900/70 sm:p-6">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                            <div class="min-w-0 flex-1 space-y-4">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="text-lg font-bold tracking-tight text-white">{{ $flight->flight_number }}</span>
                                    @if ($flight->airline)
                                        <span class="text-sm text-slate-400">{{ $flight->airline->airline_name }}</span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-baseline gap-6 text-sm">
                                    <div>
                                        <p class="text-xs uppercase tracking-wider text-slate-500">{{ __('departure') }}</p>
                                        <p class="mt-0.5 font-mono text-lg font-semibold text-white">
                                            {{ \Illuminate\Support\Str::substr($flight->departure_time, 0, 5) }}
                                        </p>
                                        <p class="text-slate-400">{{ $flight->origin }}</p>
                                    </div>
                                    <div class="hidden h-px min-w-[2rem] flex-1 bg-gradient-to-r from-transparent via-white/20 to-transparent sm:block sm:max-w-[6rem]" aria-hidden="true"></div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wider text-slate-500">{{ __('arrival') }}</p>
                                        <p class="mt-0.5 font-mono text-lg font-semibold text-white">
                                            {{ \Illuminate\Support\Str::substr($flight->arrival_time, 0, 5) }}
                                        </p>
                                        <p class="text-slate-400">{{ $flight->destination }}</p>
                                    </div>
                                    @if ($flight->route && $flight->route->distance_km)
                                        <div class="w-full sm:w-auto">
                                            <p class="text-xs uppercase tracking-wider text-slate-500">{{ __('distance') }}</p>
                                            <p class="mt-0.5 text-slate-300">{{ number_format($flight->route->distance_km) }} km</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex shrink-0 flex-col items-stretch gap-2 border-t border-white/5 pt-4 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0">
                                <span class="inline-flex items-center self-start rounded-lg bg-indigo-500/15 px-2.5 py-1 text-xs font-medium text-indigo-200 ring-1 ring-indigo-500/25">
                                    {{ __('flight_class') }}: {{ $seatClassLabel ?: 'N/A' }}
                                </span>
                                <p class="text-xs text-slate-500">{{ __('price_per_ticket') }}</p>
                                <p class="text-xl font-bold text-white">
                                    Rp{{ number_format($pricePerPax, 0, ',', '.') }} <span class="text-sm font-medium text-slate-400">/ pax</span>
                                </p>
                                <p class="text-xs text-slate-500">{{ __('total_for_passengers', ['count' => $passengerCount]) }}</p>
                                <p class="text-2xl font-bold text-sky-300">Rp{{ number_format($totalPrice, 0, ',', '.') }}</p>
                                <a
                                    href="{{ route('flights.show', ['flightSchedule' => $flight->id, 'origin' => $criteria['origin'], 'destination' => $criteria['destination'], 'departure_date' => $criteria['departure_date'], 'passenger_count' => $passengerCount, 'seat_class' => $selectedSeatClass, 'departure_slots' => $selectedDepartureSlots, 'arrival_slots' => $selectedArrivalSlots, 'back_to_results' => request()->fullUrl()]) }}"
                                    class="mt-2 inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-500/20 transition hover:from-emerald-400 hover:to-sky-500 hover:shadow-emerald-500/35 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
                                >
                                    <span>{{ __('view_details') }}</span>
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const dialog = document.getElementById('filter-dialog');
                const openButton = document.getElementById('open-filter-dialog');
                const closeButton = document.getElementById('close-filter-dialog');
                const cancelButton = document.getElementById('cancel-filter-dialog');

                if (!dialog || !openButton) {
                    return;
                }

                const closeDialog = () => {
                    if (dialog.open) {
                        dialog.close();
                    }
                };

                openButton.addEventListener('click', () => {
                    if (typeof dialog.showModal === 'function') {
                        dialog.showModal();
                    }
                });

                closeButton?.addEventListener('click', closeDialog);
                cancelButton?.addEventListener('click', closeDialog);

                dialog.addEventListener('click', (event) => {
                    const bounds = dialog.getBoundingClientRect();
                    const clickedOutside = event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom;

                    if (clickedOutside) {
                        closeDialog();
                    }
                });
            })();
        </script>
    @endpush
@endsection