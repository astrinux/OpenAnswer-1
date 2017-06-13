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

class DidNumbersController extends AppController {
	public $components = array('RequestHandler');
	public $helpers = array('Js');  
	public $layout;
  public $paginate = array(
  	'limit' => 20,
  	'recursive' => 0,
    'order' => array(
    	'DidNumber.date_entered' => 'desc'
   	)
  );
  private $days = array("1" => "Mon", "2" => "Tue", "3" => "Wed", "4" => "Thu", "5" => "Fri", "6" => "Sat", "7" => "Sun");
		  
	public function beforeFilter() {
		parent::beforeFilter();
	}
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->DidNumber->recursive = 0;
		$csv = false;
		$this->paginate['fields'] = array('DidNumber.status, DidNumberEntry.number, DidNumberEntry.alias, DidNumber.id, DidNumber.date_entered, DidNumber.difficulty, DidNumber.industry, DidNumber.company, Account.account_num, Account.deleted, DATE_FORMAT(DidNumber.date_entered, "%a %c/%e") as date_entered_f');
		$conditions = array('DidNumber.deleted' => 0, 'Account.deleted' => 0);
		if (isset($this->request->data['term']) || isset($this->request->data['Search'])) {
		  if (!empty($this->request->data['Search']['format']) && $this->request->data['Search']['format'] == 'csv') {
		    $this->paginate['limit'] = 5000;
		    $csv = true;
		  }
		  if (isset($this->request->data['term'])) {
		    $search = $this->request->data['term'];
                $conditions[] = "(Account.account_name like '%$search%' or Account.account_num like '$search%' or DidNumberEntry.number like '%$search%' or DidNumberEntry.alias like '%$search%')";
		  }
		  if (!empty($this->request->data['Search']['difficulty'])) {
		    $conditions[] =  array('DidNumber.difficulty' => $this->request->data['Search']['difficulty']);
		  }
		  if (isset($this->request->data['Search']['status']) && $this->request->data['Search']['status'] !== '') {
		    $conditions[] =  array('DidNumber.status' => $this->request->data['Search']['status']);
		  }
		}
			$joins = array(
	      array(
	        'table' => OA_TBL_PREFIX . 'did_numbers_entries',
	        'alias' => 'DidNumberEntry',
	        'type' => 'left',
	        'conditions' => array('`DidNumber`.`id` = `DidNumberEntry`.`did_id`')
	      )
	    );		  
      $this->paginate['joins'] = $joins;	
		$this->paginate['conditions'] = $conditions;
		$data = $this->paginate();
    if ($csv) {
      $rows[] = array("Account Num", "Company", "Number", "Customer Number", "Difficulty", "Taking Calls", "Created", "Industry");
      foreach ($data as $k => $v) {
        if ($v['DidNumber']['status'] == '0') $status = 'No';
        else $status = 'Yes';
        $rows[] = array($v['Account']['account_num'], $v['DidNumber']['company'],$v['DidNumberEntry']['number'], $v['DidNumberEntry']['alias'],  $v['DidNumber']['difficulty'],$status, $v['DidNumber']['date_entered'], $v['DidNumber']['industry']);          
      }
      header("Content-type: text/csv"); 
      header("Content-Disposition: attachment; filename=subaccounts.csv");
      foreach ($rows as $row)
      {
          // Loop through every value in a row
          foreach ($row as &$value)
          {
              // Apply opening and closing text delimiters to every value
              $value = "\"".$value."\"";
          }
          // Echo all values in a row comma separated
          echo implode("\t",$row)."\n";
      }
      exit;

    }
        else {
        //  $this->set('data', $data);
            $this->set(array(
                'data' => $data, 
                '_serialize' => array('data')
            ));
        }
    }
	// returns a list of sub-accounts matching the search term.  
	public function find($id = null) {
	  if ($id && is_numeric($id)) {
  		$sql = "select a.account_name, a.account_num, d.timezone, d.company, d.id, GROUP_CONCAT(e.number) as numbers, GROUP_CONCAT(COALESCE(e.alias, NULL, '')) as aliases from ".OA_TBL_PREFIX."accounts a left join ".OA_TBL_PREFIX."did_numbers d on a.id=d.account_id left join ".OA_TBL_PREFIX."did_numbers_entries e on d.id=e.did_id where d.id = '$id' and d.deleted='0' and a.deleted='0' group by e.did_id";
  		$search = '';
	  }
	  else {
            // strip any tags and escape single quote characters
            $search = str_replace("'", "\'", strip_tags($this->request->query['term']));
            
            // a superuser can also specify the unique identifying primary key of the db table entry to search for
            if ($this->isAuthorized('DidnumbersFindAll')) {
                $sql = "select a.account_name, a.account_num, d.company, d.id, GROUP_CONCAT(e.number) as numbers, GROUP_CONCAT(COALESCE(e.alias, NULL, '')) as aliases from ".OA_TBL_PREFIX."accounts a left join ".OA_TBL_PREFIX."did_numbers d on a.id=d.account_id left join ".OA_TBL_PREFIX."did_numbers_entries e on d.id=e.did_id where (a.account_name like '%$search%' or d.company like '%$search%' or a.account_num like '$search%' or e.number like '%$search%' or e.alias like '%$search%' or d.id = '$search')  and d.deleted='0' and a.deleted='0' group by e.did_id";
            }
            else {
                $sql = "select a.account_name, a.account_num, d.company, d.id, GROUP_CONCAT(e.number) as numbers, GROUP_CONCAT(COALESCE(e.alias, NULL, '')) as aliases from ".OA_TBL_PREFIX."accounts a left join ".OA_TBL_PREFIX."did_numbers d on a.id=d.account_id left join ".OA_TBL_PREFIX."did_numbers_entries e on d.id=e.did_id where (d.company like '%$search%' or a.account_name like '%$search%' or a.account_num like '$search%' or e.number like '%$search%' or e.alias like '%$search%')  and d.deleted='0' and a.deleted='0' group by e.did_id";
            }
	  }
		$accounts = $this->DidNumber->query($sql);
		$this->set('search_term', $search);
		$this->set('accounts', $accounts);
		
	}
	
	public function find2($id = null) {
	  $this->find($id);
	}	
	
	public function msgfind($id=null) {
		$this->find($id);
	}
/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
    $this->DidNumber->unbindModel(
        array('hasMany' => array('Employee', 'Calltype', 'DidFile', 'DidNumbersEdit'))
    );
	  
	  $data = $this->DidNumber->find('first', array('fields' => array('Account.account_num', 'Account.account_name', 'DidNumber.*'), 'conditions' => array('DidNumber.id' => $id)));
	  $data['DidNumber']['account_num'] = $data['Account']['account_num'];
	  $data['DidNumber']['account_name'] = $data['Account']['account_name'];
	  unset($data['Account']);

		$this->set('DidNumber', $data);
	}

/**
 * add method
 *
 * @return void
 */
	public function add($account_id) {
    if (!$this->isAuthorized('DidnumbersAdd')) {    
  	  $this->Session->setFlash(__('You are not authorized '), 'flash_jsonbad');
  		$this->render('/Elements/json_result');
  		return;
    }	  
	  $this->set('account_id', $account_id);
		if ($this->request->is('post')) {
      if (isset($this->request->data['Misc']['answerphrase']) && trim($this->request->data['Misc']['answerphrase'])) $this->request->data['DidNumber']['answerphrase'] = trim($this->request->data['Misc']['answerphrase']);
		  
      $did_number = preg_replace('/[^0-9]/', '', $this->request->data['DidNumbersEntry']['did_number']);
      $this->loadModel('DidNumbersEntry');
		  $conditions = array('DidNumber.deleted' => '0', 'number' =>  $did_number);
		  $duplicates = $this->DidNumbersEntry->find('all', array('recursive' => 0, 'conditions' => $conditions));
		  if ($duplicates) {
				$this->Session->setFlash(__('The phone number you specified is already used on another account, please specify a different phone number.'), 'flash_jsonbad');
			  $this->render('/Elements/json_result');
			  return;
		  }

      // check if DID number already assigned to someone else
		  /*$duplicate = $this->DidNumber->find('first', array('conditions' => $conditions));
		  if ($duplicate) {
				$this->Session->setFlash(__('That number is already assigned to the following account: ' . $duplicate['Account']['account_name'] . '('.$duplicate['Account']['account_num'].')'), 'flash_jsonbad');
			  $this->render('/Elements/json_result');
			  return;
		  }*/

			$this->DidNumber->create();
			/* if status is set to active, check if call types instructions have been defined */
			$this->request->data['DidNumber']['date_entered'] = date('Y-m-d G:i:s');
			if ($this->DidNumber->save($this->request->data)) {
				$this->Session->setFlash(__('The changes have been saved'), 'flash_jsongood');
				$did_id = $this->DidNumber->getInsertID();
				$data['DidNumbersEntry']['did_id'] = $did_id;
				$data['DidNumbersEntry']['number'] = $did_number;
				$this->DidNumbersEntry->create();
				$this->DidNumbersEntry->save($data['DidNumbersEntry']);
				       
        $description = 'Number assigned: '. $this->request->data['DidNumbersEntry']['did_number'];
        $this->_saveChanges($description, '', serialize($this->request->data), $account_id, $did_id, 'did', 'add');
        
			} else {
				$this->Session->setFlash(__('The changes could not be saved. Please try again later.'), 'flash_jsonbad');
			}
			$this->render('/Elements/json_result');
		}
		else {
  	  $this->set('industries', Configure::read('options_industries'));		  
		  $this->DidNumber->Account->recursive=0;
		  $account = $this->DidNumber->Account->findById($account_id);
		  $this->request->data['DidNumber']['company'] = $account['Account']['account_name'];
		  $this->request->data['DidNumber']['address1'] = $account['Account']['billing_address1'];
		  $this->request->data['DidNumber']['address2'] = $account['Account']['billing_address2'];
		  $this->request->data['DidNumber']['city'] = $account['Account']['billing_city'];
		  $this->request->data['DidNumber']['state'] = $account['Account']['billing_state'];
		  $this->request->data['DidNumber']['zip'] = $account['Account']['billing_zip'];
		  $this->request->data['DidNumber']['contact_name'] = $account['Account']['contact_name'];
		  $this->request->data['DidNumber']['contact_phone'] = $account['Account']['billing_phone'];
		  $this->request->data['DidNumber']['main_phone'] = $account['Account']['billing_phone'];
		  $this->request->data['DidNumber']['province'] = $account['Account']['billing_province'];
		}
	}

  public function record($did_id = null) {
    if (!$this->isAuthorized('DidnumbersRecord')) {    
  	  $this->Session->setFlash(__('You are not authorized '), 'flash_jsonbad');
  		$this->render('/Elements/json_result');
  		return;
    }	      
    if (empty($did_id)) $this->redirect('/');
    $this->DidNumber->recursive = 0;
    $did = $this->DidNumber->findById($did_id);
    $this->set('did_name', $did['DidNumber']['company']);
    $this->set('account_number', $did['Account']['account_num']);
    $this->set('did_id', $did_id);
    $this->layout = 'plain';
  }
  
  public function listen($did_id) {
	  $this->loadModel('CompanyAudio');
    
  	$audio = $this->CompanyAudio->findByDidId($did_id);
  	if ($audio['CompanyAudio']['company_audio_type'] == 'audio/ogg')  $filename = $did_id . "_audio.ogg";
  	else $filename = $did_id . "_audio.wav";
    header("Content-Disposition: attachment; filename=" . $did_id . "_audio"); 
    header("Content-length: " . strlen($audio['CompanyAudio']['company_audio']));
    header("Content-type: " . $audio['CompanyAudio']['company_audio_type']);
    echo  $audio['CompanyAudio']['company_audio'];
    exit;
  }
  
  public function delete_audio($did_id=null) {
	  $this->loadModel('CompanyAudio');
    if (empty($did_id)) {
  	  $this->Session->setFlash(__('You are not authorized to delete this file'), 'flash_jsonbad');
    }
    else {
      if ($this->CompanyAudio->deleteAll(array('did_id' => $did_id))) {
    	  $this->Session->setFlash(__('Your recording was deleted successfully'), 'flash_jsongood');
      }
      else {
    	  $this->Session->setFlash(__('Cannot delete your recording'), 'flash_jsonbad');
      }
    }
		$this->render('/Elements/json_result');
  }
  
  public function save_audio($did_id=null) {
	  $this->loadModel('CompanyAudio');
    
    $this->layout = 'ajax';
    if (empty($did_id)) {
  	  $this->Session->setFlash(__('You are not authorized to upload this file'), 'flash_jsonbad');
    }
    else {
      if (isset($_FILES["audio-blob"])) {
        $this->CompanyAudio->deleteAll(array('did_id' => $did_id));
          if ($_FILES["audio-blob"]['type'] == 'audio/ogg' || $_FILES["audio-blob"]['type'] == 'audio/wav') {
            $data['CompanyAudio']['company_audio'] = file_get_contents($_FILES["audio-blob"]["tmp_name"]);
            $data['CompanyAudio']['did_id'] = $_POST['did_id'];
            $data['CompanyAudio']['company_audio_type'] = $_FILES["audio-blob"]['type'];
            $this->CompanyAudio->create();
            if ($this->CompanyAudio->save($data['CompanyAudio'])) {
          	  $this->Session->setFlash(__('Your recording was saved successfully'), 'flash_jsongood');
            }
            else {
          	  $this->Session->setFlash(__('Cannot save your recording'), 'flash_jsonbad');
            }
            //$uploadDirectory = '/var/www/html/openAnswerDev/company-recordings/'.$fileName;
  /*          if (!move_uploaded_file($_FILES["${type}-blob"]["tmp_name"], $uploadDirectory)) {
                echo(" problem moving uploaded file");
                echo "<br><br>";
                echo $_FILES["audio-blob"]["tmp_name"] . "<br>";
            }*/
          }
        
      }
      else {
          	  $this->Session->setFlash(__('Cannot upload your recording'), 'flash_jsonbad');      
      }
    }
		$this->render('/Elements/json_result');
  }
  
/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($did_id = null) {
	  $this->loadModel('DidNumbersEntry');
	  $this->loadModel('CompanyAudio');
	  
    if (isset($this->passedArgs['list'])) {
      $this->set('list', true);
    }
    else $this->set('list', false);
	  $this->set('did_id', $did_id);
    if (!$did_id) {
      $this->Session->setFlash(__('Cannot find the account.'), 'flash_jsonbad');
    }
    else {    
      
  		if (($this->request->is('post') || $this->request->is('put'))&& isset($this->request->data['DidNumber'])) {

        if (!$this->isAuthorized('DidnumbersEdit')) {    
      	  $this->Session->setFlash(__('You are not authorized to edit this number'), 'flash_jsonbad');
      		$this->render('/Elements/json_result');
      		return;
        }	
        
  		  if (isset($this->request->data['Misc']['calltime'])) {
  		    $this->request->data['DidNumber']['calls_timing'] = implode(',', $this->request->data['Misc']['calltime']);
  		  }           		  
        // get current DID settings before replacing with new settings so that we can log changes
        $old = $this->DidNumber->recursive = 0;
        $old = $this->DidNumber->findById($did_id);

  		  if (strtolower($this->request->data['DidNumber']['industry']) == 'other' && trim($this->request->data['Misc']['industry_other'])) 
  		    $this->request->data['DidNumber']['industry'] = trim($this->request->data['Misc']['industry_other']);

        if (isset($this->request->data['Misc']['answerphrase']) && trim($this->request->data['Misc']['answerphrase'])) $this->request->data['DidNumber']['answerphrase'] = trim($this->request->data['Misc']['answerphrase']);

  	    if (isset($this->request->data['Misc']['hours'])) {
    	    if ($this->request->data['Misc']['hours'] == 'keep') unset($this->request->data['DidNumber']['hours']);
    	    else if ($this->request->data['Misc']['hours'] == '24/7') $this->request->data['DidNumber']['hours'] = '24/7';
          else if ($this->request->data['Misc']['hours'] == 'custom') {
            $hours_array = array();
  					for ($i = 1; $i<= 3; $i++) {
    					if (isset($this->request->data['Misc']['days'. $i])) {
    						$first = true;
    						$min_day = 0;
    						$max_day = 0;
    						
    						$prev_day = 0;
    						$cnt = 1;
    						$saved = false;
   							$hours = '';
    						foreach ($this->request->data['Misc']['days' . $i] as $day => $v) {
  								if (($day == ($prev_day+1)) && $min_day != 0 ) {
  									$max_day = $day;
  								}
  								else {
  									if ($cnt == 1) {
  										$min_day = $day;
  										$max_day = $day;
  									}
  									else {
  										if ($first) {
  											$first = false;
  											$delim = '';
  										}
  										else $delim = ', ';
  										if ($max_day != $min_day) {
  											$hours .= $delim . $this->days[$min_day] . '-' . $this->days[$max_day];
  										}
  										else {
  											$hours .= $delim . $this->days[$min_day];
  										}
  										$min_day = $day;
  										$max_day = $day;
  									}
  								}
  
  
    							$prev_day = $day;
    							$cnt++;
    						}
    						
  							if ($first) {
  								$first = false;
  								$delim = '';
  							}
  							else $delim = ', ';
  
  							if ($max_day != $min_day) {
  								$hours .= $delim . $this->days[$min_day] . '-' . $this->days[$max_day];
  							}
  							else {
  								$hours .= $delim . $this->days[$min_day];
  							}
  							
  							if ($this->request->data['Misc']['hours'.$i]=='Open') {
  								$hours .= ': ' . $this->request->data['Misc']['time'.$i.'_start'] . '-' . $this->request->data['Misc']['time'.$i.'_end'];
  							}
  							else {
  								$hours .= ": closed";
  							}  						
    						$hours_array[] = $hours;
    					}
  
    				}          
            $this->request->data['DidNumber']['hours'] = implode("\r\n", $hours_array);
          }
        }
        if (!empty($this->request->data['DidNumber']['description'])) {
            $description = $this->request->data['DidNumber']['description'];
        }
        else $description = '';
        if (isset($this->request->data['Misc']['consultday'])) $description .= "\r\nBest days(s) for consult: " . $this->request->data['Misc']['consultday'];
        if (isset($this->request->data['Misc']['consulttime'])) $description .= "\r\nBest time for consult: " . $this->request->data['Misc']['consulttime'];
        if (isset($this->request->data['Misc']['consultphone'])) $description .= "\r\nBest phone # for consult: " . $this->request->data['Misc']['consultphone'];
        if (!empty($description)) $this->request->data['DidNumber']['description'] = $description;
        
  			if ($this->DidNumber->save($this->request->data['DidNumber'])) {
          
          // compare old and new and record any changes
          $this->_compareEdits($did_id, $this->request->data, $old); 			  
          
          $this->Session->setFlash(__('The changes have been saved.'), 'flash_jsongood');
  		    $this->clearDidCache($did_id);		// clear the cache
  		    $this->render('/Elements/json_result');
  		    return;
  			} else {
  				$this->Session->setFlash(__('The changes could not be saved. Please, try again.'), 'flash_jsonbad');
  		    $this->render('/Elements/json_result');
  		    return;
  			}
  		}
  		else {
  		  $this->DidNumber->recursive = 0;
  	    $did = $this->DidNumber->findById($did_id);
  	    $audio = $this->CompanyAudio->findByDidId($did_id);
  	    $this->set('audio', $audio); 
        $all_numbers = $this->DidNumbersEntry->find('all', array('conditions' => array('did_id' => $did_id)));
        $this->set('all_numbers', $all_numbers);
        if ($did['DidNumber']['calls_timing']) {
          $temp = explode(',', $did['DidNumber']['calls_timing']);
          foreach ($temp as $v) {
            $did['Misc']['calltime'][$v] = $v;
          }
        }
        
        
        $keys = array_keys($this->global_options['answerphrases']);
        if (!in_array($did['DidNumber']['answerphrase'], $keys)) {
          $did['Misc']['answerphrase'] = $did['DidNumber']['answerphrase'];
          $did['DidNumber']['answerphrase'] = '';
        }
        
        
        $industries = Configure::read('options_industries');
        if (isset($industries)) {
            $this->set('industries', $industries);
            $keys = array_keys($industries);
            if (!in_array($did['DidNumber']['industry'], $keys)) {
                $did['Misc']['industry_other'] = $did['DidNumber']['industry'];
                $did['DidNumber']['industry'] = 'Other';
            }
        }
        else {
            $this->set('industries', '');
        }
        
        if ($did) {
          $this->request->data = $did;
        }
        else {
  				$this->Session->setFlash(__('Cannot find the did number, please try again later', 'flash_jsonbad'));
  		    $this->render('/Elements/json_result');
  			}
  		}
	  }
	}

  function _compareEdits($id, $new, $old) {
    $text = '';
    $changes = array();
    $changes['label'] = array();
    $changes['old_values'] = array();
    $changes['new_values'] = array();

    foreach ($new['DidNumber'] as $k=>$val) {
      $old_value = isset($old['DidNumber'][$k])? $old['DidNumber'][$k]: '';
      if ($old_value != $val) {
      	$changes['label'][] = $k;
        $changes['old_values'][] = isset($old['DidNumber'][$k])? $old['DidNumber'][$k]: '';
        $changes['new_values'][] = $val;
      	
        if (!is_array($val)) $text .= "'$k' changed from '".$old_value."' to '".$val."'\r\n";
      }
    }
    if ($text) {
      $this->_saveChanges(serialize($changes), serialize($old), serialize($new), $old['DidNumber']['account_id'], $id, 'did', 'edit');
        
    }
  }
  
	public function recover($did_id) {
	  $did = $this->DidNumber->findById($did_id);
	  if ($did) {
	    //check the number is an active did number assigned to other accounts
	    $conditions = array('did_number' => $did['DidNumber']['did_number'], 'DidNumber.deleted' => '0');
      $did_exists = $this->DidNumber->find('first', array('conditions' => $conditions));
      
	    if ($did_exists) {

        $this->Session->setFlash('This number is already assigned to another account', 'flash_jsonbad');	 		      
        $this->render('/Elements/json_result');    
        return;
	    }
	    $did['DidNumber']['deleted'] = '0';
	    $did['DidNumber']['deleted_ts'] = '0000-00-00 00:00:00';
	    $ok = $this->DidNumber->save($did['DidNumber']);
	    if ($ok) {
        $description = 'Phone Number \'' .$did['DidNumber']['did_number'] .' recovered';
        $this->_saveChanges($description, '', serialize($did), $did['DidNumber']['account_id'], $did_id, 'did', 'recover');
        
        $this->Session->setFlash(__('Phone number has been recovered'), 'flash_jsongood');	        
      }
      else {
        $this->Session->setFlash(__('Information cannot be recovered'), 'flash_jsonbad');	        
      }
	  }
    else {
      $this->Session->setFlash(__('Phone number cannot be recovered'), 'flash_jsonbad');	        
    }
    $this->render('/Elements/json_result');    
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
	  if (!$id) {
  	  $this->Session->setFlash(__('Cannot find the phone number, please try again later'), 'flash_jsonbad');
  		$this->render('/Elements/json_result');
  		return;
    }
    if (!$this->isAuthorized('DidnumbersDelete')) {    
  	  $this->Session->setFlash(__('You are not authorized to delete this number'), 'flash_jsonbad');
  		$this->render('/Elements/json_result');
  		return;
    }
	  $did = $this->DidNumber->findById($id);
	  if (!$did) {
  	  $this->Session->setFlash(__('Cannot find the phone number, please try again later'), 'flash_jsonbad');
  		$this->render('/Elements/json_result');
  		return;
    }
    
    // delete the number without actual removal from the DB.
    $data['DidNumber']['id'] = $id;
    $data['DidNumber']['deleted'] = '1';
    $data['DidNumber']['deleted_ts'] = date('Y-m-d H:i:s');
    if ($this->DidNumber->save($data['DidNumber'])) {
    	$this->Session->setFlash(__('The number was deleted'), 'flash_jsongood');

      $description = 'Deleted Phone number: '.$did['DidNumber']['did_number'];
      $this->_saveChanges($description, serialize($did), '', $did['DidNumber']['account_id'], $id, 'did', 'delete');
      
    }
    else {
  		$this->Session->setFlash(__('Cannot delete the phone number, please try again later'), 'flash_jsonbad');
    }
    $this->render('/Elements/json_result');
	}

	/*public function delete_final($id = null) {
	  if (!$id) {
  	  $this->Session->setFlash(__('Cannot find the phone number, please try again later'), 'flash_jsonbad');
  		$this->render('/Elements/json_result');
    }
    $data['DidNumber']['id'] = $id;
    $data['DidNumber']['deleted'] = '1';
    $data['DidNumber']['deleted_ts'] = "'".date('Y-m-d H:i:s')."'";
    if ($this->DidNumber->save($data['DidNumber'])) {
    	$this->Session->setFlash(__('Did number was deleted', 'flash_jsongood'));
    }
    else {
  		$this->Session->setFlash(__('Cannot delete the phone number, please try again later'), 'flash_jsonbad');
    }
    $this->render('/Elements/json_result');
	}*/
	
	
	public function store_instructions()
	{
		$instructions = $this->request->data['instructions'];
		//**** STORE passed along call instructions into local database ***///
		//****                                                     ***///
		// create a call log entry and insert a call start event for this call   
		$d['CallLog']['start_time'] = date('Y-m-d G:i:s');
		$d['CallLog']['end_time'] = '0000-00-00';
		
		// if there is not a unique id specified,then this is just a test call
		if (empty($this->request->data['event']['uniqueid'])) 
		{
			$d['CallLog']['cid_name'] = '';
			$d['CallLog']['cid_number'] = '';
			$d['CallLog']['unique_id'] = 'TESTCALL';
			$test_time = $this->request->data['event']['test_time']; // can be used to test the calltype instructions at a specified time for time-sensitive call types
			$d['CallEvent'] = array(
				array('user_id' => AuthComponent::user('id'), 'extension' => $this->user_extension, 'created' => $d['CallLog']['start_time'], 'description' => "Operator Screen store", 'level' => EVT_LVL_ADMIN)
			);
		}
		else
		{
			$d['CallLog']['cid_name'] = $this->request->data['event']['connectedlinename'];
			$d['CallLog']['cid_number'] = $this->request->data['event']['connectedlinenum'];
			$d['CallLog']['unique_id'] = $this->request->data['event']['uniqueid'];
			$d['CallEvent'] = array(
				array('user_id' => AuthComponent::user('id'), 'event_type' => EVENT_CALLSTART, 'extension' => $this->user_extension, 'created' => $d['CallLog']['start_time'], 'description' => "Call received from " . $this->request->data['event']['connectedlinenum'], 'level' => '1')
			);
		}
		// create call log id for this call and include in response to OA client
		$d['CallLog']['did_id'] = $did_id;
		$d['CallLog']['account_id'] = $instructions['did']['account_id'];
		$d['CallLog']['user_id'] = AuthComponent::user('id');
		$d['CallLog']['extension'] = $this->user_extension;
		$d['CallLog']['queue'] = $instructions['did']['queue'];
		$this->loadModel('CallLog');
		$this->CallLog->create();
		$this->CallLog->saveAssociated($d);
		$callid = $this->CallLog->getLastInsertID();
		
		$instructions['msg_id'] = '';
		$instructions['call_id'] = $callid;
		$instructions['success'] = true;
		$this->set('json', $instructions);	
	}
	
	//public instructions, returns an operator popup screen instruction list (call types) and also includes
	//a list of previously filled in prompts if this call matches a unique id for a previous call
	public function instructions($did_id, $did_number=null, $skip_call_log=null) 
	{ 
		$this->set('did_id', $did_id);
		$this->set('did_number', $did_number);
		$this->layout = 'json';
		$test_time = '';
		$schedule_id = '';
		
		// check if we need to pull up intructions for a specific time
		if (isset($this->request->data['event']['test_time'])) $test_time = $this->request->data['event']['test_time'];
		
		// fetch all information needed for a screen pop (calltypes, instructions, oncall lists, etc)
		// if schedule_id is specified, instructions will include it even if the schedule has been deleted
		if (!empty($this->request->data['event']['schedule_id'])) $instructions = $this->_instructions($did_id, $test_time, $this->request->data['event']['schedule_id']);
		else $instructions = $this->_instructions($did_id, $test_time);
		
		if ($instructions) 
		{
			$this->loadModel('CallLog');
			$existing_call = false;
		
			// if uniqueid is supplied, check if a call log entry already exists in the call log and is NOT a campaign outbound call.  If so, then most likely this is just 
			// a screen re-pop of an existing call
			if ((!empty($this->request->data['event']['uniqueid'])) && $this->request->data['event']['uniqueid'] != 'CAMPAIGN')
			{
				$conditions = array('unique_id' => $this->request->data['event']['uniqueid']);
				$existing_call = $this->CallLog->find('first', array('conditions' => $conditions));
			}
			// if call id is specified, check if a call log entry already exists in the call log, most likely screen pop from minders
			else if (!empty($this->request->data['event']['call_id']))
			{
				$conditions = array('call_id' => $this->request->data['event']['call_id']);
				$existing_call = $this->CallLog->find('first', array('conditions' => $conditions));
		    if (!empty($existing_call['Message'])) $schedule_id = $existing_call['Message']['schedule_id'];
			}			
			// create call log entry if it doesn't already exist
			if (empty($existing_call)) 
			{
				$current_calltype = '';
				// create a call log entry and insert a call start event for this call   
				$d['CallLog']['start_time'] = date('Y-m-d G:i:s');
				$d['CallLog']['end_time'] = '0000-00-00';
				if (!empty($this->request->data['queue'])) $d['CallLog']['queue'] = $this->request->data['queue'];
				if (!empty($did_number)) $d['CallLog']['did_number'] = $did_number;  

				
				// if there is not a unique id specified,then this is just a manual screen pop
				if (empty($this->request->data['event']['uniqueid'])) 
				{
					if (!$skip_call_log) {
						$d['CallLog']['cid_name'] = '';
						$d['CallLog']['cid_number'] = '';
						$d['CallLog']['unique_id'] = 'TESTCALL';
						
						 // can be used to test the calltype instructions at a specified time for time-sensitive call types
						$test_time = $this->request->data['event']['test_time'];
						$d['CallEvent'] = array(
							array(
								'user_id' => AuthComponent::user('id'),
								'extension' => $this->user_extension,
								'created' => $d['CallLog']['start_time'],
								'description' => "Operator Screen open",
								'level' => EVT_LVL_ADMIN
								)
							);
						}
						
				}
				// check if this an oubound dialer call
				else if (!empty($this->request->data['event']['uniqueid'] ) && $this->request->data['event']['uniqueid'] == 'CAMPAIGN') {
					if (Configure::read('outbound_dialer_enabled')) 
					{
						$this->loadModel('OutboundDialer.Contact');
						$this->loadModel('OutboundDialer.Call');
						$contact = $this->Contact->findById($this->request->data['event']['contact_id']);
						$d['CallLog']['cid_name'] = '';
						$d['CallLog']['cid_number'] = '';
						$d['CallLog']['unique_id'] = 'CAMPAIGN';
						$current_calltype =  $this->request->data['event']['calltype'];
						$test_time = $this->request->data['event']['test_time']; // can be used to test the calltype instructions at a specified time for time-sensitive call types
						$this->CallLog->bindModel(array
						(
							'hasMany' => array
							(
								'Call' => array
								(
									'className' => 'OutboundDialer.Call',
									'foreignKey' => 'call_id'
								)
							)
						));
						$d['CallEvent'] = array(
							array('user_id' => AuthComponent::user('id'), 'extension' => $this->user_extension, 'created' => $d['CallLog']['start_time'], 'description' => ("Dialed " . $contact['Contact']['name'] . ' at ' .  $contact['Contact']['phone']), 'level' => EVT_LVL_ADMIN),
							array('user_id' => AuthComponent::user('id'), 'extension' => $this->user_extension, 'created' => $d['CallLog']['start_time'], 'description' => "Outbound Operator Screen open", 'level' => EVT_LVL_ADMIN)
						);
						$d['Call'] = array
						(
							array('contact_id' => $this->request->data['event']['contact_id'])
						);
						$entered_prompts = array(PROMPT_NAME => $contact['Contact']['name'], PROMPT_PHONE => $contact['Contact']['phone']);
					}
				}
				else {
					$d['CallLog']['cid_name'] = $this->request->data['event']['connectedlinename'];
					$d['CallLog']['cid_number'] = $this->request->data['event']['connectedlinenum'];
					$d['CallLog']['unique_id'] = $this->request->data['event']['uniqueid'];
					
					// record the channel variable, sip_call_id
          if (isset($this->request->data['event']['variable']['SIPCALLID'])) $d['CallLog']['sip_call_id'] = $this->request->data['event']['variable']['SIPCALLID'];
					$d['CallEvent'] = array(
						array(
							'user_id' => AuthComponent::user('id'),
							'event_type' => EVENT_CALLSTART,
							'extension' => $this->user_extension,
							'created' => $d['CallLog']['start_time'],
							'description' => "Call received from " . $this->request->data['event']['connectedlinenum'], 'level' => '1')
						);
				}
				
				if (!$skip_call_log) {
					// create call log id for this call and include in response to OA client				  
					$d['CallLog']['did_id'] = $did_id;
					$d['CallLog']['account_id'] = $instructions['did']['account_id'];
					$d['CallLog']['user_id'] = AuthComponent::user('id');
					$d['CallLog']['extension'] = $this->user_extension;
					if (isset($this->request->data['event']['queue'])) $d['CallLog']['queue'] = $this->request->data['event']['queue'];
					$this->CallLog->create();
					$this->CallLog->saveAssociated($d);
					$callid = $this->CallLog->getLastInsertID();
				}
				else $callid = 0;
				
				$instructions['msg_id'] = '';
				$entered_prompts = array();
			}
			else 
			{ //call log already existed for this unique id
				// pull db entry id of existing call
				$callid = $existing_call['CallLog']['id'];
				if (!empty($existing_call['Message']['id'])) $instructions['msg_id'] =  $existing_call['Message']['id'];
				else $instructions['msg_id'] = '';
				// add call event to note that operator screen was re-popped
				$d['CallEvent'] =  array('call_id' => $callid, 'user_id' => AuthComponent::user('id'), 'event_type' => EVENT_REPOP, 'extension' => $this->user_extension, 'created' => date('Y-m-d H:i:s'), 'description' => "Operator screen re-popped", 'level' => '1');
				$this->CallLog->CallEvent->create();
				$this->CallLog->CallEvent->save($d['CallEvent']);
				
				// check if a message already exists.  If it doesn't grab prompts from operator inputs so far
				if (empty($existing_call['Message']['id'])) {
  				$events = $existing_call['CallEvent'];
  				
  				// sort events in descending chronological  order, this way we can 'replay' them on the operator screen
  				// in the same order that they originally occurred.
  				usort($events, function($a, $b) 
  				{
  					return $b['id'] - $a['id'];
  				});
  				// starting from most recent events, grab the calltype and prompts
				$entered_prompts = array();
				$current_calltype = '';
				foreach ($events as $k => $e) 
				{
					$event_type = $e['event_type'];
					if ($event_type == EVENT_CALLTYPE && $current_calltype == '') 
					{
						$current_calltype = str_replace('[Calltype] ', '', $e['description']);
					}
					if ($event_type == EVENT_FILL_PROMPT) 
					{
						$temp = str_replace('[PROMPT] ', '', $e['description']);
						$temp2 = split(':', $temp, 2);
						if (!empty($temp2[0]) && !isset($entered_prompts[trim($temp2[0])]))  $entered_prompts[trim($temp2[0])] = trim($temp2[1]);
					}
				}
  			}
  			// grab prompts saved on the message
  			else {
  			  $this->loadModel('MessagesPrompt');
  			  $data = $this->MessagesPrompt->find('all', array('conditions' => array('message_id' => $existing_call['Message']['id']), 'sort' => array('MessagesPrompt.action_num' => 'asc', 'MessagesPrompt.sort' => 'asc'))); 
  				$entered_prompts = array();
  				foreach ($data as $k => $d) {
					
  					$entered_prompts[$d['MessagesPrompt']['caption']] = $d['MessagesPrompt']['value'];
  				}
  			  
  			}
			}
			$instructions['call_id'] = $callid;
			$instructions['schedule_id'] = $schedule_id;
			$instructions['success'] = true;
			$instructions['current_calltype'] = $current_calltype;
			$instructions['entered_prompts'] = $entered_prompts;
		}
		else
		{ //no instructions were located in the database for this DID and time
			$instructions['success'] = false;
		}
		// return call entry along with the id of the call log entry just created
		$this->set('json', $instructions);
	}

  // search function used in autocomplete on the client input fields		
	public function search() {
		$this->layout = 'plain';
		$search = mysql_escape_string(strip_tags(trim($this->request->query['term'])));
		$query = "select account_name, account_num, id from ".OA_TBL_PREFIX."clients where account_num like '%$search%' or account_name like '%$search%'";
		$res = $this->DidNumber->query($query);
		$this->set('rows', $res);
	}
	
	public function clearDBData() {
	  $this->DidNumber->query("delete from ".OA_TBL_PREFIX."call_logs");
	  $this->DidNumber->query("delete from ".OA_TBL_PREFIX."call_events");
	  $this->DidNumber->query("delete from ".OA_TBL_PREFIX."complaints");
	  $this->DidNumber->query("delete from ".OA_TBL_PREFIX."messages");
	  $this->DidNumber->query("delete from ".OA_TBL_PREFIX."messages_delivery");
	  $this->DidNumber->query("delete from ".OA_TBL_PREFIX."messages_prompts");
	  $this->DidNumber->query("delete from ".OA_TBL_PREFIX."messages_prompts_edits");
	  $this->DidNumber->query("delete from ".OA_TBL_PREFIX."accounts_edits");
	  
	  echo 'done';exit;
	}	
	
	public function summary($id) {
	  if (!$id) {
  	  $this->Session->setFlash(__('Cannot find the phone number, please try again later'), 'flash_jsonbad');
  		$this->render('/Elements/json_result');
  		return;
    }
    $this->DidNumber->recursive = 0;
    $this->request->data = $this->DidNumber->findById($id);
        
        // load all employees associated with this sub-account
    $this->loadModel('Employee');
    $this->Employee->unbindModel(
        array('hasMany' => array('EmployeesEdit'))
    );
    $conditions = array('did_id' => $id, 'deleted' => '0');
    $data_employees = $this->Employee->find('all', array('conditions' => $conditions)); 
    $employees = array();
    $contacts = array();

    foreach ($data_employees as $k => $e) {
      $employees[$e['Employee']['id']] = $e;
      foreach ($e['EmployeesContact'] as $c) {
        $contacts[$c['id']] = $c;
      }
    }
    $this->set('employees', $employees);
    $this->set('contacts', $contacts);

        // load all calltypes for this sub-account
    $this->loadModel('Calltype');
    $this->Calltype->unbindModel(
        array('belongsTo' => array('DidNumber'))
    );
    $conditions = array('did_id' => $id, 'deleted' => '0');
    $this->Calltype->recursive = '3';
    $data_calltypes = $this->Calltype->find('all', array('conditions' => $conditions)); 
		foreach ($data_calltypes as $k => $calltype) {
			foreach ($calltype['Schedule'] as $j => $s) {
			  if ($s['active'] == '0') unset($data_calltypes[$k]['Schedule'][$j]);
				else $data_calltypes[$k]['Schedule'][$j]['schedule'] = $this->_getSchedule($s, $this->php_daysofweek);
			}
		}
    $this->set('calltypes', $data_calltypes);
    $sections = array();
        // load section definitions for all active schedules
        $sql = "select s.* from ".OA_TBL_PREFIX."sections s left join ".OA_TBL_PREFIX."schedules sc on sc.id=s.schedule_id where sc.deleted='0' and sc.active='1' and sc.did_id='$id'";
        $res = $this->DidNumber->query($sql, false);
        foreach($res as $k => &$row) {
        	$row['s']['visible'] = 0;
        	if (!isset($sections[$row['s']['schedule_id']])) $sections[$row['s']['schedule_id']] = array();
            $sections[$row['s']['schedule_id']][$row['s']['sort']] = $row['s'];
        }
        $this->set('sections', $sections);
        
        // load list of SMS email gateways
    $this->loadModel('SmsCarrier');
    $carriers = $this->SmsCarrier->find('list', array('fields' => array('id', 'name')));
    $this->set('carriers', $carriers);
    
        // load all active on-call lists
    $this->loadModel('CallList');
    $this->CallList->recursive = 1;    
    $conditions = array('did_id' => $id, 'deleted' => '0');
    $data_oncall = $this->CallList->find('all',  array('conditions' => $conditions)); 
    $oncall = array();
    foreach ($data_oncall as $k => $o) {
			foreach ($o['CallListsSchedule'] as $j => $s) {
			  if ($s['active'] == '0') unset($data_oncall[$k]['CallListsSchedule'][$j]);
				else $data_oncall[$k]['CallListsSchedule'][$j]['schedule'] = $this->_getSchedule($s, $this->php_daysofweek);
			}
      $oncall[$o['CallList']['id']] = $data_oncall[$k];
    }
    $this->set('oncall', $oncall);
    
        // load all active message summary schedules
    $this->loadModel('MessagesSummary');
    $this->MessagesSummary->recursive = 0;    
    $conditions = array('did_id' => $id, 'deleted' => '0', 'active' => '1');
    $data = $this->MessagesSummary->find('all',  array('conditions' => $conditions)); 
	  
	  $client_timezone = $this->request->data['DidNumber']['timezone'];		
    if (!$client_timezone) $client_timezone = Configure::read('default_timezone');
    $oa_timezone = Configure::read('default_timezone');
        
		foreach($data as $k => $v) {
			$v['days'] = 
		  $data[$k]['MessagesSummary']['day_range'] = $this->_getDayRanges($v['MessagesSummary'], $this->php_daysofweek);
		  if (!$data[$k]['MessagesSummary']['all_day']) {
		    if ($oa_timezone != $client_timezone) {
		      if ($data[$k]['MessagesSummary']['send_time']) {
            $date1 = new DateTime("2014-01-1 " . $data[$k]['MessagesSummary']['send_time']);
            $data[$k]['MessagesSummary']['send_time_f'] = $date1->format('g:i a');
		      }
		      if ($data[$k]['MessagesSummary']['start_time']) {
            $date1 = new DateTime("2014-01-1 " . $data[$k]['MessagesSummary']['start_time']);
            $data[$k]['MessagesSummary']['start_time_f'] = $date1->format('g:i a'); 
		      }		      
		      if ($data[$k]['MessagesSummary']['end_time']) {
            $date1 = new DateTime("2014-01-1 " . $data[$k]['MessagesSummary']['end_time']);
            $data[$k]['MessagesSummary']['end_time_f'] = $date1->format('g:i a'); 
		      }		      
		    }
		  }
		}    
    $this->set('summaries', $data);    
    
        // load all active calendars if the calendar module is enabled
    $calendars = array();
    if (Configure::read('calendar_enabled')) {
      $this->loadModel('Scheduling.EaService');
      $conditions = array('did_id' => $id, 'deleted' => 0);
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
    $this->set('calendars', $calendars);
	}
	
	public function email_summary($id) {
	  $this->set('emailed', true);
	  $email = $this->request->data['email'];
	  //$view = new View($this, false);
	  $this->summary($id);
	  $view_output = $this->render('summary'); 
	  if ($this->_sendemail('Account Summary', $view_output, $email, $format='html', '', 'account_summary')) {
	    
	    $this->set('success', true);
	  }
	  else $this->set('success', false);
    $this->set('email',  $email);	  
    $this->set('id', $id);
	  $this->render('email_summary');
	  
	}
	
	// Retrieve the most recent sub-accounts added to the specified difficulty levels
	// 
	// subaccount_difficulty - array of difficulties to fetch
	public function recent($difficulty_levels = '') {
	  $limit = 40;
	  $conditions = array('DidNumber.status' => '1', 'DidNumber.deleted' => 0);
	  $this->DidNumber->unbindModel(
        array('hasMany' => array('DidFile', 'Employee', 'Calltype', 'DidNumbersEdit', 'DidNumbersEntry'))
    );
    $data = $this->DidNumber->find('all', array('fields' => array('DidNumber.id', 'DidNumber.difficulty', 'DidNumber.deleted', 'DidNumber.company', 'Account.account_num', "DATE_FORMAT(DidNumber.date_entered, '%c/%d/%y %l:%i %p') as created", 'Account.deleted'), 'conditions' => $conditions, 'limit' => $limit, 'order' => array('DidNumber.date_entered' => 'desc')));
	  $this->set('data', $data);
	}
}
