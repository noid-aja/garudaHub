
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BidController;

Route::redirect('/', '/projects');

Route::resource('projects', ProjectController::class);

Route::post('/projects/{project}/bids',
    [BidController::class, 'store'])
    ->name('bids.store');

Route::get('/projects/{project}/bids/{bid}/edit',
    [BidController::class, 'edit'])
    ->name('bids.edit');

Route::put('/projects/{project}/bids/{bid}',
    [BidController::class, 'update'])
    ->name('bids.update');

Route::delete('/projects/{project}/bids/{bid}',
    [BidController::class, 'destroy'])
    ->name('bids.destroy');
