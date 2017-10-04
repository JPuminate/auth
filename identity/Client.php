<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 17/08/2017
 * Time: 17:24
 */

namespace JPuminate\Auth\Identity;


class Client
{
    use HasOAuthToken;

    public $client_id;
}