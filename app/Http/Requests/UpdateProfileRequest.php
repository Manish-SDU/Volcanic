<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Editing own profile is handled by controller
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name'          => ['required', 'string', 'max:255'],
            'surname'       => ['required', 'string', 'max:255'],
            'username'      => [
                'required', 'string', 'max:255',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'date_of_birth' => ['nullable', 'date'],
            'where_from'    => ['nullable', 'string', 'max:255'],
            'bio'           => ['nullable', 'string', 'max:1000'],
            // No password updates for safety reasons
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique' => 'This username is already taken.',
        ];
    }
}
