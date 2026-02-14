<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cms\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Cms\DashboardController;
use App\Http\Controllers\Cms\TicketController;

Route::group(['middleware' => 'guest'], function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost'])
        ->middleware('throttle:login')
        ->name('login.post');

    Route::get('/forgot-password', [AuthController::class, 'forgot'])->name('forgot');
    Route::post('/forgot-password', [AuthController::class, 'forgotPost'])
        ->middleware('throttle:forgot-password')
        ->name('forgot.post');
});

Route::group(['prefix' => 'reset-password'], function () {
    Route::get('/', [AuthController::class, 'resetPassword'])->name('reset_password');
    Route::post('/process', [AuthController::class, 'resetPasswordProcess'])
        ->middleware('throttle:reset-password')
        ->name('reset_password_process');
});

Route::group(['middleware' => 'auth'], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::group(['prefix' => 'cms'], function () {
        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index')->middleware(['role_or_permission:Admin|users-read']);
            Route::get('/create', [UserController::class, 'create'])->name('users.create')->middleware(['role_or_permission:Admin|users-create']);
            Route::post('/create', [UserController::class, 'store'])->name('users.store')->middleware(['role_or_permission:Admin|users-create']);
            Route::get('/{uuid}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware(['role_or_permission:Admin|users-update']);
            Route::post('/{uuid}/edit', [UserController::class, 'update'])->name('users.update')->middleware(['role_or_permission:Admin|users-update']);
            Route::post('/{uuid}/delete', [UserController::class, 'destroy'])->name('users.destroy')->middleware(['role_or_permission:Admin|users-delete']);

            Route::get('/{uuid}/profile', [UserController::class, 'profile'])->name('users.profile');
            Route::post('/{uuid}/profile', [UserController::class, 'updateProfile'])->name('users.profile.update');
        });

        Route::group(['prefix' => 'roles'], function () {
            Route::get('/', [RoleController::class, 'index'])->name('roles.index')->middleware(['role_or_permission:Admin|roles-read']);
            Route::get('/create', [RoleController::class, 'create'])->name('roles.create')->middleware(['role_or_permission:Admin|roles-create']);
            Route::post('/create', [RoleController::class, 'store'])->name('roles.store')->middleware(['role_or_permission:Admin|roles-create']);
            Route::get('/{uuid}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware(['role_or_permission:Admin|roles-update']);
            Route::post('/{uuid}/edit', [RoleController::class, 'update'])->name('roles.update')->middleware(['role_or_permission:Admin|roles-update']);
            Route::post('/{uuid}/delete', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware(['role_or_permission:Admin|roles-delete']);
        });

        Route::group(['prefix' => 'tickets'], function () {
            Route::get('/', [TicketController::class, 'index'])->name('tickets.index')->middleware(['role_or_permission:Admin|tickets-read']);
            Route::get('/create', [TicketController::class, 'create'])->name('tickets.create')->middleware(['role_or_permission:Admin|tickets-create']);
            Route::post('/create', [TicketController::class, 'store'])->name('tickets.store')->middleware(['role_or_permission:Admin|tickets-create']);
            Route::get('/{uuid}/edit', [TicketController::class, 'edit'])->name('tickets.edit')->middleware(['role_or_permission:Admin|tickets-update']);
            Route::post('/{uuid}/edit', [TicketController::class, 'update'])->name('tickets.update')->middleware(['role_or_permission:Admin|tickets-update']);
            Route::post('/{uuid}/delete', [TicketController::class, 'destroy'])->name('tickets.destroy')->middleware(['role_or_permission:Admin|tickets-delete']);
            Route::post('/{uuid}/take', [TicketController::class, 'takeTicket'])->name('tickets.take')->middleware(['role_or_permission:Admin|tickets-update']);
            Route::post('/{uuid}/reopen', [TicketController::class, 'reopenTicket'])->name('tickets.reopen')->middleware(['role_or_permission:Admin|tickets-update']);
            Route::post('/{uuid}/close', [TicketController::class, 'closeTicket'])->name('tickets.close')->middleware(['role_or_permission:Admin|tickets-update']);
            Route::get('/{uuid}', [TicketController::class, 'show'])->name('tickets.show')->middleware(['role_or_permission:Admin|tickets-read']);
        });
    });
});