<?php

namespace Tests\Feature\Http\Controllers\Highlights;

use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectHighlightsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_a_list_of_paginated_highlights_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        SubjectHighlight::factory()
                            ->count(5),
                        'highlights'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                SubjectHighlight::factory()
                    ->count(15),
                'highlights'
            )
            ->create();

        $basicUsersSubject = $basicUser->subjects->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.highlights.index', [
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
                        'content',
                        'sort',
                    ],
                ],
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.highlights.index', [
                'subject' => $viewingSubject->id,
                'page' => 1,
                'per_page' => 10,
            ]))
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.highlights.index', [
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
    public function it_can_return_data_about_a_highlight_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        SubjectHighlight::factory()
                            ->count(5),
                        'highlights'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                SubjectHighlight::factory()
                    ->count(15),
                'highlights'
            )
            ->create();

        $viewingHighlight = $viewingSubject->highlights->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersHighlight = $basicUsersSubject->highlights->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.highlights.show', [
                'subject' => $viewingSubject->id,
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
            ->get(route('subjects.highlights.show', [
                'subject' => $viewingSubject->id,
                'highlight' => $viewingHighlight->id,
            ]))
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.highlights.show', [
                'subject' => $basicUsersSubject->id,
                'highlight' => $basicUsersHighlight->id,
            ]))
            ->assertOk();
    }

    /** @test */
    public function it_can_create_a_highlight_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(Subject::factory(), 'subjects')
            ->create();

        $viewingSubject = Subject::factory()->create();

        $basicUsersSubject = $basicUser->subjects->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->post(route('subjects.highlights.store', [
                'subject' => $viewingSubject->id,
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
            ->post(route('subjects.highlights.store', [
                'subject' => $viewingSubject->id,
            ]), [
                'content' => 'Test',
            ])
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('subjects.highlights.store', [
                'subject' => $basicUsersSubject->id,
            ]), [
                'content' => 'Test',
            ])
            ->assertCreated();
    }

    /** @test */
    public function it_can_update_a_highlight_for_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        SubjectHighlight::factory()
                            ->count(5),
                        'highlights'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                SubjectHighlight::factory()
                    ->count(15),
                'highlights'
            )
            ->create();

        $updatingHighlight = $viewingSubject->highlights->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersHighlight = $basicUsersSubject->highlights->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->put(route('subjects.highlights.update', [
                'subject' => $viewingSubject->id,
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
            ->patch(route('subjects.highlights.update', [
                'subject' => $viewingSubject->id,
                'highlight' => $updatingHighlight->id,
            ]), [
                'content' => 'Test',
                'sort' => 1,
            ])
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.highlights.update', [
                'subject' => $basicUsersSubject->id,
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
    public function it_can_delete_a_highlight_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        SubjectHighlight::factory()
                            ->count(5),
                        'highlights'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                SubjectHighlight::factory()
                    ->count(15),
                'highlights'
            )
            ->create();

        $deletingHighlight = $viewingSubject->highlights->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersHighlight = $basicUsersSubject->highlights->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.highlights.destroy', [
                'subject' => $viewingSubject->id,
                'highlight' => $deletingHighlight->id,
            ]))
            ->assertOk()
            ->assertJson([
                'message' => 'Ok',
            ]);

        $deletingHighlight = SubjectHighlight::find($deletingHighlight)->first();

        $this->assertNull($deletingHighlight);

        auth()->forgetGuards();

        $deletingHighlight = $viewingSubject->highlights()->first();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.highlights.destroy', [
                'subject' => $viewingSubject->id,
                'highlight' => $deletingHighlight->id,
            ]))
            ->assertForbidden();

        $deletingHighlight = SubjectHighlight::find($deletingHighlight)->first();

        $this->assertNotNull($deletingHighlight);

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.highlights.destroy', [
                'subject' => $basicUsersSubject->id,
                'highlight' => $basicUsersHighlight->id,
            ]))
            ->assertOk();

        $basicUsersHighlight = SubjectHighlight::find($basicUsersHighlight)->first();

        $this->assertNull($basicUsersHighlight);
    }
}
