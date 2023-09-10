<?php

namespace Tests\Feature\Actions\Users;

use App\Actions\Users\Upsert;
use App\Models\DTO\UserData;
use App\Models\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpsertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_user()
    {
        $data = UserData::from([
            'first_name' => 'Jim',
            'last_name' => 'Bob',
            'email' => 'jbob@example.com',
            'password' => 'Secret!',
            'role' => UserRole::Basic,
        ]);

        $data = app(Upsert::class)->handle($data);

        $user = User::find($data->id);

        $this->assertEquals($data->first_name, $user->first_name);
        $this->assertEquals($data->last_name, $user->last_name);
        $this->assertEquals($data->email, $user->email);
        $this->assertTrue(Hash::check('Secret!', $user->password));
        $this->assertEquals($data->role, $user->role);
    }

    /** @test */
    public function is_updates_a_user()
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

        app(Upsert::class)->handle($data);

        $user->refresh();

        $this->assertEquals($data->first_name, $user->first_name);
        $this->assertEquals($data->last_name, $user->last_name);
        $this->assertEquals($data->email, $user->email);
        $this->assertTrue(Hash::check('Secret!', $user->password));
        $this->assertEquals($data->role, $user->role);
    }
}
