<?php

namespace Tests\Feature;

use App\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ManageProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @return void
     */
    public function testAUserCanViewTheirProject()
    {
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }

    /**
     * @return void
     */
    public function testGuestsCannotViewProjects()
    {
        $this->get(route('projects.index'))->assertRedirect('login');
    }

    /**
     * @return void
     */
    public function testAnAuthenticatedUserCannotViewTheProjectsOfOthers()
    {
        $this->signIn();

        $project = factory(Project::class)->create();

        $this->get($project->path())
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testGuestsCannotViewASingleProject()
    {
        $project = factory(Project::class)->create();

        $this->get($project->path())
            ->assertRedirect('login');
    }

    /**
     * @return void
     */
    public function testAUserCanCreateAProject()
    {
        $this->signIn();

        $this->get(route('projects.create'))->assertStatus(Response::HTTP_OK);

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $this->storeProject($attributes)
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', $attributes);

        $this->get(route('projects.index'))
            ->assertSee($attributes['title']);
    }

    /**
     * @return void
     */
    public function testGuestsCannotCreateProject()
    {
        $this->storeProject()->assertRedirect('login');
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
