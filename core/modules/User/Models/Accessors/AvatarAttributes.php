<?php

namespace User\Models\Accessors;

use Laravolt\Avatar\Avatar;

trait AvatarAttributes
{
    /**
     * Retrieve the Avatar instance.
     *
     * @param  array|null $config
     * @return \Laravolt\Avatar\Avatar
     */
    protected function laravolt($config = null)
    {
        return new Avatar($config ?? config('avatar'));
    }

    /**
     * Retrieve the url of the user's avatar.
     *
     * @return string
     */
    public function getAvatarAttribute()
    {
        return $this->photo ?? $this->laravolt()
            ->create($this->fullname)
            ->toBase64()
            ->getEncoded();
    }
}
