<?php

namespace Classroom\Models;

use Core\Models\Accessors\CommonAttributes;
use Core\Models\Relations\BelongsToUser;
use Course\Models\Course;
use Course\Models\Student;
use Illuminate\Database\Eloquent\Concerns\belongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use CommonAttributes,
        BelongsToUser,
        SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string
     */
    protected $guarded = [];

    /**
     * Get the lessons for the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

    /**
     * Get the lessons for the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

    /**
     * Retrieve the metadata info for the classroom resource.
     *
     * @return array
     */
    public function getMetaAttribute()
    {
        return [
            'courses:selected' => $this->courses->pluck('id')->toArray(),
            'courses:total' => $this->courses->count(),
            'students:selected' => $this->students->pluck('id')->toArray(),
            'students:total' => $this->students->count(),
        ];
    }
}
