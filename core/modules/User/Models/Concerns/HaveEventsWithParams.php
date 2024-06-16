<?php

namespace User\Models\Concerns;

trait HaveEventsWithParams
{
    /**
     * Stores event key data
     *
     * @var array
     */
    public $eventData = [];

    /**
     * Fire the given event for the model.
     *
     * @param  string  $event
     * @param  boolean $halt
     * @param  array   $data
     * @return mixed
     */
    protected function fireModelEvent($event, $halt = true, array $data = [])
    {
        $this->eventData[$event] = $data;

        return parent::fireModelEvent($event, $halt);
    }

    /**
     * Get the event data by event
     *
     * @param  string $event
     * @return array|null
     */
    public function getEventData(string $event)
    {
        if (array_key_exists($event, $this->eventData)) {
            return $this->eventData[$event];
        }

        return null;
    }

    /**
     * Save the account data [email, username, password]
     * to the details table.
     *
     * @param  array $attributes
     * @return \User\Models\User
     */
    public function record(array $attributes)
    {
        $this->fireModelEvent('recorded', false, $attributes);

        return $this;
    }
}
