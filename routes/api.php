<?php
use App\Http\Controllers\AuthController;

Route::post('/signin', [AuthController::class, 'signIn']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::patch('/updateprofile',[AuthController::class, 'updateprofile']);
Route::get('/users',[AuthController::class,'getAllUsers']);
Route::get('/users/{id}',[AuthController::class, 'getUser']);
Route::delete('/users/{id}',[AuthController::class, deleteAccount]);