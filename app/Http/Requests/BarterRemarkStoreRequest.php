<?php

namespace App\Http\Requests;

class BarterRemarkStoreRequest extends BaseRequest
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
            'datetime' => 'sometimes|nullable',
            'address' => 'sometimes|nullable|string|min:1|max:65535',
            'deliverables' => 'sometimes|nullable|array',
            'deliverables.*' => 'nullable|string|max:65535',
            'note' => 'sometimes|nullable|string|min:1|max:65535',
        ];
    }
}
