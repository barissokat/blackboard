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

    public function testAProjectHasAPath()
    {
        $this->assertEquals('/projects/' . $this->project->id, $this->project->path());
    }

    public function testAProjectBelongsToAnOwner()
    {
        $this->assertInstanceOf(User::class, $this->project->owner);
    }

    public function testAProjectCanAddATask()
    {
        $project = factory(Project::class)->create();

        $task = $project->addTask('Test task');

        $this->assertCount(1, $project->tasks);

        $this->assertTrue($project->tasks->contains($task));
    }

    function testAProjectCanInviteAUser()
    {
        $project = factory('App\Project')->create();

        $project->invite($user = factory(User::class)->create());

        $this->assertTrue($project->members->contains($user));
    }
}
