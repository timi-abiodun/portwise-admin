<?php

namespace App\Http\Requests;

use App\Models\DutyRate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DutyRateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        // Only keep the field that matches the rate type, so the DB CHECK constraint is satisfied
        if ($this->rate_type === 'ad_valorem') {
            $this->merge(['rate_per_kg' => null]);
        } elseif ($this->rate_type === 'per_kg') {
            $this->merge(['duty_pct' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'hs_code_id' => ['required', 'integer', 'exists:hs_codes,id',
                function ($attribute, $value, $fail) {
                    if ($this->input('effective_to')) {
                        return; // an ended rate can't clash with the current one
                    }
                    $current = $this->route('duty_rate'); // null on create

                    $exists = DutyRate::where('hs_code_id', $value)
                        ->whereNull('effective_to')
                        ->when($current, fn ($q) => $q->where('id', '!=', $current->id))
                        ->exists();

                    if ($exists) {
                        $fail('This HS code already has a current (open-ended) rate. Edit that one, or set an end date here.');
                    }
                },
            ],
            'rate_type' => ['required', Rule::in(['ad_valorem', 'per_kg'])],
            'duty_pct' => ['nullable', 'required_if:rate_type,ad_valorem', 'numeric', 'between:0,100'],
            'rate_per_kg' => ['nullable', 'required_if:rate_type,per_kg', 'numeric', 'min:0'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'source_ref' => ['nullable', 'string', 'max:255'],
        ];
    }
}