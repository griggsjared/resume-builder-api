<?php

namespace Tests\Feature\Http\Controllers\Subjects;

use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_a_list_of_paginated_subjects()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(Subject::factory()->count(5))
            ->create();

        Subject::factory()->count(40)->create();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.index', [
                'page' => 1,
                'per_page' => 10,
            ]))
            ->assertOk()
            ->assertJson([
                'total_items' => 45,
                'total_pages' => 5,
                'previous_page_url' => null,
            ])
            ->assertJsonStructure([
                'next_page_url',
                'previous_page_url',
                'items' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'title',
                        'city',
                        'state',
                        'phone_number',
                        'email',
                        'overview',
                    ],
                ],
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.index', [
                'page' => 1,
                'per_page' => 10,
            ]))
            ->assertOk()
            ->assertJson([
                'total_items' => 5,
                'total_pages' => 1,
                'previous_page_url' => null,
                'next_page_url' => null
            ]);
    }

    /** @test */
    public function it_can_return_data_about_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()->create();

        $showingSubject = Subject::factory()->create();
        $basicUsersSubject = Subject::factory()
            ->for($basicUser, 'user')
            ->create();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.show', $showingSubject->id))
            ->assertOk()
            ->assertJson([
                'id' => $showingSubject->id,
                'first_name' => $showingSubject->first_name,
                'last_name' => $showingSubject->last_name,
                'title' => $showingSubject->title,
                'city' => $showingSubject->city,
                'state' => $showingSubject->state,
                'phone_number' => $showingSubject->phone_number,
                'email' => $showingSubject->email,
                'overview' => $showingSubject->overview,
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.show', $showingSubject->id))
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.show', $basicUsersSubject->id))
            ->assertOk();
    }

    /** @test */
    public function it_can_create_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()->create();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->post(route('subjects.store'), [
                'first_name' => 'Test',
                'last_name' => 'Subject',
                'title' => 'Test Title',
                'city' => 'Test City',
                'state' => 'Test State',
                'phone_number' => '555-555-5555',
                'email' => 'test@example.com',
                'overview' => 'Test Overview'
            ])
            ->assertCreated()
            ->assertJson([
                'first_name' => 'Test',
                'last_name' => 'Subject',
                'title' => 'Test Title',
                'city' => 'Test City',
                'state' => 'Test State',
                'phone_number' => '555-555-5555',
                'email' => 'test@example.com',
                'overview' => 'Test Overview'
            ])
            ->assertJsonStructure([
                'id',
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('subjects.store'), [
                'first_name' => 'Test',
                'last_name' => 'Subject',
                'title' => 'Test Title',
                'city' => 'Test City',
                'state' => 'Test State',
                'phone_number' => '555-555-5555',
                'email' => 'test2@example.com',
                'overview' => 'Test Overview'
            ])
            ->assertCreated();
    }

    /** @test */
    public function it_can_update_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()->create();

        $updatingSubject = Subject::factory()->create();
        $basicUsersSubject = Subject::factory()
            ->for($basicUser, 'user')
            ->create();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->put(route('subjects.update', $updatingSubject->id), [
                'first_name' => 'Test',
                'last_name' => 'Subject',
                'title' => 'Test Title',
                'city' => 'Test City',
                'state' => 'Test State',
                'phone_number' => '555-555-5555',
                'email' => 'test@example.com',
                'overview' => 'Test Overview'
            ])
            ->assertOk()
            ->assertJson([
                'id' => $updatingSubject->id,
                'first_name' => 'Test',
                'last_name' => 'Subject',
                'title' => 'Test Title',
                'city' => 'Test City',
                'state' => 'Test State',
                'phone_number' => '555-555-5555',
                'email' => 'test@example.com',
                'overview' => 'Test Overview'
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.update', $updatingSubject->id), [
                'first_name' => 'Test',
                'last_name' => 'Subject',
                'title' => 'Test Title',
                'city' => 'Test City',
                'state' => 'Test State',
                'phone_number' => '555-555-5555',
                'email' => 'test@example.com',
                'overview' => 'Test Overview'
            ])
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.update', $basicUsersSubject->id), [
                'first_name' => 'Test',
                'last_name' => 'Subject',
                'title' => 'Test Title',
                'city' => 'Test City',
                'state' => 'Test State',
                'phone_number' => '555-555-5555',
                'email' => 'test@example.com',
                'overview' => 'Test Overview'
            ])
            ->assertOk()
            ->assertJson([
                'id' => $basicUsersSubject->id,
                'first_name' => 'Test',
                'last_name' => 'Subject',
                'title' => 'Test Title',
                'city' => 'Test City',
                'state' => 'Test State',
                'phone_number' => '555-555-5555',
                'email' => 'test@example.com',
                'overview' => 'Test Overview'
            ]);
    }

    /** @test */
    public function it_can_delete_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()->create();

        $deletingSubject = Subject::factory()->create();
        $basicUsersSubject = Subject::factory()
            ->for($basicUser, 'user')
            ->create();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.destroy', $deletingSubject->id))
            ->assertOk()
            ->assertJson([
                'message' => 'Ok'
            ]);

        $deletingSubject = Subject::find($deletingSubject)->first();

        $this->assertNull($deletingSubject);

        auth()->forgetGuards();

        $deletingSubject = Subject::factory()->create();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.destroy', $deletingSubject->id))
            ->assertForbidden();

        $deletingSubject = Subject::find($deletingSubject)->first();

        $this->assertNotNull($deletingSubject);

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.destroy', $basicUsersSubject->id))
            ->assertOk();

        $basicUsersSubject = Subject::find($basicUsersSubject)->first();

        $this->assertNull($basicUsersSubject);
    }
}
