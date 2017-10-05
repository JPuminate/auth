<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 17/08/2017
 * Time: 17:19
 */

namespace JPuminate\Auth\Identity;


class AuthDomain
{
    public static $USERS = "users";

    public static $CLIENTS = "clients";

    public static $NO_ONE = "none";
    /**
     * @var null
     */
    private $domain;

    public function __construct($domain = null)
    {
        $this->domain = $domain ? $domain : static::$NO_ONE;
    }

    public function getName(){
        return $this->domain;
    }
}