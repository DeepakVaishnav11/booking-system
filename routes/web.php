<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Auth\RegisterController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'bookings.create' : 'login');
});

Auth::routes(['verify' => true]);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
});

// routes/web.php

Route::get('verify-otp', [RegisterController::class, 'showOtpForm'])->name('verify.otp');
Route::post('verify-otp', [RegisterController::class, 'verifyOtp'])->name('verify.otp.submit');
