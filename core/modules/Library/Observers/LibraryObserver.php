<?php

namespace Library\Observers;

use Library\Models\Library;

class LibraryObserver
{
    /**
     * Listen to the Library created event.
     *
     * @param  \Library\Models\Library $resource
     * @return void
     */
    public function created(Library $resource)
    {
        session()->flash('message', sprintf(trans('%s successfully added'), $resource->name));
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Library updated event.
     *
     * @param  \Library\Models\Library $resource
     * @return void
     */
    public function updated(Library $resource)
    {
        session()->flash('message', sprintf(trans('%s successfully updated'), $resource->name));
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Library deleted event.
     *
     * @param  \Library\Models\Library $resource
     * @return void
     */
    public function deleted(Library $resource)
    {
        session()->flash('message', sprintf(trans('%s successfully deactivated'), $resource->name);
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Library restored event.
     *
     * @param  \Library\Models\Library $resource
     * @return void
     */
    public function restored(Library $resource)
    {
        session()->flash('message', sprintf(trans('%s successfully restored'), $resource->name);
        session()->flash('type', 'success');
    }
}
