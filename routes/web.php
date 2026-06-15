<?php

use App\Http\Controllers\AvailabilityRuleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// All authenticated provider-facing routes.
Route::middleware(['auth', 'verified'])->group(function () {
    // Service CRUD — controller methods handle authorization via policy.
    Route::resource('services', ServiceController::class)
        ->except(['show']);

    // Availability rules: a single endpoint replaces the entire set for a service.
    Route::put('services/{service}/availability', [AvailabilityRuleController::class, 'sync'])
        ->name('services.availability.sync');

    // Bookings management (provider side).
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public booking flow — anyone with the URL can book; no authentication required.
// Rate-limited to discourage abuse: 30 reads/min and 10 writes/min per IP.
Route::middleware('throttle:30,1')->group(function () {
    Route::get('/u/{user}', [PublicBookingController::class, 'profile'])
        ->name('public.profile');
    Route::get('/u/{user}/{service}', [PublicBookingController::class, 'show'])
        ->name('public.booking.show');
    Route::get('/booking/{token}', [PublicBookingController::class, 'confirmation'])
        ->name('public.booking.confirmation');
    Route::get('/cancel/{token}', [PublicBookingController::class, 'cancelShow'])
        ->name('public.booking.cancel.show');
});

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/u/{user}/{service}', [PublicBookingController::class, 'store'])
        ->name('public.booking.store');
    Route::post('/cancel/{token}', [PublicBookingController::class, 'cancelStore'])
        ->name('public.booking.cancel.store');
});

require __DIR__.'/auth.php';
