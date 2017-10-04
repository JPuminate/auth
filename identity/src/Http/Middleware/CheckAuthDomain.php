<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 18/08/2017
 * Time: 14:42
 */

namespace JPuminate\Auth\Identity\Http\Middleware;


use Illuminate\Auth\AuthenticationException;
use JPuminate\Auth\Identity\Exceptions\AuthDomainException;

class CheckAuthDomain
{
    public function handle($request, $next, $domain)
    {
        if (! $request->user() || ! $request->user()->token()) {
            throw new AuthenticationException;
        }


        if ($request->user()->belongs($domain)) {
            return $next($request);
        }

        throw  AuthDomainException::accessDenied($domain);
    }
}