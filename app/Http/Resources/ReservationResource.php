<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'reservation_date' => $this->reservation_date,
            'room' => new RoomResource($this->room()->first()),
            'user' => new UserResource($this->user()->first()),
            'status' => $this->status,
        ];
    }
}
