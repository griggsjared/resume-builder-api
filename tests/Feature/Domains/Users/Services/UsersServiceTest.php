<?php

namespace Tests\Feature\Domains\Users\Services;

use App\Domains\Users\Actions\DeleteUserAction;
use App\Domains\Users\Actions\UpsertUserAction;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Enums\UserRole;
use App\Domains\Users\Models\AccessToken;
use App\Domains\Users\Models\User;
use App\Domains\Users\Services\UsersService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsersServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user(): void
    {
        $data = UserData::from([
            'email' => 'tomcruise@example.com',
            'password' => 'Secret!',
            'role' => UserRole::Admin,
        ]);

        $updatedData = app(UsersService::class)->upsert($data);

        $user = User::where('id', $updatedData->id)->first();

        $this->assertEquals($data->email, $user->email);
        $this->assertTrue(Hash::check($data->password, $user->password));
        $this->assertEquals($data->role, $user->role);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        $user = User::factory()->create([
            'role' => UserRole::Basic,
        ]);

        $data = UserData::from([
            ...$user->toArray(),
            'email' => 'tomcruise@example.com',
            'password' => 'Secret!',
            'role' => UserRole::Admin,
        ]);

        app(UsersService::class)->upsert($data);

        $updatedUser = User::where('id', $data->id)->first();

        $this->assertEquals($user->id, $updatedUser->id);
        $this->assertEquals($data->email, $updatedUser->email);
        $this->assertTrue(Hash::check($data->password, $updatedUser->password));
        $this->assertEquals($data->role, $updatedUser->role);
    }

    /** @test */
    public function it_can_delete_a_user(): void
    {
        $user = User::factory()
            ->has(AccessToken::factory()->count(1), 'accessTokens')
            ->create();

        $accessToken = $user->accessTokens->first();

        $data = app(UsersService::class)->delete(
            UserData::from($user)
        );

        $user = User::find($data->id);
        $accessToken = AccessToken::find($accessToken->id);

        $this->assertNull($user);
        $this->assertNull($accessToken);
    }
}
