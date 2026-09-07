<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized via Gate in the controller.
    }

    public function rules(): array
    {
        return [
            'asset_type_id' => ['required', 'exists:asset_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'company_id' => ['required', 'exists:companies,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'received_date' => ['required', 'date'],
            'expected_release_date' => ['nullable', 'date', 'after_or_equal:received_date'],
            'priority' => ['required', Rule::enum(Priority::class)],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
