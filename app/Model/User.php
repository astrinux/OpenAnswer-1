<?php
/**
 *
 * @author          VoiceNation, LLC
 * @copyright       2015-2016, VoiceNation LLC
 * @link            http://www.voicenation.com
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU Affero General Public License as
 *   published by the Free Software Foundation, either version 3 of the
 *   License, or (at your option) any later version.

 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Affero General Public License for more details.

 *   You should have received a copy of the GNU Affero General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

App::uses('OaModel', 'Model');
/**
 * User Model
 *
 */
class User extends OaModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

  function hashPasswords($data) {
    return $data;
  }
  
  function fetchCustomers() {
    $conditions = array('deleted' => 0);
    $order = array('firstname' => 'asc');
    return $this->find('all', compact('conditions', 'order'));
  }
  
  function fetchCCStaff($include_deleted=false) {
    $order = array('firstname' => 'asc');
    if ($include_deleted) {
      $conditions = array("deleted_ts > '". date('Y-m-d 00:00:00', strtotime('-1 month')) ."' or deleted_ts is null");
    }
    else {
      $conditions = array('deleted' => 0);
    }
    return $this->find('all', array('conditions' => $conditions, 'order' => $order));
  }
  
  function getCCStaff($include_deleted = false, $all_fields = false) {
    $data = $this->fetchCCStaff($include_deleted);
    foreach ($data as $o) {
      if ($all_fields) 
        $operators[trim($o['User']['id'])] = $o['User'];
      else 
        $operators[trim($o['User']['id'])] = trim($o['User']['firstname']) . " " . trim($o['User']['lastname']);
    }
    return $operators;
  }	
  
  function getCCStaffUsernames($include_deleted = false, $all_fields = false) {
    $data = $this->fetchCCStaff($include_deleted);
    foreach ($data as $o) {
      if ($all_fields) 
        $operators[trim($o['User']['id'])] = $o['User'];
      else 
        $operators[trim($o['User']['id'])] = trim($o['User']['username']);
    }
    return $operators;
  }	  
     
  function getOperatorArrayByName($include_deleted = false) {
    $data = $this->fetchCCStaff($include_deleted);
    foreach ($data as $o) {
      $operator = trim($o['User']['firstname']) . " " . trim($o['User']['lastname']);
      $operator = preg_replace('/[^a-zA-Z ]/', '', $operator);
      $operators[$operator] = array('id' => $o['User']['id'], 'user_username' => $o['User']['username']);
    }
    return $operators;

  }	      
}
