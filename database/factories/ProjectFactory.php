<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Project;
use App\User;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    return [
        'title' => $this->faker->sentence,
        'description' => $this->faker->paragraph,
        'user_id' => fn() => factory(User::class)->create()->id,
    ];
});
