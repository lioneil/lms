<?php

namespace Core\Widgets;

use Core\Application\Service\WithService;
use Core\Application\Widget\AbstractWidget;
use Setting\Services\SettingServiceInterface;

class AppDetails extends AbstractWidget
{
    use WithService;

    /**
     * The alias of the widget when calling
     * from a blade file.
     *
     * @var string
     */
    protected $alias = 'app:details';

    /**
     * The widget description.
     *
     * @var string
     */
    protected $description = 'Display application details.';

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
     * Inject any dependencies.
     *
     * @param \Setting\Services\SettingServiceInterface $service
     */
    public function __construct(SettingServiceInterface $service)
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
        return view('widgets::details')->withDetails($this->service()->containsKey('app'));
    }
}
