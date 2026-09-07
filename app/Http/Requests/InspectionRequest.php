<?php

namespace App\Http\Requests;

use App\Enums\InspectionResult;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized via Gate in the controller.
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'work_order_id' => ['nullable', 'exists:work_orders,id'],
            'inspection_date' => ['required', 'date'],
            'result' => ['required', Rule::enum(InspectionResult::class)],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
