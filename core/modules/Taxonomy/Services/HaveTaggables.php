<?php

namespace Taxonomy\Services;

use Illuminate\Support\Collection;
use Taxonomy\Models\Tag;

trait HaveTaggables
{
    /**
     * The array of tags.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Retrieve or create new tags.
     *
     * @param  array $attributes
     * @return \Illuminate\Support\Collection
     */
    public function getOrSaveTags($attributes)
    {
        foreach ((array) $attributes as $tag) {
            $this->tags[] = Tag::updateOrCreate([
                'name' => $tag['name'] ?? $tag ?? null
            ], [
                'name' => $tag['name'] ?? $tag,
                'icon' => $tag['icon'] ?? null,
                'type' => $this->getTable(),
            ]);
        }

        return Collection::make($this->tags);
    }
}
