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
 * QueueStatistics Controller
 *
 * @property QueueStatistic $QueueStatistic
 */
class QueueStatisticsController extends AppController {
	public $paginate = array(
		'limit' => 3000,
		'maxLimit' => 3000
	);


/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->QueueStatistic->recursive = 0;
		$this->set('queueStatistics', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->QueueStatistic->id = $id;
		if (!$this->QueueStatistic->exists()) {
			throw new NotFoundException(__('Invalid queue statistic'));
		}
		$this->set('queueStatistic', $this->QueueStatistic->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->QueueStatistic->create();
			if ($this->QueueStatistic->save($this->request->data)) {
				$this->Session->setFlash(__('The queue statistic has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The queue statistic could not be saved. Please, try again.'));
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
		$this->QueueStatistic->id = $id;
		if (!$this->QueueStatistic->exists()) {
			throw new NotFoundException(__('Invalid queue statistic'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->QueueStatistic->save($this->request->data)) {
				$this->Session->setFlash(__('The queue statistic has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The queue statistic could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->QueueStatistic->read(null, $id);
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
		$this->QueueStatistic->id = $id;
		if (!$this->QueueStatistic->exists()) {
			throw new NotFoundException(__('Invalid queue statistic'));
		}
		if ($this->QueueStatistic->delete()) {
			$this->Session->setFlash(__('Queue statistic deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Queue statistic was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
	public function report($queue) {
		$this->QueueStatistic->recursive = 0;
		$end_date = date('Y-m-d');
		$start_date = date('Y-m-d', strtotime("-12 hour"));
		
		if (trim($this->data['cv_start'])) $start_date = $this->data['cv_start'];
		if (trim($this->data['cv_end'])) $end_date = $this->data['cv_end'];
		$conditions = array('queue' => $queue, "created >= '$start_date 00:00:00' and created <= '$end_date 23:59:59'");

		$this->paginate = array('conditions' => $conditions, 'order' => 'id DESC', 'limit' => 3000, 'maxLimit' => 3000);
		$statistics = $this->paginate('QueueStatistic');
		//FireCake::log(count($statistics));
		//FireCake::log($this->paginate);
		$data = Set::extract($statistics, '{n}.QueueStatistic');
		
		$this->set('statistics', array_reverse($data));	
	}
	
	function queues() {
		$this->loadModel('AsteriskQueue');
		$queues = $this->AsteriskQueue->find('all');
		$q_array = array();
		$q_list = array(); // list of queues keyed by queue number
		foreach ($queues as $queue) {
			$q_array[] = $queue['AsteriskQueue']['extension'];
			$q_list[$queue['AsteriskQueue']['extension']] = $queue['AsteriskQueue']['descr'];
		}
		$q_string = implode (',', $q_array);
		$conditions = array("queue in ($q_string)");
		$res = $this->QueueStatistic->find('all', array('conditions' => $conditions, 'order' => 'id DESC', 'limit' => count($q_array) * 2));
		$rows = array();
		$found = array();
		foreach ($res as $row) {
			if (!in_array($row['QueueStatistic']['queue'], $found)) {
				$row['QueueStatistic']['description'] = $q_list[$row['QueueStatistic']['queue']];
				$rows[$row['QueueStatistic']['queue']] = $row['QueueStatistic'];
				$found[] = $row['QueueStatistic']['queue'];
			}
		}
		krsort($rows);
		$this->set('queues', $rows);
	}
}
