<?php

namespace Tests\Feature\Actions\Subjects;

use App\Actions\Subjects\Upsert;
use App\Models\DTO\SubjectData;
use App\Models\DTO\UserData;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpsertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_at_subject()
    {
        $data = SubjectData::from([
            'first_name' => 'Jim',
            'last_name' => 'Bob',
            'email' => 'jbob@example.com',
            'title' => 'Developer',
            'city' => 'New York',
            'state' => 'NY',
            'phone_number' => '555-555-5555',
            'overview' => 'I am a developer',
            'author' => UserData::from(
                User::factory()->create()
            ),
        ]);

        $data = app(Upsert::class)->handle($data);

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
    public function it_updates_at_subject()
    {
        $subject = Subject::factory()
            ->has(User::factory(), 'author')
            ->create();

        $author = User::factory()->create();

        $data = SubjectData::from([
            ...$subject->toArray(),
            'first_name' => 'Jim',
            'last_name' => 'Bob',
            'email' => 'jbob@example.com',
            'title' => 'Developer',
            'city' => 'New York',
            'state' => 'NY',
            'phone_number' => '555-555-5555',
            'overview' => 'I am a developer',
            'author' => UserData::from($author),
        ]);

        app(Upsert::class)->handle($data);

        $subject->refresh();

        $this->assertEquals($data->first_name, $subject->first_name);
        $this->assertEquals($data->last_name, $subject->last_name);
        $this->assertEquals($data->title, $subject->title);
        $this->assertEquals($data->city, $subject->city);
        $this->assertEquals($data->state, $subject->state);
        $this->assertEquals($data->phone_number, $subject->phone_number);
        $this->assertEquals($data->email, $subject->email);
        $this->assertEquals($data->overview, $subject->overview);
        $this->assertEquals($author->id, $subject->author->id);
    }
}
