<?php

namespace Assessment\Observers;

use Assessment\Models\Submission;

class SubmissionObserver
{
    /**
     * Listen to the Submission created event.
     *
     * @param  \Submission\Models\Submission $resource
     * @return void
     */
    public function created(Submission $resource)
    {
        session()->flash('message', sprintf(trans('Submission %s successfully added'), $resource->name));
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Submission updated event.
     *
     * @param  \Submission\Models\Submission $resource
     * @return void
     */
    public function updated(Submission $resource)
    {
        session()->flash('message', sprintf(trans('Submission %s successfully updated'), $resource->name));
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Submission deleted event.
     *
     * @param  \Submission\Models\Submission $resource
     * @return void
     */
    public function deleted(Submission $resource)
    {
        session()->flash('message', sprintf(trans('Submission %s successfully deactivated'), $resource->name));
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Submission restored event.
     *
     * @param  \Submission\Models\Submission $resource
     * @return void
     */
    public function restored(Submission $resource)
    {
        session()->flash('message', sprintf(trans('Submission %s successfully restored'), $resource->name));
        session()->flash('type', 'success');
    }
}
