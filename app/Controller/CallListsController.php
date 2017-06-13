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
class CallListsController extends AppController {
    public $paginate;
  
/**
 * index method
 *
 * @return void
 */
    public function index($did_id, $section = null) {
        if (!$did_id) {
            $this->Session->setFlash(__('You must specify a DID'), 'flash_jsonbad');
            $this->render('/Elements/json_result');
        }
        if (!empty($section)) {
            $this->request->data['Search']['c_type'] = $section;
        }
        $this->loadModel('DidNumber');
        $did = $this->DidNumber->findById($did_id);
        $this->set('timezone', $this->global_options['timezone'][$did['DidNumber']['timezone']]);
        $this->set('did_id', $did_id);
        $conditions = array('did_id' => $did_id, 'deleted' => 0);
        $today = date('Y-m-d');
         
        if ($this->request->data['Search']['c_type'] == 'currentfuture') {
            $this->CallList->bindModel(array(
                'hasMany' => array(
                    'CallListsSchedule' => array(
                        'className' => 'CallListsSchedule',
                        'foreignKey' => 'call_list_id',
                        'order' => array('CallListsSchedule.start_date' => 'desc', 'CallListsSchedule.start_day' => 'desc', 'CallListsSchedule.check_days' => 'desc'),
                        'conditions' => array('CallListsSchedule.deleted <>' => '1', "((CallListsSchedule.start_date is null and CallListsSchedule.end_date is null) || (CallListsSchedule.start_date <= '$today' and CallListsSchedule.end_date >='$today') ||(CallListsSchedule.start_date is not null and CallListsSchedule.end_date is not null and CallListsSchedule.start_date > '$today'))")
                    )
                )
            ));
        }               
        else if ($this->request->data['Search']['c_type'] == 'current') {
            $this->CallList->bindModel(array(
                'hasMany' => array(
                    'CallListsSchedule' => array(
                        'className' => 'CallListsSchedule',
                        'foreignKey' => 'call_list_id',
                        'order' => array('CallListsSchedule.start_date' => 'desc', 'CallListsSchedule.start_day' => 'desc', 'CallListsSchedule.check_days' => 'desc'),
                        'conditions' => array('CallListsSchedule.deleted <>' => '1', "((CallListsSchedule.start_date is null and CallListsSchedule.end_date is null) || (CallListsSchedule.start_date <= '$today' and CallListsSchedule.end_date >='$today'))")
                    )
                )
            ));
        }
        else if ($this->request->data['Search']['c_type'] == 'expired') {
            $this->CallList->bindModel(array(
                'hasMany' => array(
                    'CallListsSchedule' => array(
                        'className' => 'CallListsSchedule',
                        'foreignKey' => 'call_list_id',
                        'order' => array('CallListsSchedule.start_date' => 'desc', 'CallListsSchedule.start_day' => 'desc', 'CallListsSchedule.check_days' => 'desc'),
                        'conditions' => array('CallListsSchedule.deleted <>' => '1', "((CallListsSchedule.start_date is not null and CallListsSchedule.end_date is not null and CallListsSchedule.end_date < '$today'))")
                    )
                )
            ));
        }
        else if ($this->request->data['Search']['c_type'] == 'future') {
            $this->CallList->bindModel(array(
                'hasMany' => array(
                    'CallListsSchedule' => array(
                        'className' => 'CallListsSchedule',
                        'foreignKey' => 'call_list_id',
                        'order' => array('CallListsSchedule.start_date' => 'desc', 'CallListsSchedule.start_day' => 'desc', 'CallListsSchedule.check_days' => 'desc'),
                        'conditions' => array('CallListsSchedule.deleted <>' => '1', "((CallListsSchedule.start_date is null and CallListsSchedule.end_date is null) ||(CallListsSchedule.start_date is not null and CallListsSchedule.end_date is not null and CallListsSchedule.start_date > '$today'))")
                    )
                )
            ));
        }
      
        $this->paginate['conditions'] = $conditions;
        $this->paginate['limit'] = 100;
        $this->paginate['recursive'] = 1;
        $call_lists = $this->paginate();
            foreach($call_lists as $j => $c) {
                foreach ($c['CallListsSchedule'] as $k => $v) {
                    $v['starttime'] = $v['start_time_f'];
                    $v['endtime'] = $v['end_time_f'];
                    $v['startdate'] = $v['startdate_f'];
                    $v['enddate'] = $v['enddate_f'];
                    //$v['days'] = getDayRanges($s, $php_daysofweek);
                    //$v['day_range'] = $day_range;                
                    $call_lists[$j]['CallListsSchedule'][$k]['schedule'] = $this->_getSchedule($v, $this->php_daysofweek);
                }
            }
        $this->loadModel('Employee');
        $conditions = array('did_id' => $did_id, 'deleted' => 0);
        $emps = $this->Employee->find('all', array('recursive' => 0, 'conditions' => $conditions));
        $employees = array();
        foreach ($emps as $k => $e) {
            $employees[$e['Employee']['id']] = $e;
        }
        $this->set('employees', $employees);
        $this->set('CallLists', $call_lists);
    }

    public function get($did_id, $timezone) {
        $datetime = new DateTime();
        $client_time = new DateTimeZone($timezone);
        $datetime->setTimezone($client_time);
        $n_day = $datetime->format('w'); // 0=sun, 6 = Saturday
        if ($n_day == 0) $n_day = 7; // make 7=Sunday
        $n_time = $n_day . $datetime->format('Hi');
        $time_mysql = $datetime->format('Y-m-d G:i:s');
        $day_of_week = strtolower($datetime->format('D'));
        $sql = "select c.title, c.id, s.* 
            FROM ".OA_TBL_PREFIX."call_lists_schedules s 
            LEFT JOIN ".OA_TBL_PREFIX."call_lists c ON c.id=s.call_list_id 
            WHERE c.deleted = '0' and s.did_id='$did_id' and s.deleted='0' 
            AND (
            (s.start_date <= '$time_mysql' and s.end_date >= '$time_mysql')       
            OR (s.start_day <= '$n_time' and s.end_day>='$n_time') 
            OR (s.start_day <= '".($n_time+70000)."' and s.end_day>='".($n_time+70000)."')
            OR (check_days='1' and ".$day_of_week."='1')          
            OR (start_date is null and start_day is null and check_days='0')
            ) order by c.sort asc, c.title, s.start_date desc, s.start_day desc, check_days desc";    
          
        $data = $this->CallList->query($sql);          
    }




    public function view_all($did_id, $section = null) {
        $this->index($did_id, $section);
    }


    public function view($list_id, $did_id=null, $test_time=null) {
        if ($list_id == 'ALL') {
            $sql = "select c.*, d.timezone from ".OA_TBL_PREFIX."call_lists c left join ".OA_TBL_PREFIX."did_numbers d on d.id=c.did_id where c.id='$list_id'";     
            $list = $this->CallList->query($sql);
            $this->set('list', $list[0]['c']);
            $tz = $list[0]['d']['timezone'];
            if (!empty($test_time)) {
                $datetime = new DateTime($test_time);
            }
            else {
                $datetime = new DateTime();
                $client_time = new DateTimeZone($tz);
                $datetime->setTimezone($client_time);    
            }
            $n_day = $datetime->format('w'); // 0=sun, 6 = Saturday
            if ($n_day == 0) {
                $n_day = 7; // make 7=Sunday
            }
            $n_time = $n_day . $datetime->format('Hi');
            $n2_time = $datetime->format('H:i:s');
          
            $time_mysql = $datetime->format('Y-m-d G:i:s');
            $day_of_week = strtolower($datetime->format('D'));
            
            $sql = "select s.* 
                FROM ".OA_TBL_PREFIX."call_lists_schedules s
                WHERE call_list_id='$list_id' 
                AND (
                (s.start_date <= '$time_mysql' and s.end_date >= '$time_mysql')       
                OR (s.start_day <= '$n_time' and s.end_day>='$n_time') 
                OR (s.start_day <= '".($n_time+70000)."' and s.end_day>='".($n_time+70000)."')
                OR (check_days='1' and ".$day_of_week."='1' and (start_time is null OR (start_time < '$n2_time' and end_time > '$n2_time')))          
                OR (start_date is null and start_day is null and check_days='0')
                ) order by s.start_date desc, s.start_day desc, check_days desc";
            $data = $this->CallList->query($sql);
            $e_ids = explode(',', $data[0]['s']['employee_ids']);
            
            $sql = "select e.* from ".OA_TBL_PREFIX."employees e where id in (".$data[0]['s']['employee_ids'].")";
            $data = $this->CallList->query($sql);
            $employees = array();
            foreach($data as $e) {
                $e['e']['gender'] = $this->global_options['gender'][$e['e']['gender']];
                $employees[] = $e['e'];
            }
            $this->set('employees', $employees);
        }
        else {
            $sql = "select c.*, d.timezone from ".OA_TBL_PREFIX."call_lists c left join ".OA_TBL_PREFIX."did_numbers d on d.id=c.did_id where c.id='$list_id'";     
            $list = $this->CallList->query($sql);
            $this->set('list', $list[0]['c']);
            $tz = $list[0]['d']['timezone'];
            if (!empty($test_time)) {
                $datetime = new DateTime($test_time);
            }
            else {
                $datetime = new DateTime(); 
                $client_time = new DateTimeZone($tz);
                $datetime->setTimezone($client_time);    
            }
            $n_day = $datetime->format('w'); // 0=sun, 6 = Saturday
            if ($n_day == 0) $n_day = 7; // make 7=Sunday
            $n_time = $n_day . $datetime->format('Hi');
            $n2_time = $datetime->format('H:i:s');
            $time_mysql = $datetime->format('Y-m-d G:i:s');
            $day_of_week = strtolower($datetime->format('D'));
            $sql = "select s.* 
                FROM ".OA_TBL_PREFIX."call_lists_schedules s
                WHERE call_list_id='$list_id' and s.deleted='0'
                AND (
                (s.start_date <= '$time_mysql' and s.end_date >= '$time_mysql')       
                OR (s.start_day <= '$n_time' and s.end_day>='$n_time') 
                OR (s.start_day <= '".($n_time+70000)."' and s.end_day>='".($n_time+70000)."')
                OR (check_days='1' and ".$day_of_week."='1' and (start_time is null OR (start_time < '$n2_time' and end_time > '$n2_time')))          
                OR (start_date is null and start_day is null and check_days='0')
                ) order by s.start_date desc, s.start_day desc, check_days desc";
          
            $data = $this->CallList->query($sql);
            if ($data) {
                $e_ids = explode(',', $data[0]['s']['employee_ids']);
            }
            else {
                $e_ids = array();
            }
          
            $the_ids = array();
            foreach($e_ids as $i) {
                if (trim($i)) $the_ids[] = $i;
            }
            $sql = "select e.* from ".OA_TBL_PREFIX."employees e where id in (".implode(',', $the_ids).") order by FIELD(id, ".implode(',', $the_ids).")";
            try {
                $data = $this->CallList->query($sql);
            }
            catch (Exception $e) {
                App::uses('CakeEmail', 'Network/Email');        
                CakeEmail::deliver(Configure::read('admin_email'), '[CallLists Controller] Bad call list query', 'List id: ' . $list_id, array('from' => Configure::read('admin_email')), 'default');
            }
            if (sizeof($the_ids) < 1) {
                echo '<span class="alert">No valid oncall list was found, please click <a href="#" onclick="openDialogWindow(\'/CallLists/view_all/'.$did_id.'/all\', \'Oncall list\', null, null, 450, 700);">here</a> to see <b>all</b> oncall lists</span>';
            }
            $employees = array();
            foreach($data as $e) {
                if (isset($e['e'])) {
                    if (!empty($e['e']['gender'])) {
                        $e['e']['gender'] = $this->global_options['gender'][$e['e']['gender']];
                    }
                    $temp[$e['e']['id']] = $e['e'];
                }
            }
            foreach($e_ids as $id) {
                if (isset($temp[$id])) {
                    $employees[] = $temp[$id];
                }
            }
            $this->set('employees', $employees);
        }
  }
  
  public function callbox_view($list_id, $action_id = '', $call_id='') {
    $this->loadModel('CallLog');
    $data = $this->CallLog->findById($call_id);
    if (isset($this->request->data['test_time'])) $test_time = trim($this->request->data['test_time']);
    else $test_time = '';
    $this->view($list_id, $data['CallLog']['did_id'], $test_time);
    $this->set('action_id', $action_id);
    if ($data) {
      $this->set('did_id', $data['CallLog']['did_id']);
      $this->set('did_number', $data['CallLog']['did_number']);
    }
    
  }
/**
 * add method
 *
 * @return void
 */
    public function add($did_id=null) {
        $this->layout = 'plain';
      $this->set('did_id', $did_id);
      if (!$did_id) {
            $this->Session->setFlash(__('You must specify a DID'), 'flash_jsonbad');
      $this->render('/Elements/json_result');
      }        
        if ($this->request->is('post') || $this->request->is('put')) {
          $data['CallList'] = $this->request->data['CallList'];
          $this->request->data['CallListsSchedule']['did_id'] = $did_id;
      if (isset($this->request->data['Misc']['day_time_start']) && trim($this->request->data['Misc']['day_time_start'])) {            
        $ts = strtotime("today " . $this->request->data['Misc']['day_time_start']);
        $mytime = date('H:i', $ts);                     
        $this->request->data['Misc']['day_time_start'] = $mytime;    
      }
      
      if (isset($this->request->data['Misc']['day_time_end']) && trim($this->request->data['Misc']['day_time_end'])) {            
        $ts = strtotime("today " . $this->request->data['Misc']['day_time_end']);
        $mytime = date('H:i', $ts);                     
        $this->request->data['Misc']['day_time_end'] = $mytime;    
      }
      
      if (isset($this->request->data['CallListsSchedule']['mon'])) {
        if ($this->request->data['CallListsSchedule']['mon'] || $this->request->data['CallListsSchedule']['tue'] || $this->request->data['CallListsSchedule']['wed'] || $this->request->data['CallListsSchedule']['thu'] || $this->request->data['CallListsSchedule']['fri'] || $this->request->data['CallListsSchedule']['sat'] || $this->request->data['CallListsSchedule']['sun']) {
          $this->request->data['CallListsSchedule']['check_days'] = '1'; 
        }
      }
      // take into account day ranges that cross over to the next week, add 7 to end day in those cases 
      if (isset($this->request->data['CallListsSchedule']['start_day']) && $this->request->data['CallListsSchedule']['start_day'] && $this->request->data['CallListsSchedule']['end_day']) {
        if ($this->request->data['CallListsSchedule']['start_day'] > $this->request->data['CallListsSchedule']['end_day']) {
          $this->request->data['CallListsSchedule']['end_day'] += 7;
        }
        $this->request->data['CallListsSchedule']['start_day'] = $this->request->data['CallListsSchedule']['start_day'] . str_replace(':', '', $this->request->data['Misc']['day_time_start']);
        $this->request->data['CallListsSchedule']['end_day'] = $this->request->data['CallListsSchedule']['end_day'] . str_replace(':', '', $this->request->data['Misc']['day_time_end']);
      }

      if (isset($this->request->data['CallListsSchedule']['start_time']) && trim($this->request->data['CallListsSchedule']['start_time'])) {            
        $ts = strtotime("today " . $this->request->data['CallListsSchedule']['start_time']);
        $mytime = date('G:i:s', $ts);                     
        $this->request->data['CallListsSchedule']['start_time'] = $mytime;    
      }
      if (isset($this->request->data['CallListsSchedule']['end_time']) && trim($this->request->data['CallListsSchedule']['end_time'])) {            
        $ts = strtotime("today " . $this->request->data['CallListsSchedule']['end_time']);
        $mytime = date('G:i:s', $ts);                     
        $this->request->data['CallListsSchedule']['end_time'] = $mytime;    
      }
      if (isset($this->request->data['Misc']['date_time_start']) && trim($this->request->data['Misc']['date_time_start'])) {            
        $ts = strtotime("today " . $this->request->data['Misc']['date_time_start']);
        $mytime = date('G:i:s', $ts);                     
        $this->request->data['Misc']['date_time_start'] = $mytime;    
      }
      
      if (isset($this->request->data['Misc']['date_time_end']) && trim($this->request->data['Misc']['date_time_end'])) {            
        $ts = strtotime("today " . $this->request->data['Misc']['date_time_end']);
        $mytime = date('G:i:s', $ts);                     
        $this->request->data['Misc']['date_time_end'] = $mytime;    
      }


          if (isset($this->request->data['CallListsSchedule']['start_date']) && trim($this->request->data['CallListsSchedule']['start_date']) && trim($this->request->data['CallListsSchedule']['end_date'])) {
              $this->request->data['CallListsSchedule']['start_date'] = $this->request->data['CallListsSchedule']['start_date'] . " " . $this->request->data['Misc']['date_time_start'];
              $this->request->data['CallListsSchedule']['end_date'] = $this->request->data['CallListsSchedule']['end_date'] . " " . $this->request->data['Misc']['date_time_end'];    
          }          
          
          $data['CallListsSchedule'] = array(
            $this->request->data['CallListsSchedule']
          );
          $saveok = $this->CallList->saveAssociated($data);
          if ($saveok) {
            
              $this->Session->setFlash(__('Your schedule has been added'), 'flash_jsongood');
          $this->loadModel('Employee');
            $conditions = array('did_id' => $this->request->data['CallListsSchedule']['did_id'], 'deleted' => 0);
          $emps = $this->Employee->find('all', array('recursive' => 0, 'conditions' => $conditions));
          foreach ($emps as $k => $e) {
            $employees[$e['Employee']['id']] = $e;
          }    

              $new_schedule = $this->_getSchedule($this->request->data['CallListsSchedule'], $this->php_daysofweek);
              $new_list = $this->_getEmployeeList($this->request->data['CallListsSchedule']['employee_ids'], $employees);
        $description = 'On-Call List \'' .$this->request->data['CallList']['title'] . '\' added - ' . $new_schedule . "<br>";
        $description .= "<i>$new_list</i>";
                        
              if (!empty($description)) {
          $e['call_list_id'] = $this->CallList->CallListsSchedule->getInsertID();
          $this->_saveChanges($description, '', serialize($this->request->data), '0', $this->request->data['CallList']['did_id'], 'oncall', 'add', $e);                    
        }                      
          }
          else {
              $this->Session->setFlash(__('Cannot save your changes, please try again later'), 'flash_jsonbad');
          }
          $this->render('/Elements/json_result');
        }
        else {
          $this->loadModel('Employee');
          $conditions = array('did_id' => $did_id, 'deleted' => 0);
          $this->set('employees', $this->Employee->find('all', array('conditions' => $conditions, 'order' => array('name' => 'asc'))));
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
        $this->CallList->id = $id;
        if ($this->request->is('post') || $this->request->is('put')) {

            if ($this->CallList->save($this->request->data)) {
                $this->Session->setFlash(__('The messages event has been saved'), 'flash_jsongood');
            } else {
                $this->Session->setFlash(__('The messages event could not be saved. Please, try again.'), 'flash_jsonbad');
            }
            $this->render('/Elements/json_result');
        } else {
            $this->request->data = $this->CallList->read(null, $id);
            $this->set('did_id', $this->request->data['CallList']['did_id']);
        }
    }
    
  function _getEmployeeList($employee_ids, $employees) {
            $ids = explode(',', $employee_ids);
      $val = '';
            $names = array();
            foreach ($ids as $id) {
              //print_r($employees[$id]);
              if (!empty($employees[$id])) $names[] = $employees[$id]['Employee']['name'];
            }
      if (sizeof($names) > 0) {
              $val =  implode(', ', $names);
            }    
            return $val;
  }
  
  public function delete_all($id) {
    if (empty($id)) {
      $this->Session->setFlash(__('Cannot delete list, please try again later'), 'flash_jsonbad');
          $this->render('/Elements/json_result');
          return;
    }
    // grab the exising oncall lists and all schedules belonging to it
    $old = $this->CallList->find('first', array('conditions' => array('id' => $id)));

    if ($old) {
      $del_date = date('Y-m-d H:i:s');
      $save_ok = true;
      
      // delete each schedule one by one
      foreach ($old['CallListsSchedule'] as $k => $s) {
        $data['CallListsSchedule'] = $s;
        $old['CallListsSchedule'][$k]['deleted'] = '1';
        $old['CallListsSchedule'][$k]['deleted_ts'] = $del_date;
        if ($this->CallList->CallListsSchedule->save( $old['CallListsSchedule'][$k])) {
          
          // create a text desctiption of the schedule
                $old['CallListsSchedule'][$k]['starttime'] = $old['CallListsSchedule']['start_time_f'];
                $old['CallListsSchedule'][$k]['endtime'] = $old['CallListsSchedule']['end_time_f'];
                $old['CallListsSchedule'][$k]['startdate'] = $old['CallListsSchedule']['startdate_f'];
                $old['CallListsSchedule'][$k]['enddate'] = $old['CallListsSchedule']['enddate_f'];
                $schedule = $this->_getSchedule($old['CallListsSchedule'][$k], $this->php_daysofweek);
    
          // log the deletion so that we can recover if needed
          $description = 'On-Call List \'' .$old['CallList']['title'] . '\' deleted<br>' . $schedule;
          $e['call_list_id'] = $old['CallListsSchedule'][$k]['id'];
          $this->_saveChanges($description, serialize($data), '', $old['CallList']['account_id'], $old['CallListsSchedule'][$k]['did_id'], 'oncall', 'delete', $e);    
        }
        else {
          $save_ok = false;
          $this->Session->setFlash(__('Cannot delete one or more on-call schedules, please try again later'), 'flash_jsonbad');
              $this->render('/Elements/json_result');
        }
      }
      // if all schedules have been deleted, delete the call list entry itself
      if ($save_ok) {
        $data = $old['CallList'];
        $old['CallList']['deleted'] = '1';
        $old['CallList']['deleted_ts'] = $del_date;
        if ($this->CallList->save($old['CallList'])) {
          $this->Session->setFlash(__('The on-call list has been deleted'), 'flash_jsongood');
              $this->render('/Elements/json_result');
        }
        else {
          $this->Session->setFlash(__('Cannot delete the on-call list, please try again later'), 'flash_jsonbad');
              $this->render('/Elements/json_result');
        }
         
      }
    }
  }

    public function delete($id = null) {
      $old = $this->CallList->CallListsSchedule->findById($id);
      $data['CallListsSchedule']['id'] = $id;
      $data['CallListsSchedule']['deleted'] = '1';
      $data['CallListsSchedule']['deleted_ts'] = date('Y-m-d H:i:s');
      
        if ($this->CallList->CallListsSchedule->save($data['CallListsSchedule'])) {
            $this->Session->setFlash(__('The list has been deleted'), 'flash_jsongood');
            $old['CallListsSchedule']['starttime'] = $old['CallListsSchedule']['start_time_f'];
            $old['CallListsSchedule']['endtime'] = $old['CallListsSchedule']['end_time_f'];
            $old['CallListsSchedule']['startdate'] = $old['CallListsSchedule']['startdate_f'];
            $old['CallListsSchedule']['enddate'] = $old['CallListsSchedule']['enddate_f'];
            $schedule = $this->_getSchedule($old['CallListsSchedule'], $this->php_daysofweek);

      $description = 'On-Call List \'' .$old['CallList']['title'] . '\' deleted<br>' . $schedule;

      $e['call_list_id'] = $old['CallListsSchedule']['id'];
          
      $this->_saveChanges($description, serialize($old), '', $old['CallList']['account_id'], $old['CallListsSchedule']['did_id'], 'oncall', 'delete', $e);     
                            
            
            // check if call list is now empty without any active schedules, if so, then delete the call list
            $found = $this->CallList->CallListsSchedule->find('all', array('conditions' => array('CallListsSchedule.deleted' => '0', 'CallListsSchedule.call_list_id' => $old['CallList']['id'])));
            $data = array();
            if (!$found) {
          $data['CallList']['id'] = $old['CallList']['id'];
          $data['CallList']['deleted'] = '1';
          $data['CallList']['deleted_ts'] = date('Y-m-d H:i:s');              
          $this->CallList->save($data['CallList']);
            }
            
        }
        else $this->Session->setFlash(__('Cannot delete list, please try again later'), 'flash_jsonbad');
        $this->render('/Elements/json_result');
    }
    
    public function recover($id = null) {
      $this->loadModel('DidNumbersEdit');
      $recovery_data = $this->DidNumbersEdit->findById($id);
      if ($recovery_data) {
        $data = unserialize($recovery_data['DidNumbersEdit']['old_values']);
        
        $call_list = $this->CallList->find('first', array('conditions' => array('CallList.id' => $data['CallListsSchedule']['call_list_id'])));
        // see if we need to recreate the CallList entry in addition to the CallListsSchedule
        $save_ok = true;
        if ($call_list && $call_list['CallList']['deleted']) {
          $d['CallList']['id'] = $data['CallListsSchedule']['call_list_id'];
          $d['CallList']['deleted'] = 0;
          $d['CallList']['deleted_ts'] = null;
          
          $save_ok = $this->CallList->save($d['CallList']);
      }

      if ($save_ok) {
          if ($this->CallList->CallListsSchedule->find('first', array('conditions' => array('CallListsSchedule.id' => $data['CallListsSchedule']['id'], 'CallListsSchedule.deleted' => '0')))) {
          $this->Session->setFlash(__('ERROR: This call list already exists, cannot overwrite an existing call list'), 'flash_jsonbad');                   
          $this->render('/Elements/json_result');    
          return;
          }
          $data['CallListsSchedule']['deleted'] = 0;
          $data['CallListsSchedule']['deleted_ts'] = null;        
          $ok = $this->CallList->CallListsSchedule->save($data['CallListsSchedule']);
          if ($ok) {
                $data['CallListsSchedule']['starttime'] = $data['CallListsSchedule']['start_time_f'];
                $data['CallListsSchedule']['endtime'] = $data['CallListsSchedule']['end_time_f'];
                $data['CallListsSchedule']['startdate'] = $data['CallListsSchedule']['startdate_f'];
                $data['CallListsSchedule']['enddate'] = $data['CallListsSchedule']['enddate_f'];
                $schedule = $this->_getSchedule($data['CallListsSchedule'], $this->php_daysofweek);
    
          $description = 'On-Call List \'' .$data['CallList']['title'] . '\' recovered<br>' . $schedule;
            
          $e['call_list_id'] = $data['CallListsSchedule']['id'];
          $this->_saveChanges($description, '', serialize($data), $call_list['CallList']['account_id'], $data['CallListsSchedule']['did_id'], 'oncall', 'recover', $e);    
            
          $this->Session->setFlash(__('On-call list has been recovered'), 'flash_jsongood');            
        }
        else {
          $this->Session->setFlash(__('Information cannot be recovered'), 'flash_jsonbad');            
        }
        $this->render('/Elements/json_result');        
      }
      else {
        $this->Session->setFlash(__('Information cannot be recovered'), 'flash_jsonbad');            
        $this->render('/Elements/json_result');        
      }
      }      
    }
    
    public function setHide($id, $status) {
      if (empty($id) || !isset($status)) {
      $this->Session->setFlash(__('On-call list status cannot be set'), 'flash_jsonbad');            
      $this->render('/Elements/json_result');        
      return;
      }
      $data['id'] = $id;
      $data['hide_from_operator'] = $status;
      if ($this->CallList->save($data)) {
      $this->Session->setFlash(__('On-call list status was set'), 'flash_jsongood');            
        $this->CallList->id = $id;
      $did_id = $this->CallList->field('did_id'); 
      $this->clearDidCache($did_id);    
      
      }
      else {
      $this->Session->setFlash(__('On-call list status cannot be set'), 'flash_jsonbad');            
      }
    $this->render('/Elements/json_result');        
    }
}
