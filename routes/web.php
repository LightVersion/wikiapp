<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/**
 * Application mainpage controller
 */
Route::get('/', [HomeController::class, 'index'])->name('index');