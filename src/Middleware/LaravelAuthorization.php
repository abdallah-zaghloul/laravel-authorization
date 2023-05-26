<?php

namespace ZaghloulSoft\LaravelAuthorization\Middleware;

use Closure;
use Illuminate\Http\Request;
use ZaghloulSoft\LaravelAuthorization\Traits\Response;
use Illuminate\Http\Exceptions\HttpResponseException;


class LaravelAuthorization
{
    use Response;

    /**
     * @param Request $request
     * @param Closure $next
     * @param string $guard
     * @param string $module
     * @param string $permission
     * @param string $relation
     * @return mixed
     */

    public function handle(Request $request, Closure $next,string $guard,string $module,string $permission ,string $relation = 'role')
    {
        try {
            $guardModuledPermissions = auth($guard)->user()->{$relation}->moduled_permissions;
            return $guardModuledPermissions[$module][$permission] === true ? $next($request) : $this->forbiddenException();
        }catch (\Exception $e){
            return $this->forbiddenException();
        }
    }

    /**
     * @return HttpResponseException
     */
    public function forbiddenException(): HttpResponseException
    {
        throw new HttpResponseException(response()->json([
            'status'=> false,
            'message'=> trans('laravel-authorization.forbidden'),
        ],$this->statusCodeForbidden));
    }
}
