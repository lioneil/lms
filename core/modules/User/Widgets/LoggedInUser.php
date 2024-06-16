<?php

namespace User\Widgets;

use Core\Application\Widget\AbstractWidget;
use Illuminate\Support\Facades\Auth;

class LoggedInUser extends AbstractWidget
{
    /**
     * The alias of the widget when calling
     * from the widget container.
     *
     * @var string
     */
    protected $alias = 'user:loggedin';

    /**
     * The widget description.
     *
     * @var string
     */
    protected $description = 'The logged in user details.';

    /**
     * Render the widget into the view.
     *
     * @return string|null
     */
    public function handle()
    {
        return view('user::widgets.loggedin')->withUser(Auth::user());
    }
}
