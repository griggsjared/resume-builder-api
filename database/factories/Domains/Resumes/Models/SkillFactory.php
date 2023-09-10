<?php

namespace Database\Factories\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    /**
     * @var class-string
     */
    protected $model = Skill::class;

    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => ucwords($this->faker->words(1, true)),
            'category' => $this->faker->randomElement([
                'Cumque',
                'Occaecati',
                'Minus',
            ]),
        ];
    }

    public function uncategorized(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'category' => null,
            ];
        });
    }
}
