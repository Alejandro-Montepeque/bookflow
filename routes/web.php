<?php

use App\Http\Controllers\AvailabilityRuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// All authenticated provider-facing routes.
Route::middleware(['auth', 'verified'])->group(function () {
    // Service CRUD — controller methods handle authorization via policy.
    Route::resource('services', ServiceController::class)
        ->except(['show']);

    // Availability rules: a single endpoint replaces the entire set for a service.
    Route::put('services/{service}/availability', [AvailabilityRuleController::class, 'sync'])
        ->name('services.availability.sync');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
