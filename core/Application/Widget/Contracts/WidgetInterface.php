<?php

namespace Core\Application\Widget\Contracts;

interface WidgetInterface
{
    /**
     * Render the widget into the view.
     *
     * @return string|null
     */
    public function handle();
}
