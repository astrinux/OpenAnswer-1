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

class QueuesController extends AppController {
	public $components = array('RequestHandler');
	public $helpers = array('Js');  
  	
	public function index() {
		$this->Queue->recursive = 0;
		$this->set('queues', $this->Queue->find('all'));
	}


	public function edit($id = null) {
    if (!$id) {
			$this->Session->setFlash(__('You must specify a queue to edit.'), 'flash_jsonbad');
			$this->render('/Elements/json_result');
    }
    $this->loadModel('UsersQueue');
		if ($this->request->is('post') || $this->request->is('put')) {
		  $data = array();
		  $no_data = true;
		  if (isset($this->request->data['Misc'])) {
		    $no_data = false;
  		  foreach ($this->request->data['Misc'] as $k => $u) {
  		    $row['user_id'] = $u;
  		    $row['queue'] = $id;
  		    $row['penalty'] = $this->request->data['Penalty'][$k];
  		    $data[] = $row;
  		    
  		  }
		  }

		  $this->UsersQueue->deleteAll(array('queue' => $id), false);
			if ($no_data) {
				$this->Session->setFlash(__('The changes have been saved'), 'flash_jsongood');
			} 
			else if ($this->UsersQueue->saveMany($data)){
				$this->Session->setFlash(__('The changes have been saved'), 'flash_jsongood');
			} 
			else {
				$this->Session->setFlash(__('The changes could not be saved. Please, try again.'), 'flash_jsonbad');
			}
			$this->render('/Elements/json_result');
		} else {
			$this->request->data = $this->Queue->read(null, $id);
      $this->loadModel('User');
  		$users = $this->User->fetchCCStaff();
			$this->set('users', $users);
      $conditions = array('queue' => $id);
  		$data = $this->UsersQueue->find('all', array('conditions' => $conditions, 'recursive' => 0));
  		$members = array();
  		$penalty = array();
  		foreach($data as $k => $m) {
  		  $members[] = $m['UsersQueue']['user_id'];
  		  $penalty[] = $m['UsersQueue']['penalty'];
  		}
  		$this->set('members', $members);
  		$this->set('penalty', $penalty);

		}


	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Queue->id = $id;
		if (!$this->Queue->exists()) {
			throw new NotFoundException(__('Invalid queue'));
		}
		if ($this->Queue->delete()) {
			$this->Session->setFlash(__('Queue deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Queue was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
