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
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
/**
 * Users Controller
 *
 * @property User $User
 */
class RolesController extends AppController {
	public $components = array('RequestHandler');
	public $helpers = array('Js');
	public $paginate;
	public function beforeFilter() {
		parent::beforeFilter();
	}
	
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Role->recursive = 0;
		$roles = $this->paginate();
		$this->set(array(
			'roles' => $roles, 
			'_serialize' => array('roles')
		));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->loadModel('Queue');
		
		$queues = $this->Queue->find('all', array('fields' => array('extension', 'descr')));
		$this->set('queues', $queues);	 
		$this->set('users_queues', array());	   
		$this->loadModel('Role');
		$roles = $this->Role->find('list', array('fields' => array('id', 'role')));
		$this->set('roles', $roles);	 
		
		if ($this->request->is('post')) {
		  $conditions = array('deleted' => '0', 'username' => trim($this->request->data['User']['username']));
		  $existing_user = $this->User->find('first', array('conditions' => $conditions));
		  if ($existing_user) {
					$this->Session->setFlash(__('The username \''.$this->request->data['User']['username'].'\' is not available, please select another.'), 'flash_jsonbad');
					$this->render('/Elements/json_result');
					return;
		  }
			
			$passwordHasher = new SimplePasswordHasher();
			$this->request->data['User']['password'] = $passwordHasher->hash(
					$this->request->data['User']['password']
			);
								
			if (isset($this->request->data['Queue'])) {

				foreach($this->request->data['Queue'] as $q) {
					$row  = array('queue' => $q);
					$this->request->data['UsersQueue'][] = $row;
				}			
			}
			$this->User->create();
			if ($this->User->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'), 'flash_jsongood');
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'flash_jsonbad');
			}
			$this->render('/Elements/json_result');
		}
	}

	
/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    
    public function edit($role_id = null) {
        $checked = array();
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Role->save($this->request->data)) {
                $this->Session->setFlash('The role has been updated','flash_jsongood');
                $this->render('/Elements/json_result');
            }
            else {
                $this->Session->setFlash('The role has not been updated','flash_jsonbad');
                $this->render('/Elements/json_result');
            }
        }
        else {
            $this->request->data = $this->Role->findById($role_id);
            if (isset($this->request->data['Permission'])) {
                foreach ($this->request->data['Permission'] as $rolepermission) {
                    $checked[$rolepermission['id']] = 'checked';
                }
            }
            //$perms = $this->Permission->find('list',array('fields' => array('Permission.id','Permission.shortname','Permission.desc'),'recursive' => 0));
            $this->loadModel('Permission');
            $perms = $this->Permission->find('all',array('recursive' => 0));
            $this->set('perms',$perms);
            $this->log($this->request->data);
            $this->log($checked);
            $this->set('checked',$checked);
            //$this->log($perms);
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
        $this->Role->delete($id);
			
        $this->Session->setFlash('Role deleted', 'flash_jsongood');
		$this->render('/Elements/json_result');		
	}
	
	
}
