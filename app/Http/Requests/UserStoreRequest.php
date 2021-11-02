<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'email' => ['required', 'email'],
            'fullName' => ['string'],
            'birthdayDate' => ['date'],
        ];
    }
}
