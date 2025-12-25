<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpsertUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }


    public function rules(): array
    {
        $isCreate = $this->routeIs('admin.users.store');
        $userId = $this->route('user')?->id;

        return [
            'name'  => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],

            'email' => [
                'required', 'email', 'max:255',
                $isCreate
                    ? 'unique:users,email'
                    : 'unique:users,email,' . $userId,
            ],

            // Password required on create, optional on edit
            'password' => array_filter([
                $isCreate ? 'required' : 'nullable',
                'string',
                Password::min(10),
            ]),

            // Roles are optional; only applied if actor has roles.manage
            'roles'   => ['nullable', 'array'],
            'roles.*' => ['string'],
        ];
    }
}
