<?php

namespace Tests\Feature\Domains\Users\Actions;

use App\Domains\Users\Actions\UpsertUserAction;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Enums\UserRole;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpsertUserActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user(): void
    {
        $data = UserData::from([
            'email' => 'tomcruise@example.com',
            'password' => 'Secret!',
            'role' => UserRole::SuperAdmin,
        ]);

        $updatedData = app(UpsertUserAction::class)->execute($data);

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
            'role' => UserRole::SuperAdmin,
        ]);

        app(UpsertUserAction::class)->execute($data);

        $updatedUser = User::where('id', $data->id)->first();

        $this->assertEquals($user->id, $updatedUser->id);
        $this->assertEquals($data->email, $updatedUser->email);
        $this->assertTrue(Hash::check($data->password, $updatedUser->password));
        $this->assertEquals($data->role, $updatedUser->role);
    }
}
