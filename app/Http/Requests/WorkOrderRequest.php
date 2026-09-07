<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized via Gate in the controller.
    }

    public function rules(): array
    {
        $rules = [
            'reported_problem' => ['required', 'string', 'max:5000'],
            'diagnosis' => ['nullable', 'string', 'max:5000'],
            'work_performed' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'priority' => ['required', Rule::enum(Priority::class)],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
        ];

        if ($this->isMethod('POST')) {
            $rules['asset_id'] = ['required', 'exists:assets,id'];
        } else {
            // Technicians submit only the repair fields on update.
            $rules['reported_problem'] = ['sometimes', 'required', 'string', 'max:5000'];
            $rules['priority'] = ['sometimes', 'required', Rule::enum(Priority::class)];
        }

        return $rules;
    }
}
