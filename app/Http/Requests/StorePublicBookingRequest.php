<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Booking is open to anyone with the URL.
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'string', 'email', 'max:255'],
            // Naive datetime "YYYY-MM-DDTHH:mm:ss" in the provider's local timezone.
            // We do the in-the-future check after parsing in the controller because
            // Laravel's `after:now` would otherwise apply server-tz to a naive string.
            'starts_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
