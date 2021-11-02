<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repository\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Carbon $carbon,
        private UserRepositoryInterface $userRepository
    )
    {
        $this->middleware('jwt.verify', ['except' => ['login', 'register']]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return $this->responseFactory->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken((string)$token);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
            'typeId' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->responseFactory->json([
                'status' => 'error',
                'message' => $validator->errors()->toArray(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'typeId' => $request->input('typeId'),
        ]);

        if ($user === null) {
            return $this->responseFactory->json([
                'status' => 'error',
                'message' => 'Cannot create new user.'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->responseFactory->json([
            'status' => 'ok',
            'message' => 'User created',
            'user' => new UserResource($user)
        ]);
    }

    public function me(): JsonResponse
    {
        return $this->responseFactory->json(new UserResource(auth()->user()), Response::HTTP_OK);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return $this->responseFactory->json([
            'status' => 'ok',
            'message' => 'Successfully logged out.',
        ], Response::HTTP_OK);
    }

    public function refresh(): JsonResponse
    {
        return $this->responseFactory->json(auth()->refresh(), Response::HTTP_OK);
    }

    private function respondWithToken(string $token): JsonResponse
    {
        return $this->responseFactory->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->carbon->addDay(),
        ]);
    }
}
