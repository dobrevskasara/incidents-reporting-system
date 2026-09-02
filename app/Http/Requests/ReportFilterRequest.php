<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'period' => ['nullable', 'in:week,month,quarter,year,custom'],
            'from' => ['nullable', 'date', 'required_if:period,custom'],
            'to' => ['nullable', 'date', 'required_if:period,custom', 'after_or_equal:from'],
            'type' => ['nullable', 'string'],
            'platform' => ['nullable', 'string'],
            'age_group' => ['nullable', 'string'],
            'sections' => ['nullable', 'array'],
            'sections.*' => ['in:type,age,platform,status,trend,comparison'],
            'compare_mode' => ['nullable', 'in:auto,custom'],
            'compare_from' => ['nullable', 'date', 'required_if:compare_mode,custom'],
            'compare_to' => ['nullable', 'date', 'required_if:compare_mode,custom', 'after_or_equal:compare_from'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'from.required_if' => 'Please enter a start date for the custom period.',
            'to.required_if' => 'Please enter an end date for the custom period.',
            'to.after_or_equal' => 'The "to" date must be the same as or after the "from" date.',
            'compare_from.required_if' => 'Please enter a start date for the period you are comparing against.',
            'compare_to.required_if' => 'Please enter an end date for the period you are comparing against.',
            'compare_to.after_or_equal' => 'The end date of the comparison period must be the same as or after the start date.',
        ];
    }
}
