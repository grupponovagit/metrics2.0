<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth:sanctum')->get('/user-details', [UserController::class, 'userDetails']);

Route::middleware('auth:sanctum')->post('/user-logout', [UserController::class, 'logOutUser']);

