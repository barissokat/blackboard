<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * Attributes to guard against mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = ['project'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'completed' => 'boolean',
    ];

    /**
     * Get the owning project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The activity feed for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    /**
     * Get the path to the task.
     *
     * @return string
     */
    public function path()
    {
        return "{$this->project->path()}/tasks/{$this->id}";
    }

    /**
     * Mark the task as complete.
     */
    public function complete()
    {
        $this->update(['completed' => true]);
        $this->recordActivity('completed_task');
    }

    /**
     * Mark the task as incomplete.
     */
    public function incomplete()
    {
        $this->update(['completed' => false]);
        $this->recordActivity('incompleted_task');
    }

    /**
     * Record activity for a task.
     *
     * @param string $description
     */
    public function recordActivity($description)
    {
        $this->activity()->create([
            'project_id' => $this->project_id,
            'description' => $description
        ]);
    }
}
