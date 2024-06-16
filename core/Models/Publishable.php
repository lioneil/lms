<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

trait Publishable
{
    /**
     * Indicates if the model can be published.
     *
     * @var boolean
     */
    public $publishes = true;

    /**
     * The name of the "published at" column.
     *
     * @var string
     */
    public static $publishedAt = 'published_at';

    /**
     * The name of the "drafted at" column.
     *
     * @var string
     */
    public static $draftedAt = 'drafted_at';

    /**
     * The name of the "expired at" column.
     *
     * @var string
     */
    public static $expiredAt = 'expired_at';

    /**
     * Retrieve the published column name.
     *
     * @return string
     */
    public function getPublishedAtKey()
    {
        return self::$publishedAt;
    }

    /**
     * Retrieve the expired column name.
     *
     * @return string
     */
    public function getExpiredAtKey()
    {
        return self::$expiredAt;
    }

    /**
     * Retrieve the drafted column name.
     *
     * @return string
     */
    public function getDraftedAtKey()
    {
        return self::$draftedAt;
    }

    /**
     * Update the publishing date to the current
     * timestamp and fire an event.
     *
     * @return boolean
     */
    public function publish()
    {
        if (! $this->canPublish()) {
            return false;
        }

        $this->{self::$draftedAt} = null;
        $this->{self::$publishedAt} = $this->freshTimestamp();

        $this->save();

        $this->fireModelEvent('published', $halt = false);

        return $this;
    }

    /**
     * Update the publishing date to null and fire an event.
     *
     * @return boolean
     */
    public function unpublish()
    {
        if (! $this->canPublish()) {
            return false;
        }

        $this->{self::$publishedAt} = null;

        $this->save();

        $this->fireModelEvent('unpublished', $halt = false);

        return $this;
    }

    /**
     * Update the drafted date to the current
     * timestamp and fire an event.
     *
     * @return boolean
     */
    public function draft()
    {
        if (! $this->canPublish()) {
            return false;
        }

        $this->{self::$draftedAt} = $this->freshTimestamp();
        $this->{self::$publishedAt} = null;

        $this->save();

        $this->fireModelEvent('drafted', $halt = false);

        return $this;
    }

    /**
     * Update the expired date to the current
     * timestamp and fire an event.
     *
     * @return boolean
     */
    public function expire()
    {
        if (! $this->canPublish()) {
            return false;
        }

        $this->{self::$draftedAt} = null;
        $this->{self::$expiredAt} = $this->freshTimestamp();

        $this->save();

        $this->fireModelEvent('expired', $halt = false);

        return $this;
    }

    /**
     * Retrieve the published column name.
     *
     * @return string
     */
    public function hasPublishDate()
    {
        return ! is_null($this->{self::$publishedAt});
    }

    /**
     * Determine if the model uses timestamps.
     *
     * @return boolean
     */
    public function canPublish()
    {
        return $this->publishes;
    }

    /**
     * Check if resource is published.
     *
     * @return boolean
     */
    public function isPublished(): bool
    {
        return ! is_null($this->{self::$publishedAt}) && $this->{self::$publishedAt}->isPast();
    }

    /**
     * Check if resource is unpublished.
     *
     * @return boolean
     */
    public function isUnpublished(): bool
    {
        return is_null($this->{self::$publishedAt});
    }

    /**
     * Check if resource is drafted.
     *
     * @return boolean
     */
    public function isDrafted(): bool
    {
        return ! is_null($this->{self::$draftedAt});
    }

    /**
     * Check if resource is expired.
     *
     * @return boolean
     */
    public function isExpired(): bool
    {
        return ! is_null($this->{self::$expiredAt}) && $this->{self::$expiredAt} < Carbon::now();
    }

    /**
     * Include only published records in the results.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @return $this
     */
    public function scopePublished(Builder $builder)
    {
        return $builder->where($this->getPublishedAtKey(), '<=', now());
    }

    /**
     * Set the published at column's value.
     *
     * @param  string $publishDate
     * @return void
     */
    public function publishBy($publishDate = null)
    {
        $this->{self::$publishedAt} = ! is_null($publishDate)
            ? Carbon::parse($publishDate)->format('Y-m-d H:i:s')
            : null;
    }
}
