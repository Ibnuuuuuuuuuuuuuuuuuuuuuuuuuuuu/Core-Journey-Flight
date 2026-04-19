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

            <div class="mt-6">
                <form method="POST" action="{{ route('bookings.confirm-payment') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ session('booking_id') }}">
                    <input type="hidden" name="payment_status" value="successful">
                    <input type="hidden" name="payment_method" value="demo">

                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-sm font-medium text-white">{{ __('payment_method') }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ __('demo_payment_note') }}</p>
                        <div class="mt-3">
                            <div class="flex items-center gap-2">
                                <input type="radio" id="demo_payment" name="payment_method" value="demo" checked class="h-4 w-4 text-sky-500">
                                <label for="demo_payment" class="text-sm text-slate-200">{{ __('demo_payment') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <a
                            href="{{ $backToFormUrl }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-5 py-2.5 text-sm font-medium text-slate-200 transition hover:border-sky-500/40 hover:bg-sky-500/10 hover:text-white"
                        >
                            {{ __('back_to_fill_data') }}
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-500/20 transition hover:from-sky-400 hover:to-indigo-500 hover:shadow-sky-500/35"
                        >
                            {{ __('confirm_payment') }}
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
