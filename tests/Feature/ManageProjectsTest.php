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
        $this->withoutExceptionHandling();

        $this->signIn();

        $this->get(route('projects.create'))->assertOk();

        $attributes = [
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'notes' => $this->faker->sentence,
        ];

        $response = $this->post('projects', $attributes);

        $project = Project::where($attributes)->first();

        $response->assertRedirect($project->path());

        $this->assertDatabaseHas('projects', $attributes);

        $this->get($project->path())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

    public function testAUserCanUpdateAProject()
    {
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);

        $attributes = [
            'notes' => 'Changed',
        ];

        $this->patch($project->path(), $attributes)
            ->assertRedirect($project->path());

        $this->assertDatabaseHas('projects', $attributes);
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

    public function testAnAuthenticatedUserCannotUpdateProjectsOfOthers()
    {
        $this->signIn();

        $project = factory(Project::class)->create();

        $this->patch($project->path(), [])
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
