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

class MistakesController extends AppController {
  public $paginate = array(
  	'limit' => 100,
  	'conditions' => array(),
    'order' => array(
    	'Mistake.id' => 'desc'
   	),

  );

	public $components = array('RequestHandler');
	public $helpers = array('Js');  
  
  public function beforeFilter() {
  	$categories = Configure::read('mistake_categories');
  	foreach ($categories as $v) {
  		$options[$v] = $v;
  	}
		$this->set('mistake_categories', $options);
		parent::beforeFilter();  	
	}
	public function by_operator($did_id = null, $msg_id = null) {
    $this->loadModel('User');
    
		if ($did_id) {
			$this->paginate['conditions'][] = array(
					'Mistake.did_id' => $did_id,
				
			);
		}		

		$joins = array(

			array('table' => OA_TBL_PREFIX . 'mistakes',
				'alias' => 'Mistake',
				'type' => 'left',
				'conditions' => array('User.id=Mistake.mistake_recipient', 'Mistake.deleted' => 0)
			),
			array('table' => OA_TBL_PREFIX . 'messages',
				'alias' => 'Message',
				'type' => 'left',
				'conditions' => array('Message.id=Mistake.message_id')
			));
			
		if (isset($this->request->data['Search']['user_id']) && $this->request->data['Search']['user_id']) {

			$conditions = array(
				'User.id' => $this->request->data['Search']['user_id']	
			);
		}
		else $conditions = array('User.deleted' => '0', 'User.display_stat' => '1');
		
		if (isset($this->request->data['Search']['start_date']) && $this->request->data['Search']['start_date'] && $this->request->data['Search']['end_date']) {
			$conditions[] = array(
				"((Message.created >= '".$this->dateMysqlize($this->request->data['Search']['start_date']) . " 00:00:00' and Message.created <= '". $this->dateMysqlize($this->request->data['Search']['end_date']) . " 23:59:59'	) or Message.created is NULL)"
			);
	  }
	  if (!$this->isAuthorized('MistakesByoperatorViewall')) {
		  $conditions = array('User.id' => AuthComponent::user('id'));
	  }
	  
	  $fields = array("count(Mistake.id) as cnt", 'User.username');
	  $data = $this->User->find('all', array('conditions' => $conditions, 'group' => array('Message.user_id'), 'joins' => $joins, 'fields' => $field, 'order' => 'User.username'));
		$this->set('Mistakes', $data);
	}
	  
	public function index($did_id = null, $msg_id = null) {
	  if (!$this->isAuthorized('MistakesByoperatorViewall')) {
		  $this->paginate['conditions'][] = array('Mistake.mistake_recipient' => AuthComponent::user('id'));
	    
	  }
		$this->Mistake->recursive = 0;
		$this->paginate['conditions'][] = array('Mistake.deleted' => 0);
		$grouped = false;
		if (!empty($this->request->data['Search']['m_group'])) {
			$this->by_operator($did_id);
			$this->render('by_operator');
			return;
		}	 		

		if ($did_id) {
			$this->paginate['conditions'][] = array(
					'Mistake.did_id' => $did_id,
				
			);
		}		
		if ($msg_id) {
			$this->paginate['conditions'][] = array(
					'Mistake.message_id' => $msg_id,
				
			);
		}		
			$joins = array(

				array('table' => OA_TBL_PREFIX . 'did_numbers',
					'alias' => 'DidNumber',
					'type' => 'left',
					'conditions' => array('DidNumber.id=Mistake.did_id')
				),	
				array('table' => OA_TBL_PREFIX . 'accounts',
					'alias' => 'Account',
					'type' => 'left',
					'conditions' => array('Account.id=DidNumber.account_id')
				),
				array('table' => OA_TBL_PREFIX . 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('User.id=Mistake.user_id')
				),
				array('table' => OA_TBL_PREFIX . 'messages',
					'alias' => 'Message',
					'type' => 'left',
					'conditions' => array('Message.id=Mistake.message_id')
				)					
			);
			
		if (isset($this->request->data['Search']['user_id']) && $this->request->data['Search']['user_id']) {

			$this->paginate['conditions'][] = array(
				'Mistake.mistake_recipient' => $this->request->data['Search']['user_id']	
			);
		}
		if (isset($this->request->data['Search']['start_date']) && $this->request->data['Search']['start_date'] && $this->request->data['Search']['end_date']) {
			$this->paginate['conditions'][] = array(
				'Message.created >= ' => $this->dateMysqlize($this->request->data['Search']['start_date']) . ' 00:00:00',
				'Message.created <= ' => $this->dateMysqlize($this->request->data['Search']['end_date']) . ' 23:59:59'	
			);
	  }
		$this->paginate['joins'] = $joins;
		$this->paginate['fields'] = array("DATE_FORMAT(Message.created, '%c/%d/%y %l:%i %p') as created_f", 'Account.account_num', 'Message.*', 'Mistake.*');
		$this->set('Mistakes', $this->paginate());

		$this->set('Mistakes', $this->paginate());
		if (!empty($this->request->data['Search']['summarize'])) {
			if (!empty($this->request->data['Search']['format']) && $this->request->data['Search']['format'] == 'csv') {
				$this->layout = 'plain';
				$find = array();
				$find['order'] = "Mistake.recipient_username";
				$find['conditions'] = $this->paginate['conditions'];
				$find['fields'] = $this->paginate['fields'];
				$find['joins'] = $joins;
				$this->set('Mistakes', $this->Mistake->find('all',$find));
				$this->render('summary_csv');
  			}
			else {
				$this->paginate['order'] = "Mistake.recipient_username";
				$this->set('Mistakes', $this->paginate());
				$this->render('summary');
			}
		}
		else {
			#default rendering
			$this->set('Mistakes', $this->paginate());
		}
	}
  	

	
	function add($did_id=null, $msg_id = null) {
		if ($this->request->is('post')) {
      if (!$this->isAuthorized('MistakesAdd')) {
  			$this->Session->setFlash(__('You are not allowed to assign mistakes'), 'flash_jsonbad');
        $this->render('/Elements/json_result');
        return;
        
      }
			if ($this->request->data['Mistake']) {
			  $this->loadModel('User');
				$save_ok = true;
				$did_id = $this->request->data['Mistake']['did_id'];
				$this->request->data['Mistake']['user_id'] = AuthComponent::user('id');
				$this->request->data['Mistake']['user_username'] = AuthComponent::user('username');
				$this->request->data['Mistake']['user_ext'] = $this->Session->read('User_extension');
				$user = $this->User->findById($this->request->data['Mistake']['mistake_recipient']);
				$this->request->data['Mistake']['recipient_username'] = $user['User']['username'];

				$this->Mistake->create();
				$save_ok = $this->Mistake->save($this->request->data['Mistake']);
				if ($save_ok) {
				  $res = array('success' => true, 'msg' => "Your entry has been added");
				  $this->loadModel('Message');
				  $this->Message->id = $this->request->data['Mistake']['message_id'];
				  $this->Message->saveField('audited', '1');
				}
				else $res = array('success' => FALSE, 'msg' => 'Cannot save Mistake, try again later');
				
			}
			$this->set('json', $res);
			$this->render('result');
		}
		else {
			if ($did_id && $msg_id) {

				if ($did_id) $this->request->data['Mistake']['did_id'] = $did_id;
				if ($msg_id) {
					$this->loadModel('Message');
					$data = $this->Message->findById($msg_id);
					$this->request->data['Mistake']['message_id'] = $msg_id;
					$this->request->data['Mistake']['mistake_recipient'] = $data['Message']['user_id'];
					$this->request->data['Mistake']['message_created'] = $data['Message']['created'];				
				}
			}
			else {
				$res = array('success' => FALSE, 'msg' => 'A DID and message id must be specified');
				
				$this->set('json', $res);
				$this->render('result');
			}
			
		}
		
		
	}
	

	function edit($id) {
		
		$this->set('mistake_id', $id);
		$save_ok = true;
		if ($this->request->is('post')) {
        if (!$this->isAuthorized('MistakesEdit')) {
    			$this->Session->setFlash(__('You are not allowed to edit mistakes'), 'flash_jsonbad');
          $this->render('/Elements/json_result');
          return;
          
        }			  
			if ($this->request->data['Mistake']) {
			  $this->loadModel('User');
			  
				if ($id) {
  				$user = $this->User->findById($this->request->data['Mistake']['mistake_recipient']);
	  			$this->request->data['Mistake']['recipient_username'] = $user['User']['username'];
					
					$save_ok = $this->Mistake->save($this->request->data['Mistake']);
				}
				if ($save_ok) $res = array('success' => true, 'msg' => 'Your changes have been saved');
				else $res = array('success' => true, 'msg' => 'Cannot save Mistake, try again later');

				$this->set('json', $res);								
				$this->render('result');
			}
		}
		else {
			$this->request->data = $this->Mistake->findById($id);
			$this->loadModel('MessagesPrompt');
			$msg_id = $this->request->data['Mistake']['message_id'];
			$conditions = array('message_id' => $msg_id);
			$prompts = $this->MessagesPrompt->find('all', array('conditions' => $conditions, 'order' => array('sort' => 'asc')));
			$allowed = false;
			
			
			
			
			
    if ($this->isAuthorized('MistakesEditAll')) {
        $allowed = true;
    }
    else if ($mistake['Mistake']['user_id'] == AuthComponent::user('id')) {
        if ($this->isAuthorized('MistakesEditOwn')) {
            $allowed = true;
        }
    }
      $this->set('allowed', $allowed);
      $this->set('prompts', $prompts);
		}
		
	}	
	public function msg_mistakes($message_id) {
		$this->loadModel('User');
		$operators = $this->User->getCCStaffUsernames();
		$this->set('operators', $operators);
	  $data = $this->Mistake->find('all', array('conditions' => array('message_id' => $message_id, 'deleted' => '0' ), 'recursive' => '0'));
	  $this->set('data', $data);
	  $this->set('message_id', $message_id);
	}	
	
	public function delete($id) {
	  if (empty($id)) {
			$this->Session->setFlash(__('You must specify a mistake to delete'), 'flash_jsonbad');
      $this->render('/Elements/json_result');
    }
    $mistake = $this->Mistake->findById($id);
    $allowed = false;
    
    
    
    if ($this->isAuthorized('MistakesDeleteAll')) {
        $allowed = true;
    }
    else if ($mistake['Mistake']['user_id'] == AuthComponent::user('id')) {
        if ($this->isAuthorized('MistakesDeleteOwn')) {
            $allowed = true;
        }
    }
    if ($allowed) {
      $mistake['Mistake']['deleted'] = '1';
      $mistake['Mistake']['deleted_ts'] = date('Y-m-d H:i:s');
      $mistake['Mistake']['deleted_userid'] = AuthComponent::user('id');
      if ($this->Mistake->save($mistake['Mistake'])) {
  			$this->Session->setFlash(__('Mistake was deleted'), 'flash_jsongood');
        $this->render('/Elements/json_result');
      }
      else {
  			$this->Session->setFlash(__('You cannot delete this mistake (1)'), 'flash_jsonbad');
        $this->render('/Elements/json_result');
      }
    }
    else {
			$this->Session->setFlash(__('You cannot delete this mistake'), 'flash_jsonbad');
      $this->render('/Elements/json_result');
    }
    
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
    $sql = "select count(*) as cnt, DATE_FORMAT(DATE(message_created),'%a %c/%e') as date_created, GROUP_CONCAT(message_id) as msgids from ".OA_TBL_PREFIX."mistakes m where m.message_created >= '$min_date 00:00:00' and m.message_created <= '$today 23:59:59' and m.message_created >= '$min_date 00:00:00' and m.mistake_recipient='$user_id' group by DATE(message_created)";
    
    $data = $this->Mistake->query($sql);
    foreach ($data as $r) {
      $days[$r[0]['date_created']] = $r[0]['cnt']; 
    }
    $this->set('mistakes', $days);
    $this->set('oa_title', 'Mistakes by the day');
    
  }

  function my_daily($user_id) {
    $this->daily($user_id);
    $this->render('daily');
  }  	
}
?>