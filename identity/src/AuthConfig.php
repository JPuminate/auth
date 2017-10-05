<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 17/08/2017
 * Time: 12:45
 */

namespace JPuminate\Auth\Identity;


class AuthConfig
{
    /**
     * @var mixed
     */
    private $config;

    /**
     * AuthConfig constructor.
     * @param mixed $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function isHosted(){
        return $this->config['subject']['hosted'];
    }

    public function isForcedToCreate(){
        return $this->config['subject']['force-create'];
    }

    public function claims(){
        return array_key_exists('claims', $this->config) ? $this->config['claims'] : [];
    }

}