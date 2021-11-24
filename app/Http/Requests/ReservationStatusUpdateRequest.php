<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Order\ReservationStatuses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReservationStatusUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => Rule::in([
                ReservationStatuses::IN_PROGRESS,
                ReservationStatuses::CONFIRMED,
                ReservationStatuses::CANCELED
            ]),
        ];
    }
}
