<?php

namespace App\Http\Requests;

class BarterInvoiceStoreRequest extends BaseRequest
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
            'barter_transaction_id' => 'required|exists:barter_transactions,id',
            'amount' => 'nullable|numeric|min:0|max:99999999.99|required_without:barter_service_ids',
            'barter_service_ids' => 'nullable|array|required_without:amount',
            'barter_service_ids.*' => 'exists:barter_services,id',
        ];
    }
}
