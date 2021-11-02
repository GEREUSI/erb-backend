<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Exception;

class JwtMiddleware extends BaseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is invalid.'
            ], Response::HTTP_FORBIDDEN);
        } catch (TokenExpiredException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is expired.'
            ], Response::HTTP_UNAUTHORIZED);
        } catch (TokenBlacklistedException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is blacklisted..'
            ], Response::HTTP_BAD_REQUEST);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authorization token not found.'
            ], Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
