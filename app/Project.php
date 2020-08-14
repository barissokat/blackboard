<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Project extends Model
{
    use RecordsActivity;

    /**
     * Attributes to guard against mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The owner of the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The tasks associated with the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Add a task to the project.
     *
     * @param  string $body
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addTask($body)
    {
        return $this->tasks()->create(compact('body'));
    }

    /**
     * Get all members that are assigned to the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members');
    }

    /**
     * Invite a user to the project.
     *
     * @param \App\User $user
     */
    public function invite(User $user)
    {
        $this->members()->attach($user);
    }

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
