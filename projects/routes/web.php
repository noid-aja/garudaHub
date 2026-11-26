<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WinnerController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard.index')
        : redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1')->name('register.store');
});

Route::get('/projects', [ProjectController::class, 'index'])
    ->name('projects.index');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::resource('projects', ProjectController::class)
        ->except(['index', 'show']);

    Route::post('/projects/{project}/bids', [BidController::class, 'store'])
        ->name('bids.store');

    Route::get('/projects/{project}/bids/{bid}/edit', [BidController::class, 'edit'])
        ->name('bids.edit');

    Route::put('/projects/{project}/bids/{bid}', [BidController::class, 'update'])
        ->name('bids.update');

    Route::delete('/projects/{project}/bids/{bid}', [BidController::class, 'destroy'])
        ->name('bids.destroy');

    Route::post('/projects/{project}/bids/{bid}/winner', [WinnerController::class, 'store'])
        ->name('bids.winner');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    Route::get('/dashboard/klien', [DashboardController::class, 'klien'])
        ->name('dashboard.klien');

    Route::get('/dashboard/developer', [DashboardController::class, 'developer'])
        ->name('dashboard.developer');
        
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->name('dashboard.admin');
});

Route::get('/projects/{project}', [ProjectController::class, 'show'])
    ->name('projects.show');
