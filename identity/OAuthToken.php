<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 17/08/2017
 * Time: 14:24
 */

namespace JPuminate\Auth\Identity;


use Illuminate\Support\Facades\Auth;

class OAuthToken
{
    private $jwt;
    private $scopes;
    private $expired_at;
    private $id;
    private $user_id;
    private $client_id;


    public function __construct($jwt)
    {
        $this->jwt = $jwt = (array)$jwt;
        $this->scopes = array_key_exists('scopes', $jwt)? $jwt['scopes'] : [];
        $this->expired_at = array_key_exists('exp', $jwt)? $jwt['exp'] : null;
        $this->id = array_key_exists('jti', $jwt)? $jwt['jti'] : null;
        $this->user_id = array_key_exists('sub', $jwt)? $jwt['sub'] : null;
        $this->client_id = array_key_exists('aud', $jwt)? $jwt['aud'] : null;
    }

    public function getClaim($name)
    {
        if (key_exists($name, $this->jwt)) return $this->jwt[$name];
        return null;
    }

    public function can($scope)
    {
        return in_array('*', $this->scopes) ||
            array_key_exists($scope, array_flip($this->scopes));
    }

    /**
     * @return mixed
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @return mixed
     */
    public function getExpiredAt()
    {
        return $this->expired_at;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->client_id;
    }


    public function user()
    {
        if(Auth::domain()->getName() === AuthDomain::$USERS){
            return Auth::getUsersProvider()->retrieveByCredentials([Identity::$foreignKey => $this->getUserId()]);
        }
        return;
    }



}