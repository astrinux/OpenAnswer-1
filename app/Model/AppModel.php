<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
    
    public function beforeSave($options=array()){
        
        //handle saving complex relationship models
        foreach (array_keys($this->hasAndBelongsToMany) as $model){
            if(isset($this->data[$this->name][$model])){
                $this->data[$model][$model] = $this->data[$this->name][$model];
                unset($this->data[$this->name][$model]);
            }
        }
        
        //handle table field encryption
        if (!isset($this->enc_fields)) { //no encrypted fields to handle, do nothing.
            return true;
        }
        foreach ($this->enc_fields as $field) {
            if (isset($this->data[$this->alias][$field])) {
                $this->data[$this->alias][$field]=$this->encrypt($this->data[$this->alias][$field]);
            }
        }
        return true;
    }
 
    public function afterFind($results,$primary=false){
        
        if (!isset($this->enc_fields)) { //no encrypted fields to handle, do nothing.
            return $results;
        }
        
        
        
        //different steps required if this is the primary model, or if this data is being pulled due to
        //an association
        if ($primary) {
            foreach($results as $key => $val){
                foreach ($this->enc_fields as $field) {
                    if(isset($results[$key][$this->alias][$field])){
                    $results[$key][$this->alias][$field] = $this->decrypt($results[$key][$this->alias][$field]);
                    }
                }
            }
        }
        else { //not primary query, must do additional format checking as response could be in one of several different formats.
               //very annoying.
            if (isset($results[$this->primaryKey])) {
                foreach ($this->enc_fields as $field) {
                    if(isset($results[$field])) {
                        $results[$field] = $this->decrypt($results[$field]);
                    }
                }
            }
            else {
                foreach($results as $key => $val){
                    foreach ($this->enc_fields as $field) {
                        if(isset($results[$key][$this->alias][$field])){
                        $results[$key][$this->alias][$field] = $this->decrypt($results[$key][$this->alias][$field]);
                        }
                    }
                }
            }
        }
        return $results;
    }
    
    function encrypt($data) {
        if (Configure::read('Encryption.enabled')) {
            $key = Configure::read('Encryption.key');
            $prefix = Configure::read('Encryption.prefix');
            $cipher = Configure::read('Encryption.cipher');
            $data = $prefix.$data;
            $result = openssl_encrypt($data,$cipher,$key);
            return $result;
        }
        else {
            return $data;
        }
    }
    
    function decrypt($data) {
        if (Configure::read('Encryption.enabled')) {
            $key = Configure::read('Encryption.key');
            $prefix = Configure::read('Encryption.prefix');
            $cipher = Configure::read('Encryption.cipher');
            $result = openssl_decrypt($data,$cipher,$key);
            if (strlen($result) >=strlen($prefix)) { //result must be equal to or longer than prefix
                if (substr($result,0,strlen($prefix)) == $prefix) { //result must have prefix included
                    return substr($result,strlen($prefix));
                }
                else { //returned data did not contain encryption prefix (likely not stored encrypted originally)
                    return $data;
                }
            }
            else { //returned data not long enough to contain encrypted data
                return $data;
            }
        }
        else { //encryption not enabled, return raw data
            return $data;
        }
    }
}
