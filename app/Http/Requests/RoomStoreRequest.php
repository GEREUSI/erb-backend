<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'address' => ['required', 'string'],
            'size' => ['required', 'integer'],
            'description' => ['required', 'string'],
            'price' => ['required', 'integer'],
            'typeId' => ['required', 'string']
        ];
    }
}
