<?php

namespace Core\Enumerations;

abstract class ModelStatus
{
    const CREATED = 'created';
    const DELETED = 'deleted';
    const DRAFTED = 'drafted';
    const PUBLISHED = 'published';
    const SAVED = 'saved';
    const SOFT_DELETED = 'soft deleted';
    const TRASHED = 'trashed';
    const UNPUBLISHED = 'unpublished';
    const UPDATED = 'updated';
}
