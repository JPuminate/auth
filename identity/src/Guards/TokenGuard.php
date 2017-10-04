<?php

namespace JPuminate\Auth\Identity\Guards;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use JPuminate\Auth\Identity\AuthConfig;
use JPuminate\Auth\Identity\AuthDomain;
use JPuminate\Auth\Identity\AuthGateway;
use JPuminate\Auth\Identity\Client;
use JPuminate\Auth\Identity\Events\HostedUserCreated;
use JPuminate\Auth\Identity\Exceptions\AuthGatewayException;
use JPuminate\Auth\Identity\Identity;
use JPuminate\Auth\Identity\OAuthToken;

/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 16/08/2017
 * Time: 20:03
 */
class TokenGuard
{

    use GuardHelpers;

    /**
     * @var AuthGateway
     */
    private $proxy;
    /**
     * @var Request
     */
    private $request;

    /**
     * @var AuthConfig
     */
    private $usersDomain;
    /**
     * @var AuthDomain
     */
    private $clientsDomain;

    private $domain;

    public function __construct(UserProvider $provider, AuthGateway $proxy, Request $request, AuthConfig $usersDomain, AuthConfig $clientsDomain)
    {
        $this->provider = $provider;
        $this->proxy = $proxy;
        $this->request = $request;
        $this->usersDomain = $usersDomain;
        $this->clientsDomain = $clientsDomain;
    }

    /**
     * Get the user for the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Request  $request
     * @return mixed
     */
    public function user()
    {
        if(!is_null($this->user)) return $this->user;
        if ($this->request->bearerToken()) {
            return $this->authenticateViaBearerToken();
        }
    }

    private function authenticateViaBearerToken()
    {
        try {
            list($token, $domain) = $this->proxy->validateAuthenticatedRequest($this->request);
            if($domain === AuthDomain::$USERS) return $this->user = $this->authenticateToUsersDomain($token);
            else if($domain === AuthDomain::$CLIENTS) return $this->user = $this->authenticateToClientsDomain($token);
        }
        catch (AuthGatewayException $e) {
            if ($e->getHttpStatus() === 401) throw new AuthenticationException;
            else if($e->getHttpStatus() === 403) throw new AuthorizationException();
            else throw $e;
        }
    }

    private function authenticateToUsersDomain(OAuthToken $token){
        $this->domain = new AuthDomain(AuthDomain::$USERS);
        if ($this->usersDomain->isHosted()) {
            $this->user = $this->provider->retrieveByCredentials([Identity::$foreignKey => $token->getClaim('sub')]);
            if (!$this->user) {
                $this->user = $this->createHostedUser($token->getClaim('sub'));
            }
        }
        // if hosted option is false, we create a new user
        if(!$this->user) {
            $this->user = $this->provider->createModel();
            $this->user->{Identity::$foreignKey} = $token->getClaim('sub');
        }
        foreach ($this->usersDomain->claims() as $key => $value) {
            $this->user->{$value} = $token->getClaim($key);
        }
        return $this->user->withAuthCredentials($token,  $this->domain);
    }

    private function authenticateToClientsDomain(OAuthToken $token){
        $this->domain = new AuthDomain(AuthDomain::$CLIENTS);
        $this->user = new Client();
        $this->user->client_id = $token->getClaim('aud');
        foreach ($this->clientsDomain->claims() as $key => $value) {
            $this->user->{$value} = $token->getClaim($key);
        }
        return $this->user->withAuthCredentials($token,  $this->domain);
    }

    private function createHostedUser($oauth_user_id)
    {
        $model =  $this->provider->createModel();
        $query = $model->newQuery();
        $query->where(Identity::$foreignKey, $oauth_user_id);
        $user_id = DB::table($model->getTable())->insert([Identity::$foreignKey => $oauth_user_id]);
        Event::dispatch(new HostedUserCreated($user_id, $oauth_user_id));
        return $query->first();
    }

    public function domain()
    {
        return !is_null($this->domain) ? $this->domain : new AuthDomain(AuthDomain::$NO_ONE);
    }

    public function getUsersProvider(){
        return $this->provider;
    }



}