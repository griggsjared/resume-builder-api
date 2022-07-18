<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use CountryState;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() : array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'title' => $this->faker->jobTitle(),
            'email' => $this->faker->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'city' => $this->faker->city(),
            'state' => $this->faker->randomElement(
                array_keys(
                    CountryState::getStates('US')
                )
            ),
            'overview' => $this->faker->sentences(8, true)
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function male() : self
    {
        return $this->state(function (array $attributes) {
            return [
                'first_name' => $this->faker->firstName('male')
            ];
        });

    }

    /**
     * @return array<string, mixed>
     */
    public function female() : self
    {
        return $this->state(function (array $attributes) {
            return [
                'first_name' => $this->faker->firstName('female')
            ];
        });
    }
}
