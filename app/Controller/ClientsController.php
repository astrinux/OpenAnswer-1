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

class ClientsController extends AppController {

	

	public function beforeFilter() {
		parent::beforeFilter();

	}
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Client->recursive = 0;
		$this->set('Clients', $this->paginate());

	}

	/*public function find() {
		$search = $this->request->query['term'];
		$sql = "select account_name, account_num, id from ".OA_TBL_PREFIX."clients where account_name like '%$search%' or account_num like '$search%'";
		$accounts = $this->Client->query($sql);
		$this->set('accounts', $accounts);
		
	}*/
	public function find() {
		$search = $this->request->query['term'];
		//$sql = "select account_name, account_num, id from ".OA_TBL_PREFIX."clients where account_name like '%$search%' or account_num like '$search%' OR ";
		$sql = "select c.account_name, c.account_num,  d.id, d.did_number from ".OA_TBL_PREFIX."clients c left join ".OA_TBL_PREFIX."dids d on c.id=d.account_id where d.did_number like '%$search%' or c.account_name like '%$search%' or c.account_num like '$search%' ";
		$accounts = $this->Client->query($sql);
		$this->set('accounts', $accounts);
		
	}
/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Client->id = $id;
		if (!$this->Client->exists()) {
			throw new NotFoundException(__('Invalid client'));
		}
		$this->set('Client', $this->Client->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Client->create();
			if ($this->Client->save($this->request->data)) {
				$this->Session->setFlash(__('The client has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The client could not be saved. Please, try again.'));
			}
		}
	}



/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($did_id = null) {
    if (!$did_id) {
      $this->Session->setFlash(__('Cannot find the account.'), 'flash_jsonbad');
    }
    else {    

  		if ($this->request->is('post') && isset($this->request->data['Client'])) {
  			if ($this->Client->save($this->request->data)) {
          $this->Session->setFlash(__('The client has been saved.'), 'flash_jsongood');
  		    $this->render('/Elements/json_result');
  			} else {
  				$this->Session->setFlash(__('The client could not be saved. Please, try again.', 'flash_jsonbad'));
  		    $this->render('/Elements/json_result');
  			}
  		}
  		else {
  	    $did = $this->Client->Did->findById($did_id);
  	    if ($did) {
          $this->request->data = $did;
        }
        else {
  				$this->Session->setFlash(__('Cannot find the account, please try again later', 'flash_jsonbad'));
  		    $this->render('/Elements/json_result');
  			}
  		}
	  }
	}

	public function company($id = null) {
		$this->edit($id);
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
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Client->id = $id;
		if (!$this->Client->exists()) {
			throw new NotFoundException(__('Invalid client'));
		}
		if ($this->Client->delete()) {
			$this->Session->setFlash(__('Client deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Client was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
	public function search() {
		$this->layout = 'plain';
		$search = mysql_escape_string(strip_tags(trim($this->request->query['term'])));
		$query = "select account_name, account_num, id from ".OA_TBL_PREFIX."clients where account_num like '%$search%' or account_name like '%$search%'";
		$res = $this->Client->query($query);
		$this->set('rows', $res);
	}
	
	public function instructions($aid) { 
		$this->layout = 'json';
    $test_time = '';
		if (!isset($this->request->data['uniqueid']) || !$this->request->data['uniqueid']) {
			$d['CallLog']['cid_name'] = '';
			$d['CallLog']['cid_number'] = '';
			$d['CallLog']['unique_id'] = 'TESTCALL';
      $test_time = $this->request->data['event'];
		}
		else {
			$d['CallLog']['cid_name'] = $this->request->data['calleridname'];
			$d['CallLog']['cid_number'] = $this->request->data['calleridnum'];
			$d['CallLog']['unique_id'] = $this->request->data['uniqueid'];
		}
		$d['CallLog']['account_id'] = $aid;
		$d['CallLog']['operator_code'] = AuthComponent::user('extension');
		$d['CallLog']['start_time'] = date('Y-m-s G:i:s');
		$d['CallLog']['end_time'] = '0000-00-00';
		$this->loadModel('CallLog');
		$this->CallLog->create();
		$this->CallLog->save($d['CallLog']);
		$callid = $this->CallLog->getLastInsertID();
		
/*		$d['Message']['account_id'] = $aid;
		$d['Message']['operator_id'] = AuthComponent::user('extension');
		$d['Message']['call_id'] = $callid;
		$d['Message']['calltype'] = '';
		$this->loadModel('Message');
		$this->Message->create();
		$this->Message->save($d['Message']);
		$msgid = $this->Message->getLastInsertID();		*/
		$instructions = $this->_instructions($aid, $test_time);
		$instructions['msg_id'] = '';
		$instructions['call_id'] = $callid;
		$this->set('json', $instructions);

	}
	
	public function screenPop() {
	  /*
        db.query("select * from ccact_clients where did='"+did+"'").execute(function(err, rows) {
	  
              	var now = new time.Date();
              	var thetime = {};
              	now.setTimezone(timezones[rows[0]['timezone']]);  
              	thetime['client_offset'] = now.getTimezoneOffset(); 
		          	
             			            console.log("Screen Pop: " + ext);
            				          var socket_id = OAClientsByExt[ext]['socket_id'];
            				          console.log('sending call_incoming');
            				          io.of('/openAnswer').socket(socket_id).emit('call_incoming', {'account': rows[0], 'time' : thetime, 'did': did, 'event': event });
       				          			if (event != null) {
       				          				monitoredChannels[ext] = event.channel;
       				          			}	  
       				          			*/
	}
}
