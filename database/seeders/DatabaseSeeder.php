<?php

namespace Database\Seeders;

use App\Models\Employer;
use App\Models\EmployerHighlight;
use App\Models\Skill;
use App\Models\Subject;
use App\Models\SubjectHighlight;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Subject::factory(10)
            ->has(SubjectHighlight::factory(3), 'highlights')
            ->has(Skill::factory(5)->uncategorized())
            ->has(Skill::factory(20))
            ->has(
                Employer::factory(1)->current()->has(EmployerHighlight::factory(3), 'highlights')
            )
            ->has(
                Employer::factory(1)->has(EmployerHighlight::factory(3), 'highlights')
            )
            ->create();
    }
}
