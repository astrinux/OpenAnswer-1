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
class EmployeesContactController extends AppController {
  public $paginate;
	public $components = array('RequestHandler');
	public $helpers = array('Js');

  public function index($account_id) {

	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		/*$this->Employee->id = $id;
		if (!$this->Employee->exists()) {
			throw new NotFoundException(__('Invalid ccact call log'));
		}
		$this->set('Employee', $this->Employee->read(null, $id));*/
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		/*if ($this->request->is('post')) {
			$this->Employee->create();
			print_r($this->request->data); exit;
			if ($this->Employee->save($this->request->data)) {
				$this->Session->setFlash(__('The ccact call log has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ccact call log could not be saved. Please, try again.'));
			}
		}*/
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Employee->id = $id;
		if (!$this->Employee->exists()) {
			throw new NotFoundException(__('Invalid ccact call log'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Employee->save($this->request->data)) {
				$this->Session->setFlash(__('Your changes have been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The changes could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Employee->read(null, $id);
			FireCake::log($this->request->data);
		}
	}

  public function checkDelete($id) {
    $contact = $this->EmployeesContact->findById($id);
    if ($contact) {
      $did_id = $contact['EmployeesContact']['did_id'];
		  // check if employee is in use in calltype instructions, on-call lists, and message_summary
		  $call_types = array();
		  $call_lists = array();
		  $summaries = array();
		  $calendars = array();
		  $today = date('Y-m-d H:i:s');
		  
		  $data = $this->EmployeesContact->query("select a.eid, a.schedule_id, c.title from ".OA_TBL_PREFIX."actions a left join ccact_schedules s on a.schedule_id=s.id left join ".OA_TBL_PREFIX."calltypes c on c.id=s.calltype_id where s.deleted='0' and a.did_id='". $did_id."'");
		  $data2 = $this->EmployeesContact->query("select s.id, s.employee_ids, c.title from ".OA_TBL_PREFIX."call_lists_schedules s left join ".OA_TBL_PREFIX."call_lists c on c.id=s.call_list_id where s.deleted='0' and s.did_id='". $did_id."' and ((s.start_date is null and s.end_date is null) || (s.start_date <= '$today' and s.end_date >='$today'))");
		  $data3 = $this->EmployeesContact->query("select id, employee_contact_ids from ".OA_TBL_PREFIX."messages_summary s where did_id='". $did_id."' and deleted='0'");    

			if (Configure::read('calendar_enabled')) {      
		    $data4 = $this->EmployeesContact->query("select p.id, s.name, contact_id from ".OA_TBL_PREFIX."ea_providers p left join ".OA_TBL_PREFIX."ea_services s on s.id=p.service_id where s.did_id='$did_id' and s.deleted='0' and p.deleted='0'");    
		  }
		  else {
		    $data4 = array();
		  }
      // check if employee is in use

			$employee_in_use = false;
			foreach ($data as $e) {
				if ($e['a']['eid'] && $e['a']['eid'] != 'ALL') {
					$e_array = explode(',', $e['a']['eid']);
          if (in_array($id, $e_array)) {
            $employee_in_use = true;
            $call_types[] = "Calltype: " . $e['c']['title'];
          }
				}
			}
			foreach ($data2 as $e) {
				if ($e['s']['employee_ids'] && $e['s']['employee_ids'] != 'ALL') {
					$e_array = explode(',', $e['s']['employee_ids']);
          if (in_array($contact['EmployeesContact']['employee_id'], $e_array)) {
            $employee_in_use = true;
            $call_lists[] = "On-call: " . $e['c']['title'];
          }
				}
			}
			foreach ($data3 as $e) {
			  if (!empty($e['s']['employee_contact_ids'])) {
			    
					$e_array = explode(',', $e['s']['employee_contact_ids']);
          if (in_array($id, $e_array)) {
            $employee_in_use = true;
            $summaries[] = "Msg Summary#: " . $e['s']['id'];
          }
        }
			}			
			foreach ($data4 as $e) {
            $ids = explode(',', $e['p']['contact_id']);
          	if (in_array($id, $ids)) {
                  $employee_in_use = true;
                  if (!in_array("Calendar: " . $e['s']['name'], $calendars)) $calendars[] = "Calendar: " . $e['s']['name'];
            }
			}						
  		
  		$where = array_merge($call_types, $call_lists, $summaries, $calendars);
  		if ($employee_in_use) {
        $this->Session->setFlash('Cannot delete this contact information, it is used in the calltype/ on-call list/ message summary shown below: <br><br><b>' . implode('<br>', $where) . '</b>', 'flash_jsonbad');
  		}		  
  		else {
        $this->Session->setFlash('Safe to delete', 'flash_jsongood');
  		}
		}
		else {
      $this->Session->setFlash('Cannot find the contact information', 'flash_jsonbad');	 		      
		}
    $this->render('/Elements/json_result');		  
  }

  public function testText($number, $carrier) {
    $this->loadModel('SmsCarrier');
    $c = $this->SmsCarrier->findById($carrier);
    $textnum = $c['SmsCarrier']['prefix'] .  trim($number) . "@" . $c['SmsCarrier']['addr']; 
    if ($this->_sendemail("Test Message", "Looks like you picked the right carrier: " . $c['SmsCarrier']['name'], $textnum, 'text')) {
      $this->Session->setFlash('Test message sent', 'flash_jsongood');
    }    
    else {
      $this->Session->setFlash('Test message cannot be sent', 'flash_jsonbad');	 		      
    }
        
    $this->render('/Elements/json_result');		      
    
  }
  
	public function delete($id = null) {
/*		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}*/
		$this->Employee->id = $id;
		if (!$this->Employee->exists()) {
			throw new NotFoundException(__('Invalid contact'));
		}
		if ($this->Employee->delete()) {
		  $msg = 'Contact was successfully delete';
		  $success = true;
		}
		else {
		  $msg = 'Cannot delete contact';
		  $success = false;
		}
    $this->set('success', $success);
    $this->set('msg', $msg);
    $this->render('result');
	}
}
