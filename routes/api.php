<?php

use App\Http\Controllers\Admin\AgentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\TicketController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;

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

    // user routes
    Route::group([
        'prefix' => 'user',
        'middleware' => 'auth:api'
    ], function () {
        Route::apiResource('tickets', TicketController::class)->names('user.tickets');
    });

    // comments
    Route::group([
        'prefix' => 'comments'
    ], function () {
        Route::get('/{id}', [CommentController::class, 'show']);
        Route::post('/', [CommentController::class, 'store']);
        Route::put('/{id}', [CommentController::class, 'update']);
        Route::delete('/{id}', [CommentController::class, 'destroy']);
    });

    // superadmin
    Route::group([
        'prefix' => 'admin',
        'middleware' => 'auth:api'
    ], function () {
        Route::group([
            'prefix' => 'tickets'
        ], function () {
            Route::get('/', [AdminTicketController::class, 'index']);
            Route::post('/{id}/assign', [AdminTicketController::class, 'assign']);
        });

        Route::group([
            'prefix' => 'agents'
        ], function () {
            Route::get('/', [AgentController::class, 'index']);
            Route::post('/', [AgentController::class, 'store']);
            Route::put('/{id}', [AgentController::class, 'update']);
            Route::put('/{id}/reset-password', [AgentController::class, 'resetPassword']);
            Route::delete('/{id}', [AgentController::class, 'destroy']);
        });
    });
});
