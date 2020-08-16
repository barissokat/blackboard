<?php

namespace Tests\Feature;

use App\User;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class InvitationsTest extends TestCase
{
    use RefreshDatabase;

    public function testNonOwnersMayNotInviteAnUser()
    {
        $this->actingAs(factory(User::class)->create())
            ->post(ProjectFactory::create()->path() . '/invitations')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAProjectOwnerCanInviteAnUser()
    {
        $project = ProjectFactory::create();

        $userToInvite = factory(User::class)->create();

        $this->actingAs($project->owner)
            ->post($project->path() . '/invitations', [
                'email' => $userToInvite->email,
            ])
            ->assertRedirect($project->path());

        $this->assertTrue($project->members->contains($userToInvite));
    }

    public function testTheEmailAddressMustBeAssociatedWithAValidBirdboardAccount()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
            ->post($project->path() . '/invitations', [
                'email' => 'notauser@example.com',
            ])
            ->assertSessionHasErrors([
                'email' => 'The user you are inviting must have a Blackboard account.',
            ]);
    }

    public function testInvitedUsersMayUpdateProjectDetails()
    {
        $project = ProjectFactory::create();

        $project->invite($newUser = factory(User::class)->create());

        $this
            ->actingAs($newUser)
            ->post(action('ProjectTaskController@store', $project), $task = ['body' => 'Foo task']);

        $this->assertDatabaseHas('tasks', $task);
    }
}
