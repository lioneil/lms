<?php

namespace Quiz\Observers;

class QuizObserver
{
    /**
     * Listen to the Quiz created event.
     *
     * @param  \Quiz\Models\Quiz $resource
     * @return void
     */
    public function created(Quiz $resource)
    {
        session()->flash('message', sprintf(trans('Quiz %s successfully added'), $resource->name));
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Quiz updated event.
     *
     * @param  \Quiz\Models\Quiz $resource
     * @return void
     */
    public function updated(Quiz $resource)
    {
        session()->flash('message', sprintf(trans('Quiz %s successfully updated'), $resource->name);
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Quiz deleted event.
     *
     * @param  \Quiz\Models\Quiz $resource
     * @return void
     */
    public function deleted(Quiz $resource)
    {
        session()->flash('message', sprintf(trans('Quiz %s successfully deactivated'), $resource->name);
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Quiz restored event.
     *
     * @param  \Quiz\Models\Quiz $resource
     * @return void
     */
    public function restored(Quiz $resource)
    {
        session()->flash('message', sprintf(trans('Quiz %s successfully restored'), $resource->name);
        session()->flash('type', 'success');
    }
}
