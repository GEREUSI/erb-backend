<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservedDatesResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'reservetion_date' => $this->reservation_date,
            'status' => $this->status,
        ];
    }
}
