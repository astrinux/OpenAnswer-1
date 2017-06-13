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
class EmployeesController extends AppController 
{
	public $paginate;
	public $components = array('RequestHandler');
	public $helpers = array('Js');
	public $theme;
	
	public function beforeFilter() 
	{
		parent::beforeFilter();
	}
	

  public function index($did_id) {
    $conditions = array('did_id' => $did_id, 'deleted' => 0);
		$this->paginate['limit'] = 600;
		$this->paginate['conditions'] = $conditions;
		$this->paginate['order'] = array('Employee.sort' => 'asc', 'Employee.name' => 'asc');
		$this->Employee->recursive = 0;
		$employees = $this->paginate();
		$cdata = $this->Employee->query("select * from ".OA_TBL_PREFIX."sms_carriers as c order by name, rank");
		$this->set('carriers', $cdata);
		
		$this->set('Employees', $employees);
		$this->set('did_id', $did_id);
//$log = $this->Employee->getDataSource()->getLog(false, false);
//FireCake::log($log);		
	}
	
	/**
	* add method
	*
	* @return void
	*/
	public function add($did_id = null) 
	{
		$this->set('did_id', $did_id);
		$cdata = $this->Employee->query("select * from ".OA_TBL_PREFIX."sms_carriers as c order by name, rank");
		$this->set('sms_carriers', $cdata);
		if ($this->request->is('post')) 
		{
			$did_id = $this->request->data['Employee']['did_id'];
			$this->loadModel('DidNumber');
			$did = $this->DidNumber->find('first', array('conditions' => array('DidNumber.id' => $did_id), 'recursive' => 0));
			$this->clearDidCache($did_id);		
			foreach ($this->request->data['Contact']['label'] as $k => $val) 
			{
				// strip off non-numeric characters for phone numbers
				if (in_array($this->request->data['Contact']['contact_type'][$k], array(CONTACT_FAX, CONTACT_PHONE, CONTACT_TEXT))) 
				{
					$this->request->data['Contact']['contact'][$k] = preg_replace('/[^0-9]/', '', $this->request->data['Contact']['contact'][$k]);
				}
				$this->request->data['Contact']['label'][$k] = str_replace(array("\r\n", "<br>"), array('',''), $this->request->data['Contact']['label'][$k]);
				$row = array('label' => $this->request->data['Contact']['label'][$k], 'did_id' => $did_id, 'contact' => $this->request->data['Contact']['contact'][$k], 'contact_type' => $this->request->data['Contact']['contact_type'][$k], 'ext' => $this->request->data['Contact']['ext'][$k], 'carrier_id' =>  $this->request->data['Contact']['carrier'][$k], 'visible' => $this->request->data['Contact']['visible'][$k]);
				$this->request->data['EmployeesContact'][] = $row;
			}
			$this->request->data['Employee']['account_id'] = $did['DidNumber']['account_id'];
			$this->Employee->create();
			$save_ok = $this->Employee->saveAssociated($this->request->data, array('deep' => true));
			if ($save_ok) 
			{
				$id = $this->Employee->getInsertID();
				$this->Session->setFlash(__('Your changes have been saved'), 'flash_jsongood');		  
				$description = 'Employee created: ' . $this->request->data['Employee']['name'] . ' (ID: '.$id.')';
				$e['employee_id'] = $id;
				$this->_saveChanges($description, '', serialize($this->request->data), $did['DidNumber']['account_id'], $did_id, 'employee', 'add', $e);  
			}
			else
			{
				$this->Session->setFlash(__('Your changes could not be saved, try again later'), 'flash_jsonbad');		  
			}
			$this->render('/Elements/json_result');
		}
		else
		{
			$this->set('did_id', $did_id);
			$this->loadModel('DidNumber');
			$this->DidNumber->id = $did_id;
			$this->set('advanced_setup', $this->DidNumber->field('advanced_setup'));
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
		$this->Employee->id = $id;

		$cdata = $this->Employee->query("select * from ".OA_TBL_PREFIX."sms_carriers as c order by name, rank");
		$this->set('sms_carriers', $cdata);
		
		if ($this->request->is('post') || $this->request->is('put')) {
        if (!$this->isAuthorized('EmployeesEdit')) {
			  $this->Session->setFlash(__('You are not allowed to make changes to this employee'), 'flash_jsonbad');
			  $this->render('/Elements/json_result');
			  return;
      }
		  $did_id = $this->request->data['Employee']['did_id'];
	    $this->clearDidCache($did_id);		
      
      // flag all contacts for this employee.  After update, delete the ones we no longer need
      $this->Employee->query("update ".OA_TBL_PREFIX."employees_contacts set flag='1' where employee_id='$id'");

      $order = 0;
      foreach ($this->request->data['Contact']['label'] as $k => $val) {
        $this->request->data['Contact']['label'][$k] = str_replace(array("\r\n", "<br>"), array('',''), $this->request->data['Contact']['label'][$k]);

        if ($this->request->data['Contact']['contact_type'][$k] == CONTACT_WEB) {
          $this->request->data['Contact']['contact'][$k] = trim($this->request->data['Contact']['contact'][$k]);
          if (strpos($this->request->data['Contact']['contact'][$k], 'http://') === false && strpos($this->request->data['Contact']['contact'][$k], 'https://') === false) {
//          $this->request->data['Contact']['contact'][$k] = str_replace('http://', '', $this->request->data['Contact']['contact'][$k]);
            $this->request->data['Contact']['contact'][$k] = 'http://' . trim($this->request->data['Contact']['contact'][$k]);
          }
        }
        // strip off non-numeric characters for phone numbers
        if (in_array($this->request->data['Contact']['contact_type'][$k], array(CONTACT_FAX, CONTACT_PHONE, CONTACT_TEXT))) {
          $this->request->data['Contact']['contact'][$k] = preg_replace('/[^0-9]/', '', $this->request->data['Contact']['contact'][$k]);
        }
        
        $row = array('sort' => $order, 'flag' => '0', 'employee_id' => $id, 'id' => $this->request->data['Contact']['id'][$k], 'label' => $this->request->data['Contact']['label'][$k], 'did_id' => $did_id, 'contact' => $this->request->data['Contact']['contact'][$k], 'contact_type' => $this->request->data['Contact']['contact_type'][$k], 'ext' => $this->request->data['Contact']['ext'][$k], 'carrier_id' => $this->request->data['Contact']['carrier'][$k], 'visible' => $this->request->data['Contact']['visible'][$k] );
			  if (!isset($this->request->data['Contact']['id'][$k]) || !$this->request->data['Contact']['id'][$k]) {
			    $this->_addContacts($id, $row, $this->request->data['Employee']['account_id'], $did_id);
			  }
        
        $this->request->data['EmployeesContact'][] = $row;
        $order++;
      }
		  $this->_compareEdits($this->request->data, $id);      

			if ($this->Employee->saveAssociated($this->request->data, array('deep' => true))) {
        $this->Session->setFlash(__('Your changes have been saved'), 'flash_jsongood');		  
        $this->_deleteContacts($id, $this->request->data['Employee']);

			} else {
        $this->Session->setFlash(__('The changes could not be saved. Please, try again.'), 'flash_jsonbad');
			}
			$this->render('/Elements/json_result');
		} else {
			$data = $this->Employee->read(null, $id);
			$this->request->data['Employee'] = $data['Employee'];
			$contacts = array();
			foreach($data['EmployeesContact'] as $e) {
        
        $contacts['id'][] = $e['id'];
        $contacts['contact'][] = $e['contact'];
        $contacts['contact_type'][] = $e['contact_type'];
        $contacts['label'][] = $e['label'];
        $contacts['ext'][] = $e['ext'];
        $contacts['visible'][] = $e['visible']? '1': '0';
        $contacts['carrier_id'][] = $e['carrier_id'];   

			}
			$this->request->data['EmployeesEdit'] = $data['EmployeesEdit'];
			$did_id = $this->request->data['Employee']['did_id'];
/*			$data = $this->Employee->query("select eid, schedule_id from ".OA_TBL_PREFIX."actions as s where did_id='$did_id'");
			$employee_x_schedule = array();
			foreach ($data as $e) {
				if ($e['s']['eid'] && $e['s']['eid'] != 'ALL') {
					$e_array = explode(',', $e['s']['eid']);
					foreach($e_array as $a) {
						if (!isset($employee_x_schedule[$a])) {
							$employee_x_schedule[$a] = array();
						}
						$employee_x_schedule[$a][] = $e['s']['schedule_id'];
					}
				}
			}*/
      $changes = array();
  		foreach ($this->request->data['EmployeesEdit'] as $k=>$e) {
  		  if ($e['change_type'] == 'edit') {
    		  if (strpos($e['description'], 'a:') !== false) $changes = unserialize($e['description']);
    		  $text = '';
    		  if (isset($changes['label'])) {
      		  foreach($changes['label'] as $j => $label) {
      		  	$text .= '<b>' . $label . '</b> changed from <i>'.$changes['old_values'][$j].'</i> to <i>'.$changes['new_values'][$j].'</i><br>';
      		  }
      		}
    	  }
    		else $text = $e['description'];
    		
  	  	$this->request->data['EmployeesEdit'][$k]['description'] = $text;
  
  		}				
  		$this->loadModel('DidNumber');
  		$this->DidNumber->id = $did_id;
 			$this->set('advanced_setup', $this->DidNumber->field('advanced_setup'));
  		$this->set('contacts', $contacts);
			$this->set('employee_id', $id);
			//$this->set('employee_x_schedule', $employee_x_schedule);

		}
	}

  function _deleteContacts($id, $employee) {
    $data = $this->Employee->query("select * from ".OA_TBL_PREFIX."employees_contacts where employee_id='$id' and flag='1'");
    $text = '';

    if ($data) {
      foreach($data as $d) {
        $text .= "Deleted '".$d[OA_TBL_PREFIX.'employees_contacts']['label']."' - " . $d[OA_TBL_PREFIX.'employees_contacts']['contact'] . ' for employee '.$employee['name'].' (ID: '.$employee['id'].')';
        if ($d[OA_TBL_PREFIX.'employees_contacts']['ext']) $text .= ' - Ext: ' . $d[OA_TBL_PREFIX.'employees_contacts']['text'];
        if ($d[OA_TBL_PREFIX.'employees_contacts']['carrier']) $text .= ' - Carrier: ' . $d[OA_TBL_PREFIX.'employees_contacts']['carrier'];         
        $e['employee_id'] = $employee['id'];
        $e['contact_id'] = $id;
        $this->_saveChanges($text, serialize($d[OA_TBL_PREFIX.'employees_contacts']), '', $employee['account_id'], $employee['did_id'], 'employee_contact', 'delete', $e);  
        
      }
      
    }
    $this->Employee->query("delete from ".OA_TBL_PREFIX."employees_contacts where employee_id='$id' and flag='1'");
    
  }

  function _addContacts($id, $data, $account_id, $did_id) {
    $text = '';
    if ($data) {
      $text .= "Added '".$data['label']."' - " . $data['contact'];
      if ($data['ext']) $text .= ' - Ext: ' . $data['ext'];
      if ($data['carrier_id']) $text .= ' - Carrier ID: ' . $data['carrier_id'];     
      $text .= ' for employee ID:' . $id;    

      $e['employee_id'] = $id;
      $this->_saveChanges($text, '',  serialize($data), $account_id, $did_id, 'employee', 'add', $e);  
      
    }    
  }

  function _compareEdits($new, $id) {
    $this->Employee->unbindModel(
      array(
       'hasMany' => array('EmployeesEdit'),
      )
    );		  
  	
    $data = $this->Employee->findById($id);

    foreach ($data['EmployeesContact'] as $k => $val) {
      $original[$val['id']] = $val;
    }
    $changes = $this->_initChanges();

    $edits = array_diff_assoc($new['Employee'], $data['Employee']);
    $text = '';
    if ($edits) {
      foreach ($edits as $k => $val) {
        if ($k == 'gender') {
          $val = $this->global_options['gender'][$val];
        }
        $changes['label'][] = $k;
        $changes['old_values'][] = $data['Employee'][$k];
        $changes['new_values'][] = $new['Employee'][$k];
    //    $text .= "'$k' changed from '".$data['Employee'][$k]."' to '".$new['Employee'][$k]."'\r\n";
      }
    }
    $edits2 = false;
    foreach ($new['EmployeesContact'] as $c) {
      if ($c['id']) {
	      $vals1 = array('employee_id' => $original[$c['id']]['employee_id'], 'label' => $original[$c['id']]['label'], 'contact' => $original[$c['id']]['contact'], 'contact_type' => $original[$c['id']]['contact_type'], 'ext' => $original[$c['id']]['ext'], 'carrier_id' => $original[$c['id']]['carrier_id']);
	      $vals2 = array('employee_id' => $c['employee_id'], 'label' => $c['label'], 'contact' => $c['contact'], 'contact_type' => $c['contact_type'],'ext' => $c['ext'],'carrier_id' => $c['carrier_id']);
	      
	      $diff = array_diff_assoc($vals1, $vals2);

	      if ($diff) {
	        foreach ($diff as $k => $val) {
	          if ($k != 'flag') {
	            if ($k == 'label') {
                $changes['label'][] = 'Label';
                $changes['old_values'][] = $vals1[$k];
                $changes['new_values'][] = $vals2[$k];
//	              $text .= "'Label' changed from '".$vals1[$k]."' to '".$vals2[$k]."'\r\n";
	            }
	            else {
	            	$changes['label'][] = $k;
                $changes['old_values'][] = $vals1[$k];
                $changes['new_values'][] = $vals2[$k];
	              //$text .= "'".$vals2['label']."' changed from '".$vals1[$k]."' to '".$vals2[$k]."'\r\n";
	            }
	            $edits = true;
	          }
	        }
	      }
    	}
    }
    
    if ($edits || $edits2) {
        $e['employee_id'] = $id;
        $this->_saveChanges(serialize($changes), serialize($data), serialize($new), $data['Employee']['account_id'], $data['Employee']['did_id'], 'employee', 'edit', $e);   
                
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
    if (!$this->isAuthorized('EmployeesDelete')) {
		  $this->Session->setFlash(__('You are not allowed to make changes to this employee'), 'flash_jsonbad');
		  $this->render('/Elements/json_result');
		  return;
    }	  
    $this->Employee->unbindModel(
      array(
       'hasMany' => array('EmployeesEdit'),
      )
    );		  
	  $employee = $this->Employee->findById($id);
	  
		if (!$employee) {
		  $msg = "Employee does not exist";
		  $success = true;
		}
		else {

		  $call_types = array();
		  $call_lists = array();
		  $summaries = array();
		  $calendars = array();
		  $today = date('Y-m-d H:i:s');
		  		  
		  // check if employee is in use in calltype instructions, on-call lists, and message_summary
		  $data = $this->Employee->query("select a.eid, schedule_id, c.title from ".OA_TBL_PREFIX."schedules as sc left join  ".OA_TBL_PREFIX."actions as a on sc.id=a.schedule_id left join ".OA_TBL_PREFIX."calltypes c on c.id=sc.calltype_id where sc.did_id='". $employee['Employee']['did_id']."' and sc.deleted='0' and a.eid != ''");
		  
		  $data2 = $this->Employee->query("select employee_ids, c.title from ".OA_TBL_PREFIX."call_lists_schedules as s left join ".OA_TBL_PREFIX."call_lists c on c.id=s.call_list_id where s.deleted='0' and s.did_id='". $employee['Employee']['did_id']."' and s.employee_ids != '' and employee_ids is not NULL and (s.end_date is null or s.end_date > '$today')");

		  $data3 = $this->Employee->query("select s.id, employee_contact_ids from ".OA_TBL_PREFIX."messages_summary as s where s.deleted='0' and did_id='". $employee['Employee']['did_id']."' ");
      // check if employee is in use
			$employee_in_use = false;

			if (Configure::read('calendar_enabled')) {
  		  $data4 = $this->Employee->query("select p.id, s.name, contact_id from ".OA_TBL_PREFIX."ea_providers p left join ".OA_TBL_PREFIX."ea_services s on s.id=p.service_id where s.deleted='0' and s.did_id='". $employee['Employee']['did_id']."' and p.deleted='0'" );    
  		}
  		else {
  		  $data4 = array();
  		}

      //check each contact for the employee against those used in message summaries and calltype instructions
			foreach ($data as $e) {
				if ($e['a']['eid'] && $e['a']['eid'] != 'ALL') {
					//$e_array1 = array_merge($e_array1, explode(',', $e['a']['eid']));
					$e_array = explode(',', $e['a']['eid']);
          foreach ($employee['EmployeesContact'] as $c) {  
                if (in_array($c['id'], $e_array)) {
                  $employee_in_use = true;
                  $call_types[] = "Calltype: " . $e['c']['title'];
                }
          }					
				}
			}
 			foreach ($data3 as $e) {
 			  if (!empty($e['s']['employee_contact_ids'])) {
 					//$e_array3 = array_merge($e_array3, explode(',', $e['s']['employee_contact_ids']));
					$e_array = explode(',', $e['s']['employee_contact_ids']);
          foreach ($employee['EmployeesContact'] as $c) {  
                if (in_array($c['id'], $e_array)) {
                  $employee_in_use = true;
                  $summaries[] = "Msg Summary#: " . $e['s']['id'];
                }
          }					

        }
 			}			

      // check each employee against list of those used in active on-call lists
 			foreach ($data2 as $e) {
 				if ($e['s']['employee_ids'] && $e['s']['employee_ids'] != 'ALL') {
  					//$e_array2 = array_merge($e_array2, explode(',', $e['s']['employee_ids']));
					$e_array = explode(',', $e['s']['employee_ids']);
                if (in_array($id, $e_array)) {
                  $employee_in_use = true;
                  $call_lists[] = "On-call: " . $e['c']['title'];
                }
  					
 				}
 			}
 			
      // check each employee against of providers in scheduling calendars
				foreach($data4 as $e) {
          foreach ($employee['EmployeesContact'] as $c) {  
            $ids = explode(',', $e['p']['contact_id']);
          	if (in_array($c['id'], $ids)) {
                  $employee_in_use = true;
                  if (!in_array("Calendar: " . $e['s']['name'], $calendars)) $calendars[] = "Calendar: " . $e['s']['name'];
                  
            }
					}
				}
 			
      $where = array_merge($call_types, $call_lists, $summaries, $calendars);
      
			if ($employee_in_use) {
        $success = 'false';
        $msg = "This employee is used in the setup of the call type/ on-call list/ message summary shown below.  You must modify the setup to use a different employee prior to deleting:<br><br><b>".implode('<br>', $where)."</b>";
			}	  
			else {
        $success = 'false';
			  $del_ok = $this->Employee->soft_delete($id);
			  if ($del_ok) {
          $e['employee_id'] = $employee['Employee']['id'];
          $description = 'Employee \'' .$employee['Employee']['name'] . '\' deleted';
          
          // save the complete employee record in the change log, we can recover from this if needed
          $this->_saveChanges($description, serialize($employee), '', $employee['Employee']['account_id'], $employee['Employee']['did_id'], 'employee', 'delete', $e);     
			    		    
          $success = 'true';
          $msg = "The employee has been deleted";
			  }
			  else {
          $success = 'false';
          $msg = "Failed to delete the employee, try again later";
			    
			  }
			}
		}
		
		$this->set('success', $success);
		$this->set('msg', $msg);
		$this->render('result');
		
	}
	
	public function recover($edit_id) {
	  $recovery_data = $this->Employee->EmployeesEdit->findById($edit_id);
	  if ($recovery_data) {
	    $employee = unserialize($recovery_data['EmployeesEdit']['old_values']);
	    if ($this->Employee->find('first', array('conditions' => array('Employee.id' => $employee['Employee']['id'], 'Employee.deleted' => '0')))) {

        $this->Session->setFlash(__('ERROR: This employee already exists, cannot overwrite an existing employee'), 'flash_jsonbad');	 		      
        $this->render('/Elements/json_result');    
        return;
	    }
	    $employee['Employee']['deleted'] = 0;
	    $employee['Employee']['deleted_ts'] = null;
	    
	    $ok = $this->Employee->saveAssociated($employee);
	    if ($ok) {
        $e['employee_id'] = $employee['Employee']['id'];
        $description = 'Employee \'' .$employee['Employee']['name'] .' (ID: '.$employee['Employee']['id'].') \'   recovered';
        $this->_saveChanges($description, '', serialize($employee), $employee['Employee']['account_id'], $employee['Employee']['did_id'], 'employee', 'recover', $e);     
	      
        $this->Session->setFlash(__('Employee has been recovered'), 'flash_jsongood');	        
      }
      else {
        $this->Session->setFlash(__('Information cannot be recovered'), 'flash_jsonbad');	        
      }
	  }
    else {
      $this->Session->setFlash(__('Information cannot be found'), 'flash_jsonbad');	        
    }
    $this->render('/Elements/json_result');    
	}
}
