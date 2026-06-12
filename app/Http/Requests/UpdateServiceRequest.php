<?php

namespace App\Http\Requests;

use App\Models\Service;

class UpdateServiceRequest extends StoreServiceRequest
{
    public function authorize(): bool
    {
        $service = $this->route('service');
        return $service instanceof Service && $this->user()?->can('update', $service);
    }
}
