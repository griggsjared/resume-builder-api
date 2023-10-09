<?php

namespace Database\Seeders;

use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\EducationHighlight;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\EmployerHighlight;
use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;
use App\Domains\Users\Models\User;
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
        $email = 'test@example.com';
        $password = 'P@ssw0rd';

        $user = User::factory()->admin()->create([
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $token = explode('|', $user->createToken('test-token')->plainTextToken);

        $this->command->info('Test user created:');
        $this->command->info("Email: {$email}");
        $this->command->info("Password: {$password}");
        $this->command->info("Access Token: {$token[1]}");

        User::factory(10)
            ->basic()
            ->has(
                Subject::factory(rand(1, 3))
                    ->has(SubjectHighlight::factory(rand(1, 3)), 'highlights')
                    ->has(Skill::factory(rand(1, 2))->uncategorized(), 'skills')
                    ->has(Skill::factory(rand(2, 8)), 'skills')
                    ->has(
                        Employer::factory(1)->current()->has(EmployerHighlight::factory(rand(1, 3)), 'highlights'),
                        'employers'
                    )
                    ->has(
                        Employer::factory(rand(2, 3))->has(EmployerHighlight::factory(rand(1, 3)), 'highlights'),
                        'employers'
                    )
                    ->has(
                        Education::factory(1)->current()->Has(EducationHighlight::factory(rand(1, 3)), 'highlights'),
                        'education'
                    )
                    ->has(
                        Education::factory(rand(2, 3))->has(EducationHighlight::factory(rand(1, 3)), 'highlights'),
                        'education'
                    ),
                'subjects'
            )
            ->create();
    }
}
