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
 * Users Controller
 *
 * @property User $User
 */
class UserLogsController extends AppController {
  public $components = array('RequestHandler');
  public $helpers = array('Js');
  public $paginate;
	public function beforeFilter() {
    parent::beforeFilter();
    $this->Auth->allow('index', 'add', 'edit'); // Letting users register themselves
	}
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->UserLogs->recursive = 0;
		$this->paginate['limit'] = 200;
    $this->paginate['conditions'] = array();
    $this->paginate['order'] = array('id' => 'desc');
    $log = $this->paginate();
    
    $this->set('log', $log);
	}


  public function events($operator_id = null) {
      if (!isset($this->request->data['Search']['inactive'])) $this->request->data['Search']['inactive'] = '30';
  	  $this->set('inactive_period', $this->request->data['Search']['inactive']);
      
  		if ((!empty($this->request->data['Search']['user_id']) || !empty($this->request->data['Search']['extension']))&& !empty($this->request->data['Search']['report_date'])) {
  		  $user_id = trim($this->request->data['Search']['user_id']);
  		  $extension = trim($this->request->data['Search']['extension']);
  		  if ($user_id) {
  		    $cond = "user_id = '$user_id'";
  		    $cond2 = "e.user_id = '$user_id'";
  		  }
  		  else {
  		    $cond = "extension = '$extension'";
  		    $cond2 = "e.extension = '$extension'";
  		  }
  		  $report_date = trim($this->request->data['Search']['report_date']);
  		  $types = array(EVENT_TEXT, EVENT_CALLSTART, EVENT_ACTIONCLICK, EVENT_DIALOUT, EVENT_DELIVERY, EVENT_TRANSFER, EVENT_PATCH, EVENT_HANGUP, EVENT_CALLEND);

  		  //if ($this->getLoginRole() == 'Superuser' || $this->getLoginRole() == 'Administrator') {
  		  if (1) {
  		  
          $sql = "select all_events.*, u.username, UNIX_TIMESTAMP(all_events.created) as created_ts, DATE_FORMAT(all_events.created, '%a %c/%d/%y %l:%i:%s %p') as created_f from (
        select '' as account_name, '' as account_num, '' as button_data, `created`, `type`, `log_type`, '' as event_type, `extension`, `break_reason`, `break_end`, '' as description, user_id from ".OA_TBL_PREFIX."user_log where $cond and created >= '$report_date 00:00:00' and created <= '$report_date 23:59:59'
        union all
        select a.account_name, a.account_num, e.button_data as button_data, e.created, '' as type, '' as log_type, e.event_type, e.extension, '' as break_reason, '' as break_end, e.description, e.user_id from ".OA_TBL_PREFIX."call_events e left join ".OA_TBL_PREFIX."call_logs c on c.id=e.call_id left join ".OA_TBL_PREFIX."accounts a on a.id=c.account_id where $cond2  and e.created >= '$report_date 00:00:00' and e.created <= '$report_date 23:59:59' ) all_events left join ".OA_TBL_PREFIX."users u on u.id=all_events.user_id order by created asc limit 5000";
  		  }
  		  else {
          $sql = "select all_events.*, u.username, DATE_FORMAT(all_events.created, '%a %c/%d/%y %l:%i:%s %p') as created_f from (
        select '' as account_name, '' as account_num, '' as button_data `created`, `type`, `log_type`, '' as event_type, `extension`, `break_reason`, `break_end`, '' as description, user_id from ".OA_TBL_PREFIX."user_log where $cond and created >= '$report_date 00:00:00' and created <= '$report_date 23:59:59'
        union all
        select a.account_name, a.account_num, e.button_data as button_data, e.created, '' as type, '' as log_type, e.event_type, e.extension, '' as break_reason, '' as break_end, e.description, e.user_id from ".OA_TBL_PREFIX."call_events e left join ".OA_TBL_PREFIX."call_logs c on c.id=e.call_id left join ".OA_TBL_PREFIX."accounts a on a.id=c.account_id where $cond2 and e.created >= '$report_date 00:00:00' and e.created <= '$report_date 23:59:59' and event_type in (".implode(',', $types) .")) all_events left join ".OA_TBL_PREFIX."users u on u.id=all_events.user_id order by created asc limit 5000";
        }
        $this->set('log', $this->UserLog->query($sql));
  		  $this->render('operator_events');
  		}
      else {
        $this->paginate['conditions'] = array();
  			if (!empty($this->request->data['Search']['user_id'])) {
  			  $this->paginate['conditions'][] = array('user_id' =>  $this->request->data['Search']['user_id']);
  			}
  			else if (!empty($this->request->data['Search']['extension'])) {
  			  $this->paginate['conditions'][] = array('UserLog.extension' =>  $this->request->data['Search']['extension']);
  			}  				
  			if (!empty($this->request->data['Search']['report_date'])) {
  			  $report_date = $this->request->data['Search']['report_date'];
  			  $this->paginate['conditions'][] = array('UserLog.created >=' => $report_date . " 00:00:00", 'UserLog.created <=' => $report_date . " 23:59:59");
  			}
        $this->UserLog->bindModel(array(
        'belongsTo' => array(
          'User' => array(
    		    'className' => 'User',
    			  'foreignKey' => 'user_id'
         ))
        ));	    			
  			$this->paginate['order'] = array('created' => 'desc');
  			$this->paginate['limit'] = '200';
  			$this->paginate['fields'] = array('UserLog.*', 'User.username', "DATE_FORMAT(UserLog.created, '%a %c/%d/%y %l:%i %p') as created_f");
  			$data = $this->paginate();
  			$this->set('log', $data);
  			//fb($data);
  		  $this->render('events_list');
  		}
		
  }
    
  public function breaks() {
    $csv = false;
	  if (!empty($this->request->data['format']) && $this->request->data['format'] == 'csv') {
		    $this->paginate['limit'] = 5000;
		    $csv = true;
	  }     
 		$this->UserLog->bindModel(
      array(
        'belongsTo' => array('User')
      )
  	);	  	
  	if (empty($this->request->data['Search']['start_date'])) {
  	  $this->request->data['Search']['end_date'] = date('Y-m-d');
  	  $this->request->data['Search']['start_date'] = date('Y-m-d', strtotime('-7 day'));
  	}
  	$start_date = $this->request->data['Search']['start_date'];
  	$end_date = $this->request->data['Search']['end_date'];
  	$this->set('start_date', $start_date);
  	$this->set('end_date', $end_date);
  	$conditions = array('UserLog.type' => USEREVT_BREAK, 'UserLog.created >=' => $start_date . ' 00:00:00', 'UserLog.created <=' => $end_date . '23:59:59');
  	$fields = array('User.id', 'User.username','UserLog.break_reason', 'TIME_TO_SEC(TIMEDIFF(UserLog.break_end, UserLog.created)) as break_len');
  	$this->paginate['conditions'] = $conditions;
  	$this->paginate['limit'] = '30000';
  	$this->paginate['order'] = array('User.firstname' => 'asc', 'User.lastname' => 'asc');
  	$this->paginate['fields'] = $fields;
    $data = $this->paginate();

    foreach ($data as $d) {
      if (!isset($breaks[$d['User']['id']]['breaks'])) $breaks[$d['User']['id']]['breaks'] = array();
      $breaks[$d['User']['id']]['breaks'][] = $d;
    }
    
    $break_reasons = Configure::read('break_reasons');
    $this->set('break_reasons', $break_reasons);
    
    foreach ($breaks as $k => $b) {
      $total = $unknown_duration = 0;
      
      // initialize break count and duration for each break type     
      foreach($break_reasons as $k2 => $r) {
        $reason[$k2] = 0;
        $duration[$k2] = 0;
      }
      
      foreach ($b['breaks'] as $d) {
      	$d[0]['break_num'] = 1;
        if (empty($d['UserLog']['break_end'])) {
          $unknown_duration++;
        }
        else {
          $total += $d[0]['break_len'];
        }
        
        // add up the break count and duration for each type of break
        $idx = array_search($d['UserLog']['break_reason'], $break_reasons);        
        $idx_other = array_search('Other', $break_reasons);        
        if ($idx !== false) {
          $reason[$idx] += $d[0]['break_num'];
          $duration[$idx] += $d[0]['break_len'];
        }
        // if break type is not defined in the config file then record it as 'Other'
        else {
          $reason[$idx_other] += $d[0]['break_num'];
          $duration[$idx_other] += $d[0]['break_len'];
        }
      }
      
      // save the count and duration of each break type for the user
      foreach($break_reasons as $k2 => $r) {
        $breaks[$k]['reason'][$k2] = $reason[$k2];
        $breaks[$k]['duration'][$k2] = $this->formatDuration($duration[$k2], ':', true);
      }
            
      $breaks[$k]['count'] = sizeof($b['breaks']);
      $breaks[$k]['total'] = $this->formatDuration($total, ':', true);
      $breaks[$k]['unknown_duration'] = $unknown_duration;
      
    }
    $this->loadModel('User');
    $this->set('operators', $this->User->getCCStaff());
    //fb($breaks); exit;
    if ($csv) {
      $row = array("Username", "Total Breaks", "Total Break Length", 'Unknown Duration');
      foreach($break_reasons as $k2 => $r) {
        $row[] = str_replace('"', '', $r);
        $row[] = str_replace('"', '', $r) . ' Length';
      }
      
      $rows[] = $row;
      foreach ($breaks as $k => $b) {
        $row = array($b['breaks'][0]['User']['username'], $b['count'], $b['total'], $b['unknown_duration']);
        foreach($break_reasons as $k2 => $r) {
          $row[] = $b['reason'][$k2];
          $row[] = $b['duration'][$k2];
        }
      	$rows[] = $row;
        
      }
      header("Content-type: text/csv"); 
      header("Content-Disposition: attachment; filename=breaks.csv");
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
    else $this->set('breaks', $breaks);
  }  

  function break_log($user_id, $start_date, $end_date) {
  		  $types = array(EVENT_TEXT, EVENT_CALLSTART, EVENT_ACTIONCLICK, EVENT_DIALOUT, EVENT_DELIVERY, EVENT_TRANSFER, EVENT_PATCH, EVENT_HANGUP, EVENT_CALLEND);
    
        $sql = "select u.username, DATE_FORMAT(l.created, '%a %c/%d/%y %l:%i:%s %p') as created_f, l.*, TIME_TO_SEC(TIMEDIFF(break_end, l.created)) as break_len from ".OA_TBL_PREFIX."user_log l left join ".OA_TBL_PREFIX."users u on u.id=l.user_id where l.created >= '$start_date 00:00:00' and l.created <= '$end_date 23:59:59' and l.user_id='$user_id' order by l.created asc limit 1000";
        
        $this->set('log', $this->UserLog->query($sql));
  }
  
  function daily($user_id) {
    $this->layout = 'ajax';
    $min_date = date('Y-m-d', strtotime('-30 day'));
    $today = date('Y-m-d');
    $days = array();
    $days_length = array();
    for ($i = 30; $i>=0; $i--)
    {
      $temp = date('D n/j', strtotime("-$i day"));
      $days[$temp] = 0;
      $days_length[$temp] = 0;
    }
    
    $break_reasons =  Configure::read('break_reasons');
    $personal_idxs =  Configure::read('personal_break_reason_idx');
    
    foreach ($personal_idxs as $k) {
      $ors[] = "break_reason='".$break_reasons[$k]."'";
    }
        
    $sql = "select count(*) as cnt, DATE_FORMAT(DATE(created),'%a %c/%e') as date_created, sum(TIME_TO_SEC(TIMEDIFF(break_end, created))) as break_len from ".OA_TBL_PREFIX."user_log m where m.created >= '$min_date 00:00:00' and m.created <= '$today 23:59:59' and m.created >= '$min_date 00:00:00' and m.user_id='$user_id' and (".implode(' OR ', $ors).") group by DATE(created)";

    $data = $this->UserLog->query($sql);
    foreach ($data as $r) {
      $days[$r[0]['date_created']] = $r[0]['cnt']; 
      $days_length[$r[0]['date_created']] = round($r[0]['break_len']/60); 
    }
    $this->set('breaks', $days);
    $this->set('breaks_length', $days_length);
    $this->set('oa_title', 'Personal Breaks by the day');
    
  }

  function my_daily($user_id) {
    $this->daily($user_id);
    $this->render('daily');
  }   
}
