@extends('layouts.app')

@section('title', __('payment_method'))

@section('content')
    <div class="mx-auto max-w-3xl">
        <section class="rounded-2xl border border-white/10 bg-slate-900/60 p-6 shadow-xl shadow-black/30 backdrop-blur-sm sm:p-8">
            <p class="text-sm font-medium text-sky-400">{{ __('next_step') }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ __('payment_method_page') }}</h1>
            <p class="mt-3 text-sm text-slate-300">
                {{ __('payment_placeholder_desc') }}
            </p>
            @if (session('booking_form_success'))
                <div class="mt-5 rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('booking_form_success') }}
                </div>
            @endif
            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a
                    href="{{ $backToFormUrl }}"
                    class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-5 py-2.5 text-sm font-medium text-slate-200 transition hover:border-sky-500/40 hover:bg-sky-500/10 hover:text-white"
                >
                    {{ __('back_to_fill_data') }}
                </a>
                <a
                    href="{{ route('flights.search') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-500/20 transition hover:from-sky-400 hover:to-indigo-500 hover:shadow-sky-500/35"
                >
                    {{ __('back_to_search') }}
                </a>
            </div>
        </section>
    </div>
@endsection
