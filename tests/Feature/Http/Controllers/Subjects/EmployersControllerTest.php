<?php

namespace Tests\Feature\Http\Controllers\Employers;

use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployersControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_a_list_of_paginated_employers_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Employer::factory()
                            ->count(5),
                        'employers'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Employer::factory()
                    ->count(15),
                'employers'
            )
            ->create();

        $basicUsersSubject = $basicUser->subjects->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.employers.index', [
                'subject' => $viewingSubject->id,
                'page' => 1,
                'per_page' => 10,
            ]))
            ->assertOk()
            ->assertJson([
                'total_items' => 15,
                'total_pages' => 2,
                'previous_page_url' => null,
            ])
            ->assertJsonStructure([
                'next_page_url',
                'items' => [
                    '*' => [
                        'id',
                        'name',
                        'city',
                        'state',
                        'started_at',
                        'ended_at',
                        'is_current',
                    ],
                ],
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.employers.index', [
                'subject' => $viewingSubject->id,
                'page' => 1,
                'per_page' => 10,
            ]))
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.employers.index', [
                'subject' => $basicUsersSubject->id,
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
    public function it_can_return_data_about_an_employer_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Employer::factory()
                            ->count(5),
                        'employers'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Employer::factory()
                    ->count(15),
                'employers'
            )
            ->create();

        $viewingEmployer = $viewingSubject->employers->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEmployer = $basicUsersSubject->employers->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.employers.show', [
                'subject' => $viewingSubject->id,
                'employer' => $viewingEmployer->id,
            ]))
            ->assertOk()
            ->assertJson([
                'id' => $viewingEmployer->id,
                'name' => $viewingEmployer->name,
                'city' => $viewingEmployer->city,
                'state' => $viewingEmployer->state,
                'started_at' => $viewingEmployer->started_at->format(DATE_ATOM),
                'ended_at' => $viewingEmployer->ended_at->format(DATE_ATOM),
                'is_current' => $viewingEmployer->is_current,
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.employers.show', [
                'subject' => $viewingSubject->id,
                'employer' => $viewingEmployer->id,
            ]))
            ->assertForbidden();
        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.employers.show', [
                'subject' => $basicUsersSubject->id,
                'employer' => $basicUsersEmployer->id,
            ]))
            ->assertOk();
    }

    /** @test */
    public function it_can_create_an_employer_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(Subject::factory(), 'subjects')
            ->create();

        $viewingSubject = Subject::factory()->create();

        $basicUsersSubject = $basicUser->subjects->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->post(route('subjects.employers.store', [
                'subject' => $viewingSubject->id,
            ]), [
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'started_at' => '2020-01-01',
                'ended_at' => '2020-02-01',
            ])
            ->assertCreated()
            ->assertJson([
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'started_at' => '2020-01-01T00:00:00+00:00',
                'ended_at' => '2020-02-01T00:00:00+00:00',
                'is_current' => false,
            ])
            ->assertJsonStructure([
                'id',
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('subjects.employers.store', [
                'subject' => $viewingSubject->id,
            ]), [
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'started_at' => '2020-01-01',
                'ended_at' => '2020-02-01',
            ])
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('subjects.employers.store', [
                'subject' => $basicUsersSubject->id,
            ]), [
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'started_at' => '2020-01-01',
                'ended_at' => '2020-02-01',
            ])
            ->assertCreated();
    }

    /** @test */
    public function it_can_update_an_employer_for_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Employer::factory()
                            ->count(5),
                        'employers'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Employer::factory()
                    ->count(15),
                'employers'
            )
            ->create();

        $updatingEmployer = $viewingSubject->employers->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEmployer = $basicUsersSubject->employers->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->put(route('subjects.employers.update', [
                'subject' => $viewingSubject->id,
                'employer' => $updatingEmployer->id,
            ]), [
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'started_at' => '2020-01-01',
                'ended_at' => '2020-02-01',
            ])
            ->assertOk()
            ->assertJson([
                'id' => $updatingEmployer->id,
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'started_at' => '2020-01-01T00:00:00+00:00',
                'ended_at' => '2020-02-01T00:00:00+00:00',
                'is_current' => false,
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.employers.update', [
                'subject' => $viewingSubject->id,
                'employer' => $updatingEmployer->id,
            ]), [
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'started_at' => '2020-01-01',
                'ended_at' => '2020-02-01',
            ])
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.employers.update', [
                'subject' => $basicUsersSubject->id,
                'employer' => $basicUsersEmployer->id,
            ]), [
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'started_at' => '2020-01-01',
                'ended_at' => '2020-02-01',
            ])
            ->assertOk()
            ->assertJson([
                'id' => $basicUsersEmployer->id,
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'started_at' => '2020-01-01T00:00:00+00:00',
                'ended_at' => '2020-02-01T00:00:00+00:00',
                'is_current' => false,
            ]);
    }

    /** @test */
    public function it_can_delete_an_employer_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Employer::factory()
                            ->count(5),
                        'employers'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Employer::factory()
                    ->count(15),
                'employers'
            )
            ->create();

        $deletingEmployer = $viewingSubject->employers->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEmployer = $basicUsersSubject->employers->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.employers.destroy', [
                'subject' => $viewingSubject->id,
                'employer' => $deletingEmployer->id,
            ]))
            ->assertOk()
            ->assertJson([
                'message' => 'Ok'
            ]);

        $deletingEmployer = Employer::find($deletingEmployer)->first();

        $this->assertNull($deletingEmployer);

        auth()->forgetGuards();

        $deletingEmployer = $viewingSubject->employers()->first();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.employers.destroy', [
                'subject' => $viewingSubject->id,
                'employer' => $deletingEmployer->id,
            ]))
            ->assertForbidden();

        $deletingEmployer = Employer::find($deletingEmployer)->first();

        $this->assertNotNull($deletingEmployer);

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.employers.destroy', [
                'subject' => $basicUsersSubject->id,
                'employer' => $basicUsersEmployer->id,
            ]))
            ->assertOk();

        $basicUsersEmployer = Employer::find($basicUsersEmployer)->first();

        $this->assertNull($basicUsersEmployer);
    }
}
