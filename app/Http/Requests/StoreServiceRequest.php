<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Service::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            // 5 minutes to 8 hours — a sensible range for a booked service.
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            // Allow free services (0 cents) up to $10,000.
            'price_cents' => ['required', 'integer', 'min:0', 'max:1000000'],
            'currency' => ['required', 'string', Rule::in(['USD', 'EUR', 'GBP', 'MXN', 'COP', 'ARS'])],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'buffer_minutes' => ['required', 'integer', 'min:0', 'max:240'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'color.regex' => 'The color must be a valid hex code (e.g. #6366f1).',
        ];
    }
}
