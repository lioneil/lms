<?php

namespace Material\Observers;

class MaterialObserver
{
    /**
     * Listen to the Material created event.
     *
     * @param  \Material\Models\Material $resource
     * @return void
     */
    public function created(Material $resource)
    {
        session()->flash('message', sprintf(trans('Material %s successfully added'), $resource->name));
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Material updated event.
     *
     * @param  \Material\Models\Material $resource
     * @return void
     */
    public function updated(Material $resource)
    {
        session()->flash('message', sprintf(trans('Material %s successfully updated'), $resource->name);
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Material deleted event.
     *
     * @param  \Material\Models\Material $resource
     * @return void
     */
    public function deleted(Material $resource)
    {
        session()->flash('message', sprintf(trans('Material %s successfully deactivated'), $resource->name);
        session()->flash('type', 'success');
    }

    /**
     * Listen to the Material restored event.
     *
     * @param  \Material\Models\Material $resource
     * @return void
     */
    public function restored(Material $resource)
    {
        session()->flash('message', sprintf(trans('Material %s successfully restored'), $resource->name);
        session()->flash('type', 'success');
    }
}
