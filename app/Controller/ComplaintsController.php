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

class ComplaintsController extends AppController {
  public $paginate = array(
  	'limit' => 100,
  	'conditions' => array(),
    'order' => array(
    	'Complaint.id' => 'desc'
   	),

  );

	public $components = array('RequestHandler');
	public $helpers = array('Js');  
  
  function beforeFilter() {
  	parent::beforeFilter();
  	$this->set('complaint_options', array('0' => 'Open', '1' => 'Pending', '2' => 'Resolved', '3' => 'Unfounded'));
  }
  
	public function index($did_id = null, $msg_id = null) {
		$this->Complaint->recursive = 0;
	  $this->set('did_id', $did_id);
	  $this->set('msg_id', $msg_id);
		
		// check if we need to limit search results to a specific sub-account
		if (isset($this->request->data['find_did_id']) && $this->request->data['find_did_id']) {
			$this->paginate['conditions'][] = array(
					'Complaint.did_id' => $this->request->data['find_did_id'],
				
			);
		}
		if ($did_id) {
			$this->paginate['conditions'][] = array(
					'Complaint.did_id' => $did_id,
			);
		}
		
		// check if we need to search for complaints for a specific message		
		if ($msg_id) {
			$this->paginate['conditions'][] = array(
					'Complaint.message_id' => $msg_id,
			);
		}		

		// left join the operators table    
		$joins = array(
			array('table' => OA_TBL_PREFIX . 'complaints_operators',
				'alias' => 'ComplaintsOperator',
				'type' => 'left',
				'conditions' => array('Complaint.id=ComplaintsOperator.complaint_id')
			),
			array('table' => OA_TBL_PREFIX . 'did_numbers',
				'alias' => 'DidNumber',
				'type' => 'left',
				'conditions' => array('DidNumber.id=Complaint.did_id')
			),
			array('table' => OA_TBL_PREFIX . 'accounts',
				'alias' => 'Account',
				'type' => 'left',
				'conditions' => array('Account.id=DidNumber.account_id')
			)			
		);
		$this->paginate['joins'] = $joins;

    
		// check if we need to limit search results to a specific user
		if (isset($this->request->data['Search']['user_id']) && $this->request->data['Search']['user_id']) {
			$this->paginate['conditions'][] = array(
				'ComplaintsOperator.operator_id' => $this->request->data['Search']['user_id']	
			);
		}
		
		// check if there are date restrictions
		if (isset($this->request->data['Search']['start_date']) && !empty($this->request->data['Search']['start_date'])) {
			$this->paginate['conditions'][] = array(
				'Complaint.incident_date >= ' => $this->dateMysqlize($this->request->data['Search']['start_date']) . ' 00:00:00'	
			);
	  }		
		if (isset($this->request->data['Search']['end_date']) && $this->request->data['Search']['end_date']) {
			$this->paginate['conditions'][] = array(
				'Complaint.incident_date <= ' => $this->dateMysqlize($this->request->data['Search']['end_date']) . ' 23:59:59'	
			);
	  }			  
	  
		$this->paginate['group'] = array('Complaint.id');
		$this->paginate['fields'] = array('Complaint.*', 'Account.account_num', 'DidNumber.company', "GROUP_CONCAT(ComplaintsOperator.operator_username order by operator_username) as operators");
		$data = $this->paginate();
		$this->set('Complaints', $data);
    $this->set('target', 'complaints-content');
		$this->set('div_id', 'complaintsidx');
	}

	public function did_index($did_id = null, $msg_id = null) {
		$this->index($did_id, $msg_id);
		$this->set('div_id', 'did_complaints');
    $this->render('index');
	}
  	
  function view($did_id=null, $msg_id=null) {
    $this->index($did_id, $msg_id); 
    $this->set('target', 'did-content');
  }
	
	function add($msg_id=null, $did_id = null) {
		$this->set('did_id', $did_id);
		if ($this->request->is('post')) {
		  $this->loadModel('User');
  		if (($this->request->is('post') || $this->request->is('put'))) {
				$save_ok = true;
				$did_id = $this->request->data['Complaint']['did_id'];
				if (empty($did_id)) {
					$this->Session->setFlash(__('Please specify which account the complaint is for'), 'flash_jsonbad');	
					$this->render('/Elements/json_result');		
					return;									
				}
				$temp = explode(',', $this->request->data['c_opsel']);
				$this->request->data['Complaint']['user_username'] = AuthComponent::user('username');
				$this->request->data['Complaint']['user_id'] = AuthComponent::user('id');
				$this->Complaint->create();
				$ok = $this->Complaint->save($this->request->data['Complaint']);
				if (!$ok) $save_ok = false;
				foreach($temp as $oid) {
					if (is_numeric($oid)) {
						$data['ComplaintsOperator']['operator_id'] = $oid;
						$data['ComplaintsOperator']['did_id'] = $did_id;
						$data['ComplaintsOperator']['complaint_id'] = $this->Complaint->id;
						$user = $this->User->findById($oid);
						
						$data['ComplaintsOperator']['operator_username'] = $user['User']['username'];
						$this->Complaint->ComplaintsOperator->create();
						$ok = $this->Complaint->ComplaintsOperator->save($data['ComplaintsOperator']);
						if (!$ok) $save_ok = false;
					}
				}
				if ($save_ok) $this->Session->setFlash(__('Entry added. '), 'flash_jsongood');
				else $this->Session->setFlash(__('Cannot save changes, please try again later. '), 'flash_jsonbad');
			}
			$this->render('/Elements/json_result');				

		}
		else {
		  $this->loadModel('Message');
		  if (is_numeric($msg_id)) $msg = $this->Message->findById($msg_id);
			if ($did_id) $this->request->data['Complaint']['did_id'] = $did_id;
			if ($did_id) {
				if (!empty($msg)) {
					$this->request->data['Complaint']['message_id'] = $msg_id;
					$this->request->data['c_opsel'] = $msg['Message']['user_id'];
					$this->request->data['Complaint']['incident_date'] = $msg['Message']['created'];
				}
			}
			
		}

	}
	

	function edit($id = null) {
		$save_ok = true;
		$this->set('complaint_id', $id);
		if ($this->request->is('post')) {
				$complaint_id = $this->request->data['Complaint']['id'];
				if ($complaint_id) {
					$this->loadModel('User');
					$temp = explode(',', $this->request->data['c_opsel']);
					$this->Complaint->save($this->request->data['Complaint']);
					$save_ok = $this->Complaint->ComplaintsOperator->deleteAll(array('ComplaintsOperator.complaint_id' => $complaint_id));
					foreach($temp as $oid) {
						if (is_numeric($oid)) {
							$data['ComplaintsOperator']['operator_id'] = $oid;
							$data['ComplaintsOperator']['complaint_id'] = $this->request->data['Complaint']['id'];
						  $user = $this->User->findById($oid);
						  $data['ComplaintsOperator']['operator_username'] = $user['User']['username'];
							$this->Complaint->ComplaintsOperator->create();
							$ok = $this->Complaint->ComplaintsOperator->save($data['ComplaintsOperator']);
							if (!$ok) $save_ok = false;						
						}
					}

				}
				if ($save_ok) {
          $this->Session->setFlash(__('Changes have been saved. '), 'flash_jsongood');
				}
				else $this->Session->setFlash(__('Your changes could not be saved. Please, try again.'), 'flash_jsonbad');

				$this->render('/Elements/json_result');
									
		}
		else {
			
		// left join the operators table    
		$joins = array(
			array('table' => OA_TBL_PREFIX . 'complaints_operators',
				'alias' => 'ComplaintsOperator',
				'type' => 'left',
				'conditions' => array('Complaint.id=ComplaintsOperator.complaint_id')
			),
			array('table' => OA_TBL_PREFIX . 'did_numbers',
				'alias' => 'DidNumber',
				'type' => 'left',
				'conditions' => array('DidNumber.id=Complaint.did_id')
			),
			array('table' => OA_TBL_PREFIX . 'accounts',
				'alias' => 'Account',
				'type' => 'left',
				'conditions' => array('Account.id=DidNumber.account_id')
			)			
		);			
			$this->request->data = $this->Complaint->find('first', array('conditions' => array('Complaint.id' => $id), 'joins' => $joins, 'fields' => array('Complaint.*', 'Account.account_num', 'DidNumber.company')));
			if (!$this->request->data['Complaint']['message_id'])  {
				$this->request->data['c_type'] = '1';				
			}
			else $this->request->data['c_type'] = '2';	
			$temp = array();
			foreach ($this->request->data['ComplaintsOperator'] as $op) {
				$temp[] = $op['operator_id'];
			}
			$this->request->data['c_opsel'] = implode(',', $temp);
		}
		
	}	
	public function msg_complaints($msg_id) {
/*		$this->loadModel('User');
		$operators = $this->User->getCCStaff(false);
		$this->set('operators', $operators);*/
	  $data = $this->Complaint->find('all', array('conditions' => array('message_id' => $msg_id), 'recursive' => '0'));
	  $this->set('Complaints', $data);	  
	  $this->set('message_id', $msg_id);
	}
	
	function delete($id) {
	  if (!$id) {
  		$this->Session->setFlash('Cannot delete note, please try again later', 'flash_jsonbad');
  		$this->render('/Elements/json_result');	    
  		return;
	  }
	  $this->Complaint->recursive = 0;
	  $old = $this->Complaint->findById($id);
	  $this->loadModel('DidNumber');
	  $this->DidNumber->recursive = 0;
	  $did = $this->DidNumber->findById($old['Complaint']['did_id']);
    $del_ok = $this->Complaint->delete($id);
	  if ($del_ok) {
/*      $e['user_id'] = AuthComponent::user('id');
      $e['user_username'] = AuthComponent::user('username');
      $e['new_values'] = '';
      $e['old_values'] = serialize($old); 
      $e['did_id'] = $did['DidNumber']['id'];
      $e['complaint_id'] = $id;
      $e['account_id'] =  $did['DidNumber']['account_id'];
      $e['description'] = 'Complaint \'' .$old['Complaint']['id'] . '\' deleted';
      $e['change_type'] = 'delete'; 
      $e['section'] = 'note'; 
      $this->_saveChanges(serialize($changes), serialize($data), serialize($new), $data['Employee']['account_id'], $data['Employee']['did_id'], 'employee', 'edit', $e); 
	    */
       	    
      $this->Session->setFlash('The complaint was deleted', 'flash_jsongood');	 		      
      $this->render('/Elements/json_result');   
	  }
	  else {
      $this->Session->setFlash('The complaint could not be deleted', 'flash_jsonbad');	 		      
      $this->render('/Elements/json_result');   
	    
	  }	  
	}
}
?>