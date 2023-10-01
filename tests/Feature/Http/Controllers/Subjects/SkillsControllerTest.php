<?php

namespace Tests\Feature\Http\Controllers\Skills;

use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_a_list_of_paginated_skills_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Skill::factory()
                            ->count(5),
                        'skills'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Skill::factory()
                    ->count(15),
                'skills'
            )
            ->create();

        $basicUsersSubject = $basicUser->subjects->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.skills.index', [
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
                        'category',
                        'sort',
                    ],
                ],
            ]);

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.skills.index', [
                'subject' => $viewingSubject->id,
                'page' => 1,
                'per_page' => 10,
            ]))
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.skills.index', [
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
    public function it_can_return_data_about_a_skill_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Skill::factory()
                            ->count(5),
                        'skills'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Skill::factory()
                    ->count(15),
                'skills'
            )
            ->create();

        $viewingSkill = $viewingSubject->skills->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersSkill = $basicUsersSubject->skills->first();

        //admin can view any skill for any subject
        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->get(route('subjects.skills.show', [
                'subject' => $viewingSubject->id,
                'skill' => $viewingSkill->id,
            ]))
            ->assertOk()
            ->assertJson([
                'id' => $viewingSkill->id,
                'name' => $viewingSkill->name,
                'category' => $viewingSkill->category,
                'sort' => $viewingSkill->sort
            ]);

        auth()->forgetGuards();

        //basic cannot view skills for subjects they do not own
        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.skills.show', [
                'subject' => $viewingSubject->id,
                'skill' => $viewingSkill->id,
            ]))
            ->assertForbidden();

        //basic can view skills for subjects they own
        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->get(route('subjects.skills.show', [
                'subject' => $basicUsersSubject->id,
                'skill' => $basicUsersSkill->id,
            ]))
            ->assertOk();
    }

    /** @test */
    public function it_can_create_an_skill_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(Subject::factory(), 'subjects')
            ->create();

        $viewingSubject = Subject::factory()->create();

        $basicUsersSubject = $basicUser->subjects->first();

        //admin can create an skill
        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->post(route('subjects.skills.store', [
                'subject' => $viewingSubject->id,
            ]), [
                'name' => 'Test',
                'category' => 'Test Category',
                'sort' => 1,
            ])
            ->assertCreated()
            ->assertJson([
                'name' => 'Test',
                'category' => 'Test Category',
                'sort' => 1,
            ])
            ->assertJsonStructure([
                'id',
            ]);

        auth()->forgetGuards();

        //basic cannot create an skill for a subject they do not own
        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('subjects.skills.store', [
                'subject' => $viewingSubject->id,
            ]), [
                'name' => 'Test',
                'category' => 'Test Category',
                'sort' => 1,
            ])
            ->assertForbidden();

        //basic can create a skill for their own subject
        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->post(route('subjects.skills.store', [
                'subject' => $basicUsersSubject->id,
            ]), [
                'name' => 'Test',
                'category' => 'Test Category',
                'sort' => 1,
            ])
            ->assertCreated();
    }

    /** @test */
    public function it_can_update_a_skill_for_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Skill::factory()
                            ->count(5),
                        'skills'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Skill::factory()
                    ->count(15),
                'skills'
            )
            ->create();

        $updatingSkill = $viewingSubject->skills->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersSkill = $basicUsersSubject->skills->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->put(route('subjects.skills.update', [
                'subject' => $viewingSubject->id,
                'skill' => $updatingSkill->id,
            ]), [
                'name' => 'Test',
                'category' => 'Test Category',
                'sort' => 1,
            ])
            ->assertOk()
            ->assertJson([
                'id' => $updatingSkill->id,
                'name' => 'Test',
                'category' => 'Test Category',
                'sort' => 1,
            ]);

        auth()->forgetGuards();

        //basic user cannot update skills they do not own
        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.skills.update', [
                'subject' => $viewingSubject->id,
                'skill' => $updatingSkill->id,
            ]), [
                'name' => 'Test',
                'category' => 'Test Category',
                'sort' => 1,
            ])
            ->assertForbidden();

        //basic user can update skills they own
        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->patch(route('subjects.skills.update', [
                'subject' => $basicUsersSubject->id,
                'skill' => $basicUsersSkill->id,
            ]), [
                'name' => 'Test',
                'category' => 'Test Category',
                'sort' => 1,
            ])
            ->assertOk()
            ->assertJson([
                'id' => $basicUsersSkill->id,
                'name' => 'Test',
                'category' => 'Test Category',
                'sort' => 1,
            ]);
    }

    /** @test */
    public function it_can_delete_an_skill_for_a_subject()
    {
        $adminUser = User::factory()->admin()->create();
        $basicUser = User::factory()->basic()
            ->has(
                Subject::factory()
                    ->has(
                        Skill::factory()
                            ->count(5),
                        'skills'
                    )
                    ->count(1),
                'subjects'
            )
            ->create();

        $viewingSubject = Subject::factory()
            ->has(
                Skill::factory()
                    ->count(15),
                'skills'
            )
            ->create();

        $deletingSkill = $viewingSubject->skills->first();

        $basicUsersSubject = $basicUser->subjects->first();
        $basicUsersSkill = $basicUsersSubject->skills->first();

        $this->withHeader('Authorization', 'Bearer '.$adminUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.skills.destroy', [
                'subject' => $viewingSubject->id,
                'skill' => $deletingSkill->id,
            ]))
            ->assertOk()
            ->assertJson([
                'message' => 'Ok'
            ]);

        $deletingSkill = Skill::find($deletingSkill)->first();

        $this->assertNull($deletingSkill);

        auth()->forgetGuards();

        $deletingSkill = $viewingSubject->skills()->first();

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.skills.destroy', [
                'subject' => $viewingSubject->id,
                'skill' => $deletingSkill->id,
            ]))
            ->assertForbidden();

        $deletingSkill = Skill::find($deletingSkill)->first();

        $this->assertNotNull($deletingSkill);

        $this->withHeader('Authorization', 'Bearer '.$basicUser->createToken('test')->plainTextToken)
            ->delete(route('subjects.skills.destroy', [
                'subject' => $basicUsersSubject->id,
                'skill' => $basicUsersSkill->id,
            ]))
            ->assertOk();

        $basicUsersSkill = Skill::find($basicUsersSkill)->first();

        $this->assertNull($basicUsersSkill);
    }
}
