<?php

namespace User\Widgets;

use Carbon\Carbon;
use Core\Application\Service\WithService;
use Core\Application\Widget\AbstractWidget;
use User\Services\UserServiceInterface;

class MonthlyBirthdayCelebrants extends AbstractWidget
{
    use WithService;

    /**
     * The alias of the widget when calling
     * from the widget container.
     *
     * @var string
     */
    protected $alias = 'monthly:birthdays';

    /**
     * The widget description.
     *
     * @var string
     */
    protected $description = 'List all birthday celebrants for the month.';

    /**
     * The interval in seconds
     * before reloading content.
     *
     * False means the widget will not reload.
     *
     * @var integer|float|boolean
     */
    protected $intervals = '1 day'; /* 20 seconds */

    /**
     * The Carbon instance.
     *
     * @var \Carbon\Carbon
     */
    protected $carbon;

    /**
     * Initialize the class and inject any service or class
     * needed for the widget.
     *
     * @param \User\Services\UserServiceInterface $service
     * @param \Carbon\Carbon                      $carbon
     */
    public function __construct(UserServiceInterface $service, Carbon $carbon)
    {
        $this->service = $service;
        $this->carbon = $carbon;
    }

    /**
     * Render the widget into the view.
     *
     * @return string|null
     */
    public function handle()
    {
        return view('user::widgets.birthdays')->withCelebrants(
            $this->service()->whereHas('details', function ($query) {
                return $query->where('key', 'birthday');
            })->get()->filter(function ($celebrant) {
                $birthmonth = $this->carbon->parse($celebrant->detail('birthday'))->month;
                $month = $this->carbon->now()->month;
                return $birthmonth === $month;
            })
        );
    }
}
