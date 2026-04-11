<?php

namespace App\Http\Requests;

use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'priority'    => ['required', Rule::in(WorkOrderPriority::all())],
            'status'      => ['required', Rule::in(WorkOrderStatus::all())],
            'due_date'    => 'nullable|date',
        ];
    }
}
