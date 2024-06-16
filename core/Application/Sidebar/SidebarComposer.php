<?php

namespace Core\Application\Sidebar;

use Illuminate\View\View;

class SidebarComposer
{
    /**
     * The view's variable.
     *
     * @var string
     */
    protected $name = 'sidebar';

    /**
     * Main function to tie everything together.
     *
     * @param  Illuminate\View\View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with($this->name, $this->handle());
    }

    /**
     * Generates the sidebar instance.
     *
     * @return array
     */
    protected function handle()
    {
        return app('sidebar')->build()->get();
    }
}
