<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inspection_date' => 'required|date',
            'inspection_day' => 'nullable|string|max:50',
            'overall_status' => 'nullable|in:Satisfactory,Unsatisfactory',
            'crash_phone_test_time' => 'nullable|date_format:H:i',
            'am_time' => 'nullable|date_format:H:i',
            'pm_time' => 'nullable|date_format:H:i',
            'other_time' => 'nullable|date_format:H:i',
            'by_1' => 'nullable|string|max:100',
            'by_2' => 'nullable|string|max:100',
            'by_3' => 'nullable|string|max:100',
            'by_4' => 'nullable|string|max:100',
            'checklist' => 'nullable|array',
            'findings' => 'nullable|string',
        ];
    }
}
