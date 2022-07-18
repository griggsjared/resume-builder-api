<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillFactory extends Factory
{
    private array $categories = [
        'Cumque',
        'Occaecati',
        'Minus'
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => ucwords($this->faker->words(1, true)),
            'category' => $this->faker->randomElement($this->categories)
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function uncategorized() : self
    {
        return $this->state(function (array $attributes) {
            return [
                'category' => null
            ];
        });
    }
}
