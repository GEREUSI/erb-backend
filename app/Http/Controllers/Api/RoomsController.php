<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationStatusUpdateRequest;
use App\Http\Requests\RoomStoreRequest;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\RoomResource;
use App\Models\Account\User;
use App\Models\Order\Reservation;
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
        private ResponseFactory $responseFactory
    ) {
        $this->middleware('jwt.verify');
    }

    public function index(Request $request): JsonResponse
    {
        $query = $request->query();
        $rooms = $this->repository->all();

        if (!empty($query['roomType'])) {
            $roomTypes = explode(',', $query['roomType']);
            $rooms = $rooms->filter(function (Room $room) use ($roomTypes): bool {
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

    public function edit(User $user, Room $room): JsonResponse
    {
        return $this->responseFactory->json([
            'room' => new RoomResource($room),
        ], Response::HTTP_OK);
    }

    public function update(RoomStoreRequest $request, User $user, Room $room): JsonResponse
    {
        try {
            $validated = $request->validated();
        } catch (ValidationException $exception) {
            return $this->responseFactory->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($this->repository->update($room->id, $validated)) {
            return $this->responseFactory->json([
                'status' => 'ok',
                'message' => 'Room has been updated.',
            ], Response::HTTP_ACCEPTED);
        }

        return $this->responseFactory->json([
            'status' => 'error',
            'message' => 'Something went wrong while updating user.',
        ], Response::HTTP_BAD_REQUEST);
    }

    public function reservations(Room $room): JsonResponse
    {
        $reservations = Reservation::where('room_id', $room->id)
            ->get();

        return $this->responseFactory->json(
            ReservationResource::collection($reservations)
        );
    }

    public function updateStatus(
        ReservationStatusUpdateRequest $request,
        Reservation $reservation
    ): JsonResponse {
        try {
            $validated = $request->validated();
        } catch (ValidationException $exception) {
            return $this->responseFactory->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($reservation->update($validated)) {
            return $this->responseFactory->json(new ReservationResource($reservation));
        }

        return $this->responseFactory->json([
            'message' => 'Cannot update reservation status',
        ], Response::HTTP_BAD_REQUEST);
    }
}
