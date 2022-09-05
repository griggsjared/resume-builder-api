<?php

namespace Tests\Feature\Actions\Users;

use App\Actions\Users\UpdateOrCreate;
use App\DTO\UserData;
use App\Models\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdateOrCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_is_created()
    {
        $data = UserData::from([
            'first_name' => 'Jim',
            'last_name' => 'Bob',
            'email' => 'jbob@example.com',
            'password' => 'Secret!',
            'role' => UserRole::Basic,
        ]);

        $user = (new UpdateOrCreate)->execute($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($data->first_name, $user->first_name);
        $this->assertEquals($data->last_name, $user->last_name);
        $this->assertEquals($data->email, $user->email);
        $this->assertTrue(Hash::check($data->password, $user->password));
        $this->assertEquals($data->role, $user->role);
    }

    /** @test */
    public function user_is_updated()
    {
        $user = User::factory()->superAdmin()->create();

        $data = UserData::from([
            ...$user->toArray(),
            'first_name' => 'Jim',
            'last_name' => 'Bob',
            'email' => 'jbob@example.com',
            'password' => 'Secret!',
            'role' => UserRole::Basic,
        ]);

        $updatedUser = (new UpdateOrCreate)->execute($data);

        $this->assertInstanceOf(User::class, $updatedUser);
        $this->assertEquals($user->id, $updatedUser->id);
        $this->assertEquals($data->first_name, $updatedUser->first_name);
        $this->assertEquals($data->last_name, $updatedUser->last_name);
        $this->assertEquals($data->email, $updatedUser->email);
        $this->assertTrue(Hash::check($data->password, $updatedUser->password));
        $this->assertEquals($data->role, $updatedUser->role);
    }

    /** @test */
    public function user_can_update_password_only()
    {
        $user = User::factory()->create();

        $data = UserData::from([
            ...$user->toArray(),
            'password' => 'Secret!',
        ]);

        $updatedUser = (new UpdateOrCreate)->execute($data);

        $this->assertTrue(Hash::check('Secret!', $updatedUser->password));
        $this->assertEquals($user->first_name, $updatedUser->first_name);
        $this->assertEquals($user->last_name, $updatedUser->last_name);
        $this->assertEquals($user->email, $updatedUser->email);
        $this->assertEquals($user->role, $updatedUser->role);
    }
}
