<?php
/**
 *
 * @author                 VoiceNation, LLC
 * @copyright               2015-2016, VoiceNation LLC
 * @link                        http://www.voicenation.com
 *
 *   This   program is free software:   you can redistribute it and/or modify
 *   it under   the terms   of the GNU Affero   General Public License as
 *   published by   the Free Software   Foundation, either version 3 of the
 *   License, or (at your option) any later   version.

 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY;   without even the implied warranty   of
 *   MERCHANTABILITY or FITNESS FOR A   PARTICULAR PURPOSE.  See the
 *   GNU Affero General Public License for more details.

 *   You should have received   a   copy of the GNU Affero General Public   License
 *   along with this program.    If not, see <http://www.gnu.org/licenses/>.
 */

App::uses('AppController', 'Controller');
/**
 * Schedules Controller
 *
 * @property Schedule $Schedule
 */
class SchedulesController extends AppController   {
    public $paginate;

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('cleanup');
	}
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Schedule->recursive = 0;
		$this->set('Schedules', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param   string $id
 * @return void
 */
	public function view($id = null) {
		$this->Schedule->id = $id;
		if (!$this->Schedule->exists()) {
			throw new NotFoundException(__('Invalid schedule'));
		}
		$this->set('Schedule', $this->Schedule->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add()   {

	}

	 
	public function edit($sid = null)   {
		$this->loadModel('DidNumber');
		// make the various action types available from the view
		$this->set('dashboard_actions', $this->dashboard_actions);
		$this->set('wizard_actions', $this->wizard_actions);
		$this->set('all_actions', $this->all_actions);
		$this->set('actionbox_actions', $this->global_options['actions']);
		$prompts = array();
		foreach ($this->global_options['prompts'] as $p) {
			$prompts[] = $p['description'];
		}
		$this->set('prompts', $prompts);
		$this->Schedule->id = $sid;
		
		// check if the specified schedule exists
		if (!$this->Schedule->exists()) {
			$this->Session->setFlash(__('The schedule could not be found. Please, try again.'));
			$this->render('/Elements/html_result');
			return;
		}
		
	
		// check if changes were posted
		if ($this->request->is('post') || $this->request->is('put')) {
			// don't allow operators to make changes
			//if (!$this->isAuthorized('SchedulesEdit')) {
				//$this->Session->setFlash(__('You are not allowed to make changes to this calltype'), 'flash_jsonbad');
				//$this->render('/Elements/json_result');
				//return;
			//}
			
			// initialize variables to keep track of what sort of actions are in this calltype
			$type_transfer = false;
			$type_blindtransfer = false;
			$type_lmr = false;
			$type_web = false;
			$type_calendar = false;
			
			$type_msg = false;
			$type_info = false;
			$type_dispatch = false;
			$type_deliver = false;
			$type_hold = false;
			$save_not_ok = false;
			$includes_dispatching = false;
			$type_ts = false;
			
			// now work on saving the action steps of the calltype instructions
			foreach ($this->request->data['Action'] as $idx => $a) {			
				$action_type = $a['action_type'];
				$dispatch_only = $a['dispatch_only'];
				
				// ignore dispatcher actions in trying to figure out what type of calltype this is.
				if (!$dispatch_only) {				
					if ($action_type == ACTION_TXF || $action_type == ACTION_TXF_DELIVER || $action_type == ACTION_VMOFFER || $action_type == ACTION_VM || $action_type == ACTION_VM_DELIVER  || $action_type == ACTION_VMOFFER_DELIVER || $action_type == ACTION_TXF_NO_ANNOUNCEMENT || $action_type == ACTION_TXF_NO_ANNOUNCEMENT_DELIVER ) {
					$type_transfer = true;
					}
					else if ($action_type == ACTION_BLINDTXF || $action_type == ACTION_BLINDTXF_DELIVER) {
					$type_blindtransfer = true;
					}
					if ($action_type == ACTION_DISPATCH) {
					$type_dispatch = true;
					}    
					if ($action_type == ACTION_DELIVER) {
					$type_deliver = true;
					}                   
					if ($action_type == ACTION_HOLD) {
					$type_hold = true;
					}
					if ($action_type == ACTION_FAX || $action_type == ACTION_FAX_DELIVER) {
					$type_msg = true;
					}          
					else if ($action_type == ACTION_TXTMSG || $action_type == ACTION_EMAIL || $action_type == ACTION_TEXT_DELIVER || $action_type == ACTION_EMAIL_DELIVER) {
					$type_msg = true;
					}
					else if ($action_type == ACTION_LMR || $action_type == ACTION_LMR_DELIVER) {
					$type_lmr = true;
					}
					else if ($action_type == ACTION_WEB) $type_web = true;
					else if ($action_type == ACTION_CALENDAR) $type_calendar = true;
				}
			}
			
			// find original schedule   so we   can compare what has changed
			$originalSchedule = $this->Schedule->find('first', array('conditions' => array('id' => $sid), 'recursive' => true));
			$old_id = $originalSchedule['Schedule']['id'];
			$old_instructions = $this->_getInstructions($originalSchedule['Schedule']['did_id'], $sid, true);
			
			
			// make a   copy of the original schedule.  When a schedule is modified, OA
			// actually creates a   new schedule and soft-deletes the old.  This will allow changes to be   
			// undone   easily          
			$newSchedule = array();
			$newSchedule['Schedule'] = $originalSchedule['Schedule'];
			$newSchedule['Schedule']['active'] = '0';   // keep the new schedule inactive for now
			$newSchedule['Action'] = $this->request->data['Action'];

			// find all active schedules for the calltype to figure out it if it time sensitive
			$all_schedules = $this->Schedule->find('all', array('conditions' => array('calltype_id' => $originalSchedule['Schedule']['calltype_id'], 'deleted' => '0', 'active' => '1')));
			if ($all_schedules &&   count($all_schedules) > 1) $type_ts = true;
			else $type_ts = false;
			
			// set the type of calltype based on the actions that it contains
			if ($type_web) $newSchedule['Schedule']['type'] = 'WEB';
			else if ($type_calendar) $newSchedule['Schedule']['type'] = 'CAL';
			else if ($type_transfer || $type_lmr) $newSchedule['Schedule']['type'] = 'TXF';
			else if ($type_blindtransfer) $newSchedule['Schedule']['type'] = 'BLINDTXF';
			else if ($type_hold) $newSchedule['Schedule']['type'] = 'MSG';
			else if ($type_msg) $newSchedule['Schedule']['type'] = 'MSG';
			else if ($type_dispatch || $type_deliver) $newSchedule['Schedule']['type'] = 'MSG';
			else $newSchedule['Schedule']['type'] = 'INFO';
			
			
//          if (isset($this->request->data['Schedule']['show_employee_picker'])) $newSchedule['Schedule']['show_employee_picker'] = $this->request->data['Schedule']['show_employee_picker'];
			unset($newSchedule['Schedule']['id']);
	
			// save the newly created schedule which is currently still inactive.   
			$this->Schedule->create();
			$saveok = $this->Schedule->saveAssociated($newSchedule, array('deep' => true)); 
			// do other tasks if schedule creation was successful
			if ($saveok) {
				// fetch the id of the newly created schedule
				$sid = $this->Schedule->id;
				$this->loadModel('Section');

				foreach ($this->request->data['Section']['section_title'] as $k => $s) {
					$section['schedule_id'] = $sid;
					$section['sort'] = $this->request->data['Section']['sort'][$k];
					$section['title'] = $this->request->data['Section']['section_title'][$k];
					$section['section_action'] = $this->request->data['Section']['section_action'][$k];
					if (!empty( $this->request->data['Section']['section_num'][$k])) $section['section_num'] = $this->request->data['Section']['section_num'][$k];
					$this->Section->create();
					$this->Section->save($section);
				}
				
				// fetch account id and subaccount id
				$account_id = $originalSchedule['Schedule']['account_id'];
				$did_id = $originalSchedule['Schedule']['did_id'];
				
				if (!$this->Schedule->save($newSchedule['Schedule'])) {
					$this->Schedule->delete($sid);
					$this->Session->setFlash(__('The changes could not be saved (call type change). Please, try again.'), 'flash_jsonbad');   
				}
				else {
					// inactivate the old instructions, replaced by the new instructions
					$this->_inactivateSchedule($old_id, $sid, $this->request->data['Schedule']['active']);    
					
					// since we are modifying this calltype schedule, clear the cache that's used for the screen pop 
					// for this DID
					$this->clearDidCache($did_id);
					if ($type_ts)   {
						$this->Schedule->query("update ".OA_TBL_PREFIX . "schedules set type='TS' where deleted='0' and active='1' and calltype_id = '".$originalSchedule['Schedule']['calltype_id']."'");
					}
					
					// log the change   in the account edit table
					$new_instructions = $this->_getInstructions($did_id, $sid, true);                   
					if ($old_instructions != $new_instructions || $this->request->data['Schedule']['active'] != $originalSchedule['Schedule']['active']) {
						$changes = array('label' => array('schedule ID'), 'old_values' => array($old_id), 'new_values' => array($sid));
						if ($old_instructions != $new_instructions)   {
							$changes['label'][] = 'instructions';
							$changes['old_values'][] = $old_instructions;
							$changes['new_values'][] = $new_instructions;
						}
						if ($this->request->data['Schedule']['active'] != $originalSchedule['Schedule']['active']) {
							$changes['label'][] = 'active';
							$changes['old_values'][] = ($originalSchedule['Schedule']['active']? 'active': 'inactive');
							$changes['new_values'][] = ($this->request->data['Schedule']['active']? 'active':   'inactive');
						}
				
						$this->_saveChanges(serialize($changes), '', '', $originalSchedule['Schedule']['account_id'], $did_id, 'schedule', 'edit');                   
					}
					
					$this->Session->setFlash(__('Your changes have been saved'), 'flash_jsongood');
				}
			}
			else {
					$this->Session->setFlash(__('The changes could not be saved. Please, try again.'), 'json_flashbad');
			}
			$this->render('/Elements/json_result');
		}   
		else {
			$this->Schedule->recursive = 3;
			$this->request->data = $this->Schedule->findById($sid);
			$this->loadModel('Section');
			$res= $this->Section->find('all', array('conditions' => array('schedule_id' => $sid)));
			$sections = array();
			foreach ($res as $s) {
			   $sections[$s['Section']['sort']] = $s['Section']; 
			}
			$this->set('sections', $sections);
			// check if the specified   schedule has been   deleted and no longer   active
			if ($this->request->data['Schedule']['deleted']) {
				$this->Session->setFlash(__('You are attempting to edit a calltype that has been modified or deleted. <br><br> Please refresh your list of calltypes and try again.</center>'));
				$this->render('/Elements/html_result');
				return;
			}
					

			// find all on-call lists   configured for this sub-account to use as   action recipients
			$this->loadModel('CallList');
			$lists = $this->CallList->find('all', array('conditions' => array('inactive' => '0', 'deleted' => '0', 'did_id' => $this->request->data['Schedule']['did_id']), 'recursive' => 0));
			
			
			// find all calendars   configured for this sub-account to use as   action recipients
			$calendars = array();
			if (Configure::read('calendar_enabled')) {
				$this->loadModel('Scheduling.EaService');
				$calendars = $this->EaService->find('all', array('conditions' => array('did_id' => $this->request->data['Schedule']['did_id'], 'deleted' => '0'), 'recursive' => 0));
			}

			// find all employees   configured for this sub-account to use as   action recipients
			$this->loadModel('EmployeesContact');               
			$conditions = array('Employee.deleted' => 0, 'Employee.did_id' => $this->request->data['Schedule']['did_id']);
			$data = $this->EmployeesContact->find('all', array('conditions' => $conditions, 'order' => array('Employee.name' => 'asc'), 'recursive' => 1));

			$contacts = array();
			
			$employees['ALL'][] = array('id' => 'ALL', 'oncall' => false, 'label' => 'Requested Staff' );
			foreach ($lists as $l) {
				$employees['Oncall'][$l['CallList']['id']] = array('id' => 'ONCALL_' . $l['CallList']['id'], 'oncall' => true, 'label' => "ON-CALL: " . $l['CallList']['title'] );

			}

			foreach ($calendars as $l) {
				$employees['Calendar'][$l['EaService']['id']] = array('id' => 'CALENDAR_' . $l['EaService']['id'], 'oncall' => false, 'label' => "CALENDAR: " . $l['EaService']['name'] );
			}
			
			/*$this->DidNumber->unbindModel(
						array('hasMany' => array('DidFile', 'DidNumbersEntry', 'DidNumbersEdits', 'Employee', 'Calltype'))            
			);*/
			$this->DidNumber->recursive = 0;
			$this->DidNumber->id = $this->request->data['Schedule']['did_id'];
			$service_sku = $this->DidNumber->field('service_sku');            
			
			if ($service_sku == 'LA30FOR30' || $service_sku == 'LABASIC') $this->set('wizard_actions', $this->dashboard_actions_msg);
			$contacts = array();

			$employees[CONTACT_PHONE] = array();
			$employees[CONTACT_EMAIL] = array();
			$employees[CONTACT_VMAIL] = array();
			$employees[CONTACT_WEB] = array();
			$employees[CONTACT_FAX] = array();
			// create   array   that organizes contact methods for each employee

			foreach ($data as   $emp) {
				if ($emp['EmployeesContact']['contact_type'] == CONTACT_CELL || $emp['EmployeesContact']['contact_type'] == CONTACT_PHONE) {
					$num = preg_replace('/[^0-9]/', '', $emp['EmployeesContact']['contact']);
					if (strlen($num) == 10) {
						$contact = substr($num, 0, 3) . '-' . substr($num, 3, 3) . '-' . substr($num, 6);
					}
					else $contact = trim($emp['EmployeesContact']['contact']);
					
					
				}
				else {
					$contact = $emp['EmployeesContact']['contact'];
				}
				if ($emp['EmployeesContact']['contact_type'] == CONTACT_CELL)   {
					$employees[CONTACT_PHONE][] = array('label' => $emp['Employee']['name'] . ' (' . $emp['EmployeesContact']['label'] . ': '   .   $contact . ')');
				}
				
				else {
					$employees[$emp['EmployeesContact']['contact_type']][] = array('id' => $emp['EmployeesContact']['id'], 'label' =>   $emp['Employee']['name'] . ' (' .   $emp['EmployeesContact']['label']   .   ': ' . $contact .   ')');
				}               
				$contacts[$emp['EmployeesContact']['id']] = $emp;               
			}
	        
 			// find all crms currently configured for this account
			$this->loadModel('Crm');
			$crms = $this->Crm->find('list', array('fields' => array('id','name'),'conditions' => array('deleted' => '0', 'parent_id' => $this->request->data['Schedule']['did_id']), 'recursive' => -1));
	        
	        
	        
			// make misc info   available   from the view
			$this->set('employees', $employees);
			$this->set('crms', $crms);
			$this->set('contacts', $contacts);
			$this->set('schedule_id', $sid);
		}

	}
	public function edit_schedule($sid = null) {
		$this->edit($sid);

	}   

	function duplicate($schedule_id) {
		$this->Schedule->recursive = 2;
		$schedule = $this->Schedule->findById($schedule_id); 
		print_r($schedule); exit;
	}
	
	public function status($schedule_id, $status)   {
		if ($status) $txt = 'activated';
		else $txt = 'deactivated';
		
		$this->Schedule->recursive = 0;
		$s = $this->Schedule->findById($schedule_id);
		if ($s) {
			$schedule = $this->_getSchedule($s['Schedule'], $this->php_daysofweek);
			$this->loadModel('Calltype');
			$c = $this->Calltype->findById($s['Schedule']['calltype_id']);
			$data['id'] = $schedule_id;
			$data['active'] = $status;
			if ($this->Schedule->save($data))   {
				$description = 'Schedule '.$txt.' for ' . $c['Calltype']['title'] . ' - ' . $schedule;
	
				$e['schedule_id'] = $schedule_id;
				$this->_saveChanges($description, '', '', $c['Calltype']['account_id'], $c['Calltype']['did_id'], 'schedule', 'status', $e);        
				$this->Session->setFlash('The schedule has been ' . $txt, 'flash_jsongood');
				$this->render('/Elements/json_result');
			}
			else {
				$this->Session->setFlash('The schedule cannot be ' . $txt, 'flash_jsonbad');
				$this->render('/Elements/json_result');
				
			}           
		}

		else {
			$this->Session->setFlash('The   schedule cannot be found');
			$this->render('/Elements/json_result');
			
		}    
	
	}


	
	function _inactivateSchedule($id, $new_id=null, $status=null)   {
		if ($new_id) {
			$this->Schedule->query("update ".OA_TBL_PREFIX."schedules set active='$status' where id='$new_id'"); //   activate new schedule
		}
		
		$this->Schedule->query("update ".OA_TBL_PREFIX."schedules set deleted='1', deleted_ts=now() where id='$id'");
		$this->Schedule->query("update ".OA_TBL_PREFIX."actions set inactive = '1' where schedule_id='$id'");
		$ids = $this->Schedule->Action->find('list', array('fields' => array('Action.id', 'Action.id'), 'recursive' => '0', 'conditions' => array('schedule_id' => $id)));
		if (sizeof($ids) > 0)   {
			$this->Schedule->query("update ".OA_TBL_PREFIX."prompts set inactive = '1' where action_id in (".implode(',', $ids).")");
		}
	}   

	
	public function cleanup()   {
		$data = $this->Schedule->find('all' , array('fields' => array('id'), 'recursive' => 0, 'conditions' => array('deleted' => '1')));
		foreach($data   as $d) {
			$this->_inactivateSchedule($d['Schedule']['id']);
		}
		$this->Schedule->query("delete from ".OA_TBL_PREFIX."actions a right join ".OA_TBL_PREFIX."schedules s on a.schedule_id=s.id where s.id is NULL");
		echo 'done'; exit;
	}
	
	public function actions($id = null)   {
		$this->layout = "plain";
			$this->request->data = $this->Schedule->find('first', array('recursive' => 2, 'conditions' => array('Schedule.id' => $id)));
			$conditions = array('did_id' => $this->request->data['Schedule']['did_id']);
			$this->loadModel('Employee');
			$data = $this->Employee->find('all', array('conditions' => $conditions, 'recursive' => 0));
			foreach ($data as $emp)   {
				$this->request->data['Employee'][$emp['Employee']['id']] = $emp['Employee'];
			}
	}
/**
 * delete   method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param   string $id
 * @return void
 */
	public function delete($id = null, $calltype_id=null)   {
        if (!$this->isAuthorized('SchedulesDelete')) {
			$this->Session->setFlash(__('You are not allowed to make changes to this calltype'), 'flash_jsonbad');
			$this->render('/Elements/json_result');
			return;
		}       
		$old = $this->Schedule->findById($id);
		$this->loadModel('Calltype');
		$c = $this->Calltype->findById($old['Schedule']['calltype_id']);
		if (!$calltype_id) $calltype_id = $old['Schedule']['calltype_id'];
		$msg = '';
		$success = 'true';
		$data['Schedule']['id'] = $id;
		$data['Schedule']['deleted'] = '1';
		$deleted_ts = date('Y-m-d H:i:s');
		$data['Schedule']['deleted_ts'] = $deleted_ts; 
		if ($this->Schedule->save($data['Schedule']))   {
			$msg = 'Calltype deleted';

			$e['schedule_id'] = $id;
			$description = 'Calltype deleted - ' . $c['Calltype']['title'] . ' - ' . $id;
			$this->_saveChanges($description, serialize($old), '', $c['Calltype']['account_id'], $c['Calltype']['did_id'], 'schedule', 'delete', $e);     
		}
		else {
			$msg = 'Cannot delete schedule';
			 $success = 'false';
		}
			$conditions = array('calltype_id' => $calltype_id, 'deleted' => 0);
			$res = $this->Schedule->find('all', array('conditions' => $conditions));
			if (!$res) {
				$data['Calltype']['id'] = $calltype_id;
				$data['Calltype']['deleted'] = '1';
				$data['Calltype']['deleted_ts'] = $deleted_ts;
				$this->loadModel('Calltype');
				$this->Calltype->save($data['Calltype']);
			}
		$this->set('msg', $msg);
		$this->set('success', $success);
	}
	    
	function view_deleted($did_id, $calltype_id = null) {
	    $this->layout = 'standalone';
	    $this->Schedule->recursive = 0;
	    
	    $this->Schedule->bindModel(
	        array('belongsTo' => array('Calltype' => array('foreignKey' => 'calltype_id'))
	    ));
	    $this->paginate['conditions'] = array('Schedule.deleted' => '1', 'Schedule.did_id' => $did_id);
	    $this->paginate['order'] = array('Schedule.deleted_ts' => 'desc');
	    $schedules = $this->paginate();
	    foreach ($schedules as $k => &$s) {
           $s['Schedule']['schedule'] = $this->_getSchedule($s, $this->php_daysofweek);
        }
        $this->set('schedules', $schedules);
	}
	
	function undelete($sid) {
	  if (empty($sid)) {
	    echo 'Invalid script'; exit;
	  }
	    $this->layout = 'standalone';
        $this->Schedule->recursive = 0;
	    $s = $this->Schedule->findById($sid);
	    
	    if ($s) {
	        $s['Schedule']['deleted'] = 0;
	        $s['Schedule']['deleted_ts'] = '';
	        $this->Schedule->save($s);
			    $this->_saveChanges('undeleted schedule id ' . $sid, '', '', $s['Schedule']['account_id'], $s['Schedule']['did_id'], 'schedule', 'undelete', $e);     
			    $this->redirect('/Schedules/view_deleted/'.$s['Schedule']['did_id']);
	    }
	    else {
	        echo 'Unable to undelete specified script'; 
	        exit;
	    }
	}
	
	function view_script($sid) {
	    $this->Schedule->recursive = 2;
	    $s = $this->Schedule->findById($sid);
	    $s['id'] = $sid;
	    $s['schedule'] = $this->_getSchedule($s, $this->php_daysofweek);

        $this->loadModel('Employee');
        $this->Employee->unbindModel(
            array('hasMany' => array('EmployeesEdit'))
        );
        $conditions = array('did_id' => $s['Schedule']['did_id'], 'deleted' => '0');
        $data_employees = $this->Employee->find('all', array('conditions' => $conditions)); 
        $employees = array();
        $contacts = array();
    
        foreach ($data_employees as $k => $e) {
          $employees[$e['Employee']['id']] = $e;
          foreach ($e['EmployeesContact'] as $c) {
            $contacts[$c['id']] = $c;
          }
        }
        
        $sections = array();
        // load section definitions for all active schedules
        $sql = "select s.* from ".OA_TBL_PREFIX."sections s left join ".OA_TBL_PREFIX."schedules sc on sc.id=s.schedule_id where sc.id='$sid'";
        $res = $this->Employee->query($sql, false);
        foreach($res as $k => &$row) {
        	$row['s']['visible'] = 0;
        	if (!isset($sections[$row['s']['schedule_id']])) $sections[$row['s']['schedule_id']] = array();
            $sections[$row['s']['schedule_id']][$row['s']['sort']] = $row['s'];
        }
        
        // load all active calendars if the calendar module is enabled
        $calendars = array();
        if (Configure::read('calendar_enabled')) {
          $this->loadModel('Scheduling.EaService');
          $conditions = array('did_id' => $s['Schedule']['did_id'], 'deleted' => 0);
          $data = $this->EaService->find('all', array('conditions' => $conditions));
          foreach ($data as $d) {
            foreach ($d['EaProvider'] as $k => $e) {
              $sql = "select * from ".OA_TBL_PREFIX."employees_contacts c left join ".OA_TBL_PREFIX."employees e on e.id=c.employee_id where c.id='".$e['contact_id']."'";
              $employee = $this->Employee->query($sql);
              if ($employee) {
                $d['EaProvider'][$k]['employee'] = $employee[0];
              }
              else {
                $d['EaProvider'][$k]['employee'] = false;
              }
            }
            $calendars[$d['EaService']['id']] = $d;
          }
        }
        
            // load all active on-call lists
        $this->loadModel('CallList');
        $this->CallList->recursive = 1;    
        $conditions = array('did_id' => $s['Schedule']['did_id'], 'deleted' => '0');
        $data_oncall = $this->CallList->find('all',  array('conditions' => $conditions)); 
        $oncall = array();
        foreach ($data_oncall as $k => $o) {
    			foreach ($o['CallListsSchedule'] as $j => $c) {
    			  if ($c['active'] == '0') unset($data_oncall[$k]['CallListsSchedule'][$j]);
    				else $data_oncall[$k]['CallListsSchedule'][$j]['schedule'] = $this->_getSchedule($c, $this->php_daysofweek);
    			}
          $oncall[$o['CallList']['id']] = $data_oncall[$k];
        }
        $this->set('oncall', $oncall);        
        
        $this->set('calendars', $calendars);        
        
        $this->set('sections', $sections);
        $this->set('employees', $employees);
        $this->set('contacts', $contacts);	    
	    
	    $this->set('s', $s);
	}
}
?>
