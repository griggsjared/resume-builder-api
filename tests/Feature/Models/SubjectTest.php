<?php

namespace Tests\Feature\Models;

use App\Models\Employer;
use App\Models\Skill;
use App\Models\Subject;
use App\Models\SubjectHighlight;
use App\Models\SubjectImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function table_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('subjects', [
                'id',
                'uuid',
                'first_name',
                'last_name',
                'email',
                'phone_number',
                'city',
                'state',
                'overview',
                'created_at',
                'updated_at',
            ]),
            1
        );
    }

    /** @test */
    public function uuid_is_valid()
    {
        $contact = Subject::factory()->create();

        $this->assertTrue(
            Uuid::isValid($contact->uuid)
        );
    }

    /** @test */
    public function has_relationships()
    {
        $subject = Subject::factory()
            ->has(SubjectImage::factory(1), 'image')
            ->has(SubjectHighlight::factory(6), 'highlights')
            ->has(Employer::factory(3), 'employers')
            ->has(Skill::factory(3), 'skills')
            ->create();

        $this->assertInstanceOf(SubjectImage::class, $subject->image);
        $this->assertCount(6, $subject->highlights);
        $this->assertCount(3, $subject->employers);
        $this->assertCount(3, $subject->skills);
    }

    /** @test */
    public function full_name_matches_first_and_last()
    {
        $user = Subject::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals($user->full_name, $user->first_name.' '.$user->last_name);
    }
}
