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
 * Calltypes Controller
 *
 * @property Calltype $Calltype
 */
class CalltypesController extends AppController {
    public $layout;

    public function index($id) {
        
        $this->Calltype->recursive = 0;
        // Let's remove the hasMany...
    
        $this->set('Calltypes', $this->Calltype->find('all', array('conditions' => array('account_id' => $id))));
    }

    public function description($id) {
        $ct = $this->Calltype->findById($id);
        $old_description = $ct['Calltype']['description'];
        $old_title = $ct['Calltype']['title'];
        if ($ct) {
            $data['id'] = $id;
            $temp = strip_tags($this->request->data['desc']);
            if (trim($temp) == '') $data['description'] = '';
            else $data['description'] = $this->request->data['desc'];
            
            $temp = strip_tags($this->request->data['title']);
            if (trim($temp) == '') $data['title'] = '';
            else $data['title'] = $this->request->data['title'];
            
            if ($data['description'] != $old_description || $data['title'] != $old_title) {  
                if ($this->Calltype->save($data)) {
                    $this->clearDidCache($ct['Calltype']['did_id']);        

                    // log changes
                    $old = "Title:".$old_title." \nDescription:".$old_description;
                    $new = "Title:".$data['title']." \nDescription:".$data['description'];
                    $description = "Calltype title and description - , changed from <i>".$old."</i> to <i>".$new."</i>";
                    $e['calltype_id'] = $id;
                    $this->_saveChanges($description, serialize($old), serialize($new), $ct['Calltype']['account_id'],$ct['Calltype']['did_id'], 'calltype', 'edit', $e);     
        
                    $this->Session->setFlash(__('Your changes have been saved.'), 'flash_jsongood');
                }
                else {
                    $this->Session->setFlash(__('Your changes could not be saved. Please, try again later.'), 'flash_jsonbad');
                }
            }
            else {
                    $this->Session->setFlash(__('No changes were detected.'), 'flash_jsongood');
            }
        }
        else {
            $this->Session->setFlash(__('Your changes could not be saved. Please, try again later.'), 'flash_jsonbad');
        }
        $this->render('/Elements/json_result');      
    }

    public function view($did_id = null) {

        $this->set('did_id', $did_id);
        $this->loadModel('DidNumber');
        $this->DidNumber->recursive = 0;
        $did = $this->DidNumber->findById($did_id);
        $this->set('timezone', $this->global_options['timezone'][$did['DidNumber']['timezone']]);       

        $res = $this->Calltype->find('all', array('fields' => array('Calltype.*'), 'recursive' => '1', 'order' => array('Calltype.sort' => 'asc', 'title' => 'asc'), 'conditions' => array('Calltype.deleted' => '0', 'Calltype.did_id' => $did_id)));
        foreach ($res as $k => $calltype) {
            foreach ($calltype['Schedule'] as $j => $s) {
                $res[$k]['Schedule'][$j]['schedule'] = $this->_getSchedule($s, $this->php_daysofweek);
            }
        }
        $this->set('Calltypes', $res);

    }

    



/**
 * add method
 *
 * @return void
 */
    public function add($did_id, $calltype_id = null) {
        App::import('Model', 'DidNumber');    
        $add_schedule = false;
        $this->set('calltype_id', $calltype_id);
        $thisDid = new DidNumber();
        $thisDid->unbindModel(
                array('hasMany' => array('DidFile', 'Employee', 'DidNumbersEdit', 'DidNumbersEntry', 'Calltype'))
        );    
        
        // find account id
        $thisDid->id = $did_id;
        $account_id = $thisDid->field('account_id');
     
        $this->set('did_id', $did_id);
        $this->set('account_id', $account_id);

        if ($this->request->is('post')) {
            if (empty($this->request->data['Calltype']['id'])) {
                if (trim($this->request->data['Misc']['title_custom'])) {
                    $this->request->data['Calltype']['title'] = trim($this->request->data['Misc']['title_custom']);
                }
                              
                $res = $this->Calltype->find('all', array('fields' => array('Calltype.*'), 'recursive' => '0', 'order' => array('Calltype.sort' => 'asc', 'title' => 'asc'), 'conditions' => array('Calltype.deleted' => '0', 'Calltype.did_id' => $did_id)));
                $max_sort = 0;
                $calltype_found = false;
                foreach($res as $c) {
                    if ($c['Calltype']['sort'] > $max_sort) $max_sort = $c['Calltype']['sort'];
                    // check if calltype is a duplicate
                    if ($c['Calltype']['title'] == $this->request->data['Calltype']['title']) {
                        $calltype_found = true; // flag calltype as already existing
                        $this->request->data['Calltype']['id'] = $calltype_id = $c['Calltype']['id'];
                        $this->Session->setFlash(__('You already have this calltype'), 'flash_jsonbad');
                        $this->render('/Elements/json_result'); 
                        return;
                    }
                }
                $this->request->data['Calltype']['sort'] = $max_sort + 1; // set the new calltype to be the last in the sort order
                
                $this->Calltype->create();


                $calltype_created = $this->Calltype->save();
                $this->request->data['Calltype']['id'] = $this->Calltype->getLastInsertId();
                
            }
            else {
                $add_schedule = true;
                $calltype_found = true;
                $calltype_id = $this->request->data['Calltype']['id'];
            }
        
                // if specific days of the week are selected, then flag this schedule as such
            if ($this->request->data['Misc']['timesensitive']) {
                $this->_tschecks();
                $this->request->data['Schedule']['active'] = '0';
                $this->request->data['Schedule']['did_id'] = $did_id;   
                $this->request->data['Schedule']['account_id'] = $account_id;   
                $this->request->data['Schedule'] = array($this->request->data['Schedule']);  
                    
            }
            else {
                $this->request->data['Schedule'] = array(
                    array('account_id' => $account_id, 'did_id' => $did_id, 'active' => '0')
                );

            }
            //check for time overlap between new schedule and existing one
            if ($calltype_found) {
                // find all schedule to see if any conflicts with the new one being created
                $conditions = array('calltype_id' => $calltype_id, 'deleted' => '0');

                $default_found = false;
                $schedules = $this->Calltype->Schedule->find('all', array('recursive' => 0, 'conditions' => $conditions));
                foreach($schedules as $s) {
                    if ($this->request->data['Misc']['timesensitive']) {
                        // check if days are specified
                        //if ($this->request->data['Schedule']['check_days'] == '') {
                        if (1) {
                            if ((!empty($this->request->data['Schedule'][0]['mon']) && $s['Schedule']['mon']) || (!empty($this->request->data['Schedule'][0]['tue']) && $s['Schedule']['tue']) || (!empty($this->request->data['Schedule'][0]['wed']) && $s['Schedule']['wed']) || (!empty($this->request->data['Schedule'][0]['thu']) && $s['Schedule']['thu']) || (!empty($this->request->data['Schedule'][0]['fri']) && $s['Schedule']['fri']) || (!empty($this->request->data['Schedule'][0]['sat']) && $s['Schedule']['sat'])|| (!empty($this->request->data['Schedule'][0]['sun']) && $s['Schedule']['sun'])) {
                                // day overlap, need to check for time overlap too
                                if (empty($this->request->data['Schedule'][0]['start_time']) || empty($s['Schedule']['start_time'])) {
                                    // schedule is valid all day, will definitely conflict
                                    $this->set('new_id', '');
                                    $this->set('msg', "This calltype overlaps with an existing calltype instruction");
                                    $this->set('success', 'false');
                                    $this->render('add_result');
                                    return;
                                }
                                else {
                                    if (!($this->request->data['Schedule'][0]['start_time'] >= $s['Schedule']['end_time'] || $this->request->data['Schedule'][0]['end_time'] <= $s['Schedule']['start_time'])) {
                                        $this->set('new_id', '');
                                        $this->set('msg', "This calltype overlaps with an existing calltype instruction");
                                        $this->set('success', 'false');
                                        $this->render('add_result');
                                        return;
                                    }
                                    
                                }
                            }
                            //else fb('no time check');
                        }
                        // check if date range is specified
                        if (!empty($this->request->data['Schedule'][0]['start_date']) && !empty($s['Schedule']['start_date'])) {
                                if (!($this->request->data['Schedule'][0]['start_date'] >= $s['Schedule']['end_date'] || $this->request->data['Schedule'][0]['end_date'] <= $s['Schedule']['start_date'])) {
                                    $this->set('new_id', '');
                                    $this->set('msg', "This calltype overlaps with an existing calltype instruction");
                                    $this->set('success', 'false');
                                    $this->render('add_result');
                                    return;                    
                                }
                        }
                        if (!empty($this->request->data['Schedule'][0]['start_day']) && !empty($s['Schedule']['start_day'])) {
                                if (!($this->request->data['Schedule'][0]['start_day'] >= $s['Schedule']['end_day'] || $this->request->data['Schedule'][0]['end_day'] <= $s['Schedule']['start_day'])) {
                                    $this->set('new_id', '');
                                    $this->set('msg', "This calltype overlaps with an existing calltype instruction");
                                    $this->set('success', 'false');
                                    $this->render('add_result');
                                    return;                    
                                }
                                if (!(($this->request->data['Schedule'][0]['start_day']+70000) >= $s['Schedule']['end_day'] || ($this->request->data['Schedule'][0]['end_day'] + 70000)<= $s['Schedule']['start_day'])) {
                                    $this->set('new_id', '');
                                    $this->set('msg', "This calltype overlaps with an existing calltype instruction");
                                    $this->set('success', 'false');
                                    $this->render('add_result');
                                    return;                    
                                }
                        }            
                    }
                    else {
                        if (!$s['Schedule']['check_days'] && empty($s['Schedule']['start_date'])&& empty($s['Schedule']['start_day'])) {
                            $this->set('new_id', '');
                            $this->set('msg', "This calltype already has a set of default calltype instructions, you cannot create another one");
                            $this->set('success', 'false');
                            $this->render('add_result');
                            return;
                        }
//            else fb('no overlap');
                    }
                }
            }
            else {
                $calltype_exists = false;
            }         
            // create default actions for the new calltype schedule - gather username and phone number
            $this->request->data['Schedule'][0]['Action'] = array(
                array(
                    'sort' => '1', 'action_text' => 'Gather User Information', 'action_type' => ACTION_PROMPTS, 'eid' => '', 'did_id' => $did_id, 'account_id' => $account_id, 
                    'Prompt' => array(
                        array('ptype' => '2', 'caption' => 'First and Last Name', 'sort' => '1', 'maxchar' => '255', 'required' => '1', 'verification' => 0),
                        array('ptype' => '2', 'caption' => 'Phone Number', 'sort' => '2', 'maxchar' => '255', 'required' => '1', 'verification' => 1)
                    )
                )
            );
            $save_ok = $this->Calltype->saveAssociated($this->request->data, array('deep' => true));        
            if ($save_ok) {
                
                $new_id = $this->Calltype->Schedule->id;                
                $schedule = $this->_getSchedule($this->request->data['Schedule'][0], $this->php_daysofweek, $this->request->data['Misc']['timesensitive']);
                $e['schedule_id'] = $new_id;
                if ($add_schedule == true) {
                    $description = 'New Schedule added - '  . $schedule;
                    $msg = 'A new schedule has been added ('  . $schedule . '), please specify the call handling instructions for this new schedule';
                }
                else {
                    $description = 'Calltype created - ' . $this->request->data['Calltype']['title'] . ' - ' . $schedule;
                    $msg = 'Your new calltype was created, please specify the call handling instructions for this new calltype';
                }
                $this->_saveChanges($description, '', serialize($this->request->data), $account_id, $did_id, 'calltype', 'add', $e);
                                    
                $this->set('new_id', $new_id);
                $this->set('msg', $msg);
                $this->set('success', 'true');
                $this->clearDidCache($did_id);      
            } else {
                $this->set('new_id', '');
                $this->set('msg', 'The calltype could not be saved. Please, try again.');
                $this->set('success', 'false');
            }
            $this->render('add_result');
            
        }
        else {
            if ($calltype_id) {
                $this->Calltype->recursive = 0;
                $this->set('calltype', $this->Calltype->findById($calltype_id));
            }
            else {
                $this->set('calltype', false);
            }
        }
            $options = array();
            foreach ($this->global_options['calltypes'] as $k => $val) {
                $options[$val['caption']] = $val['description'];
            }
            $this->set('calltype_options', $options);

    }
    
    
    function _tschecks() {
        if ($this->request->data['Schedule']['mon'] || $this->request->data['Schedule']['tue']|| $this->request->data['Schedule']['wed']|| $this->request->data['Schedule']['thu']|| $this->request->data['Schedule']['fri']|| $this->request->data['Schedule']['sat']|| $this->request->data['Schedule']['sun']) {
            $this->request->data['Schedule']['check_days'] ='1';
            if (isset($this->request->data['CallListsSchedule']['start_time'])) {
                $ts1 = strtotime("today " . $this->request->data['Schedule']['start_time']);
                $ts2 = strtotime("today " . $this->request->data['Schedule']['end_time']);
                if ($ts2 < $ts1) {
                    $this->Session->setFlash(__('Please specify a time range that does not cross over midnight'), 'flash_jsonbad');
                    $this->render('/Elements/json_result');
                    return;                         
                }
            }                   
        }
        else $this->request->data['Schedule']['check_days'] = 0;
        
        if (trim($this->request->data['Misc']['day_time_start'])) {         
            $ts = strtotime("today " . $this->request->data['Misc']['day_time_start']);
            $mytime = date('H:i', $ts);                   
            $this->request->data['Misc']['day_time_start'] = $mytime;   
        }
        
        if (trim($this->request->data['Misc']['day_time_end'])) {           
            $ts = strtotime("today " . $this->request->data['Misc']['day_time_end']);
            $mytime = date('H:i', $ts);                   
            $this->request->data['Misc']['day_time_end'] = $mytime; 
        }
        else $this->request->data['Misc']['day_time_end'] = '23:59';

        // take into account day ranges that cross over to the next week 
        if ($this->request->data['Schedule']['start_day'] && $this->request->data['Schedule']['end_day']) {
            if ($this->request->data['Schedule']['start_day'] > $this->request->data['Schedule']['end_day']) {
                $this->request->data['Schedule']['end_day'] += 7;
            }
            $this->request->data['Schedule']['start_day'] = $this->request->data['Schedule']['start_day'] . str_replace(':', '', $this->request->data['Misc']['day_time_start']);
            $this->request->data['Schedule']['end_day'] = $this->request->data['Schedule']['end_day'] . str_replace(':', '', $this->request->data['Misc']['day_time_end']);
        }

        if (trim($this->request->data['Schedule']['start_time'])) {         
            $ts = strtotime("today " . $this->request->data['Schedule']['start_time']);
            $mytime = date('H:i:s', $ts);                 
            $this->request->data['Schedule']['start_time'] = $mytime;   
        }
        else if ($this->request->data['Schedule']['check_days'] == '1') {
            $this->request->data['Schedule']['start_time'] = '00:00:00';
        }        
        if (trim($this->request->data['Schedule']['end_time'])) {           
            $ts = strtotime("today " . $this->request->data['Schedule']['end_time']);
            $mytime = date('H:i:s', $ts);                 
            $this->request->data['Schedule']['end_time'] = $mytime; 
        }
        else if ($this->request->data['Schedule']['check_days'] == '1') {
            $this->request->data['Schedule']['end_time'] = '23:59:59';
        }
                        
        if (trim($this->request->data['Misc']['date_time_start'])) {            
            $ts = strtotime("today " . $this->request->data['Misc']['date_time_start']);
            $mytime = date('H:i:s', $ts);                 
            $this->request->data['Misc']['date_time_start'] = $mytime;  
        }
        
        if (trim($this->request->data['Misc']['date_time_end'])) {          
            $ts = strtotime("today " . $this->request->data['Misc']['date_time_end']);
            $mytime = date('H:i:s', $ts);                 
            $this->request->data['Misc']['date_time_end'] = $mytime;    
        }


        if (trim($this->request->data['Schedule']['start_date']) && trim($this->request->data['Schedule']['end_date'])) {
            if ($this->request->data['Misc']['date_time_start']) $this->request->data['Schedule']['start_date'] = $this->request->data['Schedule']['start_date'] . " " . $this->request->data['Misc']['date_time_start'];
            else $this->request->data['Schedule']['start_date'] = $this->request->data['Schedule']['start_date'] . " " . '00:00:00';
            if ($this->request->data['Misc']['date_time_end']) $this->request->data['Schedule']['end_date'] = $this->request->data['Schedule']['end_date'] . " " . $this->request->data['Misc']['date_time_end'];   
            else $this->request->data['Schedule']['end_date'] = $this->request->data['Schedule']['end_date'] . " " . '23:59:59';    
        }   
    }
    
/**
 * template method
 *
 * Handles copying calltypes that are marked as templates from one account to another
 *
 *
 * @return void
 */
    public function template($did_id, $calltype_id = null) {
        //make sure the models we need for this are loaded
        $this->loadModel('Schedule');
        $this->loadModel('DidNumber');
        $this->loadModel('CallType');
        $this->loadModel('Action');
        $this->loadModel('Prompt');
        $this->loadModel('Section');
        //look for and load the subaccount specified to make sure it exists.
        $did = $this->DidNumber->findById($did_id);
        if (!$did) {
            //we didn't find the account specified, fail with a message to the user.
            $this->Session->setFlash(__('The Account referenced does not exist, try again later'), 'flash_jsonbad');
            $this->render('/Elements/json_result');
            return;
        }
        //pull the account id
        $destination_account_id = $did['DidNumber']['account_id'];
        $destination_did_id = $did['DidNumber']['id'];
        $this->set('account_id', $destination_account_id);
        if ($this->request->is('post')) {
            if (empty($this->request->data['Calltypes'])) {
                $this->Session->setFlash(__('No Templates Added'), 'flash_jsonbad');
                $this->render('/Elements/json_result'); 
                return;
            }
            foreach ($this->request->data['Calltypes'] as $source_calltype_id => $value) {
                $calltype = $this->Calltype->find('first',array(  'recursive' => -1,'order' => array('Calltype.sort' => 'asc','title' => 'asc'),'conditions' => array('Calltype.id' => $source_calltype_id,'Calltype.deleted' => '0')));
                $calltype['Calltype']['id'] = null;
                $calltype['Calltype']['account_id'] = $destination_account_id;
                $calltype['Calltype']['did_id'] = $destination_did_id;
                $calltype['Calltype']['template'] = '0'; //set template to off, so the destination account doesn't become a template as well.
                $this->Calltype->save($calltype);
                $destination_calltype_id = $this->Calltype->id;
                $this->loadModel('Schedule');
                $schedules = $this->Schedule->find('all',array('recursive' => -1,'conditions' => array('Schedule.calltype_id' => $source_calltype_id,'Schedule.deleted' => '0')));
                foreach ($schedules as $schedule) {
                    $source_schedule_id = $schedule['Schedule']['id'];
                    $schedule['Schedule']['id'] = null;
                    $schedule['Schedule']['account_id'] = $destination_account_id;
                    $schedule['Schedule']['did_id'] = $destination_did_id;
                    $schedule['Schedule']['calltype_id'] = $destination_calltype_id;
                    $this->Schedule->save($schedule);
                    $destination_schedule_id = $this->Schedule->id;
                    $sections = $this->Section->find('all',array('recursive' => -1,'conditions' => array('schedule_id' => $source_schedule_id)));
                    foreach ($sections as $s) {
                        $s['Section']['id'] = null;
                        $s['Section']['schedule_id'] = $destination_schedule_id;
                        $this->Section->create();
                        $this->Section->save($s);
                    }
                    
                    
                    $actions = $this->Action->find('all',array('recursive' => -1,'conditions' => array('Action.schedule_id' => $source_schedule_id)));
                    foreach ($actions as $action) {
                        $source_action_id = $action['Action']['id'];
                        $action['Action']['id'] = null;
                        $action['Action']['did_id'] = $destination_did_id;
                        $action['Action']['schedule_id'] = $destination_schedule_id;
                        $this->Action->save($action);
                        $destination_action_id = $this->Action->id;
                        $prompts = $this->Prompt->find('all',array('recursive' => -1,'conditions' => array('Prompt.action_id' => $source_action_id)));
                        foreach($prompts as $prompt) {
                            $source_prompt_id = $prompt['Prompt']['id'];
                            $prompt['Prompt']['id'] = null;
                            $prompt['Prompt']['did_id'] = $destination_did_id;
                            $prompt['Prompt']['action_id'] = $destination_action_id;
                            $this->Prompt->save($prompt);
                            $destination_prompt_id = $this->Prompt->id;
                            
                        }
                    }
                }
                
            }
            //$this->_saveChanges($description, '', serialize($data), $did['DidNumber']['account_id'], $did_id, 'calltype', 'add', $e);
            $msg='The templates have been imported, you may now rename them or modify them';
            $this->set('msg', $msg);
            $this->set('new_id', '0');
            $this->set('success', 'true');
            $this->clearDidCache($did_id);
            $this->render('add_result');
        }
        else {
                $joins = array(
                  array(
                    'table' => OA_TBL_PREFIX . 'did_numbers',
                    'alias' => 'DidNumber',
                    'type' => 'LEFT',
                    'conditions' => array('`DidNumber`.`id` = `Calltype`.`did_id`')
                  ),
                  array(
                    'table' => OA_TBL_PREFIX . 'accounts',
                    'alias' => 'Account',
                    'type' => 'LEFT',
                    'conditions' => array('`DidNumber`.`account_id` = `Account`.`id`')
                  )
                  
                );          
            $res = $this->Calltype->find('all', array('fields' => array('Calltype.*', 'Account.account_num', 'DidNumber.company'), 'recursive' => '-1', 'joins' => $joins, 'order' => array('Calltype.sort' => 'asc', 'title' => 'asc'), 'conditions' => array('Calltype.deleted' => '0', 'Calltype.template' => '1')));
            $this->set('Calltypes', $res);
        }
        $options = array();
        foreach ($this->global_options['calltypes'] as $k => $val) {
            $options[$val['caption']] = $val['description'];
        }
        
        
        $this->set('calltype_options', $options);
        $this->set('did_id', $did_id);
    }
        
        
    public function clearCallTypes() {
        $this->Calltype->deleteAll(array('did_id' => '1'), true);
        echo 'done'; exit;
    }
    
    function _isTimeSensitive($schedule) {
      if ($schedule['mon'] || $schedule['tue'] || $schedule['wed'] || $schedule['thu'] || $schedule['fri'] || $schedule['sat'] || $schedule['sun'] || ($schedule['start_date'] > '0000-00-00') || ($schedule['end_date'] > '0000-00-00')  || ($schedule['start_day'])  || ($schedule['end_day']) ) return true;
      else return false;
    }

    public function schedule_edit($id = null) {

        if (isset($this->request->data['Schedule'])) {
            $temp = $this->Calltype->Schedule->findById($id);
            $old['Schedule'] = $temp['Schedule'];
                $temp = $this->Calltype->findById($old['Schedule']['calltype_id']);
            $old['Calltype'] = $temp['Calltype'];
          
            $did_id = $this->request->data['Schedule']['did_id'];
            if (trim($this->request->data['Misc']['title_custom'])) {
              $this->request->data['Calltype']['title'] = trim($this->request->data['Misc']['title_custom']);
            }
            // check if schedule is time sensitive
            if ($this->request->data['Misc']['timesensitive']) {
              
                $this->_tschecks();
                $this->request->data['Schedule'] = array($this->request->data['Schedule']);  
                  
            }
            else {
                // if not time sensitive then blank out time sensitive fields
                $this->request->data['Schedule']['mon'] = 0;
                $this->request->data['Schedule']['tue'] = 0;
                $this->request->data['Schedule']['wed'] = 0;
                $this->request->data['Schedule']['thu'] = 0;
                $this->request->data['Schedule']['fri'] = 0;
                $this->request->data['Schedule']['sat'] = 0;
                $this->request->data['Schedule']['sun'] = 0;
                $this->request->data['Schedule']['check_days'] = 0;
                $this->request->data['Schedule']['start_time'] = NULL;
                $this->request->data['Schedule']['end_time'] = NULL;
                $this->request->data['Schedule']['start_day'] = NULL;
                $this->request->data['Schedule']['end_day'] = NULL;
                $this->request->data['Schedule']['start_date'] = NULL;
                $this->request->data['Schedule']['end_date'] = NULL;
                $this->request->data['Schedule'] = array($this->request->data['Schedule']);             
            }
            $save_ok = $this->Calltype->saveAssociated($this->request->data, array('deep' => true));        
//          $save_ok = $this->Calltype->save($this->request->data);     
            if ($save_ok) {
                // record any edits
                $this->_compareScheduleEdits($old, $this->request->data);
                $new_id = $this->Calltype->id;
                $this->set('msg', 'Calltype has been saved');
                $this->set('success', 'true');
                
                // clear the did cache so that operator screen pop will pull latest info
            $this->clearDidCache($did_id);      

            } else {
                $this->set('msg', 'The Calltype could not be saved. Please, try again.');
                $this->set('success', 'false');
            }
            $this->render('save_result');
        }
        else {
            $schedule = $this->Calltype->Schedule->find('first', array('conditions' => array('Schedule.id' => $id), 'recursive' => 0));
            if ($schedule) {
                $calltype = $this->Calltype->find('first', array('conditions' => array('Calltype.id' => $schedule['Schedule']['calltype_id']), 'recursive' => 0));
                $this->set('account_id', $schedule['Schedule']['account_id']);
                $this->request->data['Schedule'] = $schedule['Schedule'];
                $this->request->data['Calltype'] = $calltype['Calltype'];
                
                $title_found = false;
                foreach ($this->global_options['calltypes'] as $k => $val) {
                    $options[$val['caption']] = $val['description'];
                    if ($calltype['Calltype']['title'] == $val['caption']) $title_found = true;
                }
                
                // if title was custom, then add it to the dropdown selection for this particular schedule.
                if ($this->_isTimeSensitive($schedule['Schedule'])) {
                    $this->request->data['Misc']['timesensitive'] = '1';
                    if ($schedule['Schedule']['start_day']) {
                        $day = substr($schedule['Schedule']['start_day'], 0, -4);
                        $ts = strtotime("today " . substr($schedule['Schedule']['start_day'], -4, 2) . ":" . substr($schedule['Schedule']['start_day'], -2) . ":00");
                        $mytime = date('g:ia', $ts);                  
                        $this->request->data['Schedule']['start_day'] = $day;
                        $this->request->data['Misc']['day_time_start'] = $mytime;
                    }
                    if ($schedule['Schedule']['end_day']) {
                        $day = substr($schedule['Schedule']['end_day'], 0, -4);
                        if ($day > 7) $day -= 7;
                        $ts = strtotime("today " . substr($schedule['Schedule']['end_day'], -4, 2) . ":" . substr($schedule['Schedule']['end_day'], -2) . ":00");
                        $mytime = date('g:ia', $ts);                  
                        $this->request->data['Schedule']['end_day'] = $day;
                        $this->request->data['Misc']['day_time_end'] = $mytime;
                    }
                    if ($schedule['Schedule']['start_date']) {
                        $this->request->data['Schedule']['start_date'] = substr($schedule['Schedule']['start_date_f'], 0, 10);
                        $this->request->data['Misc']['date_time_start'] = strtolower(substr($schedule['Schedule']['start_date_f'], -7));
                      
                    }
                    if ($schedule['Schedule']['end_date']) {
                        $this->request->data['Schedule']['end_date'] = substr($schedule['Schedule']['end_date_f'], 0, 10);
                        $this->request->data['Misc']['date_time_end'] = strtolower(substr($schedule['Schedule']['end_date_f'], -7));
                    }
          
                }
                else $this->request->data['Misc']['timesensitive'] = '0';
                if (!$title_found) {
                    $this->request->data['Misc']['title_custom'] = $calltype['Calltype']['title'];
                    $this->request->data['Calltype']['title'] = '';
                }
                $this->set('calltype_options', $options);
            }
        }
    }
    
    function _compareScheduleEdits($old, $new) {

        $old_schedule = $this->_getSchedule($old['Schedule'], $this->php_daysofweek);
        $new_schedule = $this->_getSchedule($new['Schedule'][0], $this->php_daysofweek);
        $changes = array();
        $changes['label'] = array();
        $changes['old_values'] = array();
        $changes['new_values'] = array();
        $changed = false;
        if ($old_schedule != $new_schedule) {
            $changes['label'][] = $old['Calltype']['title'];
            $changes['old_values'][] = $old_schedule;
            $changes['new_values'][] = $new_schedule;
            $changed = true;
        }
        
        foreach ($new['Calltype'] as $k => $v) {
            if ($v != $old['Calltype'][$k]) {
                $changes['label'][] = str_replace('_', ' ',  $k);
                $changes['old_values'][] = $old['Calltype'][$k];
                $changes['new_values'][] = $v;
            }
        }
        $e['schedule_id'] = $old['Schedule']['id'];
        if (count($changes['label'] > 0)) $this->_saveChanges(serialize($changes), serialize($old), serialize($new), $old['Schedule']['account_id'],$old['Schedule']['did_id'], 'calltype', 'edit', $e);
    }
/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function edit($id = null) {
        $this->Calltype->id = $id;
        if ($this->request->is('post') || $this->request->is('put')) {
            $old = $this->Calltype->find('first', array('recursive' => 3, 'conditions' => array('Calltype.id' => $id)));

            if ($this->Calltype->save($this->request->data)) {
                $this->Session->setFlash(__('The calltype has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The calltype could not be saved. Please, try again.'));
            }
        } else {
        $this->Calltype->unbindModel(
        array('belongsTo' => array('DidNumber'))
        );              

            $this->request->data = $this->Calltype->find('first', array('recursive' => 3, 'conditions' => array('Calltype.id' => $id)));
            if (!$this->request->data) {
                throw new NotFoundException(__('Invalid calltype'));
            }
            $conditions = array('account_id' => $this->request->data['Calltype']['account_id']);
            $this->loadModel('Employee');
            $data = $this->Employee->find('all', array('conditions' => $conditions, 'recursive' => 0));
            foreach ($data as $emp) {
                $employees[$emp['Employee']['id']] = $emp['Employee'];
            }
            $this->set('employees', $employees);
            $this->set('id', $id);
        }
        
    }

  public function reorder() {
    
      if ($this->request->is('post')) {
          $saveok = true;
          if (isset($this->request->data['list'])) {
              $changes = $this->_initChanges();
              foreach ($this->request->data['list'] as $k => $calltype_id) {
                  $this->Calltype->recursive = 0;
                  
                  $old = $this->Calltype->findById($calltype_id);
                  $this->Calltype->unbindModel(
                      array('belongsTo' => array('DidNumber'))
                  );         
                  if ($old['Calltype']['sort'] != ($k+1)) {
                    $changes['label'][] = "Sort order for calltype " . $old['Calltype']['title'];
                    $changes['old_values'][] = $old['Calltype']['sort'];
                    $changes['new_values'][] = $k+1;          
                  }
                  $data['Calltype']['id'] = $calltype_id;
                  $data['Calltype']['sort'] = $k+1;
          
                  if (!$this->Calltype->save($data['Calltype'])) $saveok = false;
              }
              if (!$saveok) $this->Session->setFlash(__('Your changes could not be saved. Please, try again.'), 'flash_jsonbad');
              else {
                  $this->Session->setFlash(__('Your changes were saved.'), 'flash_jsongood');
                  $empty_array = array();
                  if (count($changes['label'] > 0)) $this->_saveChanges(serialize($changes), '', '', $old['Calltype']['account_id'], $old['Calltype']['did_id'], 'calltype', 'edit');           
              }
          }
          else {
            $this->Session->setFlash(__('Your changes could not be saved. Please, try again.'), 'flash_jsonbad');
          }
          $this->render('/Elements/json_result');
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
        $old = $this->Calltype->findById($id);
        $this->layout = 'ajax';
        $msg = '';
        $success = 'true';
        $data['Calltype']['id'] = $id;
        $data['Calltype']['deleted'] = '1';
        $deleted_ts = date('Y-m-d H:i:s');
        $data['Calltype']['deleted_ts'] = $deleted_ts; 
        if ($this->Calltype->save($data['Calltype'])) {
            $msg = 'Calltype deleted';
            $this->Calltype->Schedule->updateAll(array('deleted' => '1', 'deleted_ts' => "'$deleted_ts'"), array('calltype_id' => $id));
            $e['schedule_id'] = $id;
            $schedule = $this->_getSchedule($old['Schedule'], $this->php_daysofweek);
            $description = 'Calltype deleted - ' . $old['Calltype']['title'] . ' - ' . $schedule;
            $this->_saveChanges($description, serialize($old), '', $old['Calltype']['account_id'],$did_id, 'calltype', 'delete', $e);     
        }
        else {
            $msg = 'Cannot delete calltype';
            $success = 'false';
        }
        
        $this->set('msg', $msg);
        $this->set('success', $success);
    }
    
    public function delete_superuser($id = null) {

        $msg = '';
        $success = 'true';

        if ($this->Calltype->deleteAll(array('id' => $id), true)) {
            $msg = 'Calltype deleted';
        }
        else {
            $msg = 'Cannot delete calltype';
             $success = 'false';
        }
        
        $this->set('msg', $msg);
        $this->set('success', $success);
        $this->render('delete');
    }       
  
  //Activates or deactivates the CallType as a template.
    public function status($calltype_id, $status) {
        if ($status) $txt = 'activated';
        else $txt = 'deactivated';
        $c = $this->Calltype->findById($calltype_id);
        $data['id'] = $calltype_id;
        $data['template'] = $status;
        if ($this->Calltype->save($data)) {
            $this->Session->setFlash('The template has been ' . $txt, 'flash_jsongood');
            $this->render('/Elements/json_result');       
        }
        else {
            $this->Session->setFlash('The template cannot be ' . $txt, 'flash_jsonbad');
            $this->render('/Elements/json_result');
        }       
    }  
}
