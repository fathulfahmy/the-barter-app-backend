<?php

namespace App\Http\Requests;

class BarterServiceUpdateRequest extends BaseRequest
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
            'barter_category_id' => 'sometimes|required|exists:barter_categories,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:65535',
            'min_price' => 'required_with:max_price|numeric|min:0|max:99999999.99',
            'max_price' => 'required_with:min_price|numeric|min:0|max:99999999.99|gte:min_price',
            'price_unit' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string',
            'images' => 'sometimes|nullable',
            'images.*' => 'sometimes|nullable',
        ];
    }
}
