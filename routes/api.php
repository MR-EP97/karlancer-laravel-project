<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

use Illuminate\Support\Facades\Route;

Route::get('/test',function (){
    return 'yes';
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/email/verify/{id}', [AuthController::class, 'verify'])->name('verification.verify')->middleware(['auth:sanctum','signed']);
Route::post('/email/resend', [AuthController::class, 'resend'])->middleware('auth:sanctum')->name('verification.resend');

Route::apiResource('/tasks', TaskController::class)->middleware(['auth:sanctum', 'verified']);

