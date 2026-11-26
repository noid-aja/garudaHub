
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::redirect('/', '/projects');

Route::resource('projects', ProjectController::class);
