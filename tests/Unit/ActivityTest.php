<?php

namespace Tests\Unit;

use App\Activity;
use App\User;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    public function testAnActivityHasAnUser()
    {
        $user = $this->signIn();

        $project = ProjectFactory::ownedBy($user)->create();

        $this->assertEquals($user->id, $project->activity->first()->user->id);
    }
}
