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
		'limit' => 200,
		'order' => array(
			'Message.id' => 'desc'
		)
	);
	public $components = array('RequestHandler');
	public $helpers = array('Js');  
	protected $did;
	protected $call;
	protected $msg_id;
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('process_hold_until');
	}
	
	public function index($did_id = null) {
		// get a list of employees who can be faxed/ emailed a message summmary of selected messages.  
		$recipients = $this->_get_recipients($did_id);
		$this->set('recipients', $recipients);
		
		// check if we need to search within date range
		$end_date = $start_date = '';
		if (!empty($this->request->data['Search']['m_start_date'])) {
			$start_date = $this->request->data['Search']['m_start_date'];
		}
		else $start_date = $this->request->data['Search']['m_start_date'] = date('Y-m-d', strtotime('-7 day'));
		
		if (!empty($this->request->data['Search']['m_end_date'])) {
			$end_date = $this->request->data['Search']['m_end_date'];
		}
		else $end_date = $this->request->data['Search']['m_end_date'] = date('Y-m-d', strtotime('today'));
		if (!empty($this->request->data['Search']['m_start_time'])) {
			$start_time = date('H:i:s', strtotime('Today ' . $this->request->data['Search']['m_start_time']));
		} 
		else $start_time = '00:00:00';   

		if (!empty($this->request->data['Search']['m_end_time'])) {
			$end_time = date('H:i:s', strtotime('Today ' . $this->request->data['Search']['m_end_time']));
		} 
		else $end_time = '23:59:59';   
						
		$this->Message->recursive = 1;
		
		// unbind unnecessary models
		$this->Message->unbindModel(
				array('hasMany' => array('MessagesPrompt', 'Complaint', 'MessagesDelivery'))
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
				'alias' => 'DidNumber2',
				'type' => 'left',
				'conditions' => array('`DidNumber2`.`id` = `Message`.`did_id`')
			),           
				array(
					'table' => OA_TBL_PREFIX . 'accounts',
					'alias' => 'Account',
					'type' => 'left',
					'conditions' => array('DidNumber2.account_id = Account.id')
				)         
		);	
		$this->paginate['fields'] = array('CallLog.queue', 'Account.account_num', 'Message.*','DidNumber2.timezone', "DATE_FORMAT(CONVERT_TZ(Message.created, '".Configure::read('default_timezone')."', DidNumber2.timezone), '%c/%d/%y %l:%i %p') as createdf", "DATE_FORMAT(CONVERT_TZ(Message.hold_until, '".Configure::read('default_timezone')."', DidNumber2.timezone), '%c/%d/%y %l:%i %p') as hold_until_f", 'User.username', 'DidNumber2.did_number', 'DidNumber2.company');

		if ($did_id) {
			$this->paginate['conditions'] = array(
					'Message.did_id' => $did_id,
			);      
		}
		// check for various search filters
		$extra_conditions = '';
		$conditions = array();
		$search_delivered = $search_undelivered = $search_audited = $search_hold = $search_minder = 0;
		if (isset($this->request->data['Search']['m_type'])) {
			foreach ($this->request->data['Search']['m_type'] as $type) {
				if ($type == 'delivered') {
					$conditions[] = "Message.delivered = '1'";
					 $this->paginate['conditions'][] = "Message.created >= '" . date('Y-m-d H:i:s', strtotime('-14 day')) ."'";
					
					$search_delivered = 1;
				}
				if ($type == 'undelivered') {
					$conditions[] = "Message.delivered = '0'";
					 $this->paginate['conditions'][] = "Message.created >= '" . date('Y-m-d H:i:s', strtotime('-14 day')) ."'";
					$search_undelivered = 1;
				}        
				if ($type == 'unaudited') {
					$conditions[] = "Message.audited = '0'";
					$search_audited = 1;
				}        
				if ($type == 'hold') {
					$conditions[] = "Message.hold = '1'";
					$search_hold = 1;
				}        
				if ($type == 'minder') {
					$conditions[] = "Message.minder = '1'";
					$search_minder = 1;
				 
				}        
			}
		}
		
		// search messages created by a specific user
		if (isset($this->request->data['Search']['user_id']) && $this->request->data['Search']['user_id']) {
			$this->paginate['conditions'][] = "Message.user_id = {$this->request->data['Search']['user_id']}";
			
		}    
		
		// search messages newer than the specified creation date
		if (isset($this->request->data['Search']['m_start_date']) && $this->request->data['Search']['m_start_date']) {
			$this->paginate['conditions'][] = "Message.created >= '$start_date $start_time'";
			
		}    

		// search messages older than the specified creation date
		if (isset($this->request->data['Search']['m_end_date']) && $this->request->data['Search']['m_end_date']) {
			$this->paginate['conditions'][] = "Message.created <= '$end_date $end_time'";
			
		}            
		
		// search for a specific message identified by the message id
		if (isset($this->request->data['Misc']['message_id']) && $this->request->data['Misc']['message_id']) {
			$this->paginate['conditions'] = array();
			$this->paginate['conditions'][] = "Message.id = {$this->request->data['Misc']['message_id']}";
			
		}      
		if (sizeof($conditions)) {
			$extra_conditions = implode (' or ', $conditions);
		}
		if ($extra_conditions) $this->paginate['conditions'][] = '(' . $extra_conditions . ')';
		$this->paginate['joins'] = $joins;
		
		// sort results by specified field if necessary		
		if (isset($this->params['named']['sort'])) {
			$sort = $this->params['named']['sort'];
			$sort_dir = $this->params['named']['direction'];
		}
		else {  // default sort is descending by message id
			$this->request->data['Search']['search'] = '';
			$sort = 'Message.created';
			$sort_dir = 'desc';
		}
		$this->paginate['order'] = array('Message.created' => 'desc');
		
		$messages = $this->paginate();
		// pass parameters to the view
		$this->set('did_id', $did_id);
		$this->set('sort', $sort);
		$this->set('sort_dir', $sort_dir);
		$this->set('Messages', $messages);
		$this->set('search_delivered' , $search_delivered);
		$this->set('search_undelivered' , $search_undelivered);
		$this->set('search_hold' , $search_hold);
		$this->set('search_audited' , $search_audited);
		$this->set('search_minder' , $search_minder);
		$this->set('start_date' , $start_date);
		$this->set('end_date' , $end_date);
		
	}
	public function review($did_id=null) {
		$this->loadModel('AppSetting');
		$conditions = array();
		$default_settings = $this->AppSetting->find('list', array('fields' => array('field','value'), 'conditions' => $conditions));

		$conditions = array('AppSetting.user_id' => AuthComponent::user('id'));
		$user_settings = $this->AppSetting->find('list', array('fields' => array('field','value'), 'conditions' => $conditions));
		
		foreach ($default_settings as $key => $val) {
			if (!isset($user_settings[$key])) $user_settings[$key] = $val;
		}
		
		$this->set('settings', $user_settings);
		
		$this->loadModel('User');
		$users = $this->User->fetchCCStaff();
		foreach($users as $u) {
			$r = array('value' => $u['User']['id'], 'label' => trim($u['User']['firstname']) . ' ' . trim($u['User']['lastname']), 'id' => $u['User']['id'], 'text' => trim($u['User']['firstname']) . ' ' . trim($u['User']['lastname']) . ' - ' .  trim($u['User']['username']));
			$operators[] = $r;
		}
		$this->set('operators', $operators);
	
		$this->index($did_id);
		
	}
	
	public function recent($operator_id) {
		$this->Message->unbindModel(
				array('belongsTo' => array('CallLog', 'DidNumber'))
		);		
		 $joins = array(
			array(
				'table' => OA_TBL_PREFIX . 'did_numbers',
				'alias' => 'DidNumber2',
				'type' => 'left',
				'conditions' => array('`DidNumber2`.`id` = `Message`.`did_id`')
			),
			array(
				'table' => OA_TBL_PREFIX . 'accounts',
				'alias' => 'Account',
				'type' => 'left',
				'conditions' => array('`DidNumber2`.`account_id` = `Account`.`id`')
			)
			);
		$conditions = array('Message.user_id' => $operator_id, 'DidNumber2.deleted' => '0');
		$this->paginate['recursive'] = 0;
		$this->paginate['limit'] = 20;
		$this->paginate['joins'] = $joins;
		$this->paginate['conditions'] = $conditions;
		$this->paginate['fields'] = array("DATE_FORMAT(Message.created, '%a %b %d %y %l:%i %p') as created_f, Account.account_num, Message.delivered, Message.calltype, DidNumber2.company, Message.id, Message.call_id");
		$this->paginate['order'] = array('id' => 'desc');
		$messages = $this->paginate();
		$this->set('messages', $messages);
	}

	public function cleanup() {
        echo 'disabled'; exit;
		if ($this->isAuthorized('MessagesCleanup')) {
		$this->Message->unbindModel(
				array('belongsTo' => array('DidNumber', 'CallLog'))
		);		
			
			if ($this->Message->deleteAll(array('id >' =>  '0'), true)) echo 'deleted ';
			else echo 'failed';
		} 
		echo 'done'; exit; 
	}
	
	public function view_msgs($id = null, $target='msg-detail') {
		$this->set('target', $target);
		$this->index($id);

		if (isset($this->params['named']['sort'])) {
			$sort = $this->params['named']['sort'];
			$sort_dir = $this->params['named']['direction'];
			
		}
		else {
			$sort = 'Message.id';
			$sort_dir = 'desc';

		}	  
	}
/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($did_id = null) { 

		if (!isset($this->request->data['Search']['m_group'])) {
			$this->view_msgs($did_id, 'msg-content');
			$this->render('view_msgs');
			return;
		}	  
		
		if (!empty($this->request->data['Search']['m_start_time'])) {
			$start_time = date('H:i:s', strtotime('Today ' . $this->request->data['Search']['m_start_time']));
		} 
		else $start_time = '00:00:00';   

		if (!empty($this->request->data['Search']['m_end_time'])) {
			$end_time = date('H:i:s', strtotime('Today ' . $this->request->data['Search']['m_end_time']));
		} 
		else $end_time = '23:59:59';


		if (!empty($this->request->data['Search']['m_start_date'])) {
			$start_date = "{$this->request->data['Search']['m_start_date']} $start_time";
		}
		else $start_date = date('Y-m-d', strtotime('-1 day')) . " $start_time";

		if (!empty($this->request->data['Search']['m_end_date'])) {
			$end_date = "{$this->request->data['Search']['m_end_date']} $end_time";
		}
		else $end_date = date('Y-m-d') . " $end_time";
		
	
		$this->Message->recursive = 1;
		$this->Message->unbindModel(
				array('hasMany' => array('MessagesPrompt', 'Complaint', 'MessagesDelivery'))
			);				
		$joins = array(

			array(
				'table' => OA_TBL_PREFIX . 'did_numbers',
				'alias' => 'DidNumber2',
				'type' => 'left',
				'conditions' => array('`DidNumber2`.`id` = `Message`.`did_id`')
			),           
				array(
					'table' => OA_TBL_PREFIX . 'accounts',
					'alias' => 'Account',
					'type' => 'left',
					'conditions' => array('DidNumber2.account_id = Account.id')
				)         
		);	
		$this->paginate['fields'] = array('Account.account_num', 'count(Message.id) as total', 'sum(Message.delivered) as delivered', 'sum(Message.minder) as minder', 'sum(Message.hold) as hold', 'sum(Message.audited) as audited', 'DidNumber2.company', 'DidNumber2.id');

		if ($did_id) {
			$this->paginate['conditions'] = array(
					'Message.did_id' => $did_id,
			);      
		}
		
		$extra_conditions = '';
		$this->paginate['conditions'][] = "Message.created >= '$start_date $start_time'";
		$this->paginate['conditions'][] = "Message.created <= '$end_date $end_time'";
		$conditions = array();
		if (isset($this->request->data['Search']['m_type'])) {
			foreach ($this->request->data['Search']['m_type'] as $type) {
				if ($type == 'delivered') {
					$conditions[] = "Message.delivered = '1'";
				}
				if ($type == 'unaudited') {
					$conditions[] = "Message.audited = '0'";
				}        
				if ($type == 'undelivered') {
					$conditions[] = "Message.delivered = '0'";
				}        
				if ($type == 'hold') {
					$conditions[] = "Message.hold = '1'";
				}        
				if ($type == 'minder') {
					$conditions[] = "Message.minder = '1'";
				}        
			}
		}

		
		if (isset($this->request->data['Search']['message_id']) && $this->request->data['Search']['message_id']) {
			$this->paginate['conditions'] = array();	
			$this->paginate['conditions'][] = "Message.id = {$this->request->data['Search']['message_id']}";
			
		}      
		else if (sizeof($conditions)) {
			$this->paginate['conditions'][] = array ('or' => $conditions);
		}

		if (isset($this->params['named']['sort'])) {
			$sort = $this->params['named']['sort'];
			$sort_dir = $this->params['named']['direction'];
		}
		else {
			$sort = 'count(Message.id)';
			$sort_dir = 'desc';
		}		
		$this->paginate['order'] = array($sort => $sort_dir);
		$this->paginate['limit'] = '50';    
		$this->paginate['joins'] = $joins;
		$this->paginate['group'] = array('Message.did_id');
		$messages = $this->paginate();
		$this->set('did_id', $did_id);
		$this->set('Messages', $messages);
		$this->set('sort', $sort);
		$this->set('sort_dir', $sort_dir);
		

	}

	public function summary($id = null) {
		$this->index($id);

	}


	// get all employees of the DID that has Fax/ Email 
	function _get_recipients($did_id) {
		$this->loadModel('EmployeesContact');
		$conditions = array('EmployeesContact.did_id' => $did_id, "(contact_type = '".CONTACT_EMAIL."' OR contact_type='".CONTACT_FAX."')", 'Employee.deleted' => 0);
		$order = array('contact_type' => 'asc', 'Employee.name' => 'asc');
		$recipients = $this->EmployeesContact->find('all', array('fields' => array('EmployeesContact.id', 'Employee.name', 'EmployeesContact.contact', 'EmployeesContact.contact_type'), 'conditions' => $conditions, 'order' => $order));
		return $recipients;
	}

	public function edit($id = null, $did_id=null, $dir = null) {
		$current = $total = '';
		
		// total is number of messages found in the query, current is the index of the current msg within result
		if (isset($this->request->query['total'])) $total = $this->request->query['total'];
		if (isset($this->request->query['current'])) $current = $this->request->query['current'];
		$this->set('current', $current);
		$this->set('total', $total);
		
		if ($id == 'null' || !is_numeric($id)) $id = null;
		if ($this->request->is('post') || $this->request->is('put')) {
			// nothing to do here
		} 
		else {
			$order_array = array('Message.id' => 'desc');
			 
			// check to see if we need to display message navigation links (next/ prev links)
			if (!empty($current)) $navigation = true;
			else $navigation = false;					  
			$this->set('navigation', $navigation);
			
			$fields = array('Message.*', 'DidNumber.*', 'CallLog.*',   "IF (end_time <> '0000-00-00', UNIX_TIMESTAMP(CallLog.end_time) - UNIX_TIMESTAMP(CallLog.start_time), 'UNKNOWN') as duration",
		"DATE_FORMAT(start_time, '%a %b %D, %Y %l:%i %p') as start_time_f", "DATE_FORMAT(CONVERT_TZ(Message.created, '".Configure::read('default_timezone')."', DidNumber.timezone), '%a %c/%d/%y %l:%i %p') as createdf");
				$this->Message->unbindModel(
					array('hasMany' => array('Calltypes'))
				);	

			if (!isset($this->request->data['Message'])) $this->request->data = $this->Message->find('first', array('fields' => $fields, 'conditions' => array('Message.id' => $id)));

			if (isset($this->request->data['Message'])) {
				$this->logEvent($this->request->data['Message']['call_id'], "Msg ID# ". $this->request->data['Message']['call_id']. ' accessed by ' . AuthComponent::user('username'), EVENT_MSGVIEW);			    
				// fetch any scheduling appointments made for this call/message
				$appts = $this->_getAppointments($this->request->data['Message']['call_id'], true);
				$this->set('appts', $appts);
				
				// check if the message is being held and correct for timezone
				if (!empty($this->request->data['Message']['hold_until'])) {
					$oa_timezone = Configure::read('default_timezone');          
					$date1 = new DateTime($this->request->data['Message']['hold_until']);
					$date1->setTimezone(new DateTimeZone($this->request->data['DidNumber']['timezone']));
					$this->request->data['Message']['hold_until'] = $date1->format('Y-m-d g:i a');          
				}
				
				// look up account number
				$this->loadModel('Account');
				$this->Account->id = $this->request->data['DidNumber']['account_id'];
				$this->set('account_num', $this->Account->field('account_num'));
				// lookup the client timezone
				if ($this->request->data['DidNumber']['timezone']) $client_tz = $this->request->data['DidNumber']['timezone'];
				else $client_tz = Configure::read('default_timezone');
				$this->set('client_tz', $client_tz);
				
				// fetch the agent script for this particular calltype shceudle
				$schedule_id = $this->request->data['Message']['schedule_id'];
				$this->set('schedule_id', $schedule_id);
				$data = $this->instructions($this->request->data['Message']['did_id'], $this->request->data['Message']['schedule_id']);
				
				// fetch the edit history of the caller prompts
				$this->loadModel('MessagesPromptsEdit');
				$this->set('edited', $this->MessagesPromptsEdit->find('first', array('conditions' => array('message_id' => $this->request->data['Message']['id']))));
				
				// set view parameters  			
				$this->set('message_id', $id);
				$this->set('did_id', $this->request->data['Message']['did_id']);
				$this->set('data', $data);
			}
			
		}
	}
	

	// sets status of a message
	public function setStatus($message_id, $status, $value) {
		if (!in_array($status, array('urgent', 'delivered', 'minder', 'hold'))) {
			$this->Session->setFlash(__('Invalid message status'), 'flash_jsonbad');
			$this->render('/Elements/json_result');
		}
		$data = $this->Message->find('first', array('conditions' => array('id' => $message_id), 'recursive' => 0));
		if ($data) {
			$data['Message'][$status] = $value;      
			if ($this->Message->save($data['Message'])) logEvent($msg_id, "Message Status: $status set to $value", $data['Message']['call_id']);
		}
		else {
			$this->Session->setFlash(__('Invalid message'), 'flash_jsonbad');
		}
		$this->render('/Elements/json_result');

	}
	
	public function msg_deliveries($message_id) {
			$joins = array(
				array(
					'table' => OA_TBL_PREFIX . 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('`User`.`id` = `MessagesDelivery`.`delivered_by_userid`')
				),  
				array(
					'table' => OA_TBL_PREFIX . 'messages',
					'alias' => 'Message2',
					'type' => 'left',
					'conditions' => array('Message2.id = MessagesDelivery.message_id')
				),
				array(
					'table' => OA_TBL_PREFIX . 'did_numbers',
					'alias' => 'DidNumber',
					'type' => 'left',
					'conditions' => array('DidNumber.id = Message2.did_id')
				)        
			);		
		
		$data = $this->Message->MessagesDelivery->find('all', array('fields' => array("DATE_FORMAT(CONVERT_TZ(MessagesDelivery.delivered_time, '".Configure::read('default_timezone')."', DidNumber.timezone), '%c/%d/%y %l:%i %p') as delivered_time_f", 'User.username', 'MessagesDelivery.*'),'joins' => $joins, 'order' => array('MessagesDelivery.id' => 'desc'), 'conditions' => array('message_id' => $message_id), 'recursive' => '0'));
		$this->set('data', $data);
		$this->set('message_id', $message_id);
	}



	public function msg_event_log($message_id) {
		$data = $this->Message->MessagesEvent->find('all', array('conditions' => array('message_id' => $message_id), 'recursive' => '0'));
		$this->set('data', $data);
	}

	public function delete($id = null) {
		$this->layout = 'plain';
		$this->Message->unbindModel(
				array('belongsTo' => array('Client'))
		);				
		$data = $this->Message->findById($id);
		
		if (sizeof($data['MessagesEvent']) < 1) {
			$this->Message->delete($id);
		}
	}
	
	public function undeliver($message_id, $call_id) {
		$this->layout = 'plain';
		$data['Message']['id'] = $message_id;
		$data['Message']['delivered'] = '0';
		$data['Message']['summary_last_sent'] = '';
		$data['Message']['active_ts'] = date('Y-m-d H:i:s');
		
		if ($this->Message->save($data['Message']))	{
			$this->logEvent($call_id, "Marked UNDELIVERED by " . AuthComponent::user('username'), EVENT_UNDELIVER, '');       
			$this->Session->setFlash(__('The delivery status was updated'), 'flash_jsongood');
			$this->loadModel('MessagesSummarySent');
			$this->MessagesSummarySent->deleteAll(array('message_id' => $message_id));
		}
		else {
			$this->Session->setFlash(__('The delivery status could not be saved. Please, try again.'), 'flash_jsonbad');
		}	
	}
	
	public function process_hold_until() {
		$conditions = array("created >= '".date('Y-m-d H:i:s', strtotime('-7 day'))."'", 'hold' => '2', 'hold_until < ' => date('Y-m-d H:i:s'));
		$this->Message->recursive = 0;
			$this->Message->unbindModel(
					array(
						'belongsTo' => array('CallLog', 'DidNumber')
					)
			);		  
		$messages = $this->Message->find('all', array('conditions' => $conditions));
		if ($messages) {
			//print_r($messages);
			foreach ($messages as $m) {
				
				$m['Message']['hold'] = '0';
				$m['Message']['hold_until'] = '';
				$m['Message']['minder'] = '1';
				$m['Message']['minder_ts'] = date('Y-m-d H:i:s');
				$call_id = $m['Message']['call_id'];
				//print_r($m['id']);
				if ($this->Message->save($m['Message'])) {
					$this->logEvent($call_id, "Remove Hold-Until, message sent to minder", EVENT_UNHOLD, ''); 
				}
			}
		}
		//echo '<br><br>done'; 
		exit;
	}
	
	public function hold_until($message_id, $call_id) {
		$this->layout = 'plain';
		if ($this->request->is('post')) {
			$this->Message->recursive = 0;
			$this->Message->unbindModel(
					array(
						'belongsTo' => array('CallLog')
					)
			);	
			
			$old = $this->Message->findById($message_id);		  
			$client_timezone = $old['DidNumber']['timezone'];
			$oa_timezone = Configure::read('default_timezone');
			
			$this->request->data['Message']['minder'] = '0';
			$this->request->data['Message']['minder_ts'] = '0000-00-00 00:00:00';
			$this->request->data['Message']['delivered'] = '0';
			$hour = $this->request->data['Message']['hold_until']['hour'];
			if ($this->request->data['Message']['hold_until']['meridian'] == 'pm' && ($hour < 12)) $hour =  $hour+12;
			else $hour =  $this->request->data['Message']['hold_until']['hour'];
			$hold_date = $this->request->data['Message']['hold_until']['year'] . '-' . $this->request->data['Message']['hold_until']['month'] . '-' . $this->request->data['Message']['hold_until']['day'] . ' ' . sprintf("%02d", $hour) . ':' . $this->request->data['Message']['hold_until']['min'] . ":00";

			$date1 = new DateTime($hold_date, new DateTimeZone($client_timezone));
			$date1->setTimezone(new DateTimeZone($oa_timezone));
			$this->request->data['Message']['hold_until'] = $date1->format('Y-m-d H:i:s');
			
			unset($this->request->data['Message']['year']);
			unset($this->request->data['Message']['month']);
			unset($this->request->data['Message']['day']);
			unset($this->request->data['Message']['hour']);
			unset($this->request->data['Message']['min']);
			unset($this->request->data['Message']['meridian']);
		
			
			if ($this->Message->save($this->request->data['Message'])) {
				$this->logEvent($call_id, "Hold Until ". $hold_date);       
				$this->Session->setFlash(__('The message has been put on hold'), 'flash_jsongood');
			}
			else {
				$this->Session->setFlash(__('Cannot put message on hold'), 'flash_jsonbad');
			}
			$this->render('/Elements/json_result');
			
		}
		else {
			$this->Message->recursive = 0;
			$this->Message->unbindModel(
					array(
						'belongsTo' => array('CallLog')
					)
			);	
			
			$this->request->data = $this->Message->findById($message_id);
			$date = new DateTime($this->request->data['Message']['hold_until']);
			$date->setTimezone(new DateTimeZone($this->request->data['DidNumber']['timezone']));
			if ($this->request->data['Message']['hold_until']) {      
				$this->request->data['Message']['hold_until'] = $date->format('Y-m-d H:i:s');
			}       
			$this->set('current_time', $date->format('D m/d/Y g:ia T'));
			$this->set('message_id', $message_id);
			$this->set('call_id', $call_id);
		}
	}

	public function deliver_lmr($message_id, $call_id, $employee_id) {
		
		$this->layout = 'plain';
		$this->Message->id = $message_id;
		$data['Message']['id'] = $message_id;
		$data['Message']['delivered'] = '1';
		$data['Message']['minder'] = '0';
		$data['Message']['hold'] = '0';


			$delivery['message_id'] = $message_id;
			$delivery['delivered_time'] = date('Y-m-d H:i:s');
			$delivery['delivery_name'] = $this->request->data['name'];
			$delivery['delivery_contact'] = '';
			$delivery['delivery_contact_id'] = '';
			$delivery['delivery_contact_label'] = '';
			$delivery['employee_id'] = $employee_id;
			$delivery['delivered_by_userid'] = AuthComponent::user('id');
			$delivery['delivered_by_ext'] = $this->user_extension;
			$delivery['delivery_method'] = CONTACT_LMR;
		if ($this->Message->MessagesDelivery->save($delivery)) 
	
		
//		if ($this->Message->saveField('delivered', '1'))
		if ($this->Message->save($data)) 
		{
			$this->logEvent($call_id, "Marked DELIVERED by LMR by " . AuthComponent::user('username'), EVENT_DELIVER, '');       
			$this->Session->setFlash(__('The delivery status was updated'), 'flash_jsongood');
		}
		else {
			$this->Session->setFlash(__('The delivery status could not be saved. Please, try again.'), 'flash_jsonbad');
		}
		$this->render('/Elements/json_result');
		
	}	

	public function deliver($message_id, $call_id) {
		$this->layout = 'plain';
		$this->Message->id = $message_id;
		$data['Message']['id'] = $message_id;
		$data['Message']['delivered'] = '1';
		$data['Message']['minder'] = '0';
		$data['Message']['hold'] = '0';
		
//		if ($this->Message->saveField('delivered', '1'))
		if ($this->Message->save($data)) 
		{
			$this->logEvent($call_id, "Marked DELIVERED by " . AuthComponent::user('username'), EVENT_DELIVER, '');       
			$this->Session->setFlash(__('The delivery status was updated'), 'flash_jsongood');
		}
		else {
			$this->Session->setFlash(__('The delivery status could not be saved. Please, try again.'), 'flash_jsonbad');
		}
	}
	public function unminder($message_id, $call_id) {
		$this->layout = 'plain';
		$this->Message->id = $message_id;
		$data['Message']['id'] = $message_id;
		$data['Message']['active_ts'] = date('Y-m-d H:i:s');
		$data['Message']['minder'] = 0;
		if ($this->Message->save($data['Message']))
		{
			$this->logEvent($call_id, "Set to UNMINDER by " . AuthComponent::user('username'), EVENT_UNMINDER, '');       
			$this->Session->setFlash(__('The minder status was updated'), 'flash_jsongood');
		}
		else {
			$this->Session->setFlash(__('The minder status could not be saved. Please, try again.'), 'flash_jsonbad');
		}	
	}
	
	public function minder($message_id, $call_id) {
		$this->layout = 'plain';
		$data['Message']['id'] = $message_id;
		$data['Message']['minder'] = 1;
		$data['Message']['minder_ts'] = date('Y-m-d H:i:s');
		$data['Message']['deliver'] = 0;
		if ($this->Message->save($data['Message']))
		{
			$this->logEvent($call_id, "Set to MINDER and UNDELIVERED by " . AuthComponent::user('username'), EVENT_MINDER, '');       
			$this->Session->setFlash(__('The minder status was updated'), 'flash_jsongood');
		}
		else {
			$this->Session->setFlash(__('The minder status could not be saved. Please, try again.'), 'flash_jsonbad');
		}
	}

	
	function instructions($did_id, $schedule_id) { 
		return $this->_instructions($did_id, null, $schedule_id);
				
	}
	

	function save_prompts($call_id) {
		$this->Message->CallLog->unbindModel(
			array('hasMany' => array('CallEvent'))
		);	
	  $old_message = $this->Message->CallLog->findById($call_id);
	  
	  if ($old_message) {
      $msg_id = $old_message['Message']['id'];
      $old_schedule_id = $old_message['Message']['schedule_id'];
			$new_schedule_id = $this->request->data['new_schedule_id'];
      $old_ct = $old_message['Message']['calltype_id'];    

			$new_ct = $this->request->data['new_ct'];
			$did_id = $old_message['Message']['did_id'];

			$old_ct_title = $old_message['Message']['calltype'];
			$new_ct_title = $this->request->data['new_ct_title'];
			
			// get old calltype instructions verbiage
			$old_instructions = $old_message['Message']['calltype_instructions'];
			
			// get new calltype instructions verbiage
			if (empty($this->request->data['instructions'])) $new_instructions = $this->_getInstructions($did_id, $old_message['Message']['schedule_id'], true);  		    
			else $new_instructions = $this->request->data['instructions'];
			
			// filter out superficial change in the instructions text triggered by the 'show disp' button on the operator screen
			$new_instructions = str_replace('dispatcher2', 'dispatcher', $new_instructions);
					
			// set message to new calltype
			$this->layout = 'plain';		
			$data['Message']['id'] = $msg_id;
			$data['Message']['calltype_id'] = $new_ct;
			$data['Message']['schedule_id'] = $new_schedule_id;
			$data['Message']['calltype'] = $new_ct_title;
			$data['Message']['calltype_instructions'] = $new_instructions;

			$save_ok = $this->Message->save($data['Message']);
			if ($save_ok) {
				// if instructions have changed, then log in call log
				if ($old_instructions != $new_instructions) {
					$this->logEvent($call_id, "Instruction changed from ".$old_instructions." to $new_instructions");
		
				}
				
				// if calltype has changes, then log in call log
				if ($new_ct != $old_ct) {
					$this->logEvent($call_id, "Calltype changed from '$old_ct_title' to '$new_ct_title'");
				}
				
				if ($new_schedule_id != $old_schedule_id) {
					$this->logEvent($call_id, "Schedule changed from '$old_schedule_id' to '$new_schedule_id'");
				}				
				
				// get old prompts
				$old_prompts = $this->Message->MessagesPrompt->find('all', array('order' => array('sort' => 'asc'), 'conditions' => array('message_id' => $msg_id)));
				$old = array();
				$new = array();
				$edits = array();
				$edit_time = date('Y-m-d H:i:s');
				if ($old_prompts) {
					foreach($old_prompts as $k => $p) {
						// stamp edit time and record user who made the change
						$p['MessagesPrompt']['edit_time'] = $edit_time;
						$p['MessagesPrompt']['user_id'] = AuthComponent::user('id');
						$p['MessagesPrompt']['user_name'] = AuthComponent::user('username');
						$old[$p['MessagesPrompt']['caption']] = $p['MessagesPrompt']['value'];          
						unset($p['MessagesPrompt']['id']);
						$edits[] = $p['MessagesPrompt'];
					}				

				}
        foreach ($this->request->data['ptitle'] as $action_num => $v1) {				
				  foreach ($v1 as $k => $v) {
					  $new[$v] = $this->request->data['pvalue'][$action_num][$k];
					}
				}
				
				//compare old and new prompts and save changes if different
				if ($new == $old) {
					// NOTE: /View/Message/edit.ctp parses for the phrase 'No changes', do not change message text
					// without editing the view file
					if ($new_ct == $old_ct) $this->Session->setFlash('No changes were detected, so nothing was saved', 'flash_jsongood');
					else $this->Session->setFlash('Calltype change has been saved', 'flash_jsongood');
				}
				else {
					$this->loadModel('MessagesPromptsEdit');
					// save the current prompts
					$this->MessagesPromptsEdit->create();
					if (sizeof($edits) < 1 || $this->MessagesPromptsEdit->saveMany($edits)) {
						$saveok = true;
						
						$this->Message->MessagesPrompt->deleteAll(array('message_id' => $msg_id), false);
						// now save new prompts
						foreach ($this->request->data['ptitle'] as $action_num => $v1) {
						  foreach ($v1 as $k => $v) {
  							if (trim($this->request->data['ptitle'][$action_num][$k]) || trim($this->request->data['pvalue'][$action_num][$k])) {
  								$data['MessagesPrompt']['message_id'] = $msg_id;
  								$data['MessagesPrompt']['caption'] = $this->request->data['ptitle'][$action_num][$k];
  								$data['MessagesPrompt']['value'] = $this->request->data['pvalue'][$action_num][$k];
  								$data['MessagesPrompt']['options'] = $this->request->data['poptions'][$action_num][$k];
  								if ($this->request->data['ptitle'][$action_num][$k] == 'Misc') {
  								  if (trim($this->request->data['pvalue'][$action_num][$k]) == '') {
  								    continue;
  								  }
  								  $data['MessagesPrompt']['sort'] = '99';
  								}
  								else $data['MessagesPrompt']['sort'] = $k;
  								$data['MessagesPrompt']['ptype'] = $this->request->data['ptype'][$action_num][$k];
  								$data['MessagesPrompt']['action_num'] = $action_num;
  								$data['MessagesPrompt']['maxchar'] = $this->request->data['pmaxchar'][$action_num][$k];
  								$this->Message->MessagesPrompt->create();
  								$saveok = $saveok & $this->Message->MessagesPrompt->save($data['MessagesPrompt']);
  							}
  						}
						}
						
						$this->logEvent($call_id, "Message modified by " . AuthComponent::user('username'), EVENT_MSGEDITED, '');       
						
						if ($saveok) {
							$this->Session->setFlash('The changes have been saved', 'flash_jsongood');
						}
						else $this->Session->setFlash('Cannot save all of the changes', 'flash_jsonbad');
					}
					else {
						$this->Session->setFlash('Cannot save the changes', 'flash_jsonbad');          
					}
													
				}		    
			}

	  }	
  	else {
	  	$this->Session->setFlash('Cannot save the changes', 'flash_jsonbad');          
		}	  
  	$this->render('/Elements/json_result');		
	}

    		
	public function get_calltype_instructions($schedule_id, $msgid, $call_id, $did_id) {
		$new_ct = $this->request->data['new_ct'];
		$old_ct = $this->request->data['old_ct'];

		$this->Message->unbindModel(
				array(
					'hasMany' => array('MessagesDelivery', 'Mistake', 'Complaint'),
					'belongsTo' => array('DidNumber', 'CallLog')
				)
		);	


		$old_data = $this->Message->findById($msgid);
		$old_schedule_id = $old_data['Message']['schedule_id'];
		
        if ($schedule_id != $old_schedule_id) $new_instructions = $this->_getInstructions($did_id, $schedule_id, true);  		    
        else $new_instructions = $old_data['Message']['calltype_instructions'];

		$prompts = $old_data['MessagesPrompt'];
		$old_prompts = $old_prompts_by_action_num = array();
		$i = 1;
		foreach($prompts as $k => $val) {
			if (!trim($val['caption'])) {
				$caption = 'unknown' . $i;
				$i++;
			}
			else $caption = trim($val['caption']);
			

            // check if we're fetching instructions that were displayed on the operator screen at the time the message was taken
            // if so, then we can match up the prompts using the action number to get exact match
		    if ($old_schedule_id == $schedule_id) {
			    $old_prompts_by_action_num[$val['action_num']][$caption] = $val;
		    }
		    
		    $old_prompts[$caption] = $val;
		}
		$this->layout = 'plain';		
		if (1) {
			$data = $this->Message->query("select p.*, a.sort, a.section from ".OA_TBL_PREFIX."actions a left join ".OA_TBL_PREFIX."prompts p on a.id=p.action_id  where a.schedule_id='$schedule_id' and p.id is not null order by a.sort, p.sort");
			$html =  '<table class="prompts" cellpadding="4" cellspacing="0">';
			$cnt = 0;


			$old_action_num = '';
			$first = true;
			foreach ($data as $k => $v) {
			    
				$rclass = '';
				$p = $v['p'];

                $val = '';				
                if ($old_schedule_id == $schedule_id) {
                    
                    if (isset($old_prompts_by_action_num[$v['a']['sort']][$p['caption']])) {
                        $val = trim($old_prompts_by_action_num[$v['a']['sort']][$p['caption']]['value']);  
               			unset($old_prompts[$p['caption']]);
                    }
                    else if (isset($old_prompts[$p['caption']])) {
                        $val = trim($old_prompts[$p['caption']]['value']);
               			unset($old_prompts[$p['caption']]);
               		}

    		    }
    		    else {
        			if (isset($old_prompts[$p['caption']])) $val = trim($old_prompts[$p['caption']]['value']);
        			unset($old_prompts[$p['caption']]);
    		    }

				if ($p['action_id'] != $old_action_num) {
					if (!$first) $rclass ='section section' . $v['a']['section'];
					$first = false;
				}	
				
				$html .= '<input type="hidden" name="ptitle[1][]"  value="'.$p['caption'].'">';
				$html .= '<input type="hidden" name="ptype[1][]"  value="'.$p['ptype'].'">';
				$html .= '<input type="hidden" name="pmaxchar[1][]"  value="'.$p['maxchar'].'">';
				$html .= '<input type="hidden" name="poptions[1][]" value="'.$p['options'] .'">';
				if ($p['ptype'] == '2')  {
					$html .= '<tr class="'.$rclass.'" section="'.$v['a']['section'].'"><td>' . $p['caption'] . '</td><td><textarea class="uprompt" onfocus="if ($(\'#save_msg_button\').is(\':hidden\')) {this.blur();}" name="pvalue[1][]" rows="2" cols="50" >'.$val.'</textarea></td></tr>';
				}
				else if ($p['ptype'] == '4' ) {
						$html .= '<tr section="" class="'.$rclass.'" section="'.$v['a']['section'].'"><td>' . $p['caption'] . '</td><td>';
						
						$temp = explode('||', $p['options']);
						
						$poptions = explode('|', $temp[0]);
						$pactions = explode('|', $temp[1]);
						$html .= '<textarea class="is_hidden">'.$val.'</textarea><select onfocus="if ($(\'#save_msg_button\').is(\':hidden\')) {this.blur();}" data-action-num="'. $v['a']['sort'].'"  class="conditional uprompt " ';
						//if ($element_id) $html .= 'id="'. $element_id . '" ';
//echo 'onchange="if (isEditable(this)) logCallEvent(\''.$this->request->data['Message']['call_id'].'\', \'[PROMPT] \' + $(this).siblings(\'label\').html() + \': \' + $(this).val(), \''.EVENT_FILL_PROMPT.'\');"  
						$html .= ' class="uprompt" name="pvalue[1][]"><option value="">Select</option>';
						foreach ($poptions as $k=> $o) {
								$html .= '<option value="'.$o.'" data-action="'.$pactions[$k].'"';
								if ($val == $o) $html .= ' selected';
								$html .= '>'.$o.'</option>';
						}
						$html .= '</select>';                                
						$html .= '</td></tr>';
				}
				
				
				
				else {
					$html .= '<tr class="'.$rclass.'" section="'.$v['a']['section'].'"><td>' . $p['caption'] . '</td><td><input class="uprompt" onfocus="if ($(\'#save_msg_button\').is(\':hidden\')) {this.blur();}" type="text" name="pvalue[1][]" value="'. $val . '" size="50"></td></tr>';
				}
				//unset($old_prompts[$p['caption']]);
				$cnt++;
				$old_action_num = $p['action_id'];
			}
			// new calltype has less prompts than the old, create extra fields
			foreach ($old_prompts as $v) {
    				$html .= '';
    				$html .= '<input type="hidden" name="ptype[1][]"  value="'.$v['ptype'].'">';
    				$html .= '<input type="hidden" name="pmaxchar[1][]"  value="'.$v['maxchar'].'">';
    				if ($v['ptype'] == '2') {
    					$html .= '<tr><td><input type="text" name="ptitle[1][]"  class="pcaption" onfocus="if ($(\'#save_msg_button\').is(\':hidden\')) {this.blur();}" value="'.$v['caption'].'"></td><td><textarea class="uprompt" name="pvalue[1][]" onfocus="if (!msg_editable) {this.blur();}" rows="2" cols="50">'.$v['value'].'</textarea>';
                    }
    
    				else {
    					$html .= '<tr><td><input type="text" name="ptitle[1][]"  class="pcaption" onfocus="if ($(\'#save_msg_button\').is(\':hidden\')) {this.blur();}" value="'.$v['caption'].'"></td><td><input class="uprompt" type="text" name="pvalue[1][]" onfocus="if (!msg_editable) {this.blur();}"value="'. $v['value'] . '" size="50">';
    				}
    				$html .= '</td></tr>';
			}
			
			$html .= '</table>';
						
			$msg = "Calltype found";    	
			$success = true;
			$prompts_html = $html;
			
			$instructions_html = $new_instructions;
		}
		else {
			$success = false;
			$msg = "Calltype cannot be changed";    	
			$instructions_html = $prompts_html = '';
		}
		$this->set(compact('msg', 'success', 'instructions_html', 'prompts_html'));
		$this->set('_serialize', array('msg', 'success', 'instructions_html', 'prompts_html'));

	}
	

	
	// resets the minder timestamp when dispatcher dials out
	public function reset_minder($call_id) {
		$m = $this->Message->find('first', array('conditions' => array('Message.call_id' => $call_id)));
		if ($m) {
			if ($m['Message']['minder']) {
				$m['Message']['minder_ts'] = date('Y-m-d H:i:s');
				if ($this->Message->save($m['Message'])) {
					$this->Session->setFlash(__('The minder has been reset'), 'flash_jsongood');          
				}
				else {
					$this->Session->setFlash(__('Minder cannot be reset'), 'flash_jsonbad');
				}
			}
			else $this->Session->setFlash('The minder has been reset', 'flash_jsongood');   
		}
		else {
			$this->Session->setFlash('The minder has been reset', 'flash_jsongood');          
		}
		$this->render('/Elements/json_result');	        

	}
	
	// this function will create a message if one does not exist for the call specified by the call id
	// otherwise it will return the message and will also update the calltype if necessary
	function _create_msg($call_id, $did_id, $schedule_id,  $delivered, $minder, $hold, $contact_id) {
		$message = $this->Message->find('first', array('recursive' => '0', 'conditions' => array('Message.call_id' => $call_id)));
		if ($contact_id) {
			$this->loadModel('EmployeesContact');
			$contact = $this->EmployeesContact->findById($contact_id);
		}
		
		// if a schedule id is specified then retrieve the calltype
		if (!empty($schedule_id)) {
			$this->loadModel('Schedule');
			$this->Schedule->bindModel(
				array('belongsTo' => 
					array(
						'Calltype' => array(
							'foreignKey' => 'calltype_id'
						)
					)
				)
			);     
			$schedule = $this->Schedule->findById($schedule_id);
			$did_id = $schedule['Schedule']['did_id'];
		}		

		if (empty($message['Message']['id'])) {
			// no message exists, so create one
			$message['Message']['did_id'] = $did_id;
			$message['Message']['schedule_id'] = $schedule_id;
			$message['Message']['user_id'] = AuthComponent::user('id');
			$message['Message']['user_name'] = AuthComponent::user('username');
			$message['Message']['extension'] = $this->user_extension;
			$message['Message']['call_id'] = $call_id;
			$message['Message']['delivered'] = $delivered;
			$message['Message']['minder'] = $minder;
			$message['Message']['hold'] = $hold;
			
			// this will keep track of the last employee that was contacted.  The employee picker on the 
			// message review screen will default to the last person contacted
			if (isset($contact)) $message['Message']['last_eid'] = $contact['EmployeesContact']['employee_id'];
			
			$ts = date('Y-m-d H:i:s');
			$message['Message']['active_ts'] = $ts;
			$message['Message']['created'] = $ts;
			
			// set the calltype if the info is available
			if (isset($schedule)) {
				$message['Message']['calltype']= $schedule['Calltype']['title'];
				$message['Message']['calltype_id']= $schedule['Calltype']['id'];				
			}

			// get a user-friendly listing of calltype instructions
			if ($schedule_id) {
  			// get new calltype instructions verbiage
  			if (empty($this->request->data['instructions'])) $new_instructions = $this->_getInstructions($did_id, $schedule_id, true);  		    
  			else $new_instructions = $this->request->data['instructions'];
				$message['Message']['calltype_instructions'] = $new_instructions;
			}
			$this->Message->create();
			$this->Message->save($message['Message']);
			$message['Message']['id'] = $this->Message->getLastInsertID();	
		}
		else {
			// check if we need to update the calltype by comparing new vs old
			if ($schedule_id) {
				if ($schedule['Calltype']['id'] != $message['Message']['calltype_id']) {
					$message['Message']['calltype']= $schedule['Calltype']['title'];
					$message['Message']['calltype_id']= $schedule['Calltype']['id'];					
					$data = $this->_getInstructions($schedule['Schedule']['did_id'], $schedule_id, true);
					$message['Message']['calltype_instructions']= $data;
					$this->Message->save($message['Message']);
				}			
			}
		}	
		return $message;
	}
		
	public function dial($call_id) {
		$contact_id = $this->request->data['contact_id'];
		$outbound_id = '';
		
		// create a record of the outbound call
		if (!empty($this->request->data['num_dialed'])) {
			$num_dialed = $this->request->data['num_dialed'];
			$unique_id = empty($this->request->data['unique_id'])? '':$this->request->data['unique_id'] ;
			$did_id = empty($this->request->data['did_id'])? '0':$this->request->data['did_id'] ;
		
			$d['user_id'] = AuthComponent::user('id');
			$d['extension'] = $this->user_extension;
			$d['called_num'] = $num_dialed;
			$d['did_id'] = $did_id;
			$d['call_id'] = $call_id;
			$d['incoming_unique_id'] = $unique_id;
			$this->loadModel('Outbound');
		
			$this->Outbound->create();
			$this->Outbound->save($d);
			$outbound_id = $this->Outbound->getLastInsertID();	
		}
		else {
			$msg = 'Unable to dialout...';
			$success = false;
			$this->Session->setFlash(__('Unable to dialout...'), 'flash_jsonbad');
			return;
		}
		$this->set('outbound_id', $outbound_id);
		
		$schedule_id = $this->request->data['schedule_id'];
		
		$message = $this->_create_msg($call_id, $did_id, $schedule_id, 0, 0, 0, $contact_id);
		$message_id = $this->msg_id = $message['Message']['id'];

		$prompts = $this->_getPrompts();
		
		$save_ok = $this->logMessage($call_id, CONTACT_PHONE, 'dial', $message_id,  $prompts, $contact_id, $message['Message']['minder']);			

		if ($save_ok) {
			$msg = 'The dial out has been recorded...';
			$success = true;
		} 
		else {
			$msg = 'Unable to dialout...';
			$success = false;
			$this->Session->setFlash(__('Unable to dialout...'), 'flash_jsonbad');
		}
		$this->set('json', array('success' => $success, 'msg' => $msg, 'outbound_id' => $outbound_id));
		$this->render('result');					  
	}
	
	public function transfer($call_id,  $msg_action) {
		$contact_id = $this->request->data['contact_id'];
		$schedule_id = $this->request->data['schedule_id'];
		if (!empty($this->request->data['did_id'])) $did_id = $this->request->data['did_id'];
		else $did_id = '';
		
		$hold = 0;
		$minder = 0;
		if ($msg_action == 'deliver') {
			$delivered = '1';
		}
		else {
			$delivered = '0';
		}
		
		$message = $this->_create_msg($call_id, $did_id, $schedule_id, $delivered, $minder, $hold, $contact_id);		
		$message_id = $this->msg_id = $message['Message']['id'];
		$prompts = $this->_getPrompts();
		
		$save_ok = $this->logMessage($call_id, CONTACT_PHONE, $msg_action, $message_id,  $prompts, $contact_id, $message['Message']['minder']);			

		if ($save_ok) {
			$this->Session->setFlash(__('The transfer has been recorded...'), 'flash_jsongood');
		} 
		else {
			$this->Session->setFlash(__('Unable to record the transfer...'), 'flash_jsonbad');
		}
		$this->render('/Elements/json_result');				
		
	}
	

	// execute an action in the call type script
	public function execute($action_id, $call_id, $contact_id=null) {
		if ($this->request->is('post')) {
			if (!isset($this->CallLog)) $this->loadModel('CallLog');

			$this->CallLog->unbindModel(
				array('hasMany' => array('CallEvent'))
			);				

			$this->call = $this->CallLog->find('first', array('recursive' => 0, 'conditions' => array('CallLog.id' => $call_id)));
			if (!isset($this->Action)) $this->loadModel('Action');		  
			//look up details of the action to be executed and the call details
			
			$action = $this->Action->find('first', array('conditions' => array('Action.id' => $action_id)));

			if ($action && $call_id) {
				// get the employee ids associated with this call
				$emp_ids = $action['Action']['eid'];
				if ($emp_ids) $employees = $this->_getEmployees($emp_ids);
				else $employees = null;
				
				$this->call_id = $call_id;
				$schedule_id = $action['Action']['schedule_id'];
				$did_id = $action['Action']['did_id'];
				// check if message entry already exists.  If not, create one.
				$message = $this->_create_msg($call_id, $did_id, $schedule_id, '0', '0', 0, '');		
				
			  $msg = $message['Message'];
				$message_id = $this->msg_id = $message['Message']['id'];				

				$this->Message->DidNumber->recursive = 0;
				$this->did = $this->Message->DidNumber->findById($did_id);

				// find account number and company name for populating message notification
				$account_num = $this->did['Account']['account_num'];
				$company = $this->did['DidNumber']['company'];
				
				// check the email format the client prefers (html/text)
				$email_format = ($this->did['DidNumber']['email_format'] == '0')? 'both': 'text';
				
				if ($action['Action']['action_type'] == ACTION_TXTMSG || $action['Action']['action_type'] == ACTION_TEXT_DELIVER) {
					$prompts = $this->_getPrompts();
					$appts = $this->_getAppointments($call_id);
					
					$save_ok = $this->_text('Text', $this->request->data['bdata'], $prompts, $message['Message']['calltype'], $account_num, $appts['active']);
					if ($save_ok) {
						$this->logEvent($call_id, __('Text sent to queue: ' . $this->request->data['bdata']), EVENT_DELIVERY);
					
						if ($action['Action']['action_type'] == ACTION_TEXT_DELIVER)
							$this->logMessage($call_id, CONTACT_TEXT, 'deliver', $this->msg_id, $prompts, $emp_ids, $msg['minder']);		
						else
							$this->logMessage($call_id, $this->request->data['btype'], '', $this->msg_id, $prompts, $emp_ids, $msg['minder']);		
						$this->Session->setFlash(__('Text sent to queue: '.$this->request->data['bdata']), 'flash_jsongood');
					}
					else {
						$this->Session->setFlash(__('Unable to send the message to queue, try again...'), 'flash_jsonbad');
						$this->logEvent($call_id, __('Unable to send text to queue'), EVENT_DELIVERY);
						
					}
				}	
				else if ($action['Action']['action_type'] == ACTION_FAX || $action['Action']['action_type'] == ACTION_FAX_DELIVER) {
					$prompts = $this->_getPrompts();
					$appts = $this->_getAppointments($call_id);
					$save_ok = $this->_fax($msg, 'Fax', $this->request->data['bdata'], $prompts, $message['Message']['calltype'], $account_num, '', $appts['active']);
					if ($save_ok) {
						$this->logEvent($call_id, __('Fax sent to fax server: ' . $this->request->data['bdata']), EVENT_DELIVERY);
					
						if ($action['Action']['action_type'] == ACTION_FAX_DELIVER) {
							$this->logMessage($call_id, $this->request->data['btype'], 'deliver',  $this->msg_id, $prompts, $emp_ids, $msg['minder']);		
						}
						else {
							$this->logMessage($call_id, $this->request->data['btype'], '', $this->msg_id, $prompts, $emp_ids, $msg['minder']);		
							
						}
						$this->Session->setFlash(__('Fax sent to FAX server: '.$this->request->data['bdata']), 'flash_jsongood');
					}
					else {
						$this->Session->setFlash(__('Unable to send the message, try again...'), 'flash_jsonbad');
						$this->logEvent($call_id, __('Unable to send fax'), EVENT_DELIVERY);
						
					}
				}					
				else if ($action['Action']['action_type'] == ACTION_DISPATCH) {
					$this->dispatchMessage('Dispatch', $message_id, $call_id);
				}					
				else if ($action['Action']['action_type'] == ACTION_EMAIL || $action['Action']['action_type'] == ACTION_EMAIL_DELIVER) {
					$prompts = $this->_getPrompts();
					$appts = $this->_getAppointments($call_id);
					
					if (!empty($this->did['DidNumber']['email_subject_template'])) {
						// %a = account number, %n = company name, %c = calltype, %d = caller id, %m = message id
						$subject = str_replace('%a', $account_num, $this->did['DidNumber']['email_subject_template']);
						$subject = str_replace('%n', $this->did['DidNumber']['company'], $subject);
						$subject = str_replace('%d', $this->_getCID(true), $subject);
						$subject = str_replace('%c', $message['Message']['calltype'], $subject);
						$subject = str_replace('%m', $msg['id'], $subject);
					}
					else $subject = '';
					$save_ok = $this->_email('Email', $this->request->data['bdata'],  $prompts, $message['Message']['calltype'], $account_num, $email_format, $company, $subject, $appts['active'],$this->request->data['contact_id']);
					if ($save_ok) {
										$this->request->data['bdata'] = str_replace(array(',', ';'), array(', ', ', '), $this->request->data['bdata']);
						$this->logEvent($call_id, __('Message sent to queue: ' . $this->request->data['bdata']), EVENT_DELIVERY);
						if ($action['Action']['action_type'] == ACTION_EMAIL_DELIVER)
							$this->logMessage($call_id, CONTACT_EMAIL, 'deliver', $this->msg_id,  $prompts, $emp_ids,$msg['minder']);		
						else
							$this->logMessage($call_id, CONTACT_EMAIL, '', $this->msg_id,  $prompts, $emp_ids, $msg['minder']);		
						$this->Session->setFlash(__('Message sent to queue: '.$this->request->data['bdata']), 'flash_jsongood');
						if ( $action['Action']['action_type'] == ACTION_EMAIL_DELIVER) {
						}
					}
					else {
						$this->Session->setFlash(__('Unable to send message to queue, try again later...'), 'flash_jsonbad');
						$this->logEvent($call_id, __('Unable to send message to queue'), EVENT_DELIVERY);

					}					
				}
				else if ($action['Action']['action_type'] == ACTION_HOLD) {
					$prompts = $this->_getPrompts();
					if ($this->logMessage($call_id, '', 'hold', $this->msg_id, $prompts, $emp_ids, $msg['minder'])) {
						$this->Session->setFlash(__('Message held'), 'flash_jsongood');					  
					}
					else {
						$this->Session->setFlash(__('Cannot hold message'), 'flash_jsonbad');		
						$this->logEvent($call_id, __('Unable to set hold'), EVENT_DELIVERY);
					}
				}			
				else if ($action['Action']['action_type'] == ACTION_DELIVER) {
					$prompts = $this->_getPrompts();
					if ($this->logMessage($call_id, '', 'mark deliver', $this->msg_id, $prompts, $emp_ids, $msg['minder'])) {
						$this->Session->setFlash('Message marked as delivered', 'flash_jsongood');
					}
					else {
						$this->Session->setFlash('Cannot mark message as delivered', 'flash_jsongood');
					}
				}			
				else if ($action['Action']['action_type'] == ACTION_WEB) {
					$prompts = $this->_getPrompts();
					if ($this->logMessage($call_id, '', 'webform', $this->msg_id, $prompts, $emp_ids, $msg['minder'])) {
						$this->Session->setFlash('Webform recorded', 'flash_jsongood');
					}
					else {
						$this->Session->setFlash('Cannot record webform', 'flash_jsongood');
					}
				}

			}
			else $this->Session->setFlash(__('Cannot find the call action'), 'flash_jsonbad');
			$this->render('/Elements/json_result');				
		}		
	}

	function dispatchMessage($action, $mid, $call_id) {
		$prompts = $this->_getPrompts();
		$ok = $this->logMessage($call_id, '',  'dispatch', $mid, $prompts);		
		if ($ok) $this->Session->setFlash(__('Message sent to dispatch'), 'flash_jsongood');
		else $this->Session->setFlash(__('Unable to send the message to dispatch'), 'flash_jsonbad');
	}	
		
	public function btnClick($call_id) {
	  
		if (!isset($this->CallLog)) $this->loadModel('CallLog');
		$this->CallLog->unbindModel(
				array('hasMany' => array('CallEvent'))
		);				

		$this->call = $this->CallLog->find('first', array('conditions' => array('CallLog.id' => $call_id)));
		if (!$this->call) {
			$this->Session->setFlash(__('Message cannot be sent'), 'flash_jsonbad');
			$this->render('/Elements/json_result');
		}
		$this->call_id = $call_id;
		
		$did_id = $this->call['CallLog']['did_id'];
		if (!empty($this->request->data['schedule_id'])) $schedule_id = $this->request->data['schedule_id'];
		else $schedule_id = '';
		if (!empty($this->request->data['contact_id'])) $contact_id = $this->request->data['contact_id'];		
		else $contact_id = '';
		
		// create the message for this call if it doesn't exist already
		$message = $this->_create_msg($call_id, $did_id, $schedule_id, '0', '0', '0', $contact_id);		
		$this->msg_id = $message['Message']['id'];

	 	$this->Message->DidNumber->recursive = 0;
		$this->did = $temp = $this->Message->DidNumber->findById($did_id);

		$account_num = $temp['Account']['account_num'];
		$company = $temp['DidNumber']['company'];
		$did_number = $temp['DidNumber']['did_number'];
		$email_format = ($temp['DidNumber']['email_format'] == '0')? 'both': 'text';

		// check the button type and process the button click accordingly
		if ($this->request->data['btype'] == CONTACT_EMAIL) {
			$prompts = $this->_getPrompts();
			$appts = $this->_getAppointments($call_id);
			
			// if a custom email subject if found, then construct the custom subject line
			if (!empty($this->did['DidNumber']['email_subject_template'])) {
				// %a = account number, %n = company name, %c = calltype, %d = caller id		        
				$subject = str_replace('%a', $account_num, $this->did['DidNumber']['email_subject_template']);
				$subject = str_replace('%n', $this->did['DidNumber']['company'], $subject);
				$subject = str_replace('%d', $this->_getCID(true), $subject);
				$subject = str_replace('%c', $message['Message']['calltype'], $subject);
				$subject = str_replace('%m', $this->msg_id, $subject);
				
			}
			else $subject = '';      
			
			// email the message and log the message in the db 
			$save_ok = $this->_email('Email', $this->request->data['bfulldata'], $prompts, $message['Message']['calltype'], $account_num, $email_format, $company, $subject, $appts['active'], $this->request->data['contact_id']);
			if ($save_ok) {
				$this->logMessage($call_id, CONTACT_EMAIL, '', $this->msg_id, $prompts, $this->request->data['contact_id'], $message['Message']['minder']);		
				$this->Session->setFlash(__('Message sent to queue'), 'flash_jsongood');
				$this->logEvent($call_id, __('Message sent to queue'), EVENT_DELIVERY);
			}
			else {
				$this->Session->setFlash(__('Unable to send the message, try again...'), 'flash_jsonbad');
				$this->logEvent($call_id, __('Unable to send email to queue'), EVENT_DELIVERY);
			}
		}	
		
		// text the message
		else if ($this->request->data['btype'] == CONTACT_TEXT) {
			$prompts = $this->_getPrompts();
			$appts = $this->_getAppointments($call_id);
			
			$save_ok = $this->_text('Text', $this->request->data['bfulldata'], $prompts, $message['Message']['calltype'], $account_num, $appts['active']);
			if ($save_ok) {
				$this->logMessage($call_id, CONTACT_TEXT, '', $this->msg_id,  $prompts, $this->request->data['contact_id'], $message['Message']['minder']);		
				$this->Session->setFlash(__('Message sent to queue'), 'flash_jsongood');
				$this->logEvent($call_id, __('Message sent to queue'), EVENT_DELIVERY);
			}
			else {
				$this->Session->setFlash(__('Unable to send the text message to queue, try again...'), 'flash_jsonbad');
				$this->logEvent($call_id, __('Unable to send text to queue'), EVENT_DELIVERY);
			}
		}
		else if ($this->request->data['btype'] == CONTACT_FAX) {
			$prompts = $this->_getPrompts();
			$appts = $this->_getAppointments($call_id);
			
			$save_ok = $this->_fax($message, 'Fax', $this->request->data['bdata'], $prompts, $message['Message']['calltype'], $account_num, '', $appts['active']);
			if ($save_ok) {
				$this->logMessage($call_id, CONTACT_FAX, '', $this->msg_id,  $prompts, $this->request->data['contact_id'], $message['Message']['minder']);		
				$this->Session->setFlash(__('Message delivered to FAX server'), 'flash_jsonbad');
				$this->logEvent($call_id, __('Message delivered to FAX server'), EVENT_DELIVERY);
			}
			else {
				$this->Session->setFlash(__('Unable to send the fax message, try again...'), 'flash_jsonbad');
				$this->logEvent($call_id, __('Unable to send to FAX server'), EVENT_DELIVERY);
			}
		}		
		// send the message to be dispatched (will appear in the minder window)
		else if ($this->request->data['btype'] == BUTTON_DISPATCH) {
			$prompts = $this->_getPrompts();
			$save_ok = $this->logMessage($call_id, '', 'dispatch', $this->msg_id,  $prompts, $this->request->data['contact_id'], $message['Message']['minder']);	
			if ($save_ok) {
				$this->Session->setFlash(__('Message has been sent to dispatch'), 'flash_jsongood');
			}
			else {
				$this->Session->setFlash(__('Unable to send the message, try again...'), 'flash_jsonbad');
			}
		}		
		else if ($this->request->data['btype'] == BUTTON_DELIVER) {
			$prompts = $this->_getPrompts();
			$save_ok = $this->logMessage($call_id, '', 'mark deliver', $this->msg_id,  $prompts, $this->request->data['contact_id'], $message['Message']['minder']);	
			$data = $this->Message->query("select d.id from ".OA_TBL_PREFIX."messages_delivery d left join ".OA_TBL_PREFIX."messages m on m.id=d.message_id where m.call_id='$call_id'");
			
			if (sizeof($data) < 1) {
				App::uses('CakeEmail', 'Network/Email');		
				
				// notify QA monitors if message has been marked as delivered when there are no actual deliveries recorded
				if (Configure::read('qa_email')) {
					if (!is_array(Configure::read('qa_email'))) {
						$recipients = explode(',', Configure::read('qa_email'));
						
						// list events in notification email to make it easier to review
						$this->loadModel('CallEvent');
						$events = $this->CallEvent->findAllByCallId($call_id);
						$txt = '';
						foreach ($events as $e) {
							$c = $e['CallEvent'];
							if ($c['event_type'] == EVENT_FILL_PROMPT || $c['event_type'] == EVENT_CALLTYPE || $c['event_type'] == EVENT_MINDERCLICK || $c['event_type'] == EVENT_DEBUG) {
							}
							else {
								$txt .= $c['created'] . ' - ';
								if ($c['event_type'] == '2') {
									$button_data = unserialize($c['button_data']);
									if (isset($button_data['button_type']) && $button_data['button_type']) 
										$txt .= ' - ['.$button_data['button_type'].' BTN] ';
									else
										$txt .= ' - [BTN CLICK] ';
									if (isset($button_data['emp_name']) && $button_data['emp_name']) $txt .= $button_data['emp_name'] . ' - ' ;
									$txt .= ' ' .  $button_data['blabel'];
									if (isset($button_data['bfulldata'])) $txt .= ' - '.$button_data['bfulldata'].' ';    
								}
								else
									$txt .= $c['description'];
								$txt .= "\r\n";
							}
						}
		
						CakeEmail::deliver($recipients, "[OpenAnswer] Message marked DELIVERED w/o any deliveries", "The following message has been marked as delivered even though there has not be an actual delivery recorded in OpenAnswer.  \r\n\r\nMessage id: " . $this->msg_id . " \r\n\r\n$txt", 'admin');
					}
				}
			}
			 
			if ($save_ok) {
				$this->Session->setFlash(__('Message has been marked as DELIVERED'), 'flash_jsongood');
			}
			else {
				$this->Session->setFlash(__('Unable to mark message as delivered, try again...'), 'flash_jsonbad');
			}
		}			
		else if ($this->request->data['btype'] == CONTACT_WEB) {
				$this->Session->setFlash(__('Webform has been opened'), 'flash_jsongood');
		}
		$this->render('/Elements/json_result');
								
	}

	// returns an array of scheduling calendar appointments
	function _getAppointments($call_id, $deleted = false) {
		$appts = array('active' => array(), 'deleted' => array());
		if (Configure::read('calendar_enabled')) {
			$this->loadModel('Scheduling.EaAppointment');
			$appts = $this->EaAppointment->get_appointments($call_id, $deleted);
		}
		return $appts;   
	}
	
	// returns an array of prompts posted for this message
	function _getPrompts() {
		
		if (isset($this->request->data['ptitle'])) $ptitles = $this->request->data['ptitle'];
		else $ptitles = array();
		if (isset($this->request->data['pvalue'])) $pvalues = $this->request->data['pvalue'];
		else $pvalues = array();
		if (isset($this->request->data['pmaxchar'])) $pmaxchar = $this->request->data['pmaxchar'];
		else $pmaxchar = array();
		if (isset($this->request->data['ptype'])) $ptype = $this->request->data['ptype'];
		else $ptype = array();
		if (isset($this->request->data['poptions'])) $poptions = $this->request->data['poptions'];
		else $poptions = array();
		$p_array = array();
		$prompt_phone = '';
		$prompt_name = '';
		$misc_array = null;
		foreach ($ptitles as $p_aid => $prompts) {
			//if ($p_aid <= $index) {
			if (1) {
				foreach ($prompts as $key => $prompt) {
					if ($prompt == PROMPT_MISC) {
						$prompt_misc = $prompt;
						$misc_array = array('caption' => trim($prompt), 'value' => trim($pvalues[$p_aid][$key]), 'pmaxchar' => $pmaxchar[$p_aid][$key], 'ptype' => $ptype[$p_aid][$key], 'options' => '', 'action_num' => (count($ptitles) + 1));
					}
					else {
						if ($prompt == PROMPT_NAME) $prompt_name = trim($pvalues[$p_aid][$key]);
						if ($prompt == PROMPT_PHONE) $prompt_phone = trim($pvalues[$p_aid][$key]);
						$p_array[] = array('caption' => trim($prompt), 'value' => trim($pvalues[$p_aid][$key]), 'pmaxchar' => $pmaxchar[$p_aid][$key], 'ptype' => $ptype[$p_aid][$key], 'options' => $poptions[$p_aid][$key], 'action_num' => $p_aid);
					}
				}
			}
		}
		if ($misc_array != null) $p_array[] = $misc_array;
		// save name and phone in a db table to be used for auto-complete of caller info, query will silently fail if name/cid pair already exists
		if ($prompt_phone && $prompt_name) {
			$sql = "INSERT IGNORE into " . OA_TBL_PREFIX . "cids (number, name) values ('$prompt_phone', '$prompt_name')";
		 // $this->Message->query($sql);
		}
		
		return $p_array;
	}
	
	function _getEmployees($search_ids) {
		// return list of employees from list of contact ids.
		$conditions = array('EmployeesContact.id in ('.$search_ids.')');
		$this->loadModel('EmployeesContact');
		$employees = $this->EmployeesContact->find('all', array('fields' => array('EmployeesContact.*', 'Employee.*'), 'conditions' => $conditions, 'recursive' => 0));
		$addrs = array();
		
		foreach($employees as $emp) {
			$names[] = $emp['Employee']['name'];
			$addrs[] = $emp['EmployeesContact']['contact'] . ($emp['EmployeesContact']['ext']? (' Ext: ' . $emp['EmployeesContact']['ext']): '');
			$labels[] = $emp['EmployeesContact']['label'];
			$ids[] = $emp['EmployeesContact']['employee_id'];
			$contact_ids[] = $emp['EmployeesContact']['id'];
		}  	
		$emp['names'] = $names;
		$emp['addrs'] = $addrs;
		$emp['labels'] = $labels;
		$emp['ids'] = $ids;
		$emp['contact_ids'] = $contact_ids;
		return $emp;
	}	
	
	// create a report of # of messages per hour
	function hourly($user_id, $date) {
		$this->layout = 'ajax';
		$sql = "select count(*) as cnt, DATE_FORMAT(created, '%l%p') as hour_created, GROUP_CONCAT(id) as msgids from ".OA_TBL_PREFIX."messages m where m.created >= '$date 00:00:00' and m.created <= '$date 23:59:59' and m.user_id='$user_id' group by hour_created";
		
		$data = $this->Message->query($sql);

		for ($i = 23; $i>=0; $i--)
		{
			$temp = date('ga', strtotime("-$i hour"));
			$hours[$temp] = 0;
		}
		foreach ($data as $r) {
			$hours[strtolower($r[0]['hour_created'])] = $r[0]['cnt']; 
		}

		$this->set('messages', $hours);
		$this->set('oa_title', 'Messages by the hour');
		
	}

	function my_hourly($user_id, $date) {
		$this->hourly($user_id, $date);
		$this->render('hourly');
	}
	
	function daily($user_id) {
		$this->layout = 'ajax';
		$min_date = date('Y-m-d', strtotime('-30 day'));
		$today = date('Y-m-d');
		$days = array();
		for ($i = 30; $i>=0; $i--)
		{
			$temp = date('D n/j', strtotime("-$i day"));
			$days[$temp] = 0;
		}
		$sql = "select count(*) as cnt, DATE_FORMAT(DATE(created),'%a %c/%e') as date_created from ".OA_TBL_PREFIX."messages m where m.created >= '$min_date 00:00:00' and m.created <= '$today 23:59:59' and m.created >= '$min_date 00:00:00' and m.user_id='$user_id' group by DATE(created)";
		
		$data = $this->Message->query($sql);
		foreach ($data as $r) {
			$days[$r[0]['date_created']] = $r[0]['cnt']; 
		}
		$this->set('messages', $days);
		$this->set('oa_title', 'Messages by the day');
		
	}

	function my_daily($user_id) {
		$this->daily($user_id);
		$this->render('daily');
	}  
				
	// saves message and sets status of message (hold/ delivered/ mindered, etc)
	function logMessage($call_id, $method,  $message_action, $msgId, $prompts, $contact_ids = null, $msg_minder='0') {
		if (!$msgId) return false;
	

		$first_eid = 0;
		if ($contact_ids) {
			$employees = $this->_getEmployees($contact_ids);
			$temp = explode(',', $contact_ids);
		}
		else $employees = null;
		$minder_date = date('Y-m-d H:i:s');
		
		$hold_delivery = '0';
		$save_delivery = true;
		$d['Message']['last_eid'] = $employees['ids'][0];
		if ($message_action == 'deliver') {
			$d['Message']['delivered'] = '1';
			$d['Message']['minder'] = '0';
			$d['Message']['minder_ts'] = '0000-00-00 00:00:00';
			$this->logEvent($call_id, __('Message marked as delivered'));		  
			$save_delivery = true;
		}
		if ($message_action == 'mark deliver') {
			$d['Message']['delivered'] = '1';
			$d['Message']['minder'] = '0';
			$d['Message']['minder_ts'] = '0000-00-00 00:00:00';
			$this->logEvent($call_id, __('Message marked as delivered'));		  
			$save_delivery = false;
		}		
		if ($message_action == 'hold') {
			$d['Message']['delivered'] = '0';
			$d['Message']['hold'] = '1';
			$hold_delivery = '1';
			$save_delivery = false;
			$this->logEvent($call_id, __('Message held'));		  
		}
		else if ($message_action == 'dispatch') {
			$d['Message']['delivered'] = '0';
			$d['Message']['minder'] = '1';
			$d['Message']['minder_ts'] = $minder_date;
			$msg_minder = '1';
			$save_delivery = false;
			$this->logEvent($call_id, __('Message sent to dispatch'), EVENT_MINDER);		  
		}
		else if ($message_action == 'minder') {
			$d['Message']['delivered'] = '0';
			$d['Message']['minder'] = '1';
			$msg_minder = '1';
			$d['Message']['minder_ts'] = $minder_date;
			$this->logEvent($call_id, __('Message sent to minder'), EVENT_MINDER);		  
		}
		else if ($message_action == 'webform') {
			$d['Message']['delivered'] = '0';
			$this->logEvent($call_id, __('Went to webform'));		  
		}
		else if ($message_action == 'hangup') {
			$save_delivery = false;
		}		
		else if ($message_action == 'dial') {
			$save_delivery = false;
		}	
		$d['Message']['id'] = $msgId;
				// get old prompts
				$old_prompts = $this->Message->MessagesPrompt->find('all', array('conditions' => array('message_id' => $msgId)));
				$old = array();
				$new = array();
				if ($old_prompts) {
					foreach($old_prompts as $k => $p) {
						$old[$p['MessagesPrompt']['caption']] = $p['MessagesPrompt']['value'];          
					}				
				}
				foreach ($prompts as $k => $p) {
					$new[$p['caption']] = $p['value'];
				}
				
				//compare old and new prompts and save changes if different
				if ($new == $old) {
					//fb('no prompts changed');
				}
				else {
					$this->Message->MessagesPrompt->deleteAll(array('message_id' => $msgId), false);		
					foreach ($prompts as $k => $p) {
						$row['message_id'] = $msgId;
						$row['caption'] = $p['caption'];
						$row['action_num'] = $p['action_num'];
						$row['value'] = $p['value'];
						$row['ptype'] = $p['ptype'];
						$row['options'] = $p['options'];
						$row['maxchar'] = $p['pmaxchar'];
						$row['sort'] = ($k+1);
						$d['MessagesPrompt'][] = $row;
					}      
				}
		
		if ($msg_minder == '1') {
			$d['Message']['minder_ts'] = $minder_date; // reset minder timestamp		  
		}

		if ($save_delivery) {
			$delivery['delivered_time'] = $minder_date;
			$delivery['delivery_name'] = implode(',', $employees['names']);
			$delivery['delivery_contact'] = implode(',', $employees['addrs']);
			$delivery['delivery_contact_id'] = implode(',', $employees['contact_ids']);
			$delivery['delivery_contact_label'] = implode(',', $employees['labels']);
			$delivery['employee_id'] = implode(',', $employees['ids']);
			$delivery['delivered_by_userid'] = AuthComponent::user('id');
			$delivery['delivered_by_ext'] = $this->user_extension;
			$delivery['delivery_method'] = $method;
			$delivery['hold'] = $hold_delivery;
			$d['MessagesDelivery'][] = $delivery;
		}
//$this->logEvent($call_id, "save new prompts ".print_r($d, true), EVENT_DEBUG);		
		
		$save_ok = $this->Message->saveAssociated($d);
		return $save_ok;		
	}  
	
	function _getLocalTime() {
		if (empty($this->did['DidNumber']['timezone'])) $oa_timezone = Configure::read('default_timezone');          
		else $oa_timezone = $this->did['DidNumber']['timezone'];
		
		$date1 = new DateTime();
		$date1->setTimezone(new DateTimeZone($oa_timezone));    
		$local_time = $date1->format('Y-m-d g:i a');   
		return $local_time;
	}
	
	function _getCID($force = false) {
		$caller_id = '';
		if ($this->did['DidNumber']['include_cid'] == '1' || $force) {
			if (!empty($this->call['CallLog']['cid_number']) && is_numeric($this->call['CallLog']['cid_number'])) $caller_id = $this->call['CallLog']['cid_number'];
			else $caller_id = 'UNKNOWN';
		}
		return $caller_id;
	}
	
	//*********** BEGIN delivery methods for messages
	function _text($template_type, $recipient, $prompts,  $calltype_caption, $account_num='', $appts = null) {
		$this->loadModel('EmailQueue');
		$this->layout = "plain";
		$recipient_array = explode(',', $recipient);
		App::uses('CakeEmail', 'Network/Email');		
		$Email = new CakeEmail();		
		$caller_id = $this->_getCID();
		$local_time = $this->_getLocalTime();          
		$exclude_prompt_titles = $this->did['DidNumber']['exclude_prompts'];
		$saveok = true;
		$msg_id = $this->msg_id;
		
		foreach($recipient_array as $r) {
			if ($r) {
				$view = new View($this, false);
				$view->set('calltype', $calltype_caption);
				$view->set('prompts', $prompts);
				$view->set('appts', $appts);
				$view->set('account', $account_num);
				$view->set('local_time', $local_time);
				$view->set('caller_id', $caller_id);
				$view->set('recipient',trim($r));
				$view->set('exclude_prompt_titles', $exclude_prompt_titles);
                if ($this->did['DidNumber']['hipaa'] == '1') {
				    $view_output = $view->render('/Emails/html/deliver_text_msg_secure');
				}
				else {
				    $view_output = $view->render('/Emails/html/deliver_text_msg');
				}
				$data = array();
				$data['did_id'] = $this->did['DidNumber']['id'];
				$data['call_id'] = $this->call_id;      
				$data['subject'] = "Account: " . $account_num;
				$data['content_html'] = '';
				$data['content_text'] = $view_output;
				$data['format'] = 'text';
				$data['template'] = $template_type;
				$data['recipients'] = serialize(trim($r));
				$data['processed'] = '0';
				$this->EmailQueue->create();
				if (!$this->EmailQueue->save($data)) {
					$saveok = false;
				}
			}


		}
		return $saveok;
	}	
	
	function _fax($msg, $template_type, $recipient, $prompts,  $calltype_caption, $account_num='', $did_number='', $appts = null) {
		$this->layout = "plain";
		$recipient = str_replace(';', ',', $recipient);
		$recipient_array = explode(',', $recipient);
		$this->autoRender = false;  // make sure controller doesn't auto render
		$local_time = $this->_getLocalTime();          
		$caller_id = $this->_getCID();
	 
		/* Set up new view that won't enter the ClassRegistry */
		/* Grab output into variable without the view actually outputting! */
		foreach($recipient_array as $r) {		
			$view = new View($this, false);
			$view->set('calltype', $calltype_caption);
			$view->set('prompts', $prompts);
			$view->set('appts', $appts);
			$view->set('account', $account_num);
			$view->set('include_coverpage', '1');
			$view->set('message', $msg);
			$view->set('local_time', $local_time);
			$view->set('caller_id', $caller_id);
			
			$view->set('faxnote', '* Live Answering Messages *');
			$view->set('faxstatus', 'For Review');
			$view->set('faxto', $r);
			$view->set('faxfrom', '(866) 766-5050');
			$view->set('faxnumber', $r);
			$view->set('faxphone', '(866) 766-5050');
			$view->set('faxdate', $local_time);
			$view->set('faxre', htmlspecialchars("Live Answering Messages - Account: " . $account_num));

			$view_output = $view->render('deliver_fax_msg');  		
			$data['FaxQueue']['fax_text'] = $view_output;
			$data['FaxQueue']['fax_processed'] = '0';
			$data['FaxQueue']['src_fax'] = $did_number;
			$data['FaxQueue']['dst_fax'] = $r;
			$data['FaxQueue']['format'] = 'html';
			$data['FaxQueue']['account_num'] = $account_num;
/*  		if (!$this->_sendfax("Account: " . $account_num, $view_output, $r)) {
				return false;
			}*/
			$this->loadModel('FaxQueue');
			$this->FaxQueue->create();
			$this->FaxQueue->save($data['FaxQueue']);
			
		}
		return true;
	}		
	
	
	
    function _email($template_type, $recipient,$prompts, $calltype_caption, $account_num=null, $format='both', $company, $subject = '', $appts = null, $contact_ids = null) {
        
        //don't include all of the regular items that would be on a web view, since this is an email
        $this->layout = "plain";
        
        //results stored here for queuing
        $this->loadModel('EmailQueue');
        
        //used to look up contact address
        $this->loadModel('EmployeesContact');
        
        $local_time = $this->_getLocalTime();          
        $caller_id = $this->_getCID();
        $msg_id = '';
        if ($this->did['DidNumber']['include_msg_id'] == '1') {
            $msg_id = $this->msg_id;
        }
        
        $contacts = explode(',', $contact_ids);
        
        //If HIPAA security is required, and SMTP profile is not secured
        // we must generate a different email for each employee
        if (($this->did['DidNumber']['hipaa'] == '1') && ($this->did['DidNumber']['smtp_profile'] != 'secure_only')) {
            //each contact will get an individually crafted email
            foreach ($contacts as $contact) {
                //Retrieve the email address(s) for this contact
                $this->EmployeesContact->id = $contact;
                $recipient = $this->EmployeesContact->field('contact');
                $employee_id = $this->EmployeesContact->field('employee_id');
                $recipients = explode(';', $recipient);
                
                //Build HTML version of the email
                $view = new View($this, false);
                $view->set('calltype', $calltype_caption);
                $view->set('employee_id', $employee_id);
                $view->set('prompts', $prompts);
                $view->set('appts', $appts);
                $view->set('msg_id', $msg_id);
                $view->set('message_id', $this->msg_id);
                $view->set('account', $account_num);
                $view->set('recipient',$recipient);
                $view->set('local_time', $local_time);
                $view->set('caller_id', $caller_id);
                $view_output_html = $view->render('/Emails/html/deliver_email_msg_secure');
                
                
                //Build TEXT version of the email
                $view2 = new View($this, false);
                $view2->set('local_time', $local_time);
                $view2->set('msg_id', $msg_id);
                $view2->set('message_id', $this->msg_id);
                $view2->set('calltype', $calltype_caption);
                $view2->set('employee_id', $employee_id);
                $view2->set('prompts', $prompts);
                $view2->set('appts', $appts);
                $view2->set('account', $account_num);
                $view2->set('recipient',$recipient);
                $view_output_text = $view2->render('/Emails/text/deliver_email_msg_secure');
                
                
                // save data in the email queue to be processed in the background
                $data = array();
                $data['did_id'] = $this->did['DidNumber']['id'];
                $data['call_id'] = $this->call_id;      
                if (empty($subject)) {
                    $data['subject'] = "Account: " . $account_num . " " . $company;
                }
                else {
                    $data['subject'] = $subject;
                }
                $data['content_html'] = $view_output_html;
                $data['content_text'] = $view_output_text;
                $data['format'] = $format;
                $data['recipients'] = serialize($recipients);
                $data['template'] = $template_type;
                $data['processed'] = '0';
                $this->EmailQueue->create();
                $this->EmailQueue->save($data);
            }
        }
        else {
            //one email will be crafted and sent to all recipients
            //compile all of the email addresses for the contacts
            $recipients = array();
            foreach ($contacts as $contact) {
                $this->EmployeesContact->id = $contact;
                $recipient = $this->EmployeesContact->field('contact');
                //if multiple email addresses are specified, split them into an array
                $recipient = explode(';', $recipient);
                //add the array for this contact into the recipients array
                $recipients = array_merge($recipients,$recipient);
                $recipients_string = implode(";",$recipients);
            }
            //$recipients should now be an array of all the email addresses for all contacts specified.
            
            //Build HTML version of the email
            $view = new View($this, false);
            $view->set('calltype', $calltype_caption);
            $view->set('prompts', $prompts);
            $view->set('appts', $appts);
            $view->set('msg_id', $msg_id);
            $view->set('message_id', $this->msg_id);
            $view->set('account', $account_num);
            $view->set('recipient',$recipients_string);
            $view->set('local_time', $local_time);
            $view->set('caller_id', $caller_id);
            $view_output_html = $view->render('/Emails/html/deliver_email_msg');
            
            
            //Build TEXT version of the email
            $view2 = new View($this, false);
            $view2->set('local_time', $local_time);
            $view2->set('msg_id', $msg_id);
            $view2->set('message_id', $this->msg_id);
            $view2->set('calltype', $calltype_caption);
            $view2->set('prompts', $prompts);
            $view2->set('appts', $appts);
            $view2->set('account', $account_num);
            $view2->set('recipient',$recipients_string);
            $view_output_text = $view2->render('/Emails/text/deliver_email_msg');
            
            // save data in the email queue to be processed in the background
            $data = array();
            $data['did_id'] = $this->did['DidNumber']['id'];
            $data['call_id'] = $this->call_id;      
            if (empty($subject)) {
                $data['subject'] = "Account: " . $account_num . " " . $company;
            }
            else {
                $data['subject'] = $subject;
            }
            $data['content_html'] = $view_output_html;
            $data['content_text'] = $view_output_text;
            $data['format'] = $format;
            $data['recipients'] = serialize($recipients);
            $data['template'] = $template_type;
            $data['processed'] = '0';
            $this->EmailQueue->create();
            $this->EmailQueue->save($data);
        }
        return true;
    }

	//*********** END delivery methods for messages	

	public function set_audit($msg_id, $val) {
		if (empty($msg_id)) return;
		$this->Message->recursive = 0;
		$data = $this->Message->findById($msg_id);
		if ($data) {
			$save['id'] = $msg_id;
			$save['audited'] = $val;
			$save['audited_by'] = AuthComponent::user('username');
			$save_ok = $this->Message->save($save);
			if ($save_ok) {
				if ($val) {
					$this->Session->setFlash(__('The message was flagged as audited'), 'flash_jsongood');
					$this->logEvent($data['Message']['call_id'], "Audited msg#" . $msg_id, EVENT_AUDIT, '');	  
				}
				else {
					$this->Session->setFlash(__('The audit flag has been cleared'), 'flash_jsongood');
					$this->logEvent($data['Message']['call_id'], "Cleared audit status for msg#" . $msg_id, EVENT_AUDIT, '');	  
				}
			} 
			else {
				$this->Session->setFlash(__('Cannot flag message as audited'), 'flash_jsonbad');
			}
			$this->render('/Elements/json_result');			  	
		}
	}
	
	public function delivery_check($call_id) {

	  $data = $this->Message->query("select d.id from ".OA_TBL_PREFIX."messages_delivery d left join ".OA_TBL_PREFIX."messages m on m.id=d.message_id where m.call_id='$call_id'");
	  
    echo sizeof($data); 
    exit;	  
  }
  
  public function api_index($subaccount_id) {
  	if ($subaccount_id) {
  	}
  }
  
}
