<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Attributes to guard against mass assignment.
 *
 * @var array
 */
class Activity extends Model
{
    protected $guarded = [];

    public function subject()
    {
        return $this->morphTo();
    }
}
