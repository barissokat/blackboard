<?php

namespace Tests\Feature;

use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;

    public function testCreatingAProjectRecordsActivity()
    {
        $project = ProjectFactory::create();

        $this->assertCount(1, $project->activity);
        $this->assertEquals('created', $project->activity->first()->description);
    }

    public function testUpdatingAProjectRecordsActivity()
    {
        $project = ProjectFactory::create();

        $project->update(['title' => 'changed']);

        $this->assertCount(2, $project->activity);
        $this->assertEquals('updated', $project->activity->last()->description);
    }

        public function testCreatingANewTaskRecordsProjectActivity()
        {
            $project = ProjectFactory::withTasks(1)->create();

            $this->assertCount(2, $project->activity);
            $this->assertEquals('created_task', $project->activity[1]->description);
        }

        public function testCompletingANewTaskRecordsProjectActivity()
        {
            $project = ProjectFactory::withTasks(1)->create();

            $this->actingAs($project->owner)
                ->patch($project->tasks->first()->path(), [
                'body' => 'text',
                'completed' => true,
            ]);

            $this->assertCount(3, $project->activity);
            $this->assertEquals('completed_task', $project->activity->last()->description);
        }
}
