<?php

declare(strict_types=1);

namespace App\Http\Requests\Subjects;

use Illuminate\Foundation\Http\FormRequest;

class UpsertSubjectHighlightRequest extends FormRequest
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
            'content' => ['required', 'string'],
        ];
    }
}
