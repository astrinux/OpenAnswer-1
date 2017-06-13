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

App::uses('AppController', 'Controller');

class DidNumbersEntriesController extends AppController {
	public $components = array('RequestHandler');
	public $helpers = array('Js');  

  public function add($did_id,$number) {
    if ($this->isAuthorized('DidnumbersentriesAdd')) {
      $all_numbers = $this->DidNumbersEntry->find('list', array('conditions' => array('did_id' => $did_id), 'fields' => array('id', 'number')));
      $this->set('all_numbers', $all_numbers);  		  	
  	  $this->Session->setFlash(__('You are not authorized to perform this operation.'));      
    }	  
    
	  $conditions = array('DidNumber.deleted' => '0', 'number' =>  $number);
	  $duplicates = $this->DidNumbersEntry->find('all', array('recursive' => 0, 'conditions' => $conditions));
	  if ($duplicates) {
			$this->Session->setFlash(__('The phone number you specified is already used on another account, please specify a different phone number.'));
			
	  }
	  else {
	  	$did = $this->DidNumbersEntry->DidNumber->findById($did_id);
	  	$entry['did_id'] = $did_id;
	  	$entry['number'] = preg_replace('/[^0-9]/', '', $number);
	  	$this->DidNumbersEntry->create();
	  	$this->DidNumbersEntry->save($entry);
			$this->_saveChanges($number . " added", '', '', $did['DidNumber']['account_id'], $did_id, 'did', 'text');  		  	
			$this->Session->setFlash(__('The phone number has been added.'));
	  }
    $all_numbers = $this->DidNumbersEntry->find('all', array('conditions' => array('did_id' => $did_id)));
      $this->set('all_numbers', $all_numbers);  		  	

	  $this->render('index');
  }
  
  public function index($did_id) {
    $all_numbers = $this->DidNumbersEntry->find('list', array('conditions' => array('did_id' => $did_id), 'fields' => array('id', 'number')));
    $this->set('all_numbers', $all_numbers);
  }
  
  public function delete($id) {
    if (!$this->isAuthorized('DidnumbersentriesDelete')) {
  	  $this->Session->setFlash(__('You are not authorized to perform this operation.'));      
  	  return;
    }	  
    
  	$old = $this->DidNumbersEntry->findById($id);
		if ($this->DidNumbersEntry->delete($id)) {
  		$this->Session->setFlash(__('The phone number has been deleted.'));
  		$this->_saveChanges($old['DidNumbersEntry']['number'] . " deleted", '', '', $old['DidNumber']['account_id'], $old['DidNumbersEntry']['did_id'], 'did', 'text');
		}
		else {
  				$this->Session->setFlash(__('Cannot delete the phone number.'));
		}
    $all_numbers = $this->DidNumbersEntry->find('all', array('conditions' => array('did_id' => $old['DidNumbersEntry']['did_id'])));
      $this->set('all_numbers', $all_numbers);  		  	

	  $this->render('index');		
  }
  
}
