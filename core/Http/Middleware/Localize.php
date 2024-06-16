<?php

namespace Core\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class Localize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (in_array($request->lang, collect(config('language.supported'))->keys()->all())) {
            app()->setLocale($request->lang);
        }

        return $next($request);
    }
}
