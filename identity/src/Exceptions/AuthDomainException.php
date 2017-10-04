<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 18/08/2017
 * Time: 16:15
 */

namespace JPuminate\Auth\Identity\Exceptions;


use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class AuthDomainException extends AuthorizationException
{


    /**
     * @var string
     */
    private $domain;
    /**
     * @var int
     */

    public function __construct($domain,  $message = "")
    {
        parent::__construct($message);
        $this->domain = $domain;
    }


    public static function accessDenied($domain){
        return new static($domain, 'Inaccessible Domain : '.$domain);
    }

    public function domain(){
        return $this->domain;
    }
}