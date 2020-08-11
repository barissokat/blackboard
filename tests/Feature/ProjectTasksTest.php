<?php

namespace Tests\Feature;

use App\Project;
use App\Task;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestsCannotAddTasksToProjects()
    {
        $project = factory(Project::class)->create();

        $this->post($project->path() . '/tasks')->assertRedirect('login');
    }

    public function testOnlyTheOwnerOfAProjectMayAddTasks()
    {
        $this->signIn();

        $project = factory(Project::class)->create();

        $attributes = ['body' => 'Test task'];

        $this->post($project->path() . '/tasks', $attributes)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('tasks', $attributes);
    }

    public function testOnlyAOwnerOfAProjectMayUpdateATask()
    {
        $this->signIn();

        $project = ProjectFactory::withTasks(1)->create();

        $attributes = ['body' => 'changed'];

        $this->patch($project->tasks->first()->path(), $attributes)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('tasks', $attributes);
    }

    public function testAProjectCanHaveTasks()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', ['body' => 'Test task']);

        $this->get($project->path())
            ->assertSee('Test task');
    }

    public function testATaskCanBeUpdated()
    {
         $project = ProjectFactory::withTasks(1)->create();

        $attributes = [
            'body' => 'changed',
            'completed' => true,
        ];

        $this->actingAs($project->owner)
            ->patch($project->tasks->first()->path(), $attributes);

        $this->assertDatabaseHas('tasks', $attributes);
    }

    public function testATaskUpdateAlsoUpdatesItsProjectTimestamp()
    {
        $project = factory(Project::class)->create(['updated_at' => now()->subDays(30)]);

        $task = $project->addTask('Test task');

        $this->assertEquals($project->fresh()->updated_at, $task->updated_at);
    }

    public function testATaskRequiresABody()
    {
        $project = ProjectFactory::create();

        $attributes = factory(Task::class)->raw(['body' => '']);

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', $attributes)
            ->assertSessionHasErrors('body');
    }
}
