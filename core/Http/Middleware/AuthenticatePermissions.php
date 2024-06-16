<?php

namespace Core\Http\Middleware;

use Closure;
use Core\Application\Permissions\RemoveApiPrefixFromPermission;
use Illuminate\Http\Response;

class AuthenticatePermissions
{
    use RemoveApiPrefixFromPermission;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->cannot($this->removeApiPrefixFromPermission($request->route()->getName()))) {
            return abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
