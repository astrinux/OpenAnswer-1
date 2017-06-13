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
 * Messages Controller
 *
 * @property Message $Message
 */
class MessagesController extends AppController {
	
  public $paginate = array(
  	'limit' => 100,
    'order' => array(
    	'Message.id' => 'desc'
   	)
  );
	public $components = array('RequestHandler');
	public $helpers = array('Js');  
  
	public function index($did_id = null) {
		$this->Message->recursive = 1;
		$this->Message->unbindModel(
        array('hasMany' => array('MessagesEvent','MessagesPrompt', 'Complaint'))
    	);				
    $joins = array(
      array(
        'table' => OA_TBL_PREFIX . 'users',
        'alias' => 'User',
        'type' => 'left',
        'conditions' => array('`User`.`id` = `Message`.`user_id`')
      ),
      array(
        'table' => OA_TBL_PREFIX . 'did_numbers',
        'alias' => 'DidNumber',
        'type' => 'left',
        'conditions' => array('`DidNumber`.`id` = `Message`.`did_id`')
      )       
    );		
		if ($did_id) {
		  $this->paginate['joins'] = $joins;
		  $this->paginate['fields'] = array('Message.*', "CONVERT_TZ(".Configure::read('default_timezone').",'GMT',DidNumber.timezone)", 'User.username');
			$this->paginate['conditions'] = array(
					'Message.did_id' => $did_id,
			);
			$messages = $this->paginate();
		  $this->set('Messages', $messages);
		  FireCake::log($messages);
		}
		else $this->set('Messages', false);

	}



	public function view($id = null) {
		/*$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Invalid message'));
		}
		$this->set('Message', $this->Message->read(null, $id));*/
	}


	public function add() {
	  /*
		if ($this->request->is('post')) {
			$this->Message->create();
			if ($this->Message->save($this->request->data)) {
				$this->Session->setFlash(__('The message has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The message could not be saved. Please, try again.'));
			}
		}*/
	}


	public function edit($did_id, $message_id = null, $delivery_id = null, $dir = null) {
		$this->set('did_id', $did_id);
		$this->Message->unbindModel(
			array('hasMany' => array('MessagesDelivery'))
		);
		$delivery = $this->Message->MessagesDelivery->find('first', array('conditions' => array('MessagesDelivery.id' => $delivery_id), 'recursive' => 2));
		
		$account = $this->Message->DidNumber->Account->find('first', array('conditions'=> $delivery['Message']['DidNumber']['account_id'], 'recursive' => false));
		$delivery['Account'] = $account['Account'];
		
		//FireCake::log("MESSAGEDEL"); FireCake::log($delivery);
		$message = $delivery['Message'];		
		
		if (!$message) {
				$this->Session->setFlash(__('The message cannot be found'), 'flash_jsonbad');
				$this->render('json_result');
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Message->save($this->request->data)) {
				$this->Session->setFlash(__('The message has been saved'), 'flash_jsongood');
				//$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The message could not be saved. Please, try again.'), 'flash_jsonbad');
			}
		} else {
			$data = $this->instructions($did_id, $message_id, $delivery);
			$this->set('data', $delivery);
			FireCake::log($delivery); 
			
			
			/*if ($message_id && $dir == 'next') {
				$order = array('id' => 'desc');
				$conditions[] = array( 'Message.id < ' => $id);
				$this->request->data = $this->Message->find('first', array('order' => $order, 'conditions' => $conditions));
			}
			else if ($message_id && $dir == 'prev') {
				$order = array('id' => 'desc');
				$conditions[] = array( 'Message.id > ' => $id);
				$this->request->data = $this->Message->find('first', array('order' => $order, 'conditions' => $conditions));
			}
			else if ($message_id) {
				$this->request->data = $this->Message->read(null, $message_id);
			}
			else {
				$order = array('Message.id' => 'desc');
				$conditions = array();
				$this->request->data = $this->Message->find('first', array('order' => $order, 'conditions' => $conditions));
			}*/
		}
	}
	
	function instructions($did_id, $message_id, &$delivery) { 
		$schedule_id = $delivery['Message']['schedule_id'];
		if (1) {
			//print_r($account); 
			$tz = $delivery['Message']['DidNumber']['timezone'];

			$employees = array();
			$employees_contacts = array();
			
			// get all employees for this DID indexed by employee id
			$sql = "select * from ".OA_TBL_PREFIX."employees e where did_id='$did_id'"; 	
			$data = $this->Message->query($sql);
			foreach ($data as $row) {
				$employees[$row['e']['id']] = $row['e'];
			}
			
      // get all contact info for each employee, save it under each employee record $employees[$emp_id]['contacts'] = array(...), 
      // also create a contacts lookup array indexed by contact id
			$sql = "select * from ".OA_TBL_PREFIX."employees_contact c where did_id='$did_id' order by contact_type, `primary` desc"; 	
			$data = $this->Message->query($sql);
			foreach ($data as $row) {
			  if (isset($employees[$row['c']['employee_id']])) {
				  $employees[$row['c']['employee_id']]['contacts'][] = $row['c'];
				  //$employees_contacts_per_eid[$row['c']['employee_id']][] = $row['c'];
  			  $employees_contacts[$row['c']['id']] = $row['c'];				
  			}
			}
					 
			$sql = "select * from ".OA_TBL_PREFIX."actions a where schedule_id='$schedule_id'"; 
			$data = $this->Message->query($sql);
			foreach ($data as $row) {
				if (!isset($ct_actions[$row['a']['schedule_id']])) 
					$ct_actions[$row['a']['schedule_id']] = array();
				if (!isset($ct_actions[$row['a']['schedule_id']][$row['a']['sort']]))  
				  $ct_actions[$row['a']['schedule_id']][$row['a']['sort']] = $row['a'];
				else
				  $ct_actions[$row['a']['schedule_id']][] = $row['a'];
			}		

  		$sql = "select * from ".OA_TBL_PREFIX."messages_prompts p where message_id=$message_id order by sort asc"; 
  		$data = $this->Message->query($sql);
  		$ct_prompts = array();
  		foreach ($data as $row) {
  			/* if (!isset($ct_prompts[$row['p']['action_id']])) 
  				$ct_prompts[$row['p']['action_id']] = array();
  			$ct_prompts[$row['p']['action_id']][$row['p']['sort']] = $row['p'];*/
        $ct_prompts[$row['p']['sort']] = $row['p'];  			
  		}
			$delivery['contacts'] = $employees_contacts;
			$delivery['employees'] = $employees;
			$delivery['ct_actions'] = $ct_actions;
			$delivery['prompts'] = $ct_prompts;
			//$instructions = array('account'=> $delivery['Account'], 'didNumber' => $delivery['Message']['DidNumber'], 'contacts' => $employees_contacts, 'employees' => $employees, 'ct_actions' => $ct_actions, 'prompts' => $ct_prompts);
			//Cache::write('msg_instructions' . $did_id, $instructions, 'long');
			
		}				


	  date_default_timezone_set($tz);
     $ts = time();

		$n_day = date('w', $ts); // 0=sun, 6 = Saturday
		if ($n_day == 0) $n_day = 7; // make 7=Sunday

		$n_time = $n_day . date('Hi', $ts);
		$time_mysql = date('Y-m-d G:i:s', $ts);
		$day_of_week = strtolower(date('D', $ts));

		$sql = "select distinct t.title, s.* 
		FROM ".OA_TBL_PREFIX."schedules s 
		LEFT JOIN ".OA_TBL_PREFIX."calltypes t ON t.id=s.calltype_id 
		WHERE s.did_id='$did_id' and active='1'  
		AND ( 
		  (s.delete_flag='0' AND (s.start_date <= '$time_mysql' and s.end_date >= '$time_mysql')       
		  OR (s.start_day <= '$n_time' and s.end_day>='$n_time') 
		  OR (s.start_day <= '".($n_time+70000)."' and s.end_day>='".($n_time+70000)."')
		  OR (check_days='1' and ".$day_of_week."='1')		  
		  OR (start_date is null and start_day is null and check_days='0')) OR (s.id='$schedule_id')
		) order by t.sort asc, s.start_date desc, s.start_day desc, check_days desc";

		$schedules = array();
		$data = $this->Message->query($sql);
		FireCake::log($sql);
		FireCake::log($data);
		$valid_calltypes = array();
		$check_duplicates = array();
    $all_calltypes = array();
		foreach ($data as $row) {
			if (!in_array($row['t']['title'], $check_duplicates)) {
			  $schedules[$row['s']['id']] = $row['s'];
			  $check_duplicates[] = $row['t']['title'];
			  $valid_calltypes[] = $row['s']['calltype_id'];
			  $all_calltypes[] = $row;
			}
		}
    $delivery['all_calltypes'] = $all_calltypes;
 
		/*$sql = "select * from ".OA_TBL_PREFIX."prompts p where account_id='$account_id' order by action_id asc, sort asc"; 
		$data = $this->Client->query($sql);
		foreach ($data as $row) {
			if (!isset($ct_prompts[$row['p']['action_id']])) 
				$ct_prompts[$row['p']['action_id']] = array();
			$ct_prompts[$row['p']['action_id']][$row['p']['sort']] = $row['p'];
		}*/
		
		$calltypes = array();

		foreach ($delivery['all_calltypes'] as $row) {
		    if ($row['s']['delete_flag'] == '1') $txt = ' (INACTIVE)';
		    else $txt = '';
			  $calltypes[] = array('id' => $row['s']['id'], 'title' => $row['t']['title'] . $txt, 'type' => $schedules[$row['s']['id']]['type']);
		}	
		FireCake::log('valid'); FireCake::log($valid_calltypes); FireCake::log($calltypes);			
		$delivery['calltypes'] = $calltypes;
		$delivery['schedules'] = $schedules;
		//return $instructions;
      
      	
	}	
	public function minder($msg_id, $call_id) {
	  if (!$msg_id) {
      $this->Session->setFlash(__('The message has been saved'), 'flash_jsonbad');	    
      $this->render('/Elements/json_result');
	  }
    
	  $data['Message']['id'] = $msg_id;
	  $data['Message']['minder'] = '1';
	  $data['Message']['minder_ts'] = date('Y-m-d H:i:s');
	  
	  $this->logEvent($msg_id, "Message mindered", $call_id);
	  
	}
	
	public function unminder($msg_id, $call_id) {
	  if (!$msg_id) {
      $this->Session->setFlash(__('The message has been saved'), 'flash_jsonbad');	    
      $this->render('/Elements/json_result');
	  }
    
	  $data['Message']['id'] = $msg_id;
	  $data['Message']['minder'] = '0';
	  $data['Message']['minder_ts'] = '';
	  
	  $this->logEvent($msg_id, "Message un-mindered", $call_id);
	  
	}
	
	public function deliver($msg_id, $call_id) {
	  if (!$msg_id) {
      $this->Session->setFlash(__('The message has been saved'), 'flash_jsonbad');	    
      $this->render('/Elements/json_result');
	  }
    
	  $data['Message']['id'] = $msg_id;
	  $data['Message']['delivered'] = '1';
	  
	  $this->logEvent($msg_id, "Message delivered", $call_id);
	  
	}
	
	public function undeliver($msg_id, $call_id) {
	  if (!$msg_id) {
      $this->Session->setFlash(__('The message has been saved'), 'flash_jsonbad');	    
      $this->render('/Elements/json_result');
	  }
    
	  $data['Message']['id'] = $msg_id;
	  $data['Message']['delivered'] = '0';
	  
	  $this->logEvent($msg_id, "Message un-delivered", $call_id);
	  
	}	
	
	public function send($message_id, $delivered, $emp_contact_ids, $from=null) {
    $this->loadModel('EmployeesContact');
		$error = '';
		$success = true;
    
		if ($delivered) {
    	$this->Message->unbindModel(
  	    array(
  	     	'belongsTo' => array('Client'),
  	     	'hasMany' => array('MessagesEvent', 'MessagesDelivery')
  	     )
   		);     
			$message = $this->Message->findById($message_id);

   		$emp_contacts_array = explode('_', $emp_contact_ids);
			foreach ($emp_contacts_array as $emp_contact_id) {
	    	$this->EmployeesContact->unbindModel(
  	     	array('belongsTo' => array('Client'))
    		);     
    		$contact = $this->EmployeesContact->findById($emp_contact_id);
    		if ($contact) {
    			
    			$send_ok = $this->sendByTextOrEmail($message['Message']['calltype'], $contact, $message['MessagesPrompt']);
    			if ($send_ok) {
    				$delivery['delivered_time'] = date("Y-m-d G:i:s");
    				$delivery['delivery_name'] = $contact['Employee']['name'];
    				$delivery['delivery_contact'] = $contact['EmployeesContact']['contact'];
    				$delivery['delivery_contact_label'] = $contact['EmployeesContact']['label'];
    				$delivery['delivery_method'] = $contact['EmployeesContact']['contact_type'];
    				$delivery['delivered_by_userid'] = AuthComponent::user('login');
    				$delivery['employee_id'] = $emp_contact_id;
    				$data['MessagesDelivery'][] = $delivery;
        		$event['operator_id'] = AuthComponent::user('extension'); 
        		$event['message_id'] = $message_id;         
        		$event['user'] = AuthComponent::user('username');         
        		$event['description'] = "Delivered message (".$this->contact_types[$contact['EmployeesContact']['contact_type']].") to " . $contact['Employee']['name']. " at " . $contact['EmployeesContact']['contact'];  
    				$data['MessagesEvent'][] = $event;
    			}
    			else {
        		$event['operator_id'] = AuthComponent::user('extension'); 
        		$event['message_id'] = $message_id;         
        		$event['user'] = AuthComponent::user('username');         
        		$event['description'] = "ERROR in delivering message (".$this->contact_types[$contact['EmployeesContact']['contact_type']].") to " . $contact['Employee']['name']. " at " . $contact['EmployeesContact']['contact'];  
    				$data['MessagesEvent'][] = $event;
						$error .=  "ERROR in delivering message (".$this->contact_types[$contact['EmployeesContact']['contact_type']].") to " . $contact['Employee']['name']. " at " . $contact['EmployeesContact']['contact'] . "\r\n";
    				$success = false;
    			}
				}
				else {
					$error .= "Unable to find employee contact id: " . $emp_contact_id . "\r\n";
					$success = false;
				}

			}
		}
		else {
    	$event['operator_id'] = AuthComponent::user('extension'); 
    	$event['message_id'] = $message_id;         
    	$event['user'] = AuthComponent::user('username');         
    	$event['description'] = "Setting messages to undelivered";  
			$data['MessagesEvent'][] = $event;
		}
		$data['Message']['id'] = $message_id;
		$data['Message']['delivered'] = $delivered;

    $saveok = $this->Message->saveAssociated($data);
    if (!$saveok) {
    	$error .= "Unable to save changes\r\n";
			$success = false;
    }
		$this->set('jsondata', array('success' => $success, 'msg' => $error));
	}
	
	function sendByTextOrEmail($calltype, $contact, $prompts) {
		if ($contact['EmployeesContact']['contact_type'] ==  CONTACT_TEXT)
			$type = 'Text';
		else if ($contact['EmployeesContact']['contact_type'] ==  CONTACT_EMAIL)
			$type = 'Email';
		
		
		try {
			App::uses('CakeEmail', 'Network/Email');		
			$Email = new CakeEmail();
			$Email->config('default');
			$Email->template('deliver'.$type . 'Msg', 'default');
			$Email->viewVars(array('calltype' => $calltype, 'prompts' => $prompts)); 
			$Email->to($contact['EmployeesContact']['contact']);
			$Email->emailFormat('html');
			$Email->subject('[VN] ' . $calltype);
			$Email->send();
			return true;
		}	catch (Exception $e) {
			return false;
		}
	
	}


	public function delete($id = null) {
		$this->layout = 'plain';
    $this->Message->unbindModel(
        array('belongsTo' => array('Client'))
    );				
		$data = $this->Message->findById($id);
		FireCake::log($data);
		
		if (sizeof($data['MessagesEvent']) < 1) {
			$this->Message->delete($id);
			FireCake::log('deleting empty message');
		}
		else FireCake::log('not deleting  message');
	}
	


		
	public function setCalltype($account_id, $msgid) {
		$new_ct = $this->request->data['new_ct'];
		$old_ct = $this->request->data['old_ct'];
		$old_ct_title = $this->request->data['old_ct_title'];
		$new_ct_title = $this->request->data['new_ct_title'];

		$this->layout = 'plain';
		
		$json['success'] = true;
		$this->set('json', $json);

		$data['Message']['id'] = $msgid;
		$data['Message']['calltype_id'] = $new_ct;
		$data['Message']['calltype'] = $new_ct_title;
    $this->Message->save($data['Message']);     		
		
		$data['MessagesEvent']['message_id'] = $msgid;
    $data['MessagesEvent']['operator_id'] = AuthComponent::user('extension'); 
    $data['MessagesEvent']['user'] = AuthComponent::user('username');         
    $data['MessagesEvent']['description'] = "Changed call type from $old_ct_title to $new_ct_title";    
    $this->Message->MessagesEvent->create();     		
    $this->Message->MessagesEvent->save($data['MessagesEvent']);     		
	}
	
	public function setEmployee($account_id, $msgid) {
		$new_emp = $this->request->data['new_emp'];
		$old_emp = $this->request->data['old_emp'];
		$old_emp_name = $this->request->data['old_emp_name'];
		$new_emp_name = $this->request->data['new_emp_name'];

		$this->layout = 'plain';
		
		$this->loadModel('Employee');
		
		$sql = "select * from ".OA_TBL_PREFIX."employees_contact c where employee_id='$new_emp' order by contact_type asc"; 	
		$data = $this->Employee->query($sql);
		$html = '';
		
		if ($data) {
			foreach ($data as $row) {
				$html .= '<button class="actbtn" id="btn_emp" num="'.$row['c']['contact'].'" cid="'.$row['c']['id'].'" ctype="'.$row['c']['contact_type'].'">'.$row['c']['label'].'</button>';
			}				
		}		
		$json['success'] = true;
		$json['html'] = $html;
		$this->set('json', $json);
		$data['Message']['id'] = $msgid;
		$data['Message']['employee_id'] = $new_emp;
		$this->Message->save($data['Message']);
		
		$data['MessagesEvent']['message_id'] = $msgid;
    $data['MessagesEvent']['operator_id'] = AuthComponent::user('extension'); 
    $data['MessagesEvent']['user'] = AuthComponent::user('username');         
    $data['MessagesEvent']['description'] = "Changed employee from $old_emp_name to $new_emp_name";    
    $this->Message->MessagesEvent->create();     		
    $this->Message->MessagesEvent->save($data['MessagesEvent']);     		
	}

  public function dumpTestData() {
    if ($this->getLoginRole() != 'Superuser') {
      echo '<i>(Not allowed, user role:' . $this->getLoginRole() . ')</i>'; exit;
    }
    else {
      $this->Message->deleteAll(array('did_id' => '1'), true);
      echo '<i>(done)</i>'; exit;
    }
    
  }
	
}
