<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 18/08/2017
 * Time: 16:45
 */

namespace JPuminate\Auth\Identity\Http\Middleware;


use Illuminate\Auth\AuthenticationException;
use JPuminate\Auth\Identity\Exceptions\MissingScopeException;

class CheckScopes
{

    public function handle($request, $next, ...$scopes)
    {
        if (! $request->user() || ! $request->user()->token()) {
            throw new AuthenticationException;
        }
        foreach ($scopes as $scope) {
            if (! $request->user()->tokenCan($scope)) {
                throw new MissingScopeException($scope);
            }
        }
        return $next($request);
    }
}