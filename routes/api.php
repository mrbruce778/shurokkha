<?php
use App\Http\Controllers\AuthController;

Route::post('/signin', [AuthController::class, 'signIn']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::patch('/update',[AuthController::class, 'updateprofile']);