<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\DeleteSubjectAction;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteSubjectActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_a_subject(): void
    {
        $subject = Subject::factory()->create();

        app(DeleteSubjectAction::class)->execute(SubjectData::from($subject));

        $subject = Subject::find($subject->id);

        $this->assertNull($subject);
    }
}
