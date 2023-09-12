<?php

namespace Database\Factories\Domains\Resumes\Models;

use App\Domains\Resumes\Models\EducationHighlight;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EducationHighlight>
 */
class EducationHighlightFactory extends Factory
{
    /**
     * @var class-string
     */
    protected $model = EducationHighlight::class;

    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'content' => $this->faker->sentences(4, true),
        ];
    }
}
