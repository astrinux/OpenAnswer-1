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
/**
 * Operators Controller
 *
 * @property Operator $Operator
 */
class OperatorsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Operator->recursive = 0;
		$this->set('operators', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Operator->id = $id;
		if (!$this->Operator->exists()) {
			throw new NotFoundException(__('Invalid operator'));
		}
		$this->set('operator', $this->Operator->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Operator->create();
			if ($this->Operator->save($this->request->data)) {
				$this->Session->setFlash(__('The operator has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The operator could not be saved. Please, try again.'));
			}
		}
		$queues = $this->Operator->Queue->find('list');
		$this->set(compact('queues'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Operator->id = $id;
		if (!$this->Operator->exists()) {
			throw new NotFoundException(__('Invalid operator'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Operator->save($this->request->data)) {
				$this->Session->setFlash(__('The operator has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The operator could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Operator->read(null, $id);
		}
		$queues = $this->Operator->Queue->find('list');
		$this->set(compact('queues'));
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
		$this->Operator->id = $id;
		if (!$this->Operator->exists()) {
			throw new NotFoundException(__('Invalid operator'));
		}
		if ($this->Operator->delete()) {
			$this->Session->setFlash(__('Operator deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Operator was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
