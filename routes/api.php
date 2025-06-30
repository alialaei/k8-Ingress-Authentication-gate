<?php

use App\Http\Controllers\Authentication\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Authentication\RegisterController;

Route::post('/register', RegisterController::class);
Route::post('/login', [LoginController::class, '__invoke'])->middleware('throttle:10,1');


Route::middleware('auth:api')
    ->get('/user', function (Request $request) {
        return $request->user();
});

// Example of a protected route group
Route::middleware('auth:api')->group(function () {
    Route::get('/some-protected-data', function () {
        return response()->json(['message' => 'This is protected data.']);
    });

    // Other protected API endpoints for your authentication service
    // e.g., for managing users, roles, etc.
});
Route::middleware('auth:api')->get('/verify-token', function (Request $request) {
    return response()->json([
        'message' => 'Token is valid',
        'user_id' => $request->user()->id,
        'user_email' => $request->user()->email,
    ])
    ->header('X-User-ID', $request->user()->id)
    ->header('X-User-Email', $request->user()->email);
});