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
class MessagesEventsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->MessagesEvent->recursive = 0;
		$this->set('MessagesEvents', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->MessagesEvent->id = $id;
		if (!$this->MessagesEvent->exists()) {
			throw new NotFoundException(__('Invalid messages event'));
		}
		$this->set('MessagesEvent', $this->MessagesEvent->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add($aid) {
		$this->layout = 'plain';
		$this->loadModel('Message');
		
		if ($this->request->is('post')) {
			$data['MessagesEvent']['call_id'] = $this->request->data['callId'];
			if (!$this->request->data['msgId']) {
				$data['Message']['account_id'] = $aid;
				$data['Message']['operator_id'] = $this->user_extension;
				$data['Message']['user'] = AuthComponent::user('username');
				$data['Message']['call_id'] = $this->request->data['callId'];
				$data['Message']['calltype'] = '';
				$this->Message->create();
				$this->Message->save($data['Message']);
				$msgId = $this->Message->getLastInsertID();
			}
			else $msgId = $this->request->data['msgId'];
			
			if ($msgId) {
				$data['MessagesEvent']['message_id'] = $msgId;
				$data['MessagesEvent']['account_id'] = $aid;
				$data['MessagesEvent']['operator_id'] = $this->user_extension;
				$data['MessagesEvent']['user'] = AuthComponent::user('username');
				$data['MessagesEvent']['description'] = $this->request->data['event'];

				$this->MessagesEvent->create();
				if ($this->MessagesEvent->save($data['MessagesEvent'])) {
					$result = array('success' => true, 'msgId' => $msgId, 'msg' => 'The event has been saved');
					$this->set('result', $result);
				} else {
					$result = array('success' => false, 'msgId' => '', 'msg' => 'There was an error saving the message');
				}

			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->MessagesEvent->id = $id;
		if (!$this->MessagesEvent->exists()) {
			throw new NotFoundException(__('Invalid messages event'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->MessagesEvent->save($this->request->data)) {
				$this->Session->setFlash(__('The messages event has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The messages event could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->MessagesEvent->read(null, $id);
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
		$this->MessagesEvent->id = $id;
		if (!$this->MessagesEvent->exists()) {
			throw new NotFoundException(__('Invalid messages event'));
		}
		if ($this->MessagesEvent->delete()) {
			$this->Session->setFlash(__('messages event deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('messages event was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
