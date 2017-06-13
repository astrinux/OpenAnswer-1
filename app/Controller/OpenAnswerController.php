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
class OpenAnswerController extends AppController {
	public $uses = array('AppSetting');
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('start');
	}
	
	public function index() {
		
		
		$profile_path= getcwd() . '/profiles/'.AuthComponent::user('username') . '.jpeg';
		$this->set('profile_ok', file_exists($profile_path));
		
		$conditions = array("AppSetting.user_id" => '0');
		$admin_only = false;
		if (!empty($this->params['named']['role']) && $this->params['named']['role'] == 'adminonly') {
			$admin_only = true;
		}
		$this->set('admin_only', $admin_only);
		$default_settings = $this->AppSetting->find('list', array('fields' => array('field','value'), 'conditions' => $conditions));

		$conditions = array('AppSetting.user_id' => AuthComponent::user('id'));
		$user_settings = $this->AppSetting->find('list', array('fields' => array('field','value'), 'conditions' => $conditions));
		
		foreach ($default_settings as $key => $val) {
			if (!isset($user_settings[$key])) $user_settings[$key] = $val;
		}
		
		$this->set('settings', $user_settings);
		
		$this->layout = 'openanswer_layout';
		$this->set('openconnector_server', Configure::read('openConnectorServer'));
		$this->set('break_reasons', Configure::read('break_reasons'));
		$this->set('break_count_reasons', Configure::read('break_count_reasons'));
		$this->set('pause_agent', Configure::read('pause_agent'));
		
		$this->loadModel('UsersQueue');
		$this->loadModel('Bulletin');
		$this->loadModel('Schedule');
		$joins = array(
			array(
				'table' => OA_TBL_PREFIX . 'bulletin_recipients',
				'alias' => 'BulletinRecipient',
				'type' => 'RIGHT',
				'conditions' => array(
					'Bulletin.id = BulletinRecipient.bulletin_id'
				)
			),
			array(
				'table' => OA_TBL_PREFIX . 'users',
				'alias' => 'User',
				'type' => 'LEFT',
				'conditions' => array(
					'User.id = Bulletin.created_by'
				)
			),
		);
		$thedate = date('Y-m-d', strtotime('-1 month'));
		$conditions = array(
			'BulletinRecipient.user_id' => $this->Auth->user('id'), 		
			'OR' => array(
				array('Bulletin.required' => '1', 'BulletinRecipient.ack_ts' => '0000-00-00 00:00:00'),
				'Bulletin.created_ts >=' => $thedate
			 )
		);
		$fields = array('Bulletin.*', 'BulletinRecipient.id', 'BulletinRecipient.ack_ts', 'DATE_FORMAT(Bulletin.created_ts, \'%a %c/%e/%y %l:%i %p\') as created', 'User.firstname', 'User.lastname');
		
		$bulletins = $this->Bulletin->find('all', array('joins' => $joins, 'conditions' => $conditions, 'fields' => $fields, 'limit' => 10, 'order' => array('created_ts' => 'desc'), 'recursive' => false));
		$required = array();
		foreach ($bulletins as $b) {
			if ($b['Bulletin']['required'] && $b['BulletinRecipient']['ack_ts'] == '0000-00-00 00:00:00') {
				$required[] = $b;
			}
		}
		$this->set('required', $required);
		$this->set('bulletins', $bulletins);

	 // $s = $thisSchedule->find('all', array('conditions' => array('operator_code' =>  $this->Session->read('Auth_operator_code'), 'start_date >=' => date('Y-m-d', strtotime("last Sunday")))));	  
		
		$res = $this->UsersQueue->find('all', array('conditions' => array('user_id' => $this->Auth->user('id')))); 
		$queues = array();
		$penalties = array();
		foreach ($res as $r) {
			$queues[] = $r['UsersQueue']['queue'];
			$penalties[] = $r['UsersQueue']['penalty'];
		}
		$this->set('queues', $queues);
		$this->set('penalties', $penalties);
		$this->loadModel('User');
		$users = $this->User->fetchCCStaff();
		foreach($users as $u) {
//			$r = array('id' => $u['User']['id'], 'text' => $u['User']['firstname'] . ' ' . $u['User']['lastname'] );
			$r = array('value' => $u['User']['id'], 'label' => trim($u['User']['firstname']) . ' ' . trim($u['User']['lastname']), 'id' => $u['User']['id'], 'text' => trim($u['User']['firstname']) . ' ' . trim($u['User']['lastname']) . ' - ' .  trim($u['User']['username']));
			$operators[] = $r;
		}
		$actionbox_actions = $this->global_options['actions'];
		
		// remove the option to select calendar calltype action if calendar plugin is not enabled
		if (Configure::read('calendar_enabled') == false) {
			if (isset($actionbox_actions[ACTION_CALENDAR])) unset($actionbox_actions[ACTION_CALENDAR]);
		}
		$this->set('actionbox_actions', $actionbox_actions);
		$this->set('operators', $operators);
	}

	public function start() {
		$this->loadModel('WelcomeMsg');
		$msg = $this->WelcomeMsg->find('first', array('order' => array('RAND()')));
		$this->set('msg', $msg);
		$this->set('openconnector_server', Configure::read('openConnectorServer'));
	}
	
	public function test_index() {
		$this->render('index');
	}
	
	public function keepalive() {
		//
	}
	public function flushStorage() {
		$storage = Configure::read('storage_days');	  
		$cutoff_date = date('Y-m-d 00:00:00', strtotime("- $storage day"));
		echo $cutoff_date;
		$query = "SELECT id, deleted, deleted_ts FROM ccact_accounts where deleted='1' and deleted_ts < '$cutoff_date'";
		echo $query . "<br><br>";
		$badentries = $this->AppSetting->query($query); 
		print_r($badentries);
		/*foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_actions where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		} */   
		$query = "SELECT id, deleted, deleted_ts FROM ccact_did_numbers where deleted='1' and (deleted_ts < '$cutoff_date')";
		echo $query . "<br><br>";
		$badentries = $this->AppSetting->query($query); 
		$this->cleanup();
		echo 'done';exit;
	}
	public function cleanup() {
		/*$this->loadModel('DidNumber');
		$query = "SELECT d.id FROM ccact_did_numbers d LEFT JOIN ccact_accounts a ON a.id = d.account_id WHERE a.id IS NULL";
		$badnumbers = $this->AppSetting->query($query); 
		foreach ($badnumbers as $b) {
			$this->DidNumber->delete($b['d']['id'], true);
			echo 'deleting ID: ' .$b['d']['id']. ',';
		}*/
		echo 'ACTIONS';
		$query = "SELECT t.id FROM ccact_actions t LEFT JOIN ccact_did_numbers d ON d.id = t.did_id WHERE d.id IS NULL";
		$badentries = $this->AppSetting->query($query); 
		foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_actions where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		}    


		echo '<br><br>CALLTYPES';
		$query = "SELECT t.id FROM ccact_calltypes t LEFT JOIN ccact_did_numbers d ON d.id = t.did_id WHERE d.id IS NULL";
		$badentries = $this->AppSetting->query($query); 
		foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_calltypes where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		}    

		echo '<br><br>EMPLOYEES';
		$query = "SELECT t.id FROM ccact_employees t LEFT JOIN ccact_did_numbers d ON d.id = t.did_id WHERE d.id IS NULL";
		$badentries = $this->AppSetting->query($query); 
		foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_employees where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		}    
		
		echo '<br><br>CONTACTS';
		$query = "SELECT t.id FROM ccact_employees_contacts t LEFT JOIN ccact_did_numbers d ON d.id = t.did_id WHERE d.id IS NULL";
		$badentries = $this->AppSetting->query($query); 
		foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_employees_contacts where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		}      

		echo '<br><br>FILES';
		$query = "SELECT t.id FROM ccact_files t LEFT JOIN ccact_did_numbers d ON d.id = t.did_id WHERE d.id IS NULL";
		$badentries = $this->AppSetting->query($query); 
		foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_files where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		}  
		
		echo '<br><br>MESSAGES';
		$query = "SELECT t.id FROM ccact_messages t LEFT JOIN ccact_did_numbers d ON d.id = t.did_id WHERE d.id IS NULL";
		$badentries = $this->AppSetting->query($query); 
		foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_messages where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		}     
		
		echo '<br><br>DELIVERIES';
			$query = "SELECT t.id FROM ccact_messages_delivery t LEFT JOIN ccact_messages d ON d.id = t.message_id WHERE d.id IS NULL";
			$badentries = $this->AppSetting->query($query); 
			foreach ($badentries as $b) {
				$this->AppSetting->query("delete from ccact_messages_delivery where id='".$b['t']['id']."'");
				echo 'deleting ID: ' .$b['t']['id']. ',';
			}     
			
		echo '<br><br>MESSAGE PROMPTS';
			$query = "SELECT t.id FROM ccact_messages_prompts t LEFT JOIN ccact_messages d ON d.id = t.message_id WHERE d.id IS NULL";
			$badentries = $this->AppSetting->query($query); 
			foreach ($badentries as $b) {
				$this->AppSetting->query("delete from ccact_messages_prompts where id='".$b['t']['id']."'");
				echo 'deleting ID: ' .$b['t']['id']. ',';
			}         
	
		echo '<br><br>MESSAGE PROMPT EDITS';
			$query = "SELECT t.id FROM ccact_messages_prompts_edits t LEFT JOIN ccact_messages d ON d.id = t.message_id WHERE d.id IS NULL";
			$badentries = $this->AppSetting->query($query); 
			foreach ($badentries as $b) {
				$this->AppSetting->query("delete from ccact_messages_prompts_edits where id='".$b['t']['id']."'");
				echo 'deleting ID: ' .$b['t']['id']. ',';
			}         


		echo '<br><br>MESSAGE SUMMARY';
		$query = "SELECT t.id FROM ccact_messages_summary t LEFT JOIN ccact_did_numbers d ON d.id = t.did_id WHERE d.id IS NULL";
		$badentries = $this->AppSetting->query($query); 
		foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_messages_summary where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		}
		
		echo '<br><br>MESSAGE SUMMARY LOG';
		$query = "SELECT t.id FROM ccact_messages_summary_log t LEFT JOIN ccact_did_numbers d ON d.id = t.did_id WHERE d.id IS NULL";
		$badentries = $this->AppSetting->query($query); 
		foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_messages_summary_log where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		}    

		echo '<br><br>MISTAKES';
		$query = "SELECT t.id FROM ccact_mistakes t LEFT JOIN ccact_did_numbers d ON d.id = t.did_id WHERE d.id IS NULL";
		$badentries = $this->AppSetting->query($query); 
		foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_mistakes where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		}

		echo '<br><br>NOTES';
		$query = "SELECT t.id FROM ccact_notes t LEFT JOIN ccact_did_numbers d ON d.id = t.did_id WHERE d.id IS NULL";
		$badentries = $this->AppSetting->query($query); 
		foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_notes where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		}  
		
		echo '<br><br>PROMPTS';
		$query = "SELECT t.id FROM ccact_prompts t LEFT JOIN ccact_did_numbers d ON d.id = t.did_id WHERE d.id IS NULL";
		$badentries = $this->AppSetting->query($query); 
		foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_prompts where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		} 
		
		echo '<br><br>SCHEDULES';
		$query = "SELECT t.id FROM ccact_schedules t LEFT JOIN ccact_did_numbers d ON d.id = t.did_id WHERE d.id IS NULL";
		$badentries = $this->AppSetting->query($query); 
		foreach ($badentries as $b) {
			$this->AppSetting->query("delete from ccact_schedules where id='".$b['t']['id']."'");
			echo 'deleting ID: ' .$b['t']['id']. ',';
		}               
		echo 'done'; exit;
	}	
	
	public function welcome($userId = null) {
		$this->welcome_myinfo($userId);
		$this->set('user_id', $userId);
	}

	public function welcome_myinfo($userId = null) {
		$start_of_current_week = $this->_getStartOfWeek();
		$start_of_last_week = date('Y-m-d', strtotime('-7 day', strtotime($start_of_current_week)));
		$today = date('Y-m-d');

		$this->loadModel('Mistake');
		$joins = array(
			array(
				'table' => OA_TBL_PREFIX . 'messages',
				'alias' => 'Message',
				'type' => 'left',
				'conditions' => array(
					'Mistake.message_id = Message.id'
				)
			)
		);
		$conditions = array('mistake_recipient' => $userId, 'Mistake.deleted' => '0', "Mistake.message_created < '$start_of_current_week 00:00:00'", "Mistake.message_created >= '$start_of_last_week 00:00:00'");
		$mistakes_last_week = $this->Mistake->find('count', array('conditions' => $conditions, 'joins' => $joins));
		$conditions = array('mistake_recipient' => $userId, 'Mistake.deleted' => '0',  "Mistake.message_created >= '$start_of_current_week 00:00:00'");
		$mistakes_current_week = $this->Mistake->find('count', array('conditions' => $conditions, 'joins' => $joins));
		
		$this->loadModel('Message');
		
		$this->Message->unbindModel(
				array('belongsTo' => array('DidNumber', 'CallLog'))
		);	
		$this->Message->recursive = 0;
		
		$conditions = array('user_id' => $userId, 'deleted' => '0', "created >= '$today 00:00:00'");
		$messages_today = $this->Message->find('count', array('conditions' => $conditions));

		$this->Message->unbindModel(
				array('belongsTo' => array('DidNumber', 'CallLog'))
		);	
		$this->Message->recursive = 0;

		$conditions = array('user_id' => $userId, 'deleted' => '0', "created >= '$start_of_current_week 00:00:00'");
		$messages_current_week = $this->Message->find('count', array('conditions' => $conditions));


		$this->Message->unbindModel(
				array('belongsTo' => array('DidNumber', 'CallLog'))
		);	
		$this->Message->recursive = 0;
		$conditions = array('user_id' => $userId, 'deleted' => '0', 'audited' => '1', "created >= '$start_of_current_week 00:00:00'");
		$audited_current_week = $this->Message->find('count', array('conditions' => $conditions));

		$this->Message->unbindModel(
				array('belongsTo' => array('DidNumber', 'CallLog'))
		);	
		$this->Message->recursive = 0;
		$conditions = array('user_id' => $userId, 'deleted' => '0', 'audited' => '1', "created >= '$start_of_last_week 00:00:00'", "created <= '$start_of_current_week 00:00:00'");
		$audited_last_week = $this->Message->find('count', array('conditions' => $conditions));

		
		$this->loadModel('CallLog');
		$this->CallLog->recursive = 0;    
		$this->CallLog->unbindModel(
				array('hasOne' => array('Message'))
		);	    
		$conditions = array('user_id' => $userId, "start_time >= '$today 00:00:00'", "unique_id <>" => "TESTCALL");
		$calls_today = $this->CallLog->find('count', array('conditions' => $conditions));

		$this->CallLog->recursive = 0;    
		$this->CallLog->unbindModel(
				array('hasOne' => array('Message'))
		);	    
		$conditions = array('user_id' => $userId, "start_time >= '$start_of_current_week 00:00:00'", "unique_id <>" => "TESTCALL");
		$calls_current_week = $this->CallLog->find('count', array('conditions' => $conditions));

		$break_reasons =  Configure::read('break_reasons');
		$personal_idxs =  Configure::read('personal_break_reason_idx');
		
		$this->loadModel('UserLog');
		$conditions = array('user_id' => $userId, "created >= '$today 00:00:00'");
		$ors = $personal_breaks = array();
		foreach ($personal_idxs as $k) {
			$ors[] = array('break_reason' => $break_reasons[$k]);
			$personal_breaks[] =  $break_reasons[$k];
		}
		if (count($ors) > 0) {
			$conditions['OR'] = $ors;
		}
		
		
		$breaks = $this->UserLog->find('first', array('fields' => array('count(*) as cnt', 'sum(TIME_TO_SEC(TIMEDIFF(UserLog.break_end, UserLog.created))) as break_len'), 'conditions' => $conditions));
		
		$this->set(compact('breaks', 'calls_today', 'calls_current_week', 'messages_current_week', 'messages_today', 'mistakes_current_week', 'mistakes_last_week', 'personal_breaks', 'audited_current_week', 'audited_last_week'));
		
	}
	
	function chart($id) {
		$this->set('user_id', $id);
	}
	
}
