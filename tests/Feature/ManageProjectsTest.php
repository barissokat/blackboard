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

    public function testGuestCannotManageProjects()
    {
        $project = factory(Project::class)->create();

        $this->get('projects')->assertRedirect('login');
        $this->get('projects/create')->assertRedirect('login');
        $this->post('projects', $project->toArray())->assertRedirect('login');
        $this->get($project->path())->assertRedirect('login');
    }

    public function testGuestsCannotCreateProject()
    {
        $this->storeProject()
            ->assertRedirect('login');
    }

    public function testGuestsCannotViewProjects()
    {
        $this->get(route('projects.index'))
            ->assertRedirect('login');
    }

    public function testGuestsCannotViewASingleProject()
    {
        $project = factory(Project::class)->create();

        $this->get($project->path())
            ->assertRedirect('login');
    }

    public function testAUserCanCreateAProject()
    {
        $this->signIn();

        $this->get(route('projects.create'))->assertOk();

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $response = $this->post('projects', $attributes)
            ->assertRedirect(Project::where($attributes)->first()->path());

        $this->assertDatabaseHas('projects', $attributes);

        $this->get(route('projects.index'))
            ->assertSee($attributes['title']);
    }

    public function testAUserCanViewTheirProject()
    {
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee(\Illuminate\Support\Str::limit($project->description, 100));
    }

    public function testAnAuthenticatedUserCannotViewTheProjectsOfOthers()
    {
        $this->signIn();

        $project = factory(Project::class)->create();

        $this->get($project->path())
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAProjectRequiresATitle()
    {
        $this->signIn();

        $this->storeProject(['title' => null])
            ->assertSessionHasErrors('title');
    }

    public function testAProjectRequiresADescription()
    {
        $this->signIn();

        $this->storeProject(['description' => null])
            ->assertSessionHasErrors('description');
    }

    public function storeProject($attributes = [])
    {
        $project = factory(Project::class)->raw($attributes);

        return $this->post(route('projects.store'), $project);
    }
}
