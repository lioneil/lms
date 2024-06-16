<?php

namespace Course\Models;

use Codrasil\Closurable\WithClosures;
use Comment\Models\Relations\MorphManyComments;
use Core\Enumerations\ModelStatus;
use Core\Models\Accessors\CommonAttributes;
use Core\Models\Publishable;
use Core\Models\Relations\BelongsToUser;
use Core\Models\Sluggable;
use Course\Enumerations\CourseDictionary;
use Course\Enumerations\LessonMetadataKeys;
use Course\Events\UserSubscribedToCourse;
use Course\Events\UserUnsubscribedToCourse;
use Course\Models\Courseware;
use Course\Models\Lesson;
use Course\Models\Student;
use Favorite\Models\Favoritable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Material\Models\Material;
use Subscription\Models\Progressible;
use Subscription\Models\Progression;
use Subscription\Models\Subscribable;
use Taxonomy\Models\Relations\BelongsToCategory;
use Taxonomy\Models\Relations\MorphToManyTags;
use User\Models\User;

class Course extends Model
{
    use BelongsToCategory,
        BelongsToUser,
        CommonAttributes,
        Favoritable,
        MorphManyComments,
        MorphToManyTags,
        Progressible,
        Publishable,
        Searchable,
        Sluggable,
        SoftDeletes,
        Subscribable,
        WithClosures;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'collection',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'published_at',
        'drafted_at',
        'expired_at',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'subscribed' => UserSubscribedToCourse::class,
        'unsubscribed' => UserUnsubscribedToCourse::class,
    ];

    /**
     * Get the lessons for the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('sort');
    }

    /**
     * Get the coursewares for the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function coursewares()
    {
        return $this->morphMany(Courseware::class, 'coursewareable');
    }

    /**
     * Get the materials for the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function materials()
    {
        return $this->morphMany(Material::class, 'coursewareable');
    }

    /**
     * Retrieve the course description in HTML format.
     *
     * @return string
     */
    public function getOverviewAttribute()
    {
        return new HtmlString($this->description);
    }

    /**
     * Retrieve the status of the course.
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        $status = ModelStatus::UNPUBLISHED;

        if ($this->isPublished()) {
            $status = ModelStatus::PUBLISHED;
        }

        if ($this->isDrafted()) {
            $status = ModelStatus::DRAFTED;
        }

        if ($this->trashed()) {
            $status = ModelStatus::TRASHED;
        }

        return Str::title($status);
    }

    /**
     * Retrieve the full lessons playlist.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPlaylistAttribute()
    {
        return $this->getPlaylistOfStudent(Auth::user());
    }

    /**
     * Retrieve the playlist for the passed in user id.
     *
     * @param  mixed $student
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPlaylistOfStudent($student = null)
    {
        if (is_numeric($student)) {
            $student = Student::find($student);
        }

        if (is_null($student)) {
            $student = Auth::user();
        }

        $playlist = $this->root('lessons')->get();
        $progress = $this->progressionsOf($student)->first();

        return $playlist->map(function ($lesson) use ($progress) {
            return $this->setLessonProgress($lesson, $progress);
        });
    }

    /**
     * Retrieve the playable lessons.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPlayablesAttribute()
    {
        return $this->lessons->filter(function ($lesson) {
            return $lesson->isPlayable();
        });
    }

    /**
     * Retrieve the first lesson of the course.
     *
     * @return \Course\Models\Lesson
     */
    public function getFirstLesson()
    {
        return $this->playables->first();
    }

    /**
     * Retrieve the first lesson of the course.
     *
     * @param  \Course\Models\Lesson $lesson
     * @param  \User\Models\User     $user
     * @return \Course\Models\Lesson
     */
    public function getNextLesson(Lesson $lesson, User $user)
    {
        return $this->getLessonsWithProgressOf($user)->whereNotIn(
            'id', $lesson->getKey()
        )->whereIn(
            'type', LessonMetadataKeys::playables()
        )->where(
            'sort', '>=', $lesson->sort
        )->first();
    }

    /**
     * Retrieve the last lesson of the course.
     *
     * @return \Course\Models\Lesson
     */
    public function getLastLesson()
    {
        return $this->playables->last();
    }

    /**
     * Retrieve meta information about the course.
     *
     * @return array
     */
    public function getMetaAttribute()
    {
        $metadata = is_null($this->metadata) ? [] : $this->metadata->toArray();

        return array_merge($metadata, [
            'lessons' => [
                'count' => $this->lessons->reject(function ($lesson) {
                    return $lesson->isSection();
                })->count(),
                'total' => $total = $this->lessons->count(),
                'is_empty' => $total <= 0,
                'is_not_empty' => $total > 0,
                'first' => $this->getFirstLesson(),
                'last' => $this->getlastLesson(),
            ]
        ]);
    }

    /**
     * Check if course is lockable.
     *
     * @return boolean
     */
    public function isLockable()
    {
        return $this->metadata && $this->metadata->get('lockable') ?: false;
    }

    /**
     * Set the lockable metadata to 'true'.
     *
     * @return boolean
     */
    public function makeLockable()
    {
        $this->metadata = array_merge(
            $this->metadata ? $this->metadata->toArray() : [],
            [CourseDictionary::LOCKABLE => true]
        );

        return $this->save();
    }

    /**
     * Set the lockable metadata to 'false'.
     *
     * @return void
     */
    public function makeNonLockable()
    {
        $this->metadata = array_merge($this->metadata ?? [], [CourseDictionary::LOCKABLE => false]);
        $this->save();
    }

    /**
     * Map the User's progress to the course's lessons.
     *
     * @param  \User\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLessonsWithProgressOf(User $user)
    {
        $progression = $this->progressionsOf($user)->first();

        return $this->lessons->each(function ($lesson) use ($progression) {
            $this->setLessonProgress($lesson, $progression);
        });
    }

    /**
     * Set the lesson progress attribute.
     *
     * @param  \Course\Models\Lesson            $lesson
     * @param  \Subscription\Models\Progression $progression
     * @return \Course\Models\Lesson
     */
    public function setLessonProgress(Lesson $lesson, Progression $progression = null)
    {
        $lesson->setAttribute(
            'progress',
            is_null($progression)
                ? $this->getDefaultLessonProgress($lesson)
                : collect($progression->metadata['lessons'])->firstWhere('id', $lesson->getKey())
        );

        return $lesson;
    }

    /**
     * Retrieve the default values of lesson progress.
     *
     * @param  \Course\Models\Lesson $lesson
     * @return array
     */
    protected function getDefaultLessonProgress(Lesson $lesson)
    {
        return [
            CourseDictionary::TYPE => $lesson->type,
            CourseDictionary::LOCKED => $this->isLockable(),
            CourseDictionary::STATUS => CourseDictionary::NOT_APPLICABLE,
        ];
    }

    /**
     * Mark the course as complete and assume all lessons
     * have been completed as well.
     *
     * @param  \User\Models\User $user
     * @return void
     */
    public function markAsComplete(User $user)
    {
        $progression = $this->progressionsOf($user)->first();
        $progression->status = CourseDictionary::COMPLETED;
        $progression->save();
    }
}
