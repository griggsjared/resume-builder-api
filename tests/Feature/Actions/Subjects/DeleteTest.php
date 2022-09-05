<?php

namespace Tests\Feature\Actions\Subjects;

use App\Actions\Subjects\Delete;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function subject_is_deleted()
    {
        $subject = Subject::factory()->create();

        $deleted = (new Delete)->execute($subject);

        $subject = Subject::where('id', $subject->uuid)->first();

        $this->assertTrue($deleted);
        $this->assertNull($subject);
    }
}
