<?php
/**
 * Config settings to connect to the Redis server.
 * There are different configs for servers performing
 * different tasks to allow each server to be configured
 * to it's specific task
 */
class RedisConfig {

    static public $default = array(

        'session' => array(
            'host' => REDIS_SERVER 
            , 'port' => 6379
            , 'password' => REDIS_PASSWORD
        )
    );
}
// Redis plugin depends on Predis
// @link https://github.com/nrk/predis
// 2016-08-09 TLC there is an issue with PHP version greater than 5.3, that cause the CakePHP autoloader
// to not work here for some reason, so we are loading it manually.
App::import('Lib', 'RedisCache.Predis/Autoloader');
//require "Predis/Autoloader.php";

Predis\Autoloader::register();
