<?php

namespace Tests\Feature\Actions\Subjects;

use App\Actions\Subjects\AssignAuthor;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignAuthorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function author_is_assigned()
    {
        $subject = Subject::factory()->create();
        $user = User::factory()->create();

        $assigned = (new AssignAuthor)->execute($subject, $user);

        $this->assertTrue($assigned);
        $this->assertEquals($subject->author->id, $user->id);
    }
}
