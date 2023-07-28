<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\UserController;

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
        'message' => 'API route',
    ], 200);
});

Route::get('/google-maps-api-key', function () {
    return response()->json([
        env('GOOGLE_MAPS_API_KEY'),
    ], 200);
});

// register route
Route::post('/auth/register', [AuthController::class, 'register']);


// Mobile authentication
Route::post('/auth/login', [AuthController::class, 'token']);

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

// Group middleware routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('ride', RideController::class)->except(['create', 'edit'])->middleware('api');

    Route::get('/feed', [FeedController::class, 'index']);

    Route::get('/users/me', [UserController::class, 'me']);
    Route::get('/user/following', [UserController::class, 'following']);
    Route::get('/user/followers', [UserController::class, 'followers']);
    Route::resource('user', UserController::class)->except(['index', 'create', 'edit']);
    Route::get('/users/search', [UserController::class, 'searchUsers']);
    Route::delete('/user/{id}/unfollow', [UserController::class, 'unfollow']);
    Route::post('/user/{id}/follow', [UserController::class, 'follow']);

    //Route::post('/follow/{id}', [FollowController::class, 'follow']);
    //Route::delete('/follow/{id}', [FollowController::class, 'unfollow']);
});
