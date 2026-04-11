<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePassAlongRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'specialist_name' => ['nullable', 'string', 'max:255'],
            'shift_start_time' => ['nullable', 'date_format:H:i'],
            'shift_end_time'   => ['nullable', 'date_format:H:i'],
            'am_part_139'       => ['sometimes', 'boolean'],
            'am_perimeter'      => ['sometimes', 'boolean'],
            'am_terminal'       => ['sometimes', 'boolean'],
            'pm_part_139'       => ['sometimes', 'boolean'],
            'pm_terminal'       => ['sometimes', 'boolean'],
            'ramp_apron_patrol' => ['sometimes', 'boolean'],
            'wildlife_patrol'   => ['sometimes', 'boolean'],
            'significant_activity' => ['nullable', 'string'],
            'sections' => ['nullable', 'array'],
            'sections.*.title' => ['required_with:sections', 'string'],
            'sections.*.rows' => ['required_with:sections', 'array'],
            'sections.*.rows.*.label' => ['nullable', 'string'],
            'sections.*.rows.*.value' => ['nullable', 'string'],
            'row_attachments' => ['nullable', 'array'],
            'row_attachments.*' => ['nullable', 'array'],
            'row_attachments.*.*' => ['nullable', 'array'],
            'row_attachments.*.*.*' => ['file', 'max:12288', 'mimes:jpg,jpeg,png,webp,pdf'],
        ];
    }
}
