<?php

namespace App\Http\Requests;

class UserReportStoreRequest extends BaseRequest
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
            'user_report_reason_id' => 'required|exists:user_report_reasons,id',
            'model_id' => 'required|integer',
            'model_name' => 'required|string|max:255',
        ];
    }
}
