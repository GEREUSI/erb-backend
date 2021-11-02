<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
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

    Route::apiResource('user', UserController::class)
        ->only('index', 'update', 'destroy', 'show');
});

Route::any(
    '{any}',
    fn(): JsonResponse => response()->json([
        'status' => 'error',
        'message' => 'Resource not found'
    ], Response::HTTP_NOT_FOUND)
)->where('any', '.*');
