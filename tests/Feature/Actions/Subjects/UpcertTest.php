<?php

namespace Tests\Feature\Actions\Subjects;

use Tests\TestCase;
use App\Models\User;
use App\DTO\UserData;
use App\Models\Subject;
use App\DTO\SubjectData;
use App\Actions\Subjects\Upsert;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function subject_is_created()
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
            )
        ]);

        $subject = (new Upsert)->execute($data);

        $this->assertInstanceOf(Subject::class, $subject);
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
    public function subject_is_updated()
    {
        $subject = Subject::factory()
            ->has(User::factory(), 'author')
            ->create();

        $newAuthor = User::factory()->create();

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
            'author' => UserData::from($newAuthor)
        ]);

        $updatedSubject = (new Upsert)->execute($data);

        $this->assertInstanceOf(Subject::class, $updatedSubject);
        $this->assertInstanceOf(User::class, $updatedSubject->author);
        $this->assertEquals($subject->id, $updatedSubject->id);
        $this->assertEquals($data->first_name, $updatedSubject->first_name);
        $this->assertEquals($data->last_name, $updatedSubject->last_name);
        $this->assertEquals($data->title, $updatedSubject->title);
        $this->assertEquals($data->city, $updatedSubject->city);
        $this->assertEquals($data->state, $updatedSubject->state);
        $this->assertEquals($data->phone_number, $updatedSubject->phone_number);
        $this->assertEquals($data->email, $updatedSubject->email);
        $this->assertEquals($data->overview, $updatedSubject->overview);
        $this->assertEquals($newAuthor->id, $updatedSubject->author->id);
    }

    /** @test */
    public function subject_is_updated_with_no_author()
    {
        $subject = Subject::factory()
            ->has(User::factory(), 'author')
            ->create();

        $data = SubjectData::from($subject)->additional([
            'author' => null
        ]);

        $updatedSubject = (new Upsert)->execute($data);

        $this->assertNull($updatedSubject->author);
    }
}

