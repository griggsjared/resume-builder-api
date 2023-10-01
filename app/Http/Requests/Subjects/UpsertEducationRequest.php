<?php

declare(strict_types=1);

namespace App\Http\Requests\Subjects;

use Illuminate\Foundation\Http\FormRequest;

class UpsertEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('subject'));
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'major_degree' => ['nullable', 'string', 'max:255'],
            'earned_major_degree' => ['nullable', 'boolean'],
            'minor_degree' => ['nullable', 'string', 'max:255'],
            'earned_minor_degree' => ['nullable', 'boolean'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date', 'after:start_date'],
        ];
    }
}
