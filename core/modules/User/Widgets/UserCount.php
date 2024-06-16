<?php

namespace User\Widgets;

use Core\Application\Service\WithService;
use Core\Application\Widget\AbstractWidget;
use Illuminate\Support\Facades\View;
use User\Services\UserServiceInterface;

class UserCount extends AbstractWidget
{
    use WithService;

    /**
     * The alias of the widget when calling
     * from the widget container.
     *
     * @var string
     */
    protected $alias = 'user:count';

    /**
     * The widget description.
     *
     * @var string
     */
    protected $description = 'The total user count.';

    /**
     * The interval in seconds
     * before reloading content.
     *
     * False means the widget will not reload.
     *
     * @var integer|float|boolean
     */
    protected $intervals = false;

    /**
     * Initialize the class and inject any service or class
     * needed for the widget.
     *
     * @param \User\Services\UserServiceInterface $service
     */
    public function __construct(UserServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Render the widget into the view.
     *
     * @return string|null
     */
    public function handle()
    {
        return view('user::widgets.count')->withCount($this->service()->count());
    }
}
