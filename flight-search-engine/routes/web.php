<?php

use App\Http\Controllers\FlightSearchController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/flights/search');

Route::get('/flights/search', [FlightSearchController::class, 'search'])->name('flights.search');
Route::get('/flights/available-dates', [FlightSearchController::class, 'availableDates'])->name('flights.available-dates');
Route::get('/flights/results', [FlightSearchController::class, 'results'])->name('flights.results');
Route::get('/flights/{flightSchedule}', [BookingController::class, 'showFlight'])->name('flights.show');
Route::get('/bookings/{flightSchedule}/create', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings/{flightSchedule}', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/{flightSchedule}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
Route::post('/bahasa/{lang}', [BookingController::class, 'switchLanguage'])->name('language.switch');
