<?php

namespace Tests\Feature\Domains\Users\Models;

use App\Domains\Users\Models\AccessToken;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_model_relationships()
    {
        $token = AccessToken::factory()
            ->for(User::factory(), 'tokenable')
            ->create();

        $this->assertInstanceOf(User::class, $token->tokenable);
    }

    /** @test */
    public function it_can_assess_an_expires_in_attribute()
    {
        $this->freezeTime();

        $token = AccessToken::factory()
            ->for(User::factory(), 'tokenable')
            ->create([
                'expires_at' => now()->addDay(),
            ]);

        $this->assertEquals(86400, $token->expires_in);
    }
}
