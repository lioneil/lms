<?php

namespace Core\Application\Widget;

use Carbon\CarbonInterval;

abstract class AbstractWidget implements Contracts\WidgetInterface
{
    use Concerns\Reloadable;

    /**
     * The interval in milliseconds
     * before reloading content.
     *
     * False means the widget will not reload.
     *
     * @var integer|float|boolean
     */
    protected $intervals = false;

    /**
     * The interval in seconds
     * before cache expires.
     *
     * False means no caching.
     *
     * @var integer|boolean
     */
    protected $cached = false;

    /**
     * The array of extra attributes
     * to be injected in the widget view.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Build the data to be rendered.
     *
     * @return \Illuminate\View\View
     */
    abstract public function handle();

    /**
     * Render the widget into the view.
     *
     * @return string|null
     */
    public function render()
    {
        $attributes = func_get_args();

        return $this->wrap(
            $this->handle()->with(
                array_merge($this->attributes, array_shift($attributes))
            )->render()
        );
    }

    /**
     * Retrieve the attributes array.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * Retrieve the widget reload intervals.
     *
     * @return integer|float
     */
    public function getIntervals()
    {
        if (is_string($this->intervals)) {
            return CarbonInterval::fromString($this->intervals)->cascade()->total('seconds');
        }

        return $this->intervals === false ? 0 : $this->intervals;
    }

    /**
     * Check if widget is auto-reloading.
     *
     * @return boolean
     */
    protected function hasIntervals()
    {
        return $this->getIntervals() > 0;
    }
}
