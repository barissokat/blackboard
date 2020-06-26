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
    public function testAUserCanCreateAProject()
    {
        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $this->storeProject($attributes)->assertRedirect('/projects');

        $this->assertDatabaseHas('projects', $attributes);

        $this->get(route('projects.index'))
            ->assertSee($attributes['title']);
    }

    /**
     * @return void
     */
    public function testAProjectRequiresATitle()
    {
        $this->storeProject(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /**
     * @return void
     */
    public function testAProjectRequiresADescription()
    {
        $this->storeProject(['description' => null])
            ->assertSessionHasErrors('description');
    }

    /**
     *
     * @return void
     */
    public function storeProject($overrides = [])
    {
        $project = factory(Project::class)->make($overrides);

        return $this->post(route('projects.store'), $project->toArray());
    }
}
