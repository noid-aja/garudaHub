
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\WinnerController;
use App\Http\Controllers\DashboardController;

Route::redirect('/', '/projects');

Route::resource('projects', ProjectController::class);

Route::post('/projects/{project}/bids',
    [BidController::class, 'store'])
    ->name('bids.store');

Route::post(
    '/projects/{project}/bids/{bid}/winner',
    [WinnerController::class, 'store']
)->name('bids.winner');

Route::get('/projects/{project}/bids/{bid}/edit',
    [BidController::class, 'edit'])
    ->name('bids.edit');

// Dashboard simulasi pengguna
Route::get('/dashboard', [
    DashboardController::class, 'index'
])->name('dashboard.index');

Route::get('/dashboard/klien/{user}', [
    DashboardController::class, 'klien'
])->name('dashboard.klien');

Route::get('/dashboard/developer/{user}', [
    DashboardController::class, 'developer'
])->name('dashboard.developer');

Route::get('/dashboard/admin', [
    DashboardController::class, 'admin'
])->name('dashboard.admin');

Route::put('/projects/{project}/bids/{bid}',
    [BidController::class, 'update'])
    ->name('bids.update');

Route::delete('/projects/{project}/bids/{bid}',
    [BidController::class, 'destroy'])
    ->name('bids.destroy');
