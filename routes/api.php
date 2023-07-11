<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// add test route
Route::get('/', function () {
    return response()->json([
        'message' => 'Hello World!',
    ], 200);
});

// register route
Route::post('/auth/register', [AuthController::class, 'register']);


// Mobile authentication
Route::post('/auth/login', [AuthController::class, 'token']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
