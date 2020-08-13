<?php

namespace Tests\Feature;

use App\Task;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TriggerActivityTest extends TestCase
{
    use RefreshDatabase;

    public function testCreatingANewProject()
    {
        $project = ProjectFactory::create();

        $this->assertCount(1, $project->activity);

        tap($project->activity->last(), function ($activity) {
            $this->assertEquals('created', $activity->description);

            $this->assertNull($activity->changes);
        });
    }

    public function testUpdatingAProject()
    {
        $project = ProjectFactory::create();

        $originalTitle = $project->title;

        $project->update(['title' => 'Changed']);

        $this->assertCount(2, $project->activity);

        tap($project->activity->last(), function ($activity) use ($originalTitle) {
            $this->assertEquals('updated', $activity->description);

            $expected = [
                'before' => ['title' => $originalTitle],
                'after' => ['title' => 'Changed'],
            ];

            $this->assertEquals($expected, $activity->changes);
        });
    }

    public function testCreatingANewTask()
    {
        $project = ProjectFactory::create();

        $project->addTask('Some task');

        $this->assertCount(2, $project->activity);

        tap($project->activity->last(), function ($activity) {
            $this->assertEquals('created_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
            $this->assertEquals('Some task', $activity->subject->body);
        });
    }

    public function testCompletingATask()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks->first()->path(), [
                'body' => 'text',
                'completed' => true,
            ]);

        $this->assertCount(3, $project->activity);

        tap($project->activity->last(), function ($activity) {
            $this->assertEquals('completed_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
        });
    }

    public function testIncompletingATask()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks->first()->path(), [
                'body' => 'text',
                'completed' => true,
            ]);

        $this->assertCount(3, $project->activity);

        $this->actingAs($project->owner)
            ->patch($project->tasks->first()->path(), [
                'body' => 'text',
                'completed' => false,
            ]);

        $this->assertCount(4, $project->refresh()->activity);

        $this->assertEquals('incompleted_task', $project->activity->last()->description);
    }

    public function testDeletingATask()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $project->tasks->first()->delete();

        $this->assertCount(3, $project->refresh()->activity);

        $this->assertEquals('deleted_task', $project->activity->last()->description);

    }
}
