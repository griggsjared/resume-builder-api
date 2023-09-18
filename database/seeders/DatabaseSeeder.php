<?php

namespace Database\Seeders;

use App\Domains\Resumes\Models\Education;
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
        $email = 'text@example.com';
        $password = 'password';

        $user = User::factory()->admin()->create([
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $token = $user->createToken('test-token');

        Subject::factory(1)
            ->for($user, 'user')
            ->has(SubjectHighlight::factory(3), 'highlights')
            ->has(Skill::factory(5)->uncategorized(), 'skills')
            ->has(Skill::factory(20), 'skills')
            ->has(
                Employer::factory(1)->current()->has(EmployerHighlight::factory(3), 'highlights')
            )
            ->has(
                Employer::factory(1)->has(EmployerHighlight::factory(3), 'highlights')
            )
            ->has(Education::factory(1), 'education')
            ->create();

        $this->command->info('Test user created:');
        $this->command->info("Email: {$email}");
        $this->command->info("Password: {$password}");
        $this->command->info("Access Token: {$token->plainTextToken}");
    }
}
