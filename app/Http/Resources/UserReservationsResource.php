<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserReservationsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'room' => new RoomResource($this->room()->first()),
            'reservation_date' => $this->reservation_date,
            'status' => $this->status,
        ];
    }
}
