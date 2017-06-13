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
class CallLogsController extends AppController {
    public $paginate;
    public $components = array('RequestHandler');
    public $helpers = array('Js');

    /*function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Setting');
    $this->set('timezones', $this->Setting->fetchOptionsByName('options_timezones'));
        
    }*/
    
    public function index($did_id=null) {
        $fields = array('TIMESTAMPDIFF( 
SECOND, CallLog.start_time, CallLog.wrapup_time ) as duration', 'TIMESTAMPDIFF( 
SECOND, CallLog.end_time, CallLog.wrapup_time ) as wrapup', 'Account.account_name','Message.id', 'Message.did_id', 'Message.delivered', 'User.*', 'Account.account_num', 'CallLog.*', "DATE_FORMAT(CONVERT_TZ(CallLog.start_time, '".Configure::read('default_timezone')."', DidNumber.timezone), '%c/%d/%y %l:%i %p') as starttimef", "DATE_FORMAT(CallLog.start_time, '%c/%d/%y %l:%i %p') as starttime", "DATE_FORMAT(CallLog.end_time, '%c/%d/%y %l:%i %p') as endtime", "if (CallLog.end_time <> '0000-00-00', DATE_FORMAT(CONVERT_TZ(CallLog.end_time, '".Configure::read('default_timezone')."', DidNumber.timezone), '%c/%d/%y %l:%i %p'), '') as endtimef", 'DidNumber.did_number', 'DidNumber.company', 'DidNumber.timezone');
        $joins = array(
            array('table' => OA_TBL_PREFIX . 'accounts',
                'alias' => 'Account',
                'type' => 'left',
                'conditions' => array('Account.id=CallLog.account_id')
            ),
            array('table' => OA_TBL_PREFIX . 'users',
                'alias' => 'User',
                'type' => 'left',
                'conditions' => array('User.id=CallLog.user_id')
            ),          
            array('table' => OA_TBL_PREFIX . 'did_numbers',
                'alias' => 'DidNumber',
                'type' => 'left',
                'conditions' => array('DidNumber.id=CallLog.did_id')
            )   
        );
        if ($did_id) {
            $this->paginate['conditions'] = array(
                    'CallLog.did_id' => $did_id,
            );      
        }
                
        $extra_conditions = '';
        $conditions = array();
        if (isset($this->request->data['Search']['user_id']) && $this->request->data['Search']['user_id']) {
            $conditions[] = "CallLog.user_id = {$this->request->data['Search']['user_id']}";
            
        }   

        if (!empty($this->request->data['Search']['c_wrapup']) ) {
            $conditions[] = "((TIMESTAMPDIFF(SECOND, CallLog.end_time, CallLog.wrapup_time ) * 100)/ TIMESTAMPDIFF(SECOND, CallLog.start_time, CallLog.wrapup_time )) > ".$this->request->data['Search']['c_wrapup']."";
            
        }   
        
        if (!empty($this->request->data['Search']['c_min_duration']) ) {
            $conditions[] = "TIMESTAMPDIFF(SECOND, CallLog.start_time, CallLog.wrapup_time ) > ".$this->request->data['Search']['c_min_duration'];
            
        }   
        
        if (!empty($this->request->data['Search']['c_start_time'])) {
            $start_time = date('H:i:s', strtotime('Today ' . $this->request->data['Search']['c_start_time']));
        } 
        else $start_time = '00:00:00';   

        if (!empty($this->request->data['Search']['c_end_time'])) {
            $end_time = date('H:i:s', strtotime('Today ' . $this->request->data['Search']['c_end_time']));
        } 
        else $end_time = '23:59:59';   
        
        if (empty($this->request->data['Search']['c_start_date'])) {
            $this->request->data['Search']['c_start_date'] = date('Y-m-d', strtotime('-7 day'));
        }
        if (empty($this->request->data['Search']['c_end_date'])) {
            $this->request->data['Search']['c_end_date'] = date('Y-m-d', strtotime('today'));
        }
        $conditions[] = "CallLog.start_time >= '{$this->request->data['Search']['c_start_date']} $start_time'";
        $conditions[] = "CallLog.start_time <= '{$this->request->data['Search']['c_end_date']} $end_time'";

        $this->CallLog->unbindModel(
                array('hasMany' => array('CallEvent'))
        );  
        $this->paginate['conditions'][] = $conditions;
        $this->paginate['fields'] = $fields;
        $this->paginate['joins'] = $joins;
        $this->paginate['limit'] = 50;
        $this->paginate['order'] = array('CallLog.id' => 'desc');
        
        // find totals only if search dates span 60 days or less to avoid long queries
        $delta_secs = strtotime($this->request->data['Search']['c_end_date'] . ' 00:00:00') - strtotime($this->request->data['Search']['c_start_date'] . ' 00:00:00');
        $max_delta = 30;
        if (round($delta_secs/ (60*60*24)) < $max_delta) {
            $fields = array('sum(TIMESTAMPDIFF( 
    SECOND, CallLog.start_time, CallLog.wrapup_time )) as duration', 'sum(TIMESTAMPDIFF( 
    SECOND, CallLog.end_time, CallLog.wrapup_time )) as wrapup');
            $totals = $this->CallLog->find('all', array('conditions' => $this->paginate['conditions'], 'fields' => $fields));
            $this->set('total_duration', $totals[0][0]['duration']);
            $this->set('total_wrapup', $totals[0][0]['wrapup']);
            $this->set('max_delta', $max_delta);
        }
        else {
            $this->set('total_duration', false);
            $this->set('total_wrapup', false);
            $this->set('max_delta', $max_delta);
        }
        
        $this->CallLog->recursive = 0;
        $this->set('CallLogs', $this->paginate());
    }
    
    // function to mark a call as ended
    public function end_call($call_id) {
        $call = $this->CallLog->findById($call_id);
        // This function is called either when the inbound call hangs up or the operator screen closes.  For most calls, this function is called
        // twice per call, once when the inbound caller hangs up and another when the  operator screen closes.  For some calls, the function is called 
        // only once (ie: operator screen is closed before call ends or the browser refreshed before operator screen is closed). 
        // The first time function is called both wrapup_time and end_time is set to the current timestamp just in case 2nd call never comes.
        // If the second call comes, only wrap-up time is updated, after a check to make sure operator screen was not repopped.
        
        if ($call) {
            if ($call['CallLog']['end_time'] == '0000-00-00 00:00:00' || $call['CallLog']['end_time'] == null) {
                $call['CallLog']['wrapup_time'] = $call['CallLog']['end_time'] = date('Y-m-d H:i:s');
                if ($this->CallLog->save($call['CallLog'])) {
                    $this->Session->setFlash(__('End of call logged'), 'flash_jsongood');
                    
                }
                else {
                    $this->Session->setFlash(__('End of call cannot be logged'), 'flash_jsonbad');
                }
            }
            else {
                // check if screen was re-popped by looking for a repop event in the call log
                $conditions = array('call_id' => $call_id, 'event_type' => EVENT_REPOP);
                $repops = $this->CallLog->CallEvent->find('first', array('conditions' => $conditions, 'order' => array('created' => 'DESC')));
                
                // only record the wrap-up time if the call is NOT a re-pop
                if (sizeof($repops) < 1) {
                    $call['CallLog']['wrapup_time'] = date('Y-m-d H:i:s');
                    if ($this->CallLog->save($call['CallLog'])) {
                        $this->Session->setFlash(__('End of call logged'), 'flash_jsongood');
                        
                    }
                    else {
                        $this->Session->setFlash(__('End of call cannot be logged'), 'flash_jsonbad');
                    }
                }
                else {
                    $this->Session->setFlash(__('End of call already logged'), 'flash_jsongood');
                }
            }

        }
        else {
            $this->Session->setFlash(__('End of call cannot be logged'), 'flash_jsonbad');
        }
        $this->render('/Elements/json_result'); 

    }
    
    public function messages($did_id) {
        if (!$did_id) {
            $this->set('CallLogs', array());      
        }
        $this->set('did_id', $did_id);
        $fields = array('CallLog.did_number', 'CallLog.end_time', 'CallLog.unique_id', 'TIMESTAMPDIFF( 
SECOND, CallLog.start_time, CallLog.wrapup_time ) as duration', 'TIMESTAMPDIFF( 
SECOND, CallLog.end_time, CallLog.wrapup_time ) as wrapup', 'DidNumber.timezone', 'Message.did_id', 'Message.id', 'Message.delivered','Message.schedule_id' , 'User.*','CallLog.*', "DATE_FORMAT(CONVERT_TZ(CallLog.start_time, '".Configure::read('default_timezone')."', DidNumber.timezone), '%c/%d/%y %l:%i %p') as starttimef", "if (CallLog.end_time <> '0000-00-00', DATE_FORMAT(CONVERT_TZ(CallLog.end_time, '".Configure::read('default_timezone')."', DidNumber.timezone), '%c/%d/%y %l:%i %p'), '') as endtimef",  'DidNumber.did_number', 'DidNumber.company');
        $joins = array(

            array('table' => OA_TBL_PREFIX . 'users',
                'alias' => 'User',
                'type' => 'left',
                'conditions' => array('User.id=CallLog.user_id')
            ),          
            array('table' => OA_TBL_PREFIX . 'did_numbers',
                'alias' => 'DidNumber',
                'type' => 'left',
                'conditions' => array('DidNumber.id=CallLog.did_id')
            )           
        );
        
        $this->CallLog->unbindModel(
                array('hasMany' => array('CallEvent'))
        );  
                
        $this->paginate['conditions'] = array('CallLog.did_id' => $did_id);
        $this->paginate['fields'] = $fields;
        $this->paginate['joins'] = $joins;
        $this->paginate['limit'] = 50;
        $this->paginate['order'] = array('CallLog.id' => 'desc');
        $this->CallLog->recursive = 0;
        $data =  $this->paginate();
        //print_r($data); exit;
        $this->set('CallLogs',$data);
    }   

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function view($id = null) {
        $fields = array('Account.account_name','User.*', 'Account.account_num', 'CallLog.*', "IF (end_time <> '0000-00-00', UNIX_TIMESTAMP(CallLog.end_time) - UNIX_TIMESTAMP(CallLog.start_time), 'UNKNOWN') as duration", "IF (end_time <> '0000-00-00', UNIX_TIMESTAMP(CallLog.wrapup_time) - UNIX_TIMESTAMP(CallLog.end_time), 'UNKNOWN') as wrapup", 'DidNumber.did_number', 'DidNumber.company');
        $joins = array(
            array('table' => OA_TBL_PREFIX . 'accounts',
                'alias' => 'Account',
                'type' => 'left',
                'conditions' => array('Account.id=CallLog.account_id')
            ),
            array('table' => OA_TBL_PREFIX . 'users',
                'alias' => 'User',
                'type' => 'left',
                'conditions' => array('User.id=CallLog.user_id')
            ),          
            array('table' => OA_TBL_PREFIX . 'did_numbers',
                'alias' => 'DidNumber',
                'type' => 'left',
                'conditions' => array('DidNumber.id=CallLog.did_id')
            )
            
        );

        $conditions = array('CallLog.id' => $id);
        
        $callLog = $this->CallLog->find('first', array('fields' => $fields, 'joins' => $joins, 'conditions' => $conditions, 'recursive' => 0));
        $this->set('calls', $callLog);
        $conditions = array('CallEvent.call_id' => $id);
        $joins = array(
            array('table' => OA_TBL_PREFIX . 'users',
                'alias' => 'User',
                'type' => 'left',
                'conditions' => array('User.id=CallEvent.user_id')
            )
        );
        $data = $this->CallLog->CallEvent->find('all', array('fields' => array('CallEvent.*', 'User.username'), 'joins' => $joins, 'conditions' => $conditions, 'order' => array('created' => 'asc'), 'recursive' => 0));
        $this->set('events', $data);   
    }

    public function events($id) {
        $conditions = array('CallEvent.call_id' => $id);
        $joins = array(
            array('table' => OA_TBL_PREFIX . 'users',
                'alias' => 'User',
                'type' => 'left',
                'conditions' => array('User.id=CallEvent.user_id')
            ),
            array('table' => OA_TBL_PREFIX . 'call_logs',
                'alias' => 'CallLog',
                'type' => 'left',
                'conditions' => array('CallLog.id=CallEvent.call_id')
            ),
            
            array('table' => OA_TBL_PREFIX . 'did_numbers',
                'alias' => 'DidNumber',
                'type' => 'left',
                'conditions' => array('CallLog.did_id=DidNumber.id')
            )           
        );
        $this->set('call_id', $id);
        if ($this->isAuthorized('CalllogsEventsDebug')) $date_event = '%a %c/%d/%y %l:%i:%s %p';
        else $date_event = '%a %c/%d/%y %l:%i %p';
        $data = $this->CallLog->CallEvent->find('all', array('fields' => array('CallEvent.*', "DATE_FORMAT(CONVERT_TZ(CallEvent.created, '".Configure::read('default_timezone')."', DidNumber.timezone), '$date_event') as createdf", 'User.username'), 'joins' => $joins, 'conditions' => $conditions, 'order' => array('id' => 'asc'), 'recursive' => 0));
        $this->set('events', $data);    
    }
/**
 * add method
 *
 * @return void
 */
    public function add() {
        /*if ($this->request->is('post')) {
            $this->CallLog->create();
            print_r($this->request->data); exit;
            if ($this->CallLog->save($this->request->data)) {
                $this->Session->setFlash(__('The ccact call log has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The ccact call log could not be saved. Please, try again.'));
            }
        }*/
    }
    
    public function dumpTestData() {
        if ($this->isAuthorized('CalllogsDumptestdata')) {
            echo '<i>(Not allowed, user role:' . ')</i>'; exit;
        }
        else {
            $this->CallLog->deleteAll(array('unique_id' => 'TESTCALL'), true);
            echo '<i>(done)</i>'; exit;
        }
        
    }

    public function end() {
        
    }
/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function edit($id = null) {
        $this->CallLog->id = $id;
        if (!$this->CallLog->exists()) {
            throw new NotFoundException(__('Invalid ccact call log'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->CallLog->save($this->request->data)) {
                $this->Session->setFlash(__('The ccact call log has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The ccact call log could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->CallLog->read(null, $id);
        }
    }

    public function my_recent($operator_id) {
        // fetch most recent calls 
        $sql = "SELECT d.* from (select c.id as c_id, c.user_id, DATE_FORMAT(c.start_time, '%a %b %d %y %l:%i %p') as created_f, a.account_num, c.did_number, m.schedule_id, c.end_time, c.cid_number, c.unique_id, c.did_id, m.delivered, m.calltype, d.company, m.id, m.call_id FROM `openanswer`.`ccact_call_logs` AS `c` left JOIN `openanswer`.`ccact_did_numbers` AS `d` ON (`d`.`id` = `c`.`did_id`) left JOIN `openanswer`.`ccact_accounts` AS `a` ON (`d`.`account_id` = `a`.`id`) LEFT JOIN `openanswer`.`ccact_messages` AS `m` ON (`m`.`call_id` = `c`.`id`)  WHERE `c`.`start_time` >= '". date('Y-m-d H:i:s', strtotime('-4 day'))."' and c.`user_id` = $operator_id) d  ORDER BY d.`c_id` desc  LIMIT 15";
        
        $calls = $this->CallLog->query($sql);
        $this->set('calls', $calls);
        $this->render('recent');
    }
    
    // create a report of # of messages per hour
    function hourly($user_id, $date) {
        $this->layout = 'ajax';
        $sql = "select count(*) as cnt, DATE_FORMAT(start_time, '%l%p') as hour_created from ".OA_TBL_PREFIX."call_logs m where m.start_time >= '$date 00:00:00' and m.start_time <= '$date 23:59:59' and m.user_id='$user_id' group by hour_created";
        
        $data = $this->CallLog->query($sql);

        for ($i = 23; $i>=0; $i--)
        {
            $temp = date('ga', strtotime("-$i hour"));
            $hours[$temp] = 0;
        }
        foreach ($data as $r) {
            $hours[strtolower($r[0]['hour_created'])] = $r[0]['cnt']; 
        }

        $this->set('calls', $hours);
        $this->set('oa_title', 'Calls by the hour');
        
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
        $sql = "select count(*) as cnt, DATE_FORMAT(DATE(start_time),'%a %c/%e') as date_created from ".OA_TBL_PREFIX."call_logs m where m.start_time >= '$min_date 00:00:00' and m.start_time <= '$today 23:59:59' and m.start_time >= '$min_date 00:00:00' and m.user_id='$user_id' group by DATE(start_time)";
        
        $data = $this->CallLog->query($sql);
        foreach ($data as $r) {
            $days[$r[0]['date_created']] = $r[0]['cnt']; 
        }
        $this->set('calls', $days);
        $this->set('oa_title', 'Calls by the day');
        
    }

    function my_daily($user_id) {
        $this->daily($user_id);
        $this->render('daily');
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
/*      if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->CallLog->id = $id;
        if (!$this->CallLog->exists()) {
            throw new NotFoundException(__('Invalid ccact call log'));
        }
        if ($this->CallLog->delete()) {
            $this->Session->setFlash(__('Ccact call log deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Ccact call log was not deleted'));
        $this->redirect(array('action' => 'index'));*/
    }
}
