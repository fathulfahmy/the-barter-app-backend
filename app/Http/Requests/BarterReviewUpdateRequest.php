<?php

namespace App\Http\Requests;

class BarterReviewUpdateRequest extends BaseRequest
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
            'description' => 'sometimes|required|string|min:1|max:65535',
            'rating' => 'sometimes|required|numeric|min:0|max:5',
        ];
    }
}
