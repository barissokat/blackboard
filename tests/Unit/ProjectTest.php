<?php

namespace Tests\Unit;

use App\Project;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected $thread;

    public function setUp(): void
    {
        parent::setUp();

        $this->project = factory(Project::class)->create();
    }

    public function testProjectHasPath()
    {
        $this->assertEquals('/projects/' . $this->project->id, $this->project->path());
    }

    public function testProjectBelongsToAnOwner()
    {
        $this->assertInstanceOf(User::class, $this->project->owner);
    }

    public function testProjectCanAddATask()
    {
        $project = factory(Project::class)->create();

        $task = $project->addTask('Test task');

        $this->assertCount(1, $project->tasks);

        $this->assertTrue($project->tasks->contains($task));
    }
}
