<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\RoomsController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::group(['middleware' => ['api'], 'prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('me', [AuthController::class, 'me']);
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::patch('/{user}', [UserController::class, 'update']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::delete('/{user}', [UserController::class, 'destroy']);

        Route::post('/{user}/rooms', [RoomsController::class, 'create']);
        Route::get('/{user}/rooms', [RoomsController::class, 'show']);

        Route::get('/{user}/rooms/{room}', [RoomsController::class, 'edit']);
        Route::patch('/{user}/rooms/{room}', [RoomsController::class, 'update']);

        Route::post('/reservations/{reservation}', [RoomsController::class, 'updateStatus']);

        Route::get('/{room}/reservations', [UserController::class, 'reservations']);
    });

    Route::get('/rooms', [RoomsController::class, 'index'])->withoutMiddleware('jwt.verify');

    Route::group(['prefix' => 'room'], function () {
       Route::get('/{room}', [RoomController::class, 'show'])
        ->withoutMiddleware('jwt.verify');
       Route::post('/{room}/rate', [RoomController::class, 'rate']);
       Route::post('/{room}/reserve', [RoomController::class, 'reserve']);
       Route::get('/{room}/booked', [RoomController::class, 'bookedTimes']);
       Route::get('/{room}/reservations', [RoomsController::class, 'reservations']);
    });
});

Route::any(
    '{any}',
    fn(): JsonResponse => response()->json([
        'status' => 'error',
        'message' => 'Resource not found'
    ], Response::HTTP_NOT_FOUND)
)->where('any', '.*');
