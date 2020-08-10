<?php

namespace Tests\Feature;

use App\Project;
use App\Task;
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

        $project = factory('App\Project')->create();

        $attributes = ['body' => 'Test task'];

        $this->post($project->path() . '/tasks', $attributes)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('tasks', $attributes);
    }

    public function testOnlyAOwnerOfAProjectMayUpdateATask()
    {
        $this->signIn();

        $project = factory(Project::class)->create();

        $task = $project->addTask('Task');

        $attributes = ['body' => 'changed'];

        $this->patch($task->path(), $attributes)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('tasks', $attributes);
    }

    public function testAProjectCanHaveTasks()
    {
        $this->signIn();

        $project = auth()->user()->projects()->create(
            factory(Project::class)->raw()
        );

        $this->post($project->path() . '/tasks', ['body' => 'Test task']);

        $this->get($project->path())
            ->assertSee('Test task');
    }

    public function testATaskCanBeUpdated()
    {
        $this->signIn();

        $project = auth()->user()->projects()->create(
            factory(Project::class)->raw()
        );

        $task = $project->addTask('Test task');

        $attributes = [
            'body' => 'changed',
            'completed' => true,
        ];

        $this->patch($task->path(), $attributes);

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
        $this->signIn();

        $project = auth()->user()->projects()->create(
            factory(Project::class)->raw()
        );

        $attributes = factory(Task::class)->raw(['body' => '']);

        $this->post($project->path() . '/tasks', $attributes)
            ->assertSessionHasErrors('body');
    }
}
