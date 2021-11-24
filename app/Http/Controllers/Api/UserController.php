<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Resources\UserReservationsResource;
use App\Http\Resources\UserResource;
use App\Models\Account\User;
use App\Repository\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ResponseFactory $responseFactory
    ) {
        $this->middleware('jwt.verify');
    }

    public function index(): JsonResponse
    {
        return $this->responseFactory->json([
            'status' => 'ok',
            'users' => $this->userRepository->all()
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return $this->responseFactory->json([
            'status' => 'ok',
            'user' => new UserResource($user),
        ]);
    }

    public function update(UserStoreRequest $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validated();
        } catch (ValidationException $exception) {
            return $this->responseFactory->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }


        if ($this->userRepository->update($user->id, $validated)) {
            return $this->responseFactory->json([
                'status' => 'ok',
                'message' => 'User has been updated.',
            ], Response::HTTP_CREATED);
        }

        return $this->responseFactory->json([
            'status' => 'error',
            'message' => 'Something went wrong while updating user.',
        ], Response::HTTP_BAD_REQUEST);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($this->userRepository->deleteById($user->id)) {
            return $this->responseFactory->json([
                'status' => 'ok',
                'message' => 'User has been deleted.',
            ], Response::HTTP_OK);
        }

        return $this->responseFactory->json([
            'status' => 'error',
            'message' => 'Something went wrong..',
        ], Response::HTTP_BAD_REQUEST);
    }

    public function reservations(User $user): JsonResponse
    {
        return $this->responseFactory
            ->json(
                UserReservationsResource::collection(
                    $user->reservations()
                        ->get()
                )
            );
    }
}
