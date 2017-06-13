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
 * Prompts Controller
 *
 * @property Prompt $Prompt
 */
class PromptsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Prompt->recursive = 0;
		$this->set('Prompts', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Prompt->id = $id;
		if (!$this->Prompt->exists()) {
			throw new NotFoundException(__('Invalid ccact prompt'));
		}
		$this->set('Prompt', $this->Prompt->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Prompt->create();
			if ($this->Prompt->save($this->request->data)) {
				$this->Session->setFlash(__('The ccact prompt has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ccact prompt could not be saved. Please, try again.'));
			}
		}
		$ccactClients = $this->Prompt->CcactClient->find('list');
		$this->set(compact('ccactClients'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Prompt->id = $id;
		if (!$this->Prompt->exists()) {
			throw new NotFoundException(__('Invalid ccact prompt'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Prompt->save($this->request->data)) {
				$this->Session->setFlash(__('The ccact prompt has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ccact prompt could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Prompt->read(null, $id);
		}
		$ccactClients = $this->Prompt->CcactClient->find('list');
		$this->set(compact('ccactClients'));
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
		$this->Prompt->id = $id;
		if (!$this->Prompt->exists()) {
			throw new NotFoundException(__('Invalid ccact prompt'));
		}
		if ($this->Prompt->delete()) {
			$this->Session->setFlash(__('Ccact prompt deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Ccact prompt was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
