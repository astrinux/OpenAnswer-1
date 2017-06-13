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
class CallListsScheduleController extends AppController {
	public $paginate;
	
/**
 * index method
 *
 * @return void
 */
	public function index($did_id) {
		if (!$did_id) {
			$this->Session->setFlash(__('You must specify a DID'), 'flash_jsonbad');
			$this->render('/Elements/json_result');
		}
		$this->set('did_id', $did_id);
		$conditions = array('did_id' => $did_id, 'deleted' => 0);
		$this->paginate['conditions'] = $conditions;
		$this->CallList->recursive = 0;
		$this->set('CallLists', $this->paginate());
		
	}

/**
 * add method
 *
 * @return void
 */
	public function add($did_id, $list_id=null) {
		$this->set('did_id', $did_id);
		$this->set('list_id', $list_id);
		if (!$did_id) {
			$this->Session->setFlash(__('You must specify a DID'), 'flash_jsonbad');
			$this->render('/Elements/json_result');
		}		
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['CallListsSchedule']['did_id'] = $did_id;
			if (isset($this->request->data['Misc']['day_time_start']) && trim($this->request->data['Misc']['day_time_start'])) {		    
				$ts = strtotime("today " . $this->request->data['Misc']['day_time_start']);
				$mytime = date('H:i', $ts); 		  		  
				$this->request->data['Misc']['day_time_start'] = $mytime;	
			}
			
			if (isset($this->request->data['Misc']['day_time_end']) && trim($this->request->data['Misc']['day_time_end'])) {		    
				$ts = strtotime("today " . $this->request->data['Misc']['day_time_end']);
				$mytime = date('H:i', $ts); 		  		  
				$this->request->data['Misc']['day_time_end'] = $mytime;	
			}
			
			if (isset($this->request->data['CallListsSchedule']['mon'])) {
				if ($this->request->data['CallListsSchedule']['mon'] || $this->request->data['CallListsSchedule']['tue'] || $this->request->data['CallListsSchedule']['wed'] || $this->request->data['CallListsSchedule']['thu'] || $this->request->data['CallListsSchedule']['fri'] || $this->request->data['CallListsSchedule']['sat'] || $this->request->data['CallListsSchedule']['sun']) {
					$this->request->data['CallListsSchedule']['check_days'] = '1'; 
				}
			}
			
			// take into account day ranges that cross over to the next week, add 7 to end day in those cases 
			if (isset($this->request->data['CallListsSchedule']['start_day']) && $this->request->data['CallListsSchedule']['start_day'] && $this->request->data['CallListsSchedule']['end_day']) {
				if ($this->request->data['CallListsSchedule']['start_day'] > $this->request->data['CallListsSchedule']['end_day']) {
					$this->request->data['CallListsSchedule']['end_day'] += 7;
				}
				$this->request->data['CallListsSchedule']['start_day'] = $this->request->data['CallListsSchedule']['start_day'] . str_replace(':', '', $this->request->data['Misc']['day_time_start']);
				$this->request->data['CallListsSchedule']['end_day'] = $this->request->data['CallListsSchedule']['end_day'] . str_replace(':', '', $this->request->data['Misc']['day_time_end']);
			}

			if (isset($this->request->data['CallListsSchedule']['start_time']) && trim($this->request->data['CallListsSchedule']['start_time'])) {		    
				$ts = strtotime("today " . $this->request->data['CallListsSchedule']['start_time']);
				$mytime = date('G:i:s', $ts); 		  		  
				$this->request->data['CallListsSchedule']['start_time'] = $mytime;	
			}
			if (isset($this->request->data['CallListsSchedule']['end_time']) && trim($this->request->data['CallListsSchedule']['end_time'])) {		    
				$ts = strtotime("today " . $this->request->data['CallListsSchedule']['end_time']);
				$mytime = date('G:i:s', $ts); 		  		  
				$this->request->data['CallListsSchedule']['end_time'] = $mytime;	
			}
			if (isset($this->request->data['Misc']['date_time_start']) && trim($this->request->data['Misc']['date_time_start'])) {		    
				$ts = strtotime("today " . $this->request->data['Misc']['date_time_start']);
				$mytime = date('G:i:s', $ts); 		  		  
				$this->request->data['Misc']['date_time_start'] = $mytime;	
			}
			
			if (isset($this->request->data['Misc']['date_time_end']) && trim($this->request->data['Misc']['date_time_end'])) {		    
				$ts = strtotime("today " . $this->request->data['Misc']['date_time_end']);
				$mytime = date('G:i:s', $ts); 		  		  
				$this->request->data['Misc']['date_time_end'] = $mytime;	
			}


			if (isset($this->request->data['CallListsSchedule']['start_date']) && trim($this->request->data['CallListsSchedule']['start_date']) && trim($this->request->data['CallListsSchedule']['end_date'])) {
				$this->request->data['CallListsSchedule']['start_date'] = $this->request->data['CallListsSchedule']['start_date'] . " " . $this->request->data['Misc']['date_time_start'];
				$this->request->data['CallListsSchedule']['end_date'] = $this->request->data['CallListsSchedule']['end_date'] . " " . $this->request->data['Misc']['date_time_end'];	
			}		  
			
			$saveok = $this->CallListsSchedule->save($this->request->data['CallListsSchedule']);
			if ($saveok) {
				$this->Session->setFlash(__('Your schedule has been added'), 'flash_jsongood');
				
				$this->loadModel('Employee');
				$conditions = array('did_id' => $this->request->data['CallListsSchedule']['did_id'], 'deleted' => 0);
				$emps = $this->Employee->find('all', array('recursive' => 0, 'conditions' => $conditions));
				foreach ($emps as $k => $e) {
					$employees[$e['Employee']['id']] = $e;
				}	

				$new_schedule = $this->_getSchedule($this->request->data['CallListsSchedule'], $this->php_daysofweek);
				$new_list = $this->_getEmployeeList($this->request->data['CallListsSchedule']['employee_ids'], $employees);
				$description = 'On-Call List \'' .$this->request->data['CallList']['title'] . '\' added - ' . $new_schedule . "<br>";
				$description .= "<i>$new_list</i>";
									
				if (!empty($description)) {
					$e['call_list_id'] = $this->CallListsSchedule->getInsertID();
					$this->_saveChanges($description, '', serialize($this->request->data), '0', $this->request->data['CallList']['did_id'], 'oncall', 'add', $e);    			    
				}	
					
			
			}
			else {
				$this->Session->setFlash(__('Cannot save your changes, please try again later'), 'flash_jsonbad');
			}
			$this->render('/Elements/json_result');
		}
		else {
			$this->loadModel('Employee');
			$conditions = array('did_id' => $did_id, 'deleted' => 0);
			$this->set('employees', $this->Employee->find('all', array('conditions' => $conditions, 'order' => array('name' => 'asc'))));
			if ($list_id) {
			    $this->request->data = $this->CallListsSchedule->CallList->findById($list_id);
			    
			}			
		}
	}

	function _getEmployeeList($employee_ids, $employees) {
			$ids = explode(',', $employee_ids);
			$val = '';
			$names = array();
			foreach ($ids as $id) {
				//print_r($employees[$id]);
				if (!empty($employees[$id])) $names[] = $employees[$id]['Employee']['name'];
			}
			if (sizeof($names) > 0) {
				$val =  implode(', ', $names);
			}    
			return $val;
	}
/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if ($this->request->is('post') || $this->request->is('put')) {
			$old = $this->CallListsSchedule->findById($this->request->data['CallListsSchedule']['id']);
			$this->loadModel('Employee');
			$conditions = array('did_id' => $old['CallListsSchedule']['did_id'], 'deleted' => 0);
			$emps = $this->Employee->find('all', array('recursive' => 0, 'conditions' => $conditions));
			foreach ($emps as $k => $e) {
				$employees[$e['Employee']['id']] = $e;
			}		  
			if (!$this->isAuthorized('CalllistsscheduleEdit')) {
				$this->Session->setFlash(__('You are not allowed to make changes to this on call list'), 'flash_jsonbad');
				$this->render('/Elements/json_result');
				return;
			}


			if (isset($this->request->data['Misc']['day_time_start']) && trim($this->request->data['Misc']['day_time_start'])) {		    
				$ts = strtotime("today " . $this->request->data['Misc']['day_time_start']);
				$mytime = date('H:i', $ts); 		  		  
				$this->request->data['Misc']['day_time_start'] = $mytime;	
			}
			
			if (isset($this->request->data['Misc']['day_time_end']) && trim($this->request->data['Misc']['day_time_end'])) {		    
				$ts = strtotime("today " . $this->request->data['Misc']['day_time_end']);
				$mytime = date('H:i', $ts); 		  		  
				$this->request->data['Misc']['day_time_end'] = $mytime;	
			}
			
			// set 'check_days' to '1' if schedule is day-dependant
			if (isset($this->request->data['CallListsSchedule']['mon'])) {
				if ($this->request->data['CallListsSchedule']['mon'] || $this->request->data['CallListsSchedule']['tue'] || $this->request->data['CallListsSchedule']['wed'] || $this->request->data['CallListsSchedule']['thu'] || $this->request->data['CallListsSchedule']['fri'] || $this->request->data['CallListsSchedule']['sat'] || $this->request->data['CallListsSchedule']['sun']) {
					$this->request->data['CallListsSchedule']['check_days'] = '1'; 
					
					if (isset($this->request->data['CallListsSchedule']['start_time'])) {
						$ts1 = strtotime("today " . $this->request->data['CallListsSchedule']['start_time']);
						$ts2 = strtotime("today " . $this->request->data['CallListsSchedule']['end_time']);
						if ($ts2 < $ts1) {
							$this->Session->setFlash(__('Please specify a time range that does not cross over midnight'), 'flash_jsonbad');
							$this->render('/Elements/json_result');
							return;							
						}
					}
					
				}
			}
			// take into account day ranges that cross over to the next week 
			if (isset($this->request->data['CallListsSchedule']['start_day']) && ($this->request->data['CallListsSchedule']['start_day'] != '') && ($this->request->data['CallListsSchedule']['end_day'] != '')) {
				if ($this->request->data['CallListsSchedule']['start_day'] > $this->request->data['CallListsSchedule']['end_day']) {
					$this->request->data['CallListsSchedule']['end_day'] += 7;
				}
				$this->request->data['CallListsSchedule']['start_day'] = $this->request->data['CallListsSchedule']['start_day'] . str_replace(':', '', $this->request->data['Misc']['day_time_start']);
				$this->request->data['CallListsSchedule']['end_day'] = $this->request->data['CallListsSchedule']['end_day'] . str_replace(':', '', $this->request->data['Misc']['day_time_end']);
			}

			if (isset($this->request->data['CallListsSchedule']['start_time']) && trim($this->request->data['CallListsSchedule']['start_time'])) {		    
				$ts = strtotime("today " . $this->request->data['CallListsSchedule']['start_time']);
				$mytime = date('G:i:s', $ts); 		  		  
				$this->request->data['CallListsSchedule']['start_time'] = $mytime;	
			}
			if (isset($this->request->data['CallListsSchedule']['end_time']) && trim($this->request->data['CallListsSchedule']['end_time'])) {		    
				$ts = strtotime("today " . $this->request->data['CallListsSchedule']['end_time']);
				$mytime = date('G:i:s', $ts); 		  		  
				$this->request->data['CallListsSchedule']['end_time'] = $mytime;	
			}
			if (isset($this->request->data['Misc']['date_time_start']) && trim($this->request->data['Misc']['date_time_start'])) {		    
				$ts = strtotime("today " . $this->request->data['Misc']['date_time_start']);
				$mytime = date('G:i:s', $ts); 		  		  
				$this->request->data['Misc']['date_time_start'] = $mytime;	
			}
			
			if (isset($this->request->data['Misc']['date_time_end']) && trim($this->request->data['Misc']['date_time_end'])) {		    
				$ts = strtotime("today " . $this->request->data['Misc']['date_time_end']);
				$mytime = date('G:i:s', $ts); 		  		  
				$this->request->data['Misc']['date_time_end'] = $mytime;	
			}

			if (isset($this->request->data['CallListsSchedule']['start_date']) && trim($this->request->data['CallListsSchedule']['start_date']) && trim($this->request->data['CallListsSchedule']['end_date'])) {
				$this->request->data['CallListsSchedule']['start_date'] = $this->request->data['CallListsSchedule']['start_date'] . " " . $this->request->data['Misc']['date_time_start'];
				$this->request->data['CallListsSchedule']['end_date'] = $this->request->data['CallListsSchedule']['end_date'] . " " . $this->request->data['Misc']['date_time_end'];	
			}
			if (empty($this->request->data['CallListsSchedule']['legacy_list'])) {
				$this->request->data['CallListsSchedule']['legacy'] = '0';
			}
			if ($this->CallListsSchedule->CallList->save($this->request->data['CallList']) && $this->CallListsSchedule->save($this->request->data['CallListsSchedule'])) {
				$old_schedule = $this->_getSchedule($old['CallListsSchedule'], $this->php_daysofweek);
				$this->request->data['CallListsSchedule']['starttime'] = $old['CallListsSchedule']['start_time_f'];
				$this->request->data['CallListsSchedule']['endtime'] = $old['CallListsSchedule']['end_time_f'];
				$new_schedule = $this->_getSchedule($this->request->data['CallListsSchedule'], $this->php_daysofweek);
				$old_list = $this->_getEmployeeList($old['CallListsSchedule']['employee_ids'], $employees);
				$new_list = $this->_getEmployeeList($this->request->data['CallListsSchedule']['employee_ids'], $employees);

				$description = '';
				if ($old_schedule != $new_schedule) {
					$description .= 'On-Call List \'' .$old['CallList']['title'] . '\' edited - ' . $new_schedule . "<br>";
					$description .= "Schedule from <i>$old_schedule</i> to <i>$new_schedule</i>";
					
				}
				
				if ($old_list != $new_list) {
					if (empty($description)) {
						$description .= 'On-Call List \'' .$old['CallList']['title'] . '\' edited - ' . $new_schedule . "<br>";
					}
					else $description .= "<br>";
					$description .= "List from <i>$old_list</i> to <i>$new_list</i>";
					
				}
				
				if (!empty($description)) {
					$e['call_list_id'] = $old['CallListsSchedule']['id'];
					$this->_saveChanges($description, serialize($old), serialize($this->request->data), $old['CallList']['account_id'], $old['CallList']['did_id'], 'oncall', 'edit', $e);    			    
				}
				
				$this->Session->setFlash(__('Your changes have been saved'), 'flash_jsongood');
			} else {
				$this->Session->setFlash(__('The changes could not be saved. Please, try again.'), 'flash_jsonbad');
			}
			$this->render('/Elements/json_result');
		}  // end if data posted
		else {
			$this->CallListsSchedule->id = $id;
			$schedule = $this->CallListsSchedule->find('first', array('conditions' => array('CallListsSchedule.id' => $id), 'recursive' => 1));
			$this->request->data = $schedule;
					if ($schedule['CallListsSchedule']['start_day']) {
						FireCake::log('start_day');
						$day = substr($schedule['CallListsSchedule']['start_day'], 0, -4);
						$ts = strtotime("today " . substr($schedule['CallListsSchedule']['start_day'], -4, 2) . ":" . substr($schedule['CallListsSchedule']['start_day'], -2) . ":00");
						$mytime = date('g:ia', $ts); 		  		  
						$this->request->data['CallListsSchedule']['start_day'] = $day;
						$this->request->data['Misc']['day_time_start'] = $mytime;
					}
					if ($schedule['CallListsSchedule']['end_day']) {
						$day = substr($schedule['CallListsSchedule']['end_day'], 0, -4);
						if ($day > 7) $day -= 7;
						$ts = strtotime("today " . substr($schedule['CallListsSchedule']['end_day'], -4, 2) . ":" . substr($schedule['CallListsSchedule']['end_day'], -2) . ":00");
						$mytime = date('g:ia', $ts); 		  		  
						$this->request->data['CallListsSchedule']['end_day'] = $day;
						$this->request->data['Misc']['day_time_end'] = $mytime;
					}
					if ($schedule['CallListsSchedule']['start_date']) {
						$this->request->data['CallListsSchedule']['start_date'] = substr($schedule['CallListsSchedule']['start_date_f'], 0, 10);
						$this->request->data['Misc']['date_time_start'] = strtolower(substr($schedule['CallListsSchedule']['start_date_f'], -7));
						
					}
					if ($schedule['CallListsSchedule']['end_date']) {
						$this->request->data['CallListsSchedule']['end_date'] = substr($schedule['CallListsSchedule']['end_date_f'], 0, 10);
						$this->request->data['Misc']['date_time_end'] = strtolower(substr($schedule['CallListsSchedule']['end_date_f'], -7));
					}


			$did_id = $this->request->data['CallListsSchedule']['did_id'];
			$this->set('did_id', $did_id);
			$this->set('id', $id);
			$this->loadModel('Employee');
			$conditions = array('did_id' => $did_id, 'deleted' => 0);
			$emps = $this->Employee->find('all', array('conditions' => $conditions, 'order' => array('name' => 'asc')));
			foreach ($emps as $k => $e) {
				$employees[$e['Employee']['id']] = $e;
			}
			$this->set('employees', $employees);
			
		}
	}

	function time_format_mysql(&$input) {
		preg_match('/([0-9]{1,2}):([0-9]{2})(am|pm){1}/', $input, $matches);
		if (sizeof($matches) && $matches[3] == 'am') $input = sprintf("%02d", $matches[1]) . ':' . $matches[2];
		else if (sizeof($matches) && $matches[3] == 'pm') $input = sprintf("%02d", ($matches[1]+12)) . ':' . $matches[2];
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
/*	  $data['CallList']['id'] = $id;
		$data['CallList']['deleted'] = '1';
		$data['CallList']['deleted_ts'] = date('Y-m-d H:i:s');
		if ($this->CallList->save($data['CallList'])) {
			$this->Session->setFlash(__('The list has been deleted'), 'flash_jsongood');
		}
		else $this->Session->setFlash(__('Cannot delete list, please try again later'), 'flash_jsonbad');
		$this->render('/Elements/json_result');*/
	}
	
}
