<?php

namespace Database\Factories;

use Carbon\Carbon;
use CountryState;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employer>
 */
class EmployerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $startedAt = $this->faker->dateTimeBetween(Carbon::parse('5 years ago'), Carbon::parse('1 years ago'));
        $endedAt = $this->faker->dateTimeBetween($startedAt, Carbon::parse('1 years ago'));

        return [
            'name' => $this->faker->company(),
            'city' => $this->faker->city(),
            'state' => $this->faker->randomElement(
                array_keys(
                    CountryState::getStates('US')
                )
            ),
            'started_at' => $startedAt,
            'ended_at' => $endedAt
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function current() : self
    {
        return $this->state(function (array $attributes) {
            return [
                'ended_at' => null
            ];
        });
    }
}
