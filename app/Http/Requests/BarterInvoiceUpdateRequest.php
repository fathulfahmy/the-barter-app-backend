<?php

namespace App\Http\Requests;

class BarterInvoiceUpdateRequest extends BaseRequest
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
            'status' => 'sometimes|required|string',
            'amount' => 'sometimes|nullable|numeric|min:0|max:99999999.99',
            'barter_service_ids' => 'sometimes|nullable|array',
            'barter_service_ids.*' => 'exists:barter_services,id',
        ];
    }
}
