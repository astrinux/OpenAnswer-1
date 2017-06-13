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

class NotesController extends AppController {
  public $paginate = array(
  	'limit' => 100,
  	'conditions' => array(),
    'order' => array(
    	'Note.id' => 'desc'
   	),

  );

	public $components = array('RequestHandler');
	public $helpers = array('Js');  
  
	public function index($did_id = null, $account_id = null) {
	  
	  if (!$account_id || $account_id == 'none') {
	    $this->loadModel('DidNumber');
	    $did = $this->DidNumber->findById($did_id);
	    $account_id = $did['DidNumber']['account_id'];
	  }
		$this->set('account_id', $account_id);
		$this->set('did_id', $did_id);
		$this->Note->recursive = 0;
		if (isset($this->request->data['find_acct_id']) && $this->request->data['find_acct_id']) {
			$this->paginate['conditions'][] = array(
					'Note.account_id' => $this->request->data['find_acct_id'],
			);
		}
		else {
			$this->paginate['conditions'][] = array(
					'Note.account_id' => $account_id,				
			);
			if ($did_id) {
        $this->paginate['conditions'][] = array(
					'Note.did_id' => $did_id,				
			  );			  
			}
		}
		
		$this->set('Notes', $this->paginate());
	}

	
	function add($account_id=null, $did_id=null, $message_id = null) {
		if ($this->request->is('post')) {
				$this->request->data['Note']['user_id'] = AuthComponent::user('id');
				$this->request->data['Note']['description'] = str_replace("\r\n", "<br>", $this->request->data['Note']['description']);
				$this->request->data['Note']['user_username'] = AuthComponent::user('username');
				$this->request->data['Note']['user_ext'] = $this->user_extension;
				$this->Note->create();
				if ($this->request->data['Note']['visible'] == 0) {
					unset( $this->request->data['Note']['start_date']);
					unset( $this->request->data['Note']['visible_when']);
					unset( $this->request->data['Note']['end_date']);
					unset( $this->request->data['Note']['start_time']);
					unset( $this->request->data['Note']['end_time']);
				}
				else if ($this->request->data['Misc']['visible_when'] == 0) {
				  $this->request->data['Note']['start_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['Misc']['start_date'] . ' ' . $this->request->data['Misc']['start_time']['hour'] .':' . $this->request->data['Misc']['start_time']['min'] . ' ' . $this->request->data['Misc']['start_time']['meridian']));
				  $this->request->data['Note']['end_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['Misc']['end_date'] . ' ' . $this->request->data['Misc']['end_time']['hour'] .':' . $this->request->data['Misc']['end_time']['min'] . ' ' . $this->request->data['Misc']['end_time']['meridian']));
				}
				$save_ok = $this->Note->save($this->request->data['Note']);
				if ($save_ok){
			    if ($this->request->data['Note']['visible']) $this->clearDidCache($did_id);	
			    $this->Session->setFlash('The note was added', 'flash_jsongood');
				}
				else {
			    $this->Session->setFlash('Cannot save Note, try again later', 'flash_jsongood');
        }				  
				
        $this->render('/Elements/json_result');
		}
		else {
		  if ($account_id == 'none' || !$account_id) {
		    $this->loadModel('DidNumber');
		    $did = $this->DidNumber->findById($did_id);
		    $account_id = $did['DidNumber']['account_id'];
		  }
			if ($did_id) {
			  $this->request->data['Note']['did_id'] = $did_id;
			}
			if ($message_id) $this->request->data['Note']['message_id'] = $message_id;
			if ($account_id) $this->request->data['Note']['account_id'] = $account_id;
  	}
	}	
	
  	
	public function msg_notes($message_id) {
	  $data = $this->Note->find('all', array('limit' => '30', 'order' => array('created' => 'desc'), 'conditions' => array('message_id' => $message_id), 'recursive' => '0'));
	  $this->set('Notes', $data);
	  $this->set('message_id', $message_id);
	}	
	
	public function operator($did_id) {
	  $this->loadModel('DidNumber');
	  $did = $this->DidNumber->findById($did_id);
	  
    $datetime = new DateTime();
    $client_time = new DateTimeZone($did['DidNumber']['timezone']);
    $datetime->setTimezone($client_time);
	  
    // cannot cache this since account might be time-sensitive
		$time_mysql = $datetime->format('Y-m-d H:i:s');

		$sql = "select * from ".OA_TBL_PREFIX."notes as n where did_id='$did_id' and visible='1' and (start_date IS NULL or (start_date <= '$time_mysql' and end_date >= '$time_mysql')) order by start_date desc"; 
		$n = $this->DidNumber->query($sql);
		$notes = array();
		foreach ($n as $note) {
		  $note['description'] = str_replace("\r\n", "<br>", $note['n']['description']);
		  $notes[] = $note;
		}	  
	  
	  $this->set('notes', $notes);
	}		
	
  function edit($id=null) {
		if ($this->request->is('post')) {
				if ($this->request->data['Misc']['visible_when'] == 0) {
				  $this->request->data['Note']['start_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['Misc']['start_date'] . ' ' . $this->request->data['Misc']['start_time']['hour'] .':' . $this->request->data['Misc']['start_time']['min'] . ' ' . $this->request->data['Misc']['start_time']['meridian']));
				  $this->request->data['Note']['end_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['Misc']['end_date'] . ' ' . $this->request->data['Misc']['end_time']['hour'] .':' . $this->request->data['Misc']['end_time']['min'] . ' ' . $this->request->data['Misc']['end_time']['meridian']));
				}
				else 
				{
          $this->request->data['Note']['start_date'] = '';
          $this->request->data['Note']['end_date'] = '';
				}		  
		  
		  $this->request->data['Note']['description'] = str_replace('<a href="', '<a onclick="window.open($(this).attr(\'href\'), \'View Note\', \'height=800,width=800\'); return false;" href="', $this->request->data['Note']['description']);
				$save_ok = false;
				$note_id = $this->request->data['Note']['id'];
				if ($note_id) {
          if (($this->request->data['Note']['visible']  || $this->request->data['Note']['old_visible']) && $this->request->data['Note']['did_id']) {
			      $this->clearDidCache($this->request->data['Note']['did_id']);	
			    }
				  //$this->request->data['Note']['description'] = str_replace("\r\n", "<br>", $this->request->data['Note']['description']);
					
					$save_ok = $this->Note->save($this->request->data['Note']);
				}
				if ($save_ok) $this->Session->setFlash('The changes were saved', 'flash_jsongood');
				else $this->Session->setFlash('Cannot save changes, please try again later', 'flash_jsongbad');

				$this->render('/Elements/json_result');							
			
		}
		else {
		  
			$this->request->data = $this->Note->findById($id);
			if (empty($this->request->data['Note']['start_date'])) {
			  $this->request->data['Misc']['visible_when'] = '1';
			} 
			else {
			  $this->request->data['Misc']['visible_when'] = '0';
			  $this->request->data['Misc']['start_date'] = date('Y-m-d', strtotime($this->request->data['Note']['start_date']));
			  $this->request->data['Misc']['end_date'] = date('Y-m-d', strtotime($this->request->data['Note']['end_date']));
			  $this->request->data['Misc']['start_time'] = date('H:i:s', strtotime($this->request->data['Note']['start_date']));
			  $this->request->data['Misc']['end_time'] = date('H:i:s', strtotime($this->request->data['Note']['end_date']));
			  
			}
			FireCake::log($this->request->data);
			//$this->request->data['Note']['description'] = str_replace("<br>", "\r\n", $this->request->data['Note']['description']);

		}
		
	}	
	
	function delete($id) {
	  if (!$id) {
  		$this->Session->setFlash('Cannot delete note, please try again later', 'flash_jsonbad');
  		$this->render('/Elements/json_result');	    
  		return;
	  }
	  $this->Note->recursive = 0;
	  $old = $this->Note->findById($id);
	  FireCake::log($old);
	  $this->loadModel('DidNumber');
	  $this->DidNumber->recursive = 0;
	  $did = $this->DidNumber->findById($old['Note']['did_id']);
    $del_ok = $this->Note->delete($id);
	  if ($del_ok) {
	    /*
      $e['user_id'] = AuthComponent::user('id');
      $e['user_username'] = AuthComponent::user('username');
      $e['new_values'] = '';
      $e['old_values'] = serialize($old); 
      $e['did_id'] = $did['DidNumber']['id'];
      $e['note_id'] = $id;
      $e['account_id'] =  $did['DidNumber']['account_id'];
      $e['description'] = 'Note \'' .$old['Note']['id'] . '\' deleted';
      $e['change_type'] = 'delete'; 
      $e['section'] = 'note'; 
      $this->Note->NotesEdit->create();
      $this->Note->NotesEdit->save($e);*/
	    
      $this->Session->setFlash('The note was deleted', 'flash_jsongood');	 		      
      $this->render('/Elements/json_result');   
	  }
	  else {
      $this->Session->setFlash('The note could not be deleted', 'flash_jsonbad');	 		      
      $this->render('/Elements/json_result');   
	    
	  }	  
	}
}
?>