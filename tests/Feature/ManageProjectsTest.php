<?php

namespace Tests\Feature;

use App\Project;
use Facades\Tests\Setup\ProjectFactory;
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

        $this->get(route('projects.index'))->assertRedirect('login');
        $this->get(route('projects.create'))->assertRedirect('login');
        $this->get($project->path() . '/edit')->assertRedirect('login');
        $this->post(route('projects.store'), $project->toArray())->assertRedirect('login');
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

        $response = $this->post(route('projects.store'), $attributes);

        $project = Project::where($attributes)->first();

        $response->assertRedirect($project->path());

        $this->get($project->path())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

    public function testAUserCanUpdateAProject()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
            ->patch($project->path(), $attributes = [
                'title' => 'changed',
                'description' => 'changed',
                'notes' => 'changed',
            ])
            ->assertRedirect($project->path());

        $this->get($project->path() . '/edit')->assertOk();

        $this->assertDatabaseHas('projects', $attributes);
    }

    public function testAUserCanUpdateProjectGeneralNote()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
            ->patch($project->path(), $attributes = [
                'notes' => 'changed',
            ]);

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
