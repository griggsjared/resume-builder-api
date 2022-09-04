<?php

namespace Tests\Feature\Models;

use App\Models\Skill;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class SkillTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function table_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('skills', [
                'id',
                'uuid',
                'subject_id',
                'name',
                'category',
                'sort',
                'created_at',
                'updated_at',
            ])
        );
    }

    /** @test */
    public function uuid_is_valid()
    {
        $skill = Skill::factory()->create();

        $this->assertTrue(
            Uuid::isValid($skill->uuid)
        );
    }

    /** @test */
    public function has_relationships()
    {
        $skill = Skill::factory()
            ->for(Subject::factory(), 'subject')
            ->create();

        $this->assertInstanceOf(Skill::class, $skill);
        $this->assertInstanceOf(Subject::class, $skill->subject);
    }
}
