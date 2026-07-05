<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\TicketController;
use App\Http\Controllers\Auth\AuthController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1'
], function () {
    // auth
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('me', [AuthController::class, 'me'])->name('me');

    Route::group(['prefix' => 'user'], function () {
        Route::apiResource('tickets', TicketController::class)->names('user.tickets');
    });
});
