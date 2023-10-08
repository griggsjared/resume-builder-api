<?php

namespace Tests\Feature\Domains\Users\Models;

use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Models\AccessToken;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function its_table_has_the_expected_columns()
    {
        $this->assertTrue(Schema::hasColumn('users', 'id'));
        $this->assertTrue(Schema::hasColumn('users', 'email'));
        $this->assertTrue(Schema::hasColumn('users', 'role'));
        $this->assertTrue(Schema::hasColumn('users', 'password'));
        $this->assertTrue(Schema::hasColumn('users', 'created_at'));
        $this->assertTrue(Schema::hasColumn('users', 'updated_at'));
    }

    /** @test */
    public function it_has_model_relationships()
    {
        $user = User::factory()
            ->has(Subject::factory(5), 'subjects')
            ->has(AccessToken::factory(5), 'accessTokens')
            ->create();

        $this->assertCount(5, $user->subjects);
        $this->assertCount(5, $user->accessTokens);
    }

    /** @test */
    public function it_can_scope_to_authorized_users()
    {
        $admin = User::factory()->admin()->create();
        $basic = User::factory()->basic()
            ->create();

        User::factory()->count(3)->create();

        $this->assertCount(5, User::authorized($admin)->get());
        $this->assertCount(1, User::authorized($basic)->get());
    }

    /** @test */
    public function it_can_scope_to_a_search_term()
    {
        User::factory()->create([
            'email' => 'searchme@example.com'
        ]);

        User::factory()->create([
            'email' => 'orthat@example.com'
        ]);

        User::factory()->create([
            'email' => 'notthis@foo.com'
        ]);

        $this->assertCount(1, User::search('search')->get());
        $this->assertCount(1, User::search('me')->get());
        $this->assertCount(2, User::search('example')->get());
    }
}
