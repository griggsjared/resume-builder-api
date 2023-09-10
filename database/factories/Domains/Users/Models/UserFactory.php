<?php

namespace Database\Factories\Domains\Users\Models;

use App\Domains\Users\Enums\UserRole;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * @var class-string
     */
    protected $model = User::class;

    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => $this->faker->randomElement(UserRole::cases()),
            'remember_token' => Str::random(10),
        ];
    }

    public function superAdmin(): self
    {
        return $this->state(fn () => [
            'role' => UserRole::SuperAdmin,
        ]);
    }

    public function basic(): self
    {
        return $this->state(fn () => [
            'role' => UserRole::Basic,
        ]);
    }
}
