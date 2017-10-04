<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 17/08/2017
 * Time: 14:08
 */

namespace JPuminate\Auth\Identity\Events;


class HostedUserCreated
{


    public $user_id;
    public $oauth_user_id;

    public function __construct($user_id, $oauth_user_id)
    {
        $this->user_id = $user_id;
        $this->oauth_user_id = $oauth_user_id;
    }
}