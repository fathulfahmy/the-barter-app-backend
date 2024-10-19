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
            'amount' => 'nullable|numeric|min:0|max:99999999.99',
            'status' => 'nullable|string',
            'barter_service_ids' => 'nullable|array',
            'barter_service_ids.*' => 'integer|exists:barter_services,id',
        ];
    }
}
