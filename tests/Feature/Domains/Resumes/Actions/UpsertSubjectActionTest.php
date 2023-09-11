<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\UpsertSubjectAction;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Actions\UpsertUserAction;
use App\Domains\Users\Models\User;
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
                'author' => $user,
            ])
        );

        $subject = Subject::find($data->id);

        $this->assertInstanceOf(User::class, $subject->author);
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
            ->has(User::factory(), 'author')
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
                'overview' => 'I am a developer'
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
}
