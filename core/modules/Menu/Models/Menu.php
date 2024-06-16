<?php

namespace Menu\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The menu location instance
     *
     * @var string
     */
    protected $location = 'main-menu';

    /**
     * Retrieve the menu URL.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url($this->parent."/".$this->uri);
    }

    /**
     * Retrieve the menu Location.
     *
     * @return string
     */
    public function getDefaultLocation()
    {
        return $this->location;
    }

    /**
     * Scope a query to only include location.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array                                 $location
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocation($query, $location)
    {
        return $query->where('location', $location);
    }
}
