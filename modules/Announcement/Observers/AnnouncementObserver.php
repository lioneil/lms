<?php

namespace Announcement\Observers;

class AnnouncementObserver
{
    /**
     * Listen to the Announcement created event.
     *
     * @param  \Announcement\Models\Announcement $resource
     * @return void
     */
    public function created(Announcement $resource)
    {
        session()->flash('message', sprintf(trans('Announcement %s successfully added'), $resource->name));
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Announcement updated event.
     *
     * @param  \Announcement\Models\Announcement $resource
     * @return void
     */
    public function updated(Announcement $resource)
    {
        session()->flash('message', sprintf(trans('Announcement %s successfully updated'), $resource->name);
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Announcement deleted event.
     *
     * @param  \Announcement\Models\Announcement $resource
     * @return void
     */
    public function deleted(Announcement $resource)
    {
        session()->flash('message', sprintf(trans('Announcement %s successfully deactivated'), $resource->name);
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Announcement restored event.
     *
     * @param  \Announcement\Models\Announcement $resource
     * @return void
     */
    public function restored(Announcement $resource)
    {
        session()->flash('message', sprintf(trans('Announcement %s successfully restored'), $resource->name);
        session()->flash('type', 'success');
    }
}
