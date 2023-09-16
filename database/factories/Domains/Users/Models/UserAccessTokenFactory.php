<?php

namespace Database\Factories\Domains\Users\Models;

use App\Domains\Users\Models\UserAccessToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<UserAccessToken>
 */
class UserAccessTokenFactory extends Factory
{
    /**
     * @var class-string
     */
    protected $model = UserAccessToken::class;

    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'token' => sprintf(
                '%s%s%s',
                config('sanctum.token_prefix', ''),
                $tokenEntropy = Str::random(40),
                hash('crc32b', $tokenEntropy)
            ),
            'abilities' => ['*'],
            'expires_at' => $this->faker->dateTimeBetween('+1 day', '+1 year'),
        ];
    }
}
