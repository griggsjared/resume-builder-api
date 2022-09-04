<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Enums\UserRole;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function table_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('users', [
                'id',
                'uuid',
                'first_name',
                'last_name',
                'email',
                'password',
                'role',
                'remember_token',
                'created_at',
                'updated_at',
            ])
        );
    }

    /** @test */
    public function uuid_is_valid()
    {
        $user = User::factory()->create();

        $this->assertTrue(
            Uuid::isValid($user->uuid)
        );
    }

    /** @test */
    public function full_name_matches_first_and_last()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals($user->full_name, $user->first_name.' '.$user->last_name);
    }

    /** @test */
    public function can_generate_role_labels()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertEquals($superAdmin->role_label, UserRole::SuperAdmin->label());
        $this->assertEquals($basic->role_label, UserRole::Basic->label());
    }
}
