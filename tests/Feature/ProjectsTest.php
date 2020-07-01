<?php

namespace Tests\Feature;

use App\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @return void
     */
    public function testOnlyAuthenticatedUsersCanCreateProject()
    {
        $this->storeProject()->assertRedirect('login');
    }

    /**
     * @return void
     */
    public function testAUserCanCreateAProject()
    {
        $this->signIn();

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $this->storeProject($attributes)
            ->assertRedirect('/projects');

        $this->assertDatabaseHas('projects', $attributes);

        $this->get(route('projects.index'))
            ->assertSee($attributes['title']);
    }

    /**
     * @return void
     */
    public function testAUserCanViewAProject()
    {
        $this->signIn();

        $project = factory(Project::class)->create();

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }

    /**
     * @return void
     */
    public function testAProjectRequiresATitle()
    {
        $this->signIn();

        $this->storeProject(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /**
     * @return void
     */
    public function testAProjectRequiresADescription()
    {
        $this->signIn();

        $this->storeProject(['description' => null])
            ->assertSessionHasErrors('description');
    }

    /**
     *
     * @return mixed
     */
    public function storeProject($attributes = [])
    {
        $project = factory(Project::class)->make($attributes);

        return $this->post(route('projects.store'), $project->toArray());
    }
}
