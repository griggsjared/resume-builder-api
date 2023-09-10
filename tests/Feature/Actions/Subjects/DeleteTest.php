<?php

namespace Tests\Feature\Actions\Subjects;

use App\Actions\Subjects\Delete;
use App\Models\DTO\SubjectData;
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

        app(Delete::class)->handle(SubjectData::from($subject));

        $subject = Subject::find($subject->id);

        $this->assertNull($subject);
    }
}
