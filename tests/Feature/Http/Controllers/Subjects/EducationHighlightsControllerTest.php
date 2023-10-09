<?php

namespace Tests\Feature\Http\Controllers\Highlights;

use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\EducationHighlight;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationHighlightsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_a_list_of_paginated_highlights_for_an_education()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Education::factory()
                            ->has(
                                EducationHighlight::factory()
                                    ->count(5),
                                'highlights'
                            )
                            ->count(1),
                        'education'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Education::factory()
                    ->has(
                        EducationHighlight::factory()
                            ->count(15),
                        'highlights'
                    )
                    ->count(1),
                'education'
            )
            ->create();

        $viewingEducation = $viewingSubject->education->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEducation = $basicUsersSubject->education->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.education.highlights.index', [
                'subject' => $viewingSubject->id,
                'education' => $viewingEducation->id,
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
                        'content',
                        'sort',
                    ],
                ],
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.education.highlights.index', [
                'subject' => $viewingSubject->id,
                'education' => $viewingEducation->id,
                'page' => 1,
                'per_page' => 10,
            ]))
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.education.highlights.index', [
                'subject' => $basicUsersSubject->id,
                'education' => $basicUsersEducation->id,
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
    public function it_can_return_data_about_a_highlight_for_an_education()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Education::factory()
                            ->has(
                                EducationHighlight::factory()
                                    ->count(5),
                                'highlights'
                            )
                            ->count(1),
                        'education'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Education::factory()
                    ->has(
                        EducationHighlight::factory()
                            ->count(15),
                        'highlights'
                    )
                    ->count(1),
                'education'
            )
            ->create();

        $viewingEducation = $viewingSubject->education->first();
        $viewingHighlight = $viewingEducation->highlights->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEducation = $basicUsersSubject->education->first();
        $basicUsersHighlight = $basicUsersEducation->highlights->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.education.highlights.show', [
                'subject' => $viewingSubject->id,
                'education' => $viewingEducation->id,
                'highlight' => $viewingHighlight->id,
            ]))
            ->assertOk()
            ->assertJson([
                'id' => $viewingHighlight->id,
                'content' => $viewingHighlight->content,
                'sort' => $viewingHighlight->sort,
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.education.highlights.show', [
                'subject' => $viewingSubject->id,
                'education' => $viewingEducation->id,
                'highlight' => $viewingHighlight->id,
            ]))
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.education.highlights.show', [
                'subject' => $basicUsersSubject->id,
                'education' => $basicUsersEducation->id,
                'highlight' => $basicUsersHighlight->id,
            ]))
            ->assertOk();
    }

    /** @test */
    public function it_can_create_a_highlight_for_an_education()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Education::factory()->count(1),
                        'education'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Education::factory()->count(1),
                'education'
            )
            ->create();

        $viewingEducation = $viewingSubject->education->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEducation = $basicUsersSubject->education->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->post(route('subjects.education.highlights.store', [
                'subject' => $viewingSubject->id,
                'education' => $viewingEducation->id,
            ]), [
                'content' => 'Test',
                'sort' => 1,
            ])
            ->assertCreated()
            ->assertJson([
                'content' => 'Test',
                'sort' => 1,
            ])
            ->assertJsonStructure([
                'id',
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('subjects.education.highlights.store', [
                'subject' => $viewingSubject->id,
                'education' => $viewingEducation->id,
            ]), [
                'content' => 'Test',
                'sort' => 1,
            ])
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('subjects.education.highlights.store', [
                'subject' => $basicUsersSubject->id,
                'education' => $basicUsersEducation->id,
            ]), [
                'content' => 'Test',
                'sort' => 1,
            ])
            ->assertCreated();
    }

    /** @test */
    public function it_can_update_a_highlight_for_an_education()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Education::factory()
                            ->has(
                                EducationHighlight::factory()
                                    ->count(5),
                                'highlights'
                            )
                            ->count(1),
                        'education'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Education::factory()
                    ->has(
                        EducationHighlight::factory()
                            ->count(15),
                        'highlights'
                    )
                    ->count(1),
                'education'
            )
            ->create();

        $viewingEducation = $viewingSubject->education->first();
        $updatingHighlight = $viewingEducation->highlights->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEducation = $basicUsersSubject->education->first();
        $basicUsersHighlight = $basicUsersEducation->highlights->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->put(route('subjects.education.highlights.update', [
                'subject' => $viewingSubject->id,
                'education' => $viewingEducation->id,
                'highlight' => $updatingHighlight->id,
            ]), [
                'content' => 'Test',
                'sort' => 1,
            ])
            ->assertOk()
            ->assertJson([
                'id' => $updatingHighlight->id,
                'content' => 'Test',
                'sort' => 1,
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.education.highlights.update', [
                'subject' => $viewingSubject->id,
                'education' => $viewingEducation->id,
                'highlight' => $updatingHighlight->id,
            ]), [
                'content' => 'Test',
                'sort' => 1,
            ])
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.education.highlights.update', [
                'subject' => $basicUsersSubject->id,
                'education' => $basicUsersEducation->id,
                'highlight' => $basicUsersHighlight->id,
            ]), [
                'content' => 'Test',
                'sort' => 1,
            ])
            ->assertOk()
            ->assertJson([
                'id' => $basicUsersHighlight->id,
                'content' => 'Test',
                'sort' => 1,
            ]);
    }

    /** @test */
    public function it_can_delete_an_highlight_for_an_education()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Education::factory()
                            ->has(
                                EducationHighlight::factory()->count(5),
                                'highlights'
                            )
                            ->count(1),
                        'education'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Education::factory()
                    ->has(
                        EducationHighlight::factory()->count(15),
                        'highlights'
                    )
                    ->count(1),
                'education'
            )
            ->create();

        $viewingEducation = $viewingSubject->education->first();
        $deletingHighlight = $viewingEducation->highlights->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersEducation = $basicUsersSubject->education->first();
        $basicUsersHighlight = $basicUsersEducation->highlights->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.education.highlights.destroy', [
                'subject' => $viewingSubject->id,
                'education' => $viewingEducation->id,
                'highlight' => $deletingHighlight->id,
            ]))
            ->assertOk()
            ->assertJson([
                'message' => 'Ok',
            ]);

        $deletingHighlight = EducationHighlight::find($deletingHighlight)->first();

        $this->assertNull($deletingHighlight);

        auth()->forgetGuards();

        $deletingHighlight = $viewingEducation->highlights()->first();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.education.highlights.destroy', [
                'subject' => $viewingSubject->id,
                'education' => $viewingEducation->id,
                'highlight' => $deletingHighlight->id,
            ]))
            ->assertForbidden();

        $deletingHighlight = EducationHighlight::find($deletingHighlight)->first();

        $this->assertNotNull($deletingHighlight);

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.education.highlights.destroy', [
                'subject' => $basicUsersSubject->id,
                'education' => $basicUsersEducation->id,
                'highlight' => $basicUsersHighlight->id,
            ]))
            ->assertOk();

        $basicUsersHighlight = EducationHighlight::find($basicUsersHighlight)->first();

        $this->assertNull($basicUsersHighlight);
    }
}
