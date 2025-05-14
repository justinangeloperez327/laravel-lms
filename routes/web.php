<?php

declare(strict_types=1);

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\BookRequestController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::resource('books', BookController::class);
    Route::resource('book-requests', BookRequestController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('borrowings', BorrowingController::class);
    Route::resource('penalties', PenaltyController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
