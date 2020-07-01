<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get a string path for the project.
     *
     * @return string
     */
    public function path()
    {
        return "/projects/{$this->id}";
    }
}
