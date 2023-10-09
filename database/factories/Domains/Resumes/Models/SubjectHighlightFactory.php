<?php

namespace Database\Factories\Domains\Resumes\Models;

use App\Domains\Resumes\Models\SubjectHighlight;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubjectHighlight>
 */
class SubjectHighlightFactory extends Factory
{
    /**
     * @var class-string
     */
    protected $model = SubjectHighlight::class;

    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'content' => $this->faker->sentences(4, true),
            'sort' => $this->faker->numberBetween(1, 9999),
        ];
    }
}
