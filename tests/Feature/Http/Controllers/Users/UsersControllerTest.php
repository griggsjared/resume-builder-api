<?php

namespace Tests\Feature\Http\Controllers\Users;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_a_list_of_paginated_users()
    {
        $user = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()->create();

        User::factory()->count(40)->create();

        $this->withHeader('Authorization', 'Bearer '.$user->createToken('test')->plainTextToken)
            ->get(route('users.index', [
                'page' => 1,
                'per_page' => 10,
            ]))
            ->assertOk()
            ->assertJson([
                'total_items' => 42,
                'total_pages' => 5,
                'previous_page_url' => null,
            ])
            ->assertJsonStructure([
                'next_page_url',
                'previous_page_url',
                'items' => [
                    '*' => [
                        'id',
                        'email',
                        'role' => [
                            'value',
                            'label',
                        ],
                    ],
                ],
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('users.index', [
                'page' => 1,
                'per_page' => 10,
            ]))
            ->assertOk()
            ->assertJson([
                'total_items' => 1,
                'total_pages' => 1,
                'previous_page_url' => null,
                'next_page_url' => null
            ]);
    }

    /** @test */
    public function it_can_return_data_about_a_single()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()->create();

        $showingUser = User::factory()->basic()->create();

        //admin can view anyone
        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('users.show', $showingUser->id))
            ->assertOk()
            ->assertJson([
                'id' => $showingUser->id,
                'email' => $showingUser->email,
                'role' => [
                    'value' => $showingUser->role->value,
                    'label' => $showingUser->role->label(),
                ],
            ]);

        auth()->forgetGuards();

        //basic cannot view another user
        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('users.show', $showingUser->id))
            ->assertForbidden();

        //basic can view themselves
        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('users.show', $basicUser ->id))
            ->assertOk();
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()->create();

        //admin can create users with any role
        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->post(route('users.store'), [
                'email' => 'test@example.com',
                'password' => 'password',
                'role' => 'admin',
            ])
            ->assertCreated()
            ->assertJson([
                'email' => 'test@example.com',
                'role' => [
                    'value' => 'admin',
                    'label' => 'Admin'
                ]
            ])
            ->assertJsonStructure([
                'id',
                'email',
                'role' => [
                    'value',
                    'label',
                ],
            ]);

        auth()->forgetGuards();

        //basic users cannot create users
        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('users.store'), [
                'email' => 'test@example.com',
                'password' => 'password',
                'role' => 'admin',
            ])
            ->assertForbidden();
    }

    /** @test */
    public function it_can_update_a_user()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()->create();

        $updatingUser = User::factory()->basic()->create();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->patch(route('users.update', $updatingUser->id), [
                'email' => 'test@example.com',
                'password' => 'password',
                'role' => 'admin',
            ])
            ->assertOk()
            ->assertJson([
                'id' => $updatingUser->id,
                'email' => 'test@example.com',
                'role' => [
                    'value' => 'admin',
                    'label' => 'Admin'
                ]
            ]);

        auth()->forgetGuards();

        //basic user cannot update another user.
        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('users.update', $updatingUser->id), [
                'email' => 'test@example.com',
                'password' => 'password',
                'role' => 'admin',
            ])
            ->assertForbidden();

        //basic user can update self but not their role.
        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('users.update', $basicUser->id), [
                'email' => 'newemail@example.com',
                'password' => 'password',
                'role' => 'admin',
            ])
            ->assertOk()
            ->assertJson([
                'id' => $basicUser->id,
                'email' => 'newemail@example.com',
                'role' => [
                    'value' => 'basic',
                    'label' => 'Basic'
                ]
            ]);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()->create();

        $deletingUser = User::factory()->basic()->create();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->delete(route('users.destroy', $deletingUser->id))
            ->assertOk()
            ->assertJson([
                'message' => 'Ok'
            ]);

        $deletingUser = User::find($deletingUser)->first();

        $this->assertNull($deletingUser);

        auth()->forgetGuards();

        $deletingUser = User::factory()->basic()->create();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('users.destroy', $deletingUser->id))
            ->assertForbidden();


        $deletingUser = User::find($deletingUser)->first();

        $this->assertNotNull($deletingUser);

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('users.destroy', $basicUser->id))
            ->assertForbidden();

        $basicUser = User::find($basicUser)->first();

        $this->assertNotNull($basicUser);
    }
}
