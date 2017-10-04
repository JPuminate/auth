<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 18/08/2017
 * Time: 15:36
 */

namespace JPuminate\Auth\Identity;


trait HasOAuthToken
{
    /**
     * The current access token for the authentication user.
     *
     */
    protected $accessToken;


    protected $domain;

    public function withAuthCredentials(OAuthToken $accessToken, AuthDomain $domain){
        $this->accessToken = $accessToken;
        $this->domain = $domain;
        return $this;
    }

    public function domain(){
        return $this->domain;
    }

    public function token(){
        return $this->accessToken;
    }

    public function getAuthDomain(){
        return $this->domain;
    }

    public function tokenCan($scope)
    {
        return $this->accessToken ? $this->accessToken->can($scope) : false;
    }

    public function belongs($domain)
    {
        return $this->domain->getName() === $domain;
    }


}