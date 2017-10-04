<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 16/08/2017
 * Time: 19:38
 */

namespace JPuminate\Auth\Identity;


class Identity
{


    public static $configFile = "identity";

    public static $userTable = "users";
    /**
     * The storage location of the encryption keys.
     *
     * @var string
     */
    public static $keyPath;
    /**
     * Indicates if Passport migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    public static $foreignKey = "jp_user_id";


    /**
     * Set the storage location of the encryption keys.
     *
     * @param  string  $path
     * @return void
     */
    public static function loadKeysFrom($path)
    {
        static::$keyPath = $path;
    }
    /**
     * The location of the encryption keys.
     *
     * @param  string  $file
     * @return string
     */
    public static function keyPath($file)
    {
        $file = ltrim($file, '/\\');
        return static::$keyPath
            ? rtrim(static::$keyPath, '/\\').DIRECTORY_SEPARATOR.$file
            : storage_path($file);
    }

    /**
     * Configure Passport to not register its migrations.
     *
     * @return static
     */
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;
        return new static;
    }

    public static function foreignKey($foreignKey=null){
        if($foreignKey) static::$foreignKey = $foreignKey;
        return static::$foreignKey;
    }

    public static function cookie($cookie = null)
    {
        if (is_null($cookie)) {
            return static::$cookie;
        } else {
            static::$cookie = $cookie;
        }

        return new static;
    }

    public static function usersTable($table=null)
    {
        if (is_null($table)) {
            return static::$userTable;
        } else {
            static::$userTable = $table;
        }

        return new static;
    }


}