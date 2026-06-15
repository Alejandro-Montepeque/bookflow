<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $validTimezones = \DateTimeZone::listIdentifiers();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            // URL slug — lowercase letters/numbers/dashes, unique except mine.
            'slug' => [
                'required',
                'string',
                'min:3',
                'max:60',
                'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'timezone' => ['required', 'string', Rule::in($validTimezones)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug can only contain lowercase letters, numbers and dashes (e.g. "alejandro-m").',
        ];
    }
}
