<?php

declare(strict_types=1);

namespace App\Http\Requests\Subjects;

use Illuminate\Foundation\Http\FormRequest;

class UpsertEmployerHighlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('subject'));
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
