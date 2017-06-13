<?php
App::uses('FormAuthenticate', 'Controller/Component/Auth');
 
class VNAuthenticate extends FormAuthenticate
{
 
    protected function _password($password)
    {
        return self::hash($password);
    }
 
    public static function hash($password)
    {
        // Manipulate $password, hash, custom hash, whatsover
        return $password;
    }
}