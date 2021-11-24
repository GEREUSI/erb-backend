<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RateStoreRequest;
use App\Http\Requests\ReservationStoreRequest;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\ReservedDatesResource;
use App\Http\Resources\RoomResource;
use App\Models\Order\ReservationStatuses;
use App\Models\Room\Rate;
use App\Models\Room\Room;
use Carbon\Carbon;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class RoomController extends Controller
{
    public function __construct(
        private ResponseFactory $responseFactory
    ) {
        $this->middleware('jwt.verify');
    }

    public function show(Room $room): JsonResponse
    {
        return $this->responseFactory->json(new RoomResource($room));
    }

    public function rate(RateStoreRequest $request, Room $room): JsonResponse
    {
        try {
            $validated = $request->validated();
        } catch (ValidationException $exception) {
            return $this->responseFactory->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        $rate = $room->rates()->where('user_id', $validated['user_id'])->first();

        if ($rate) {
            $rate->update([
                'rate' => $validated['rate']
            ]);

            return $this->responseFactory->json([
                'message' => 'Room rate was updated.',
            ]);
        }

        Rate::create([
            'room_id' => $room->id,
            'user_id' => $validated['user_id'],
            'rate' => $validated['rate'],
        ]);

        return $this->responseFactory->json([
            'message' => 'Room rate was added.',
        ]);
    }

    public function reserve(ReservationStoreRequest $request, Room $room): JsonResponse
    {
        try {
            $validated = $request->validated();
        } catch (ValidationException $exception) {
            return $this->responseFactory->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        $reservation = $room->reservations()
            ->where(
                'reservation_date',
                Carbon::parse($validated['reservation_date'])->format('Y-m-d H:i:s')
            )
            ->whereIn('status', [ReservationStatuses::IN_PROGRESS, ReservationStatuses::CONFIRMED])
            ->get();

        if ($reservation->isNotEmpty()) {
            return $this->responseFactory->json([
                'message' => 'This time is already reserved.',
            ]);
        }

        return $this->responseFactory->json(
            new ReservationResource($room->reservations()->create(
                array_merge($validated, ['status' => ReservationStatuses::IN_PROGRESS])
            ))
        );
    }

    public function bookedTimes(Room $room): JsonResponse
    {
        $reservations = $room->reservations()
            ->whereIn('status', [ReservationStatuses::IN_PROGRESS, ReservationStatuses::CONFIRMED])
            ->get();

        return $this->responseFactory->json(ReservedDatesResource::collection($reservations));
    }
}
