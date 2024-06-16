<?php

namespace Page\Observers;

use Page\Models\Page;

class PageObserver
{
    /**
     * Listen to the Page created event.
     *
     * @param  \Page\Models\Page $resource
     * @return void
     */
    public function created(Page $resource)
    {
        session()->flash('message', sprintf(trans('Page %s successfully added'), $resource->name));
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Page updated event.
     *
     * @param  \Page\Models\Page $resource
     * @return void
     */
    public function updated(Page $resource)
    {
        session()->flash('message', sprintf(trans('Page %s successfully updated'), $resource->name));
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Page deleted event.
     *
     * @param  \Page\Models\Page $resource
     * @return void
     */
    public function deleted(Page $resource)
    {
        session()->flash('message', sprintf(trans('Page %s successfully deactivated'), $resource->name));
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Page restored event.
     *
     * @param  \Page\Models\Page $resource
     * @return void
     */
    public function restored(Page $resource)
    {
        session()->flash('message', sprintf(trans('Page %s successfully restored'), $resource->name));
        session()->flash('type', 'success');
    }
}
