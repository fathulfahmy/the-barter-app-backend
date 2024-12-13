<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rules;

class UserUpdateRequest extends BaseRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'avatar' => 'sometimes|required|image',
            'email' => ['sometimes', 'required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['sometimes', 'required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
