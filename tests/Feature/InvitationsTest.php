<?php

namespace Tests\Feature;

use App\User;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationsTest extends TestCase
{
    use RefreshDatabase;

    public function testAProjectCanInviteAUser()
    {
        $project = ProjectFactory::create();

        $project->invite($newUser = factory(User::class)->create());

        $this
            ->actingAs($newUser)
            ->post(action('ProjectTaskController@store', $project), $task = ['body' => 'Foo task']);

        $this->assertDatabaseHas('tasks', $task);
    }
}
