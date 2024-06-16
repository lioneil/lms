<?php

namespace Course\Enumerations;

abstract class CourseDictionary
{
    const COMPLETE = 'complete';
    const COMPLETED = 'completed';
    const DONE = 'done';
    const IN_PROGRESS = 'in progress';
    const STARTED = 'started';
    const INCOMPLETE = 'incomplete';
    const PENDING = 'pending';
    const NOT_APPLICABLE = 'na';

    const LOCKED = 'locked';
    const UNLOCKED = 'unlocked';
    const LOCKABLE = 'lockable';
    const NONLOCKABLE = 'nonlockable';

    const FAILED = 'failed';
    const PASSED = 'passed';

    const STATUS = 'status';
    const TYPE = 'type';
}
