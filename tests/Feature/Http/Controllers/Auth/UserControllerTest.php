<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_data_for_the_authenticated_user()
    {
        $user = User::factory()->create();

        $accessToken = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->get(route('auth.user'))
            ->assertOk()
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
                'role' => [
                    'value' => $user->role->value,
                    'label' => $user->role->label(),
                ],
            ]);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $this->get(route('auth.user'))
            ->assertStatus(401);
    }
}
