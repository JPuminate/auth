<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 17/08/2017
 * Time: 11:30
 */

namespace JPuminate\Auth\Identity\Exceptions;


use Throwable;

class AuthGatewayException extends \Exception
{
    /**
     * @var int
     */
    private $httpStatus;

    public function __construct($httpStatus, $message = "", $code = 0,  Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->httpStatus = $httpStatus;
    }

    public static function expiredToken()
    {
        return new static(401, "Expired token");
    }

    public static function accessDenied($string)
    {
        return new static(401, $string);
    }

    public function getHttpStatus(){
        return $this->httpStatus;
    }

}