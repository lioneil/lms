<?php

namespace Course\Models;

use Codrasil\Closurable\Closurable;
use Comment\Models\Relations\MorphManyComments;
use Core\Models\Accessors\CommonAttributes;
use Core\Models\Relations\BelongsToUser;
use Core\Models\Sluggable;
use Course\Enumerations\CourseDictionary;
use Course\Enumerations\LessonMetadataKeys;
use Course\Enumerations\MimetypeIconKeys;
use Course\Events\LessonDeleted;
use Course\Events\LessonSaved;
use Course\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use SimpleXMLElement;
use User\Models\User;

class Lesson extends Model
{
    use BelongsToUser,
        CommonAttributes,
        Closurable,
        MorphManyComments,
        SoftDeletes,
        Sluggable;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sort' => 'integer',
        'metadata' => 'collection',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => LessonSaved::class,
        'deleting' => LessonDeleted::class,
    ];

    /**
     * The sort key string.
     *
     * @var string
     */
    protected $sortKey = 'sort';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Array of our custom model events declared under
     * model property $observables.
     *
     * @var array
     */
    protected $observables = [
        'reordered',
    ];

    /**
     * Retrieve the course that this lesson belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Retrieve the immediate children of the resource.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function children()
    {
        return $this->getChildrenWithProgressOf(Auth::user());
    }

    /**
     * Retrieve the next lesson.
     *
     * @return \Course\Models\Lesson|null
     */
    public function next()
    {
        return $this->course->lessons()
            ->whereNotIn('id', [$this->getKey()])
            ->whereIn('type', LessonMetadataKeys::playables())
            ->where('sort', '>=', $this->sort)
            ->first();
    }

    /**
     * Retrieve the lesson with progress.
     *
     * @param  \Course\Models\Student|integer $student
     * @return \Course\Models\Lesson
     */
    public function withProgressOf($student)
    {
        if (is_numeric($student)) {
            $student = Student::find($student);
        }

        if (is_null($student)) {
            $student = Auth::user();
        }

        $progress = $this->course->progressionsOf($student)->first();

        return $this->course->setLessonProgress($this, $progress);
    }

    /**
     * Retrieve the children with the passed in student's progress.
     *
     * @param  mixed $student
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getChildrenWithProgressOf($student = null)
    {
        if (is_numeric($student)) {
            $student = Student::find($student);
        }

        if (is_null($student)) {
            $student = Auth::user();
        }

        $progress = $this->course->progressionsOf($student)->first();

        return $this->closurables()->children()->map(function ($child) use ($progress) {
            return $child->course->setLessonProgress($child, $progress);
        });
    }

    /**
     * Save and trigger the reorder model event.
     *
     * @return $this
     */
    public function reorder()
    {
        $this->save();

        $this->fireModelEvent('reordered', $halt = false);

        return $this;
    }

    /**
     * Check if lesson is first.
     *
     * @return boolean
     */
    public function isFirst()
    {
        $first = $this->course->playables->sortBy('sort')->first();

        if (is_null($first)) {
            return false;
        }

        return $first->getKey() == $this->getKey();
    }

    /**
     * Check if last lesson of the course.
     *
     * @return boolean
     */
    public function isLast()
    {
        $last = $this->course->playables->sortBy('sort')->last();

        if (is_null($last)) {
            return false;
        }

        return $last->getKey() == $this->getKey();
    }

    /**
     * Check if type is section.
     *
     * @return boolean
     */
    public function isSection()
    {
        return $this->type == LessonMetadataKeys::SECTION_KEY;
    }

    /**
     * Determine if type is not a section.
     *
     * @return boolean
     */
    public function nonSection()
    {
        return ! $this->isSection();
    }

    /**
     * Check if type is section.
     *
     * @return boolean
     */
    public function isScorm()
    {
        return $this->type == LessonMetadataKeys::SCORM_KEY;
    }

    /**
     * Check if type is section.
     *
     * @return boolean
     */
    public function isPlayable()
    {
        return in_array($this->type, LessonMetadataKeys::playables());
    }

    /**
     * Determine if the lesson is locked for the user.
     *
     * @param  mixed $student
     * @return boolean
     */
    public function isLocked($student = null)
    {
        if (is_numeric($student)) {
            $student = Student::find($student);
        }

        if (is_null($student)) {
            $student = Auth::user();
        }

        if (is_null($student)) {
            return true;
        }

        return $this->course->getLessonsWithProgressOf($student)->firstWhere(
            'id', $this->getKey()
        )->progress[CourseDictionary::LOCKED] ?? null;
    }

    /**
     * Determine if the lesson is unlocked for the user.
     *
     * @param  mixed $student
     * @return boolean
     */
    public function isUnlocked($student = null)
    {
        return ! $this->isLocked($student);
    }

    /**
     * Retrieve the icon for the lesson
     * based on the associated mimetype.
     *
     * @return string
     */
    public function getIconAttribute()
    {
        return MimetypeIconKeys::guess($this->type);
    }

    /**
     * Handle the updated content's file from storage.
     *
     * @param  mixed $file
     * @return void
     */
    public function deleteStoredContentIfFile($file)
    {
        if (is_file($file ?? false)) {
            $path = ! is_null($this->metadata)
                    ? $this->metadata->get(LessonMetadataKeys::PATHNAME)
                    : false;

            if (file_exists($path)) {
                File::delete($path);
            }
        }
    }

    /**
     * Retrieve the interactive content if
     * type is interactive.
     *
     * @return array|boolean
     */
    public function getInteractiveAttribute()
    {
        if (is_null($this->metadata)) {
            return false;
        }

        if (is_dir(storage_path($this->metadata->get(LessonMetadataKeys::ARCHIVEPATH)))) {
            $path = storage_path($this->metadata->get(LessonMetadataKeys::ARCHIVEPATH)).DIRECTORY_SEPARATOR;
            $files = File::allFiles(storage_path(
                $this->metadata->get(LessonMetadataKeys::ARCHIVEPATH)
            ));

            return $files;
        }

        return false;
    }

    /**
     * Retrieve the SCORM-compliant entry point.
     *
     * @return string|boolean
     */
    public function getScormAttribute()
    {
        if (is_null($this->metadata)) {
            return false;
        }

        $path = $this->metadata->get(LessonMetadataKeys::ARCHIVEPATH);
        $entrypoint = isset($this->imsmanifest->resources->resource['href'])
            ? urlencode($this->imsmanifest->resources->resource['href'])
            : 'multiscreen.html';

        if (! file_exists(storage_path($path.DIRECTORY_SEPARATOR.$entrypoint))) {
            $entrypoint = 'index.html';
        }

        if (! file_exists(storage_path($path.DIRECTORY_SEPARATOR.$entrypoint))) {
            return false;
        }

        return url("storage/$path/$entrypoint");
    }

    /**
     * Try to get the imsmanifest.xml file as a
     * SimpleXMLElement.
     *
     * @return mixed
     */
    public function getImsmanifestAttribute()
    {
        if (is_null($this->metadata)) {
            return;
        }

        $path = $this->metadata->get(LessonMetadataKeys::ARCHIVEPATH)
            .DIRECTORY_SEPARATOR
            .'imsmanifest.xml';

        if (file_exists(storage_path($path))) {
            $xml = File::get(storage_path($path));

            return new SimpleXMLElement($xml);
        }

        return false;
    }

    /**
     * Set the progress of the lesson status to completed.
     *
     * @param  \User\Models\User $user
     * @return void
     */
    public function markAsComplete(User $user)
    {
        $progress = $this->course->progressionsOf($user)->first();
        $lesson = collect($progress->metadata['lessons'])->where('id', $this->getKey())->first();

        $lesson[CourseDictionary::LOCKED] = false;
        $lesson[CourseDictionary::STATUS] = CourseDictionary::COMPLETED;

        $lessons = collect($progress->metadata['lessons']);
        $lessons = ['lessons' => $lessons->map(function ($item) use ($lesson) {
            if ($lesson['id'] == $item['id']) {
                return $lesson;
            }

            return $item;
        })->toArray()];

        $recent = ['recent' => $this->getKey()];

        $progress->metadata = array_merge(
            $progress->metadata->toArray(),
            $lessons,
            $recent
        );

        $progress->save();

        $progress->fireModelEvent('progressed', $halt = false);
    }

    /**
     * Determine if the lesson is in pending state for the user.
     *
     * @return boolean
     */
    public function isPending()
    {
        return ($this->progress[CourseDictionary::STATUS] ?? false) == CourseDictionary::PENDING;
    }

    /**
     * Determine if the lesson is completed by the user.
     *
     * @return boolean
     */
    public function isCompleted()
    {
        return ($this->progress[CourseDictionary::STATUS] ?? false) == CourseDictionary::COMPLETED;
    }

    /**
     * Set the progress of the lesson status to in progress.
     *
     * @param  \User\Models\User $user
     * @return void
     */
    public function markAsInProgress(User $user)
    {
        $progress = $this->course->progressionsOf($user)->first();
        $lesson = collect($progress->metadata['lessons'])->where('id', $this->getKey())->first();

        $lesson[CourseDictionary::LOCKED] = false;
        $lesson[CourseDictionary::STATUS] = CourseDictionary::IN_PROGRESS;

        $lessons = collect($progress->metadata['lessons']);
        $lessons = ['lessons' => $lessons->map(function ($item) use ($lesson) {
            if ($lesson['id'] == $item['id']) {
                return $lesson;
            }

            return $item;
        })->toArray()];

        $progress->metadata = array_merge($progress->metadata->toArray(), $lessons);

        $progress->save();
    }
}
