<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomStoreRequest;
use App\Http\Resources\RoomResource;
use App\Models\Account\User;
use App\Models\Room\Room;
use App\Repository\RoomRepositoryInterface;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class RoomsController extends Controller
{
    public function __construct(
        private RoomRepositoryInterface $repository,
        private ResponseFactory         $responseFactory
    ) {
        $this->middleware('jwt.verify');
    }

    public function index(Request $request): JsonResponse
    {
        $query = $request->query();
        $rooms = $this->repository->all();

        if (!empty($query['roomType'])) {
            $roomTypes = explode(',', $query['roomType']);
            $rooms = $rooms->filter(function(Room $room) use($roomTypes): bool {
                return in_array($room->typeId, $roomTypes, true);
            });
        }

        return $this->responseFactory->json([
            'status' => 'ok',
            'rooms' => RoomResource::collection($rooms)
        ]);
    }

    public function create(RoomStoreRequest $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validated();
        } catch (ValidationException $exception) {
            return $this->responseFactory->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        $room = $user->rooms()->create($validated);

        if (!$room) {
            return $this->responseFactory->json([
                'status' => 'error',
                'message' => 'Something went wrong while updating user.',
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->responseFactory->json([
            'status' => 'ok',
            'room' => new RoomResource($room),
        ], Response::HTTP_CREATED);
    }

    public function show(User $user): JsonResponse
    {
        return $this->responseFactory->json([
            'status' => 'ok',
            'rooms' => RoomResource::collection($user->rooms()->get()),
        ], Response::HTTP_OK);
    }
}
