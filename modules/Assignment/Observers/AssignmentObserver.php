<?php

namespace Assignment\Observers;

use Assignment\Models\Assignment;

class AssignmentObserver
{
    /**
     * Listen to the Assignment created event.
     *
     * @param  \Assignment\Models\Assignment $resource
     * @return void
     */
    public function created(Assignment $resource)
    {
        session()->flash('message', sprintf(trans('Assignment %s successfully added'), $resource->name));
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Assignment updated event.
     *
     * @param  \Assignment\Models\Assignment $resource
     * @return void
     */
    public function updated(Assignment $resource)
    {
        session()->flash('message', sprintf(trans('Assignment %s successfully updated'), $resource->name);
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Assignment deleted event.
     *
     * @param  \Assignment\Models\Assignment $resource
     * @return void
     */
    public function deleted(Assignment $resource)
    {
        session()->flash('message', sprintf(trans('Assignment %s successfully deactivated'), $resource->name);
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Assignment restored event.
     *
     * @param  \Assignment\Models\Assignment $resource
     * @return void
     */
    public function restored(Assignment $resource)
    {
        session()->flash('message', sprintf(trans('Assignment %s successfully restored'), $resource->name);
        session()->flash('type', 'success');
    }
}
