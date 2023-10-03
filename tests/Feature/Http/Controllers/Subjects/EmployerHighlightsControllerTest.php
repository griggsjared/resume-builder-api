<?php

namespace Tests\Feature\Http\Controllers\Highlights;

use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\EmployerHighlight;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerHighlightsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_a_list_of_paginated_highlights_for_an_employer()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Employer::factory()
                            ->has(
                                EmployerHighlight::factory()
                                    ->count(5),
                                'highlights'
                            )
                            ->count(1),
                        'employers'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Employer::factory()
                    ->has(
                        EmployerHighlight::factory()
                            ->count(15),
                        'highlights'
                    )
                    ->count(1),
                'employers'
            )
            ->create();

        $viewingEmployer = $viewingSubject->employers->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEmployer = $basicUsersSubject->employers->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.employers.highlights.index', [
                'subject' => $viewingSubject->id,
                'employer' => $viewingEmployer->id,
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
                        'content'
                    ],
                ],
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.employers.highlights.index', [
                'subject' => $viewingSubject->id,
                'employer' => $viewingEmployer->id,
                'page' => 1,
                'per_page' => 10,
            ]))
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.employers.highlights.index', [
                'subject' => $basicUsersSubject->id,
                'employer' => $basicUsersEmployer->id,
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
    public function it_can_return_data_about_a_highlight_for_an_employer()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Employer::factory()
                            ->has(
                                EmployerHighlight::factory()
                                    ->count(5),
                                'highlights'
                            )
                            ->count(1),
                        'employers'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Employer::factory()
                    ->has(
                        EmployerHighlight::factory()
                            ->count(15),
                        'highlights'
                    )
                    ->count(1),
                'employers'
            )
            ->create();

        $viewingEmployer = $viewingSubject->employers->first();
        $viewingHighlight = $viewingEmployer->highlights->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEmployer = $basicUsersSubject->employers->first();
        $basicUsersHighlight = $basicUsersEmployer->highlights->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.employers.highlights.show', [
                'subject' => $viewingSubject->id,
                'employer' => $viewingEmployer->id,
                'highlight' => $viewingHighlight->id,
            ]))
            ->assertOk()
            ->assertJson([
                'id' => $viewingHighlight->id,
                'content' => $viewingHighlight->content,
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.employers.highlights.show', [
                'subject' => $viewingSubject->id,
                'employer' => $viewingEmployer->id,
                'highlight' => $viewingHighlight->id,
            ]))
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.employers.highlights.show', [
                'subject' => $basicUsersSubject->id,
                'employer' => $basicUsersEmployer->id,
                'highlight' => $basicUsersHighlight->id,
            ]))
            ->assertOk();
    }

    /** @test */
    public function it_can_create_a_highlight_for_an_employer()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Employer::factory()->count(1),
                        'employers'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Employer::factory()->count(1),
                'employers'
            )
            ->create();

        $viewingEmployer = $viewingSubject->employers->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEmployer = $basicUsersSubject->employers->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->post(route('subjects.employers.highlights.store', [
                'subject' => $viewingSubject->id,
                'employer' => $viewingEmployer->id,
            ]), [
                'content' => 'Test',
            ])
            ->assertCreated()
            ->assertJson([
                'content' => 'Test',
            ])
            ->assertJsonStructure([
                'id',
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('subjects.employers.highlights.store', [
                'subject' => $viewingSubject->id,
                'employer' => $viewingEmployer->id,
            ]), [
                'content' => 'Test',
            ])
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('subjects.employers.highlights.store', [
                'subject' => $basicUsersSubject->id,
                'employer' => $basicUsersEmployer->id,
            ]), [
                'content' => 'Test',
            ])
            ->assertCreated();
    }

    /** @test */
    public function it_can_update_a_highlight_for_an_employer()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Employer::factory()
                            ->has(
                                EmployerHighlight::factory()
                                    ->count(5),
                                'highlights'
                            )
                            ->count(1),
                        'employers'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Employer::factory()
                    ->has(
                        EmployerHighlight::factory()
                            ->count(15),
                        'highlights'
                    )
                    ->count(1),
                'employers'
            )
            ->create();

        $viewingEmployer = $viewingSubject->employers->first();
        $updatingHighlight = $viewingEmployer->highlights->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEmployer = $basicUsersSubject->employers->first();
        $basicUsersHighlight = $basicUsersEmployer->highlights->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->put(route('subjects.employers.highlights.update', [
                'subject' => $viewingSubject->id,
                'employer' => $viewingEmployer->id,
                'highlight' => $updatingHighlight->id,
            ]), [
                'content' => 'Test',
            ])
            ->assertOk()
            ->assertJson([
                'id' => $updatingHighlight->id,
                'content' => 'Test',
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.employers.highlights.update', [
                'subject' => $viewingSubject->id,
                'employer' => $viewingEmployer->id,
                'highlight' => $updatingHighlight->id,
            ]), [
                'content' => 'Test',
            ])
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.employers.highlights.update', [
                'subject' => $basicUsersSubject->id,
                'employer' => $basicUsersEmployer->id,
                'highlight' => $basicUsersHighlight->id,
            ]), [
                'content' => 'Test',
            ])
            ->assertOk()
            ->assertJson([
                'id' => $basicUsersHighlight->id,
                'content' => 'Test',
            ]);
    }

    /** @test */
    public function it_can_delete_an_highlight_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Employer::factory()
                            ->has(
                                EmployerHighlight::factory()->count(5),
                                'highlights'
                            )
                            ->count(1),
                        'employers'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Employer::factory()
                    ->has(
                        EmployerHighlight::factory()->count(15),
                        'highlights'
                    )
                    ->count(1),
                'employers'
            )
            ->create();

        $viewingEmployer = $viewingSubject->employers->first();
        $deletingHighlight = $viewingEmployer->highlights->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEmployer = $basicUsersSubject->employers->first();
        $basicUsersHighlight = $basicUsersEmployer->highlights->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.employers.highlights.destroy', [
                'subject' => $viewingSubject->id,
                'employer' => $viewingEmployer->id,
                'highlight' => $deletingHighlight->id,
            ]))
            ->assertOk()
            ->assertJson([
                'message' => 'Ok'
            ]);

        $deletingHighlight = EmployerHighlight::find($deletingHighlight)->first();

        $this->assertNull($deletingHighlight);

        auth()->forgetGuards();

        $deletingHighlight = $viewingEmployer->highlights()->first();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.employers.highlights.destroy', [
                'subject' => $viewingSubject->id,
                'employer' => $viewingEmployer->id,
                'highlight' => $deletingHighlight->id,
            ]))
            ->assertForbidden();

        $deletingHighlight = EmployerHighlight::find($deletingHighlight)->first();

        $this->assertNotNull($deletingHighlight);

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.employers.highlights.destroy', [
                'subject' => $basicUsersSubject->id,
                'employer' => $basicUsersEmployer->id,
                'highlight' => $basicUsersHighlight->id,
            ]))
            ->assertOk();

        $basicUsersHighlight = EmployerHighlight::find($basicUsersHighlight)->first();

        $this->assertNull($basicUsersHighlight);
    }
}
