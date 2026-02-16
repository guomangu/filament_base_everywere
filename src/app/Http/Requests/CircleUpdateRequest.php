<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CircleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:business,event,place,project'],
            'address' => ['required', 'string'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'is_public' => ['required'],
            'owner_id' => ['required', 'integer', 'exists:users,id'],
            'user_id' => ['required', 'integer', 'exists:Owners,id'],
        ];
    }
}
