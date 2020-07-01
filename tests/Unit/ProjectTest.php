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

    /**
     * @return void
     */
    public function testAProjectHasAPath()
    {
        $this->assertEquals('/projects/' . $this->project->id, $this->project->path());
    }

    /**
     * @return void
     */
    public function testAProjectHasAnOwner()
    {
        $this->assertInstanceOf(User::class, $this->project->owner);
    }
}
