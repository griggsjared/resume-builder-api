<?php

namespace Database\Factories\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
{
    /**
     * @var class-string
     */
    protected $model = Education::class;

    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        $startedAt = $this->faker->dateTimeBetween(Carbon::parse('5 years ago'), Carbon::parse('1 years ago'));
        $endedAt = $this->faker->dateTimeBetween($startedAt, Carbon::parse('1 years ago'));

        return [
            'name' => $this->faker->company(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'degree' => $this->faker->jobTitle(),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ];
    }

    public function current(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'ended_at' => null,
            ];
        });
    }
}
