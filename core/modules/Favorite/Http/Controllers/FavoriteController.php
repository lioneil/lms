<?php

namespace Favorite\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;

abstract class FavoriteController extends AdminController
{
    /**
     * Display list of favorite resources.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    abstract public function favorites(Request $request);
}
