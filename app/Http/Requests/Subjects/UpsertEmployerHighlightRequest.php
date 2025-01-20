<?php

declare(strict_types=1);

namespace App\Http\Requests\Subjects;

use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;

class UpsertEmployerHighlightRequest extends FormRequest
{
    public function __construct(
        private Subject $subject,
        private Employer $employer
    ) {
    }

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
            'sort' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
