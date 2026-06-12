<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SyncAvailabilityRulesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $service = $this->route('service');
        return $service instanceof Service && $this->user()?->can('update', $service);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rules' => ['present', 'array', 'max:50'],
            'rules.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'rules.*.start_time' => ['required', 'date_format:H:i'],
            'rules.*.end_time' => ['required', 'date_format:H:i'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            /** @var array<int, array{day_of_week:int, start_time:string, end_time:string}> $rules */
            $rules = $this->input('rules', []);

            foreach ($rules as $i => $rule) {
                if (isset($rule['start_time'], $rule['end_time']) && $rule['start_time'] >= $rule['end_time']) {
                    $v->errors()->add("rules.$i.end_time", 'End time must be after start time.');
                }
            }
        });
    }
}
