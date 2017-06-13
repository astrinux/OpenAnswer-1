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
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {
	public $components = array('RequestHandler');
	public $helpers = array('Js');
	public $paginate;
	public function beforeFilter() {
		parent::beforeFilter();
	}
	
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->User->recursive = 0;
		$joins = array(
			array(
				'table' => OA_TBL_PREFIX . 'users_queues',
				'alias' => 'Queue',
				'type' => 'left',
				'conditions' => array('`User`.`id` = `Queue`.`user_id`')
			)
		);		
		
		
		$this->paginate['limit'] = 200;
		$this->paginate['joins'] = $joins;
		$this->paginate['group'] = array('User.id');
		$this->paginate['fields'] = array('User.*', 'GROUP_CONCAT(Queue.queue) as queues');
		$this->paginate['conditions'] = array('deleted' => 0);
		$this->paginate['order'] = array('firstname' => 'asc', 'lastname' => 'asc');
		$users = $this->paginate();
		$this->set(array(
			'users' => $users, 
			'_serialize' => array('users')
		));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->loadModel('Queue');
		
		$queues = $this->Queue->find('all', array('fields' => array('extension', 'descr')));
		$this->set('queues', $queues);	 
		$this->set('users_queues', array());	   
		$this->loadModel('Role');
		$roles = $this->Role->find('list', array('fields' => array('id', 'role')));
		$this->set('roles', $roles);	 
		
		if ($this->request->is('post')) {
		  $conditions = array('deleted' => '0', 'username' => trim($this->request->data['User']['username']));
		  $existing_user = $this->User->find('first', array('conditions' => $conditions));
		  if ($existing_user) {
					$this->Session->setFlash(__('The username \''.$this->request->data['User']['username'].'\' is not available, please select another.'), 'flash_jsonbad');
					$this->render('/Elements/json_result');
					return;
		  }
			
			$passwordHasher = new SimplePasswordHasher();
			$this->request->data['User']['password'] = $passwordHasher->hash(
					$this->request->data['User']['password']
			);
								
			if (isset($this->request->data['Queue'])) {

				foreach($this->request->data['Queue'] as $q) {
					$row  = array('queue' => $q);
					$this->request->data['UsersQueue'][] = $row;
				}			
			}
			$this->User->create();
			if ($this->User->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'), 'flash_jsongood');
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'flash_jsonbad');
			}
			$this->render('/Elements/json_result');
		}
	}

	
	public function status($takingCalls = 0) {
		$this->loadModel('UserLog');
		$data['user_id'] = AuthComponent::user('id');
		$data['extension'] = $this->Session->read('User_extension');
		$data['session'] = $this->Session->id();
		
		if ($takingCalls) {
			$data['type'] = 'taking_calls';
			$data['log_type'] = USEREVT_TAKING_CALLS_BTN;      
		}
		else {
			$data['type'] = 'not_taking_calls';
			$data['log_type'] = USEREVT_NOT_TAKING_CALLS_BTN;      
			}
		if ($this->UserLog->save($data)) $this->Session->setFlash('Logged operator status', 'flash_jsongood');
		else $this->Session->setFlash('Cannot log user status', 'flash_jsonbad');
		$this->render('/Elements/json_result');	    
	}
	
	public function find($id = null) {
		if ($id) {
			$sql = "select * from ".OA_TBL_PREFIX."users User where User.id='$id' and User.deleted='0'";
		}
		else {
			$search = $this->request->query['term'];
			$sql = "select * from ".OA_TBL_PREFIX."users User where (User.firstname like '%$search%' or User.username like '%$search%' or User.lastname like '$search%' or User.extension like '$search%') and User.deleted='0'";
		}
		$users = $this->User->query($sql);
		$this->set('users', $users);
		
	}

	public function operator($id=null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			$this->Session->setFlash(__('The user could not be found. Please, try again.'));
			 $this->render('/Elements/html_result');
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			
			// hash password if passwrd is modified
			if (trim($this->request->data['Misc']['password'])) {
					$passwordHasher = new SimplePasswordHasher();
					$this->request->data['User']['password'] = $passwordHasher->hash(
							$this->request->data['Misc']['password']
					);
			}

			if ($this->User->save($this->request->data['User'])) {
				$this->Session->setFlash(__('The changes have been saved'), 'flash_jsongood');
			} else {
				$this->Session->setFlash(__('The changes could not be saved. Please try again later.', 'flash_jsonbad'));
			}
			$this->render('/Elements/json_result');
		} else {

			$this->request->data = $this->User->read(null, $id);
			$imdata = $this->request->data['User']['photo'];
			$this->set('photo_base64', $imdata);
						
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
		$this->loadModel('Queue');
	  $this->loadModel('UsersQueue');		
		$queues = $this->Queue->find('all', array('fields' => array('extension', 'descr'), 'order' => array('extension' => 'asc')));
		$this->set('queues', $queues);
		$this->User->id = $id;
		if (!$this->User->exists()) {
			$this->Session->setFlash(__('The user could not be found. Please, try again.'));
			 $this->render('/Elements/html_result');
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			
			// hash password if passwrd is modified
			if (trim($this->request->data['Misc']['password'])) {
					$passwordHasher = new SimplePasswordHasher();
					$this->request->data['User']['password'] = $passwordHasher->hash(
							$this->request->data['Misc']['password']
					);
			}
			
			// delete all queue assignments for the user
			$this->UsersQueue->deleteAll(array('user_id' => $id), false);
			if (isset($this->request->data['Queue'])) {
				
				// create new queue assignmentf for the user
				foreach($this->request->data['Queue'] as $q) {
					$row  = array('user_id' => $id, 'queue' => $q, 'penalty' => $this->request->data['Penalty'][$q]);
					$this->UsersQueue->create();
					$this->UsersQueue->save($row);
				}
			}
			
			
			if ($this->User->save($this->request->data['User'])) {
				$this->Session->setFlash(__('The changes have been saved'), 'flash_jsongood');
			} else {
				$this->Session->setFlash(__('The changes could not be saved. Please try again later.', 'flash_jsonbad'));
			}
			$this->render('/Elements/json_result');
		} else {
	    $this->User->bindModel(
	        array('hasMany' => array(
						'UsersQueue' => array(
							'foreignKey' => 'user_id',
							'order' => array('queue' => 'asc')
							)
						)
	        )
	    );			
	    $this->request->data = $this->User->read(null, $id);

			$users_queues = array();
			$users_penalty = array();
			foreach($this->request->data['UsersQueue'] as $q) {
				$users_queues[] = $q['queue'];
				$users_penalty[$q['queue']] = $q['penalty'];
			}
			$this->set('users_queues', $users_queues);
			$this->set('users_penalty', $users_penalty);
			$imdata = $this->request->data['User']['photo'];
			$this->set('photo_base64', $imdata);
			

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

		$data['User']['id'] = $id;
		$data['User']['deleted'] = 1;
		$data['User']['password'] = str_shuffle('lkjazxd!!121ljlksa');
		$data['User']['deleted_ts'] = date('Y-m-d G:i:s');
		
		if ($this->User->save($data['User'])) {
			$conditions = array('user_id' => $id);
			$this->loadModel('UsersQueue');
			$this->UsersQueue->deleteAll($conditions);
			
			$this->Session->setFlash('User deleted', 'flash_jsongood');
		}
		else {
			$this->Session->setFlash('User was not deleted', 'flash_jsonbad');
		}
		$this->render('/Elements/json_result');		
	}
	
	
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				// save the station id in a session variable
				$this->Session->write('User_extension', $this->request->data['User']['stationid']);
				
				// create login entry in the user log
				$this->loadModel('UserLog');        
				$this->UserLog->addEntry($this->request->data['User']['stationid'], 'login', USEREVT_LOGIN);        
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Invalid username or password, try again'), 'default', array(), 'auth');
			}
		}
	}
	
	public function logout() {
		$this->loadModel('UserLog');

	  // create logout entry in the user log
		$this->UserLog->addEntry($this->Session->read('User_extension'), 'logout', USEREVT_LOGOUT);        

		$this->redirect($this->Auth->logout());
	}	
	
	public function enter_break() {
		$this->loadModel('UserLog');

		if ($breakId = $this->UserLog->addEntry($this->Session->read('User_extension'), 'break', USEREVT_BREAK, $this->request->data('reason'))) {
			// return the break id, will need to reference it when the client leaves break
			$this->set('break_id', $breakId);
			$this->set('success', true);
			$this->set('msg', 'Your break has been logged');
		}
		else {
			$this->set('success', "false");
			$this->set('msg', 'Cannot log your break');
		}
	}

	public function leave_break() {

		$this->loadModel('UserLog');
		$breakId =  $this->request->data('break_id');
		$this->set('break_id', $breakId);
		$conditions = array('id' => $breakId, 'user_id' => AuthComponent::user('id'));
		$data = $this->UserLog->find('first', array('conditions' => $conditions));
		if ($data) {
			$data['UserLog']['break_end'] = date('Y-m-d H:i:s');
			$this->UserLog->save($data['UserLog']);
		}
		$data = array();

		if ($this->UserLog->addEntry($this->Session->read('User_extension'), 'leave_break', USEREVT_LEAVE_BREAK)) {
			$this->set('success', true);
			$this->set('msg', 'The end of the break has been logged');
		}
		else {
			$this->set('success', "false");
			$this->set('msg', 'Cannot log your break\'s end');
		}    
	}
	
	public function audit() {
		if (isset($this->request->data['Search']['start_date'])) $start_date = $this->request->data['Search']['start_date'];
		else $start_date = $this->_getStartOfWeek();
		if (isset($this->request->data['Search']['end_date'])) $end_date = $this->request->data['Search']['end_date'];
		else $end_date = date('Y-m-d');
		if (!isset($this->request->data['Search']['audit_goal'])) $this->request->data['Search']['audit_goal'] = 20;
		
		// find numbers of calls for each user
		$sql = "SELECT 
		User.id, User.username, User.firstname, User.lastname, 
		(SELECT COUNT(*) FROM ".OA_TBL_PREFIX."call_logs c WHERE c. user_id=User.id AND c.start_time >= '$start_date 00:00:00' AND c.start_time <= '$end_date 23:59:59' AND unique_id != 'TESTCALL') AS num_calls FROM ".OA_TBL_PREFIX."users User WHERE User.deleted='0' AND User.display_stat='1'";
		$data = $this->User->query($sql);
		$result = array();
		foreach ($data as $d) {
			$result[$d['User']['id']] = array('id' => $d['User']['id'], 'firstname' => $d['User']['firstname'], 'lastname' => $d['User']['lastname'], 'username' => $d['User']['username'], 'num_calls' => $d[0]['num_calls'], 'num_messages' => 0, 'num_audited' => 0, 'num_mistakes' => 0 );
		}
		
		// find numbers of messages for each user
		$sql = "SELECT COUNT(*) as num_messages, m.user_id FROM ".OA_TBL_PREFIX."messages m WHERE m.created >= '$start_date 00:00:00' AND m.created <= '$end_date 23:59:59' group by m.user_id"; 
		$data = $this->User->query($sql);
		foreach($data as $d) {
				if (isset($result[$d['m']['user_id']])) $result[$d['m']['user_id']]['num_messages'] = $d[0]['num_messages'];
		}  	  

		// find numbers of messages that have been audited for each user
		$sql = "SELECT COUNT(*) as num_audited, m.user_id FROM ".OA_TBL_PREFIX."messages m WHERE m.created >= '$start_date 00:00:00' AND m.created <= '$end_date 23:59:59' and m.audited='1' group by m.user_id"; 
		$data = $this->User->query($sql);
		foreach($data as $d) {
				if (isset($result[$d['m']['user_id']])) $result[$d['m']['user_id']]['num_audited'] = $d[0]['num_audited'];
		}  	  
		
		// find numbers of message mistakes for each user
		$sql = "SELECT count(*) as num_mistakes, mi.mistake_recipient FROM ".OA_TBL_PREFIX."mistakes mi LEFT JOIN ".OA_TBL_PREFIX."messages m ON m.id=mi.message_id WHERE m.created >= '$start_date 00:00:00' AND m.created <= '$end_date 23:59:59' AND mi.deleted='0' group by mi.mistake_recipient";
		$data = $this->User->query($sql);
		foreach($data as $d) {
				if (isset($result[$d['mi']['mistake_recipient']])) $result[$d['mi']['mistake_recipient']]['num_mistakes'] = $d[0]['num_mistakes'];
		}				
				
		$data = $this->User->query($sql);
			foreach ($result as $k => &$row) {
				$to_audit = round(($this->request->data['Search']['audit_goal'] * $row['num_messages']) / 100) - $row['num_audited'];
				if ($to_audit < 0) $to_audit = 0;
				$row['to_audit'] = $to_audit;
				if ($row['num_messages'] > 0) $percent = sprintf("%0.1f",( $row['num_audited']*100)/$row['num_messages']);
				else $percent = '0.0';
				$row['percent'] =  $percent;
				$percents["k" . $k] = $row['percent'] * 10;
				$num_to_audit["k" . $k] = $to_audit;
			}

		$this->set('data', $result);
		$this->set('start_date', $start_date);
		$this->set('end_date', $end_date);
		if (isset($this->request->data['format']) && $this->request->data['format'] == 'csv') {
			$this->render('audit_csv');
		}
	}
 
	function taking_calls() {
		$this->loadModel('UserLog');
		$data['user_id'] = AuthComponent::user('id');
		$data['extension'] = $this->Session->read('User_extension');
		$data['type'] = 'taking_calls_btn';
		$data['log_type'] = USEREVT_TAKING_CALLS_BTN;
		
		$data['session'] = $this->Session->id();
		$this->UserLog->create();
		if ($this->UserLog->save($data)) echo 'done';
		else echo 'failed';
		exit;
	} 

	function not_taking_calls() {
		$this->loadModel('UserLog');
		$data['user_id'] = AuthComponent::user('id');
		$data['extension'] = $this->Session->read('User_extension');
		$data['type'] = 'not_taking_calls_btn';
		$data['log_type'] = USEREVT_NOT_TAKING_CALLS_BTN;
		
		$data['session'] = $this->Session->id();
		$this->UserLog->create();
		if ($this->UserLog->save($data)) echo 'done';
		else echo 'fail';
		exit;
	}  
	
	function refresh_browser() {
		$this->loadModel('UserLog');
		$data['user_id'] = AuthComponent::user('id');
		$data['extension'] = $this->Session->read('User_extension');
		$data['type'] = 'refresh_browser';
		$data['log_type'] = USEREVT_REFRESH_BROWSER;
		
		$data['session'] = $this->Session->id();
		$this->UserLog->create();
		if ($this->UserLog->save($data)) echo 'done';
		else echo 'fail';
		exit;
	}
	
	function upload_photo($user_id=null) {
		if ($this->request->is('post') || $this->request->is('put')) {
			if (!empty($this->request->params['form']['file']['tmp_name']) && is_uploaded_file($this->request->params['form']['file']['tmp_name']) ) {
				$size = getimagesize($this->request->params['form']['file']['tmp_name']);
				
				// check if gd extension is loaded
				if (!extension_loaded('gd')) {
					echo 'failed'; exit;
				}
				if (1) {
					if ($size[0] > 200) {
						$width_org = $size[0];
						$height_org = $size[1];
						
						$scale = 200/$size[0];
						$new_width = round($scale * $width_org);
						$new_height = round($scale * $height_org);
						if ($new_width > $new_height) $new_dim = $new_height;
						else $new_dim = $new_width;
						if ($size['mime'] == 'image/jpeg') {
							$dest = imagecreatetruecolor($new_dim, $new_dim);
							$source = @imagecreatefromjpeg($this->request->params['form']['file']['tmp_name']);
							imagecopyresized($dest, $source, 0,0, 0,0, $new_dim, $new_dim, $width_org, $height_org);
							ob_start();
							imagejpeg($dest);
							$contents = ob_get_contents();
							ob_end_clean();
						}
						else if ($size['mime'] == 'image/png') {
							$dest = imagecreatetruecolor($new_dim, $new_dim);
							$source = @imagecreatefrompng($this->request->params['form']['file']['tmp_name']);
							imagecopyresized($dest, $source, 0,0, 0,0, $new_dim, $new_dim, $width_org, $height_org);
							ob_start();
							imagejpeg($dest);
							$contents = ob_get_contents();
							ob_end_clean();
						}
						else if ($size['mime'] == 'image/gif') {
							$dest = imagecreatetruecolor($new_dim, $new_dim);
							$source = @imagecreatefrompng($this->request->params['form']['file']['tmp_name']);
							imagecopyresized($dest, $source, 0,0, 0,0, $new_dim, $new_dim, $width_org, $height_org);
							ob_start();
							imagejpeg($dest);
							$contents = ob_get_contents();
							ob_end_clean();
						}
					}
					else $contents = file_get_contents($this->request->params['form']['file']['tmp_name']);
					
				}
				else $contents = file_get_contents($this->request->params['form']['file']['tmp_name']);
				$data['id'] = $this->request->data['user_id'];
				$data['photo'] = $size['mime'].";base64," . base64_encode($contents);
				if ($this->User->save($data)) echo $data['photo'];
				else echo 'failed';

			}
			else echo 'failed';		  
			exit;
		}
		else {    
			if (empty($user_id)) {
				echo '<br><br>You are not authorized to view this page';
				exit;
			}
			$this->set('user_id', $user_id);
		}
	}
	
	// returns a list of operators
	public function operators() {
		$data = $this->User->fetchCCStaff();
		$rows = array();
		foreach ($data as $d) {
			$rows[] = array('id' => $d['User']['id'], 'value' => $d['User']['id'], 'label' => $d['User']['firstname'] . " " . $d['User']['lastname'] . '('.$d['User']['username'].')', 'text' => $d['User']['firstname'] . " " . $d['User']['lastname'] . '('.$d['User']['username'].')');
		}
		$this->set(array(
			'rows' => $rows, 
			'_serialize' => array('rows')
		));
	}
	
	// include operators specified by $user_ids even if they have been deleted, which is a comma delimited list of ids to fetch
	public function operators_include_deleted($user_ids) {
		$data = $this->User->fetchCCStaff();
		$rows = array();
		foreach ($data as $d) {
			$rows[] = array('id' => $d['User']['id'], 'text' => $d['User']['firstname'] . " " . $d['User']['lastname'] . '('.$d['User']['username'].')');
		}
		
		// get an array of user IDs to check
		$temp = explode(',', $user_ids);
		
		// for each ID check if the user had been deleted and need to be included manually in list to be returned
		foreach ($temp as $user_id) {
			$d = $this->User->findById($user_id);
			if (!empty($d['User']['deleted'])) $rows[] = array('id' => $d['User']['id'], 'text' => $d['User']['firstname'] . " " . $d['User']['lastname'] . '('.$d['User']['username'].')');
		}
		
		$this->set(array(
			'more' => false,
			'results' => $rows, 
			'_serialize' => array('more', 'results')
		));
	}	
}
