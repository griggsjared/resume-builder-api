<?php

namespace Tests\Feature\Http\Controllers\Subjects;

use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_a_list_of_paginated_education_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Education::factory()
                            ->count(5),
                        'education'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Education::factory()
                    ->count(15),
                'education'
            )
            ->create();

        $basicUsersSubject = $basicUser->subjects->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.education.index', [
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
                        'major_degree',
                        'earned_major_degree',
                        'minor_degree',
                        'earned_minor_degree',
                        'is_current',
                    ],
                ],
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.education.index', [
                'subject' => $viewingSubject->id,
                'page' => 1,
                'per_page' => 10,
            ]))
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.education.index', [
                'subject' => $basicUsersSubject->id,
                'page' => 1,
                'per_page' => 10,
            ]))
            ->assertOk()
            ->assertJson([
                'total_items' => 5,
                'total_pages' => 1,
                'previous_page_url' => null,
                'next_page_url' => null,
            ]);
    }

    /** @test */
    public function it_can_return_data_about_an_education_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Education::factory()
                            ->count(5),
                        'education'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Education::factory()
                    ->count(15),
                'education'
            )
            ->create();

        $viewingEducation = $viewingSubject->education->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEducation = $basicUsersSubject->education->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.education.show', [
                'subject' => $viewingSubject->id,
                'education' => $viewingEducation->id,
            ]))
            ->assertOk()
            ->assertJson([
                'id' => $viewingEducation->id,
                'name' => $viewingEducation->name,
                'city' => $viewingEducation->city,
                'state' => $viewingEducation->state,
                'started_at' => $viewingEducation->started_at->format(DATE_ATOM),
                'ended_at' => $viewingEducation->ended_at->format(DATE_ATOM),
                'is_current' => $viewingEducation->is_current,
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.education.show', [
                'subject' => $viewingSubject->id,
                'education' => $viewingEducation->id,
            ]))
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.education.show', [
                'subject' => $basicUsersSubject->id,
                'education' => $basicUsersEducation->id,
            ]))
            ->assertOk();
    }

    /** @test */
    public function it_can_create_an_education_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(Subject::factory(), 'subjects')
            ->create();

        $viewingSubject = Subject::factory()->create();

        $basicUsersSubject = $basicUser->subjects->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->post(route('subjects.education.store', [
                'subject' => $viewingSubject->id,
            ]), [
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'major_degree' => 'Test Major',
                'earned_major_degree' => true,
                'minor_degree' => 'Test Minor',
                'earned_minor_degree' => true,
                'started_at' => '2020-01-01',
                'ended_at' => '2020-02-01',
            ])
            ->assertCreated()
            ->assertJson([
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'major_degree' => 'Test Major',
                'earned_major_degree' => true,
                'minor_degree' => 'Test Minor',
                'earned_minor_degree' => true,
                'started_at' => '2020-01-01T00:00:00+00:00',
                'ended_at' => '2020-02-01T00:00:00+00:00',
                'is_current' => false,
            ])
            ->assertJsonStructure([
                'id',
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('subjects.education.store', [
                'subject' => $viewingSubject->id,
            ]), [
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'major_degree' => 'Test Major',
                'earned_major_degree' => true,
                'minor_degree' => 'Test Minor',
                'earned_minor_degree' => true,
                'started_at' => '2020-01-01',
                'ended_at' => '2020-02-01',
            ])
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('subjects.education.store', [
                'subject' => $basicUsersSubject->id,
            ]), [
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'major_degree' => 'Test Major',
                'earned_major_degree' => true,
                'minor_degree' => 'Test Minor',
                'earned_minor_degree' => true,
                'started_at' => '2020-01-01',
                'ended_at' => '2020-02-01',
            ])
            ->assertCreated();
    }

    /** @test */
    public function it_can_update_an_education_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Education::factory()
                            ->count(5),
                        'education'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Education::factory()
                    ->count(15),
                'education'
            )
            ->create();

        $updatingEducation = $viewingSubject->education->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEducation = $basicUsersSubject->education->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->put(route('subjects.education.update', [
                'subject' => $viewingSubject->id,
                'education' => $updatingEducation->id,
            ]), [
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'major_degree' => 'Test Major',
                'earned_major_degree' => true,
                'minor_degree' => 'Test Minor',
                'earned_minor_degree' => true,
                'started_at' => '2020-01-01',
                'ended_at' => '2020-02-01',
            ])
            ->assertOk()
            ->assertJson([
                'id' => $updatingEducation->id,
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'major_degree' => 'Test Major',
                'earned_major_degree' => true,
                'minor_degree' => 'Test Minor',
                'earned_minor_degree' => true,
                'started_at' => '2020-01-01T00:00:00+00:00',
                'ended_at' => '2020-02-01T00:00:00+00:00',
                'is_current' => false,
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.education.update', [
                'subject' => $viewingSubject->id,
                'education' => $updatingEducation->id,
            ]), [
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'major_degree' => 'Test Major',
                'earned_major_degree' => true,
                'minor_degree' => 'Test Minor',
                'earned_minor_degree' => true,
                'started_at' => '2020-01-01',
                'ended_at' => '2020-02-01',
            ])
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.education.update', [
                'subject' => $basicUsersSubject->id,
                'education' => $basicUsersEducation->id,
            ]), [
                'name' => 'Test',
                'city' => 'Test City',
                'state' => 'Test State',
                'major_degree' => 'Test Major',
                'earned_major_degree' => true,
                'minor_degree' => 'Test Minor',
                'earned_minor_degree' => true,
                'started_at' => '2020-01-01',
                'ended_at' => '2020-02-01',
            ])
            ->assertOk();
    }

    /** @test */
    public function it_can_delete_an_education_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Education::factory()
                            ->count(5),
                        'education'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Education::factory()
                    ->count(15),
                'education'
            )
            ->create();

        $deletingEducation = $viewingSubject->education->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEducation = $basicUsersSubject->education->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.education.destroy', [
                'subject' => $viewingSubject->id,
                'education' => $deletingEducation->id,
            ]))
            ->assertOk()
            ->assertJson([
                'message' => 'Ok',
            ]);

        $deletingEducation = Education::find($deletingEducation)->first();

        $this->assertNull($deletingEducation);

        auth()->forgetGuards();

        $deletingEducation = $viewingSubject->education()->first();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.education.destroy', [
                'subject' => $viewingSubject->id,
                'education' => $deletingEducation->id,
            ]))
            ->assertForbidden();

        $deletingEducation = Education::find($deletingEducation)->first();

        $this->assertNotNull($deletingEducation);

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.education.destroy', [
                'subject' => $basicUsersSubject->id,
                'education' => $basicUsersEducation->id,
            ]))
            ->assertOk();

        $basicUsersEducation = Education::find($basicUsersEducation)->first();

        $this->assertNull($basicUsersEducation);
    }
}
