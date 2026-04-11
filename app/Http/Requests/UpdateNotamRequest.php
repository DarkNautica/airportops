<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'station'        => ['nullable', 'string', 'max:8'],
            'subject'        => ['nullable', 'string', 'max:255'],
            'category'       => ['required', 'string'],
            'status'         => ['required', 'string'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],
            'notam_text'     => ['required', 'string'],
        ];
    }
}
