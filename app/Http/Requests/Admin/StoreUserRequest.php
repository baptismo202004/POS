<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled by middleware; allow here
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type_id' => ['required', 'integer', 'exists:user_types,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'status' => ['nullable', 'in:active,inactive'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_type_id.required' => 'Please select a role.',
            'user_type_id.exists' => 'Selected role does not exist.',
            'branch_id.exists' => 'Selected branch does not exist.',
        ];
    }
}
