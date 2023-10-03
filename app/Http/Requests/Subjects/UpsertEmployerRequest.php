<?php

declare(strict_types=1);

namespace App\Http\Requests\Subjects;

use Illuminate\Foundation\Http\FormRequest;

class UpsertEmployerRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'major_degree' => ['nullable', 'string'],
            'earned_major_degree' => ['nullable', 'boolean'],
            'minor_degree' => ['nullable', 'string'],
            'earned_minor_degree' => ['nullable', 'boolean'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date', 'after:start_date'],
        ];
    }
}
