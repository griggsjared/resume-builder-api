<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\UpsertSubjectAction;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;
use App\Domains\Users\Models\User;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpsertSubjectActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_subject(): void
    {
        $user = User::factory()->create();

        $data = app(UpsertSubjectAction::class)->execute(
            SubjectData::from([
                'first_name' => 'Jim',
                'last_name' => 'Bob',
                'email' => 'jbob@example.com',
                'title' => 'Developer',
                'city' => 'New York',
                'state' => 'NY',
                'phone_number' => '555-555-5555',
                'overview' => 'I am a developer',
                'user' => $user,
            ])
        );

        $subject = Subject::find($data->id);

        $this->assertInstanceOf(User::class, $subject->user);
        $this->assertEquals($user->id, $subject->user->id);
        $this->assertEquals($data->first_name, $subject->first_name);
        $this->assertEquals($data->last_name, $subject->last_name);
        $this->assertEquals($data->title, $subject->title);
        $this->assertEquals($data->city, $subject->city);
        $this->assertEquals($data->state, $subject->state);
        $this->assertEquals($data->phone_number, $subject->phone_number);
        $this->assertEquals($data->email, $subject->email);
        $this->assertEquals($data->overview, $subject->overview);
    }

    /** @test */
    public function it_can_update_a_subject()
    {
        $subject = Subject::factory()
            ->has(User::factory(), 'user')
            ->create();

        $data = app(UpsertSubjectAction::class)->execute(
            SubjectData::from([
                ...$subject->toArray(),
                'first_name' => 'Jim',
                'last_name' => 'Bob',
                'email' => 'jbob@example.com',
                'title' => 'Developer',
                'city' => 'New York',
                'state' => 'NY',
                'phone_number' => '555-555-5555',
                'overview' => 'I am a developer',
            ])
        );

        $subject->refresh();

        $this->assertEquals($data->first_name, $subject->first_name);
        $this->assertEquals($data->last_name, $subject->last_name);
        $this->assertEquals($data->title, $subject->title);
        $this->assertEquals($data->city, $subject->city);
        $this->assertEquals($data->state, $subject->state);
        $this->assertEquals($data->phone_number, $subject->phone_number);
        $this->assertEquals($data->email, $subject->email);
        $this->assertEquals($data->overview, $subject->overview);
    }

    /** @test */
    public function it_can_update_a_subject_with_an_user()
    {
        $subject = Subject::factory()
            ->has(User::factory(), 'user')
            ->create();

        $user = User::factory()->create();

        app(UpsertSubjectAction::class)->execute(
            SubjectData::from([
                ...$subject->toArray(),
                'user' => $user,
            ])
        );

        $subject->refresh();

        $this->assertInstanceOf(User::class, $subject->user);
        $this->assertEquals($user->id, $subject->user->id);
    }

    /** @test */
    public function it_can_upsert_subject_highlights_for_a_subject()
    {
        $subject = Subject::factory()
            ->has(User::factory(), 'user')
            ->has(SubjectHighlight::factory()->count(2), 'highlights')
            ->create();

        $keepHighlight = $subject->highlights->first();
        $deleteHighlight = $subject->highlights->last();

        app(UpsertSubjectAction::class)->execute(
            SubjectData::from([
                ...$subject->toArray(),
                'highlights' => [
                    [
                        ...$keepHighlight->toArray(),
                        'content' => 'I did a thing',
                    ],
                    [
                        'content' => 'I did another thing',
                    ],
                ],
            ])
        );

        $subject->refresh();
        $keepHighlight->refresh();
        $deleteHighlight = SubjectHighlight::find($deleteHighlight->id);

        $this->assertCount(2, $subject->highlights);
        $this->assertEquals('I did a thing', $keepHighlight->content);
        $this->assertEquals($keepHighlight->id, $subject->highlights->first()->id);
        $this->assertEquals('I did another thing', $subject->highlights->last()->content);
        $this->assertNull($deleteHighlight);
    }

    /** @test */
    public function it_can_upsert_skills_for_a_subject()
    {
        $subject = Subject::factory()
            ->has(User::factory(), 'user')
            ->has(Skill::factory()->count(2), 'skills')
            ->create();

        $keepSkill = $subject->skills->first();
        $deleteSkill = $subject->skills->last();

        app(UpsertSubjectAction::class)->execute(
            SubjectData::from([
                ...$subject->toArray(),
                'skills' => [
                    [
                        ...$keepSkill->toArray(),
                        'name' => 'Skill 1',
                    ],
                    [
                        'name' => 'Skill 2',
                        'category' => 'Category 1',
                        'sort' => 1,
                    ],
                ],
            ])
        );

        $subject->refresh();
        $keepSkill->refresh();
        $deleteSkill = Skill::find($deleteSkill->id);

        $this->assertCount(2, $subject->skills);
        $this->assertEquals('Skill 1', $keepSkill->name);
        $this->assertEquals($keepSkill->id, $subject->skills->first()->id);
        $this->assertEquals('Skill 2', $subject->skills->last()->name);
        $this->assertEquals('Category 1', $subject->skills->last()->category);
        $this->assertEquals(1, $subject->skills->last()->sort);
        $this->assertNull($deleteSkill);
    }

    /** @test */
    public function it_can_upsert_employers_for_a_subject()
    {
        $this->freezeTime();

        $subject = Subject::factory()
            ->has(User::factory(), 'user')
            ->has(Employer::factory()->count(2), 'employers')
            ->create();

        $keepEmployer = $subject->employers->first();
        $deleteEmployer = $subject->employers->last();

        app(UpsertSubjectAction::class)->execute(
            SubjectData::from([
                ...$subject->toArray(),
                'employers' => [
                    [
                        ...$keepEmployer->toArray(),
                        'name' => 'Employer 1',
                    ],
                    [
                        'name' => 'Employer 2',
                        'city' => 'New York',
                        'state' => 'NY',
                        'started_at' => now()->subYears(1),
                        'ended_at' => now(),
                        'highlights' => [
                            [
                                'content' => 'I did a thing',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $subject->refresh();
        $keepEmployer->refresh();
        $deleteEmployer = Employer::find($deleteEmployer->id);

        $this->assertCount(2, $subject->employers);
        $this->assertEquals('Employer 1', $keepEmployer->name);
        $this->assertEquals($keepEmployer->id, $subject->employers->first()->id);
        $this->assertEquals('Employer 2', $subject->employers->last()->name);
        $this->assertEquals('New York', $subject->employers->last()->city);
        $this->assertEquals('NY', $subject->employers->last()->state);
        $this->assertEquals(now()->subYears(1)->format('Y-m-d'), $subject->employers->last()->started_at->format('Y-m-d'));
        $this->assertEquals(now()->format('Y-m-d'), $subject->employers->last()->ended_at->format('Y-m-d'));
        $this->assertEquals('I did a thing', $subject->employers->last()->highlights->first()->content);
        $this->assertNull($deleteEmployer);
    }

    /** @test */
    public function it_can_upsert_education_for_a_subject()
    {
        $this->freezeTime();

        $subject = Subject::factory()
            ->has(User::factory(), 'user')
            ->has(Education::factory()->count(2), 'education')
            ->create();

        $keepEducation = $subject->education->first();
        $deleteEducation = $subject->education->last();

        app(UpsertSubjectAction::class)->execute(
            SubjectData::from([
                ...$subject->toArray(),
                'education' => [
                    [
                        ...$keepEducation->toArray(),
                        'name' => 'Education 1',
                    ],
                    [
                        'name' => 'Education 2',
                        'city' => 'New York',
                        'state' => 'NY',
                        'degree' => 'B.S.',
                        'started_at' => now()->subYears(1),
                        'ended_at' => now(),
                        'highlights' => [
                            [
                                'content' => 'I did a thing',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $subject->refresh();
        $keepEducation->refresh();
        $deleteEducation = Education::find($deleteEducation->id);

        $this->assertCount(2, $subject->education);
        $this->assertEquals('Education 1', $keepEducation->name);
        $this->assertEquals($keepEducation->id, $subject->education->first()->id);
        $this->assertEquals('Education 2', $subject->education->last()->name);
        $this->assertEquals('New York', $subject->education->last()->city);
        $this->assertEquals('NY', $subject->education->last()->state);
        $this->assertEquals('B.S.', $subject->education->last()->degree);
        $this->assertEquals(now()->subYears(1)->format('Y-m-d'), $subject->education->last()->started_at->format('Y-m-d'));
        $this->assertEquals(now()->format('Y-m-d'), $subject->education->last()->ended_at->format('Y-m-d'));
        $this->assertEquals('I did a thing', $subject->education->last()->highlights->first()->content);
        $this->assertNull($deleteEducation);
    }
}
