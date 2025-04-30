<?php

use App\Features\Users\ShowUser;
use App\Features\Users\ListUsers;
use App\Features\Users\CreateUser;
use App\Features\Users\DeleteUser;
use App\Features\Users\UpdateUser;
use Illuminate\Support\Facades\Route;
use App\Features\Addresses\ShowUserAddress;
use App\Features\Addresses\CreateUserAddress;
use App\Features\Addresses\ListUserAddresses;

// User routes
Route::middleware(['swagger.cors'])->group(function () {
    // User routes
    Route::post('/users', CreateUser::class);
    Route::get('/users/{userId}', ShowUser::class);
    Route::get('/users', ListUsers::class);
    Route::put('/users/{userId}', UpdateUser::class);
    Route::delete('/users/{userId}', DeleteUser::class);

    // Address routes
    Route::get('/users/{userId}/addresses', ListUserAddresses::class);
    Route::get('/users/{userId}/addresses/{addressId}', ShowUserAddress::class);
    Route::post('/users/{userId}/addresses', CreateUserAddress::class);
});
