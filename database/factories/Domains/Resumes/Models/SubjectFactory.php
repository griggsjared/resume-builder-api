<?php

namespace Database\Factories\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * @var class-string
     */
    protected $model = Subject::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'title' => $this->faker->jobTitle(),
            'email' => $this->faker->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'overview' => $this->faker->sentences(8, true),
        ];
    }

    public function male(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'first_name' => $this->faker->firstName('male'),
            ];
        });
    }

    public function female(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'first_name' => $this->faker->firstName('female'),
            ];
        });
    }
}
