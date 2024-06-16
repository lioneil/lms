<?php

namespace Core\Models\Accessors;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Laravolt\Avatar\Facade as Avatar;

trait CommonAttributes
{
    /**
     * Retrieve the owner of the resource.
     *
     * @return string
     */
    public function getAuthorAttribute()
    {
        return $this->user->displayname;
    }

    /**
     * Retrieve the parsed created_at fields.
     *
     * @return string
     */
    public function getCreatedAttribute()
    {
        return $this->parseDate($this->attributes['created_at']);
    }

    /**
     * Retrieve the parsed created_at fields.
     *
     * @return string
     */
    public function getModifiedAttribute()
    {
        return $this->parseDate($this->attributes['updated_at']);
    }

    /**
     * Retrieve the parsed created_at fields.
     *
     * @return string
     */
    public function getDeletedAttribute()
    {
        if (is_null($this->attributes['deleted_at'] ?? null)) {
            return '';
        }

        return $this->parseDate($this->attributes['deleted_at']);
    }

    /**
     * Retrieve the parsed created_at fields.
     *
     * @return string
     */
    public function getJoinedAttribute()
    {
        return $this->created;
    }

    /**
     * Retrieve the featured image for the resource.
     *
     * @return string
     */
    public function getFeaturedAttribute()
    {
        return $this->photo ?? $this->image ?? null;
    }

    /**
     * Retrieve the short version text of
     * the resource's description field.
     *
     * @return string
     */
    public function getExcerptAttribute()
    {
        return Str::words(
            $this->attributes['description'] ?? $this->attributes['body'] ?? null,
            settings('display:excerpt', 15),
            '...'
        );
    }

    /**
     * Retrieve the illustration for the resource.
     *
     * @return string
     */
    public function getIllustrationAttribute()
    {
        try {
            $svg = file_get_contents(storage_path($this->svg ?? $this->image));
        } catch (\Exception $e) {
            return Avatar::create($this->title ?? $this->name ?? $this->displayname)
                ->setShape('circle')
                ->setBackground('transparent')
                ->setForeground('black')
                ->setDimension(80)
                ->toSvg();
        }

        return $svg;
    }

    /**
     * Parse the passed date.
     *
     * @param  string $date
     * @return string
     */
    protected function parseDate($date)
    {
        switch (settings('format:date')) {
            case ':human:':
                $date = Carbon::parse($date)->diffForHumans();
                break;

            default:
                $date = date(settings('format:date', 'd-M, Y'), strtotime($date));
                break;
        }

        return $date;
    }
}
