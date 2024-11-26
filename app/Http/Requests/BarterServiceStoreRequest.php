<?php

namespace App\Http\Requests;

class BarterServiceStoreRequest extends BaseRequest
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
            'barter_category_id' => 'required|exists:barter_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|string|max:255',
        ];
    }
}
