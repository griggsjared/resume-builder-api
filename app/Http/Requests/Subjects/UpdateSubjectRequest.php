<?php

declare(strict_types=1);

namespace App\Http\Requests\Subjects;

use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSubjectRequest extends FormRequest
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
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'overview' => ['nullable', 'string'],
        ];

        if($this->user()->can('assignUser', Subject::class)) {
            $rules['user'] = ['nullable', 'uuid', 'exists:users,id'];
        }

        return $rules;
    }

    public function assignUser(): User
    {
        if($this->user()->cannot('assignUser', Subject::class) || $this->has('user') === false) {
            return $this->route('subject')->user ?? $this->user();
        }

        return User::find($this->input('user'))->first();
    }
}
