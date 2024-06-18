<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::apiResource('/tasks', TaskController::class)->middleware(['auth:sanctum', 'verified']);

Route::get('email/verify', [])->middleware(['auth:sanctum', 'verified'])->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->middleware(['auth:sanctum', 'signed'])->name('verification.verify');
Route::post('email/resend', 'Auth\VerificationController@resend')->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.resend');
