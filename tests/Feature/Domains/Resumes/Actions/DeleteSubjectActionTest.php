<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\DeleteSubjectAction;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\EducationHighlight;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\EmployerHighlight;
use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteSubjectActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_a_subject(): void
    {
        $subject = Subject::factory()
            ->has(SubjectHighlight::factory(1), 'highlights')
            ->has(Skill::factory(1), 'skills')
            ->has(
                Employer::factory(1)->has(EmployerHighlight::factory(1), 'highlights'),
                'employers'
            )
            ->has(
                Education::factory(1)->has(EducationHighlight::factory(1), 'highlights'),
                'education'
            )
            ->create();

        $subjectHighlight = $subject->highlights->first();
        $skill = $subject->skills->first();
        $employer = $subject->employers->first();
        $employerHighlight = $employer->highlights->first();
        $education = $subject->education->first();
        $educationHighlight = $education->highlights->first();

        app(DeleteSubjectAction::class)->execute(SubjectData::from($subject));

        $subject = Subject::find($subject->id);
        $subjectHighlight = SubjectHighlight::find($subjectHighlight->id);
        $skill = Skill::find($skill->id);
        $employer = Employer::find($employer->id);
        $employerHighlight = EmployerHighlight::find($employerHighlight->id);
        $education = Education::find($education->id);
        $educationHighlight = EducationHighlight::find($educationHighlight->id);

        $this->assertNull($subject);
        $this->assertNull($subjectHighlight);
        $this->assertNull($skill);
        $this->assertNull($employer);
        $this->assertNull($employerHighlight);
        $this->assertNull($education);
        $this->assertNull($educationHighlight);
    }
}
