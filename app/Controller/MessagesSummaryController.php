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
class MessagesSummaryController extends AppController {
  public $paginate;
  
  function beforeFilter() {
    parent::beforeFilter();
    if ($this->isAuthorized('MessagessummaryDebug')) {
      $this->set('interval_options', array('15' => 'Every 15 min', '30' => 'Every 30 min', '60' => 'Every hour', '0' => 'Once a day', '5' => 'Every 5 min (TESTING only)'));
    }
    else {
      $this->set('interval_options', array('15' => 'Every 15 min', '30' => 'Every 30 min', '60' => 'Every hour', '0' => 'Once a day'));
    }
    $this->set('msg_options', array('1' => 'Undelivered Messages Only', '2' => 'All Messages'));
    $this->Auth->allow('msg_summary_execute');
  }
/**
 * index method
 *
 * @return void
 */
	public function index($did_id) {
	  if (!$did_id) {
			$this->Session->setFlash(__('You must specify a DID'), 'flash_jsonbad');
      $this->render('/Elements/json_result');
	  }
	  
	  // get list of employees
    $query = "select e.id, c.contact, c.contact_type, c.id, c.label  from ".OA_TBL_PREFIX."employees_contacts c left join ".OA_TBL_PREFIX."employees e on c.employee_id=e.id where c.did_id='$did_id' and c.flag='0' and (contact_type='".CONTACT_EMAIL."' OR contact_type='".CONTACT_FAX."')";
    $this->loadModel('Employee');
    $data = $this->Employee->query($query);
    $employee_contact_ids = array();
    foreach ($data as $d) {
      $employee_contact_ids[$d['c']['id']] = $d;
    }
    $this->set('contact_ids', $employee_contact_ids);
    
    
    // get client timezone
	  $this->loadModel('DidNumber');
	  $this->DidNumber->id = $did_id;
	  $client_timezone = $this->DidNumber->field('timezone');		
    if (!$client_timezone) $client_timezone = Configure::read('default_timezone');
    $oa_timezone = Configure::read('default_timezone');
    $this->set('timezone', $this->global_options['timezone'][$client_timezone]);
    
    // get list of message summaries for the subaccount	  
	  $this->set('did_id', $did_id);
	  $conditions = array('did_id' => $did_id, 'deleted' => '0');
	  $this->paginate['conditions'] = $conditions;
		$this->MessagesSummary->recursive = 0;
		$data = $this->paginate();

		// reformat dates
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
		$this->set('MessagesSummary', $data);
	}
	
  // the following function is run as a system cron task
  public function msg_summary_execute($interval=null) {
    
    $output = '';
    
    
    $offset_seconds = 120;  // number of seconds to offset the date query
    $delta_minutes = 4;
    
    
    if ($interval == null) $interval = '';
    $output .= "\r\n\r\nSTART " . date('Y-m-d H:i:s') . " $interval\r\n";
		$this->layout = "plain";
		$now_ts = time();
		
		// get list of available OA timezones
		$timezones = $this->global_options['timezone'];
    $oa_timezone = Configure::read('default_timezone');		
    $this->loadModel('MessagesSummarySent');    
    $this->loadModel('MessagesSummaryLog');
    $this->loadModel('MessagesDelivery');
    $this->loadModel('Message');
    
    //loop through each timezone and find all the message summaries we need to run
		foreach ($timezones as $timezone => $val) {
      $date1 = new DateTime();
      $date1->setTimezone(new DateTimeZone($timezone));      
      $now_time = $date1->format('H:i:s');
      $now_date = $date1->format('Y-m-d');
      $now_mysql = $date1->format('Y-m-d H:i:s');
      $now_datetime = $date1->format('m/d/Y g:i a');
      $this->set('now_time', $now_datetime);
      $field_today = strtolower($date1->format('D'));  
      $today = $date1->format('Y-m-d 00:00:00');

      // to make sure query includes the end time, subtract 2 minutes from ending time for query
      $date1->setTimestamp($now_ts - $offset_seconds);      
      $now_time_minus_two = $date1->format('H:i:s');
      
      // get all matching entries for the specified interval, executed as cron task running at various intervals
      if ($interval) {
        $query = "SELECT a.account_num, d.include_cid, d.timezone, d.did_number, d.email_format, d.company, s.* FROM ".OA_TBL_PREFIX."messages_summary s LEFT JOIN ".OA_TBL_PREFIX."did_numbers d ON d.id=s.did_id LEFT JOIN ".OA_TBL_PREFIX."accounts a ON d.account_id=a.id WHERE s.tx_interval='$interval' AND s.deleted='0' AND s.active='1' AND s.{$field_today}='1' AND d.timezone='$timezone' AND d.deleted='0' and d.status='1' AND ((start_time <='$now_time' AND end_time >= '$now_time_minus_two') OR all_day='1')";
  
      }
      // check for entries that are run at a specified time during the day
      else {
          $query = "select  a.account_num, d.include_cid, d.timezone, d.did_number, d.email_format, d.company, s.* from ".OA_TBL_PREFIX."messages_summary s left join ".OA_TBL_PREFIX."did_numbers d on d.id=s.did_id left join ".OA_TBL_PREFIX."accounts a on d.account_id=a.id where TIMEDIFF(send_time, TIME('$now_mysql')) < 0 AND all_day='0' and d.timezone='$timezone' and s.active='1' and s.deleted='0' and send_time is not null and (last_run < '$today' or last_run is null) and d.deleted='0' and d.status='1' and $field_today='1'";     
      }
$output .= '    ' . $query . "\r\n\r\n<br><br>";  
      $this->MessagesSummary->cacheQueries=false;
      $data = $this->MessagesSummary->query($query); 
      
      // go through each active message summary entry found
      foreach($data as $d) {
        $output .= ' CHECKING: ' . $d['a']['account_num'] . "\r\n";
      	$timezone = $d['d']['timezone'];
  
        $s = $d['s'];
        $schedule_id = $s['id'];
  
  
        $did_id = $s['did_id'];
        $account_num = $d['a']['account_num'];
        $did_number = $d['d']['did_number'];
        $include_cid = $d['d']['include_cid'];
        $company = $d['d']['company'];
        $email_format = ($d['d']['email_format'] == '0')? 'both': 'text';
        
        if ($did_id) {
        	if ($s['last_sent'] == null || $s['last_sent'] == '0000-00-00 00:00:00') {
        		$last_sent = $s['created'];
        	}
        	else {
        	  if ($s['created'] > $s['last_sent']) {
        	    $last_sent = $s['created'];
        	  }
        	  else {
        	    $ts_last_sent = strtotime($s['last_sent']);
        	    $ts_last_sent = $ts_last_sent - ($delta_minutes *60);
        	    
        	    $last_sent = date('Y-m-d H:i:s', $ts_last_sent);
        	    echo "LAST SENT**************** " . $s['last_sent'] . ' adjusted: ' . $last_sent;
        	  }
        	}
          $joins = array(
            array(
              'table' => OA_TBL_PREFIX . 'messages_summary_sent',
              'alias' => 'MessagesSummarySent',
              'type' => 'left',
              'conditions' => array('`MessagesSummarySent`.`message_id` = `Message`.`id`', '`MessagesSummarySent`.`messages_summary_id`'=> $schedule_id)
            )
          );
        	
          // fetch messages for the phone number, make sure to only get messages that are not marked for dispatch (minder=0)

          // need to screen out newly created messages so that they can finish before sending out, so set a 10-min delay for now
          $ts_delta_min_ago = strtotime('-'.$delta_minutes.' min');
          $time_delta_min_ago = date('Y-m-d H:i:s', $ts_delta_min_ago);
          if ($s['msg_type'] == '1') {  //undelivered only
            $conditions = array('Message.did_id' => $did_id, 'Message.hold <> ' =>'2', 'Message.minder' =>'0', 'Message.delivered' =>'0', 'Message.summary_last_sent' =>  null, "Message.active_ts >= '$last_sent'", "Message.active_ts <= '$time_delta_min_ago'");
            $conditions2 = array('Message.did_id' => $did_id, 'Message.hold <> ' =>'2', 'Message.minder' =>'0', 'Message.delivered' =>'0', 'MessagesSummarySent.summary_last_sent' =>  null, "Message.active_ts >= '$last_sent'", "Message.active_ts <= '$time_delta_min_ago'");
          }
          else if ($s['msg_type'] == '2') {  //all messages
             $conditions = array('Message.did_id' => $did_id, 'Message.hold <> ' =>'2', 
              'Message.summary_last_sent' => null,  //include only messages that haven't been delivered in summary beforehand
              'Message.minder' =>'0', 
              "Message.active_ts >= '$last_sent'",
              "Message.active_ts <= '$time_delta_min_ago'"  );
             $conditions2 = array('Message.did_id' => $did_id, 'Message.hold <> ' =>'2', 
              'MessagesSummarySent.summary_last_sent' => null,  //include only messages that haven't been delivered in summary beforehand
              'Message.minder' =>'0', 
              "Message.active_ts >= '$last_sent'",
              "Message.active_ts <= '$time_delta_min_ago'"  );
          }
  
			


          $fields = array('Message.summary_last_sent', 'Message.call_id', 'CallLog.cid_number', 'Message.calltype', 'Message.created', 'Message.delivered');
          $fields2 = array('MessagesSummarySent.summary_last_sent', 'Message.call_id', 'CallLog.cid_number', 'Message.calltype', 'Message.created', 'Message.delivered');
          $output .= "    LAST SENT DATE: $last_sent $time_delta_min_ago\r\n";
          
          $this->Message->recursive = 1;
       		// unbind unnecessary model
       		/*$this->Message->unbindModel(
            array(
              'hasMany' => array('Mistake', 'Complaint')
            )
        	);	          
          $messages2 = $this->Message->find('all', array('conditions' => $conditions, 'fields' => $fields));*/
       		// unbind unnecessary model
       		$this->Message->unbindModel(
            array(
              'hasMany' => array('Mistake', 'Complaint')
            )
        	);	
        	$messages = $this->Message->find('all', array('conditions' => $conditions2, 'joins' => $joins, 'fields' => $fields2));
					$output .= "    Found " . count($messages) . " messages\r\n";
//					$output .= "    Found(2) " . count($messages2) . " messages\r\n";
					$output .= "\r\n    " . print_r($conditions, true);
					$output .= "\r\n    " . print_r($conditions2, true);
					$output .= "\r\n    " . print_r($messages, true);
//					$output .= "\r\n    " . print_r($messages2, true);
          foreach ($messages as $km => $m) {
            $appts = $this->_get_appointments($m['Message']['call_id']);
 			      $date = new DateTime();
 			      $date->setTimestamp(strtotime($m['Message']['created']));
 			      $date->setTimezone(new DateTimeZone($timezone));
  			    $messages[$km]['Message']['created'] = $date->format('m/d/Y g:i a');	      
  			    $messages[$km]['appointments'] = $appts;

          	foreach ($m['MessagesDelivery'] as $k => $delivery) {
          		// convert delivery time to client's timezone
  			      $date = new DateTime();
  			      $date->setTimestamp(strtotime($delivery['delivered_time']));
  			      $output .= '    ' . $timezone . "\r\n";
  			      $date->setTimezone(new DateTimeZone($timezone));
  			     	$messages[$km]['MessagesDelivery'][$k]['delivered_time'] = $date->format('m/d/Y g:i a');	        
  			     	$output .= '    ' . $date->format('m/d/Y g:i a') . "\r\n";   		
          	}
          }
          $view_output = '';
          $send_no_message_notification = false;
          
          // if no messages were found, check if we need to send a 'no-message' notification
          if (!$messages && $s['no_message']) {
   					if ($s['no_message_type'] == '0') {    // beginning and end of scheduled time
   						// 
 						  $next_ts = $now_ts + ($interval*60);
 						  $prev_ts = $now_ts - ($interval*60);

   						if ($s['all_day']) {
   						  // send only if last broadcast of the day.
                $date2 = new DateTime();
                $date2->setTimezone(new DateTimeZone($timezone));       
                $date2->setTimestamp($next_ts);
                $day_of_next_run = strtolower($date2->format('D'));				
                $output .= "    ALL DAY: next run day " . $day_of_next_run . " today: $field_today\r\n";
                if ($day_of_next_run != $field_today) {
                  if ($s['last_run'] < ($now_date . ' 00:00:00')) {
                    $send_no_message_notification = true;		  
                  }                  
                }
   						}
   						else {
   						  // only sending out once a day, so send no-message notification
   						  if ($s['send_time']) {
   						    $send_no_message_notification = true;
   						  }
   						  else {
     						  // check for beginnng and end of scheduled period
                  $date2 = new DateTime();
                  $date2->setTimezone(new DateTimeZone($timezone));       
                  $date2->setTimestamp($next_ts);
                  $time_next_run = strtolower($date2->format('Y-m-d H:i:s'));				
                  $date2->setTimestamp($prev_ts);
                  $time_prev_run = strtolower($date2->format('Y-m-d H:i:s'));				
  
                  // check if last run of the day
                  $end_time = $now_date . ' ' . $s['end_time'];
                  $start_time = $now_date . ' ' . $s['start_time'];
  							  $output .= "    CHECKING: next run End Time: $end_time, Next Run:$time_next_run, Prev run: $time_prev_run, Next run:  $time_next_run, Start time:  $start_time, Last Run: " . $s['last_run'] . ", Last Sent: " . $s['last_sent'] . " \r\n";
          $joins = array(
            array(
              'table' => OA_TBL_PREFIX . 'messages_summary_sent',
              'alias' => 'MessagesSummarySent',
              'type' => 'left',
              'conditions' => array('`MessagesSummarySent`.`message_id` = `Message`.`id`', '`MessagesSummarySent`.`messages_summary_id`'=> $schedule_id)
            )
          );  							  
  							  //check if the date/time of the next run is > ending date/time to see if this is the last run of the day
                  if (substr($time_next_run, 0,16) > substr($end_time, 0, 16)) {
                    if ($s['msg_type'] == '1') {  //undelivered only
                      $conditions = array('Message.did_id' => $did_id, 'Message.hold <> ' =>'2', 'Message.minder' =>'0', 'Message.delivered' =>'0', 'Message.summary_last_sent ' =>  null, "Message.active_ts>= '$start_time'", "Message.active_ts <= '$end_time'");
                      $conditions2 = array('Message.did_id' => $did_id, 'Message.hold <> ' =>'2', 'Message.minder' =>'0', 'Message.delivered' =>'0', 'MessagesSummarySent.summary_last_sent ' =>  null, "Message.active_ts>= '$start_time'", "Message.active_ts <= '$end_time'");
                    }
                    else if ($s['msg_type'] == '2') {  //all messages
                       $conditions = array('Message.did_id' => $did_id, 'Message.hold <> ' =>'2', 
                        'Message.summary_last_sent' => null,  //include only that haven't been delivered in summary beforehand
                        'Message.minder' =>'0', 
                        "Message.active_ts >= '$start_time'",
                        "Message.active_ts <= '$end_time'"  );
                       $conditions2 = array('Message.did_id' => $did_id, 'Message.hold <> ' =>'2', 
                        'MessagesSummarySent.summary_last_sent' => null,  //include only that haven't been delivered in summary beforehand
                        'Message.minder' =>'0', 
                        "Message.active_ts >= '$start_time'",
                        "Message.active_ts <= '$end_time'"  );
                    }                    
                    $output .= print_r($conditions, true);
                    $msgs2 = $this->Message->find('all', array('conditions' => $conditions, 'recursive' => 0));
                    $msgs1 = $this->Message->find('all', array('conditions' => $conditions2, 'joins' => $joins, 'recursive' => 0));
                    $output .= "\r\n    END of day check";
                    $output .= "\r\n    " . print_r($msgs1, true);
                    $output .= "\r\n    " . print_r($msgs2, true);
                    if (!$msgs1) {
                      $send_no_message_notification = true;		  
                    }                  
  									$output .= "    LAST RUN: next run $end_time $time_next_run, Prev run: $time_prev_run ".substr($time_next_run, 0,16)." " . substr($end_time, 0, 16) . " \r\n"; 
  								}
                  
                  // check if day of previous run was yesterday to see if it is the first run
                  if ($time_prev_run < $start_time) {
                  	$send_no_message_notification = true;
  									$output .= "    FIRST RUN: next run $start_time $time_next_run, Prev run: $time_prev_run ".substr($time_next_run, 0,16)." " . substr($start_time, 0, 16) . " \r\n";
  									
  								}
							  }
   						  
   						}
   					}     	
   					else if ($s['no_message_type'] == '1') {    // as scheduled
   					  $send_no_message_notification = true;
   						$output .= "    AS SCHEDULED";
   					}  
  	
          }
          
          $save_ok = true;
          if ($send_no_message_notification) {
            if ($s['last_sent'] < $first_sent) {
            }
          }
if ($send_no_message_notification) $output .= "    MUST SEND no-message notification\r\n";     
else  $output .= "    NOT SENDING no-message notification\r\n";      
//          $destinations_email = array();
//          $destinations_fax = array();
          $destination_emails = array();
          $destination_faxes = array();
          
          
          // check if imported TAS destinations (VN-specific legacy field) are present
          if (trim($s['destination_email'])) {
            $s['destination_email'] = str_replace(' ', '', $s['destination_email']);
            $destination_emails = explode(';', $s['destination_email']);
             
          }
          if (trim($s['destination_fax'])) {
            $destination_faxes[] = $s['destination_fax'];          
          }
          
          // get info for employee contacts specified as destination (fax/email, name)
          if (trim($s['employee_contact_ids'])) {
            $this->loadModel('Employee');
            $temp_ids = explode(',', $s['employee_contact_ids']);
            $temp_array = array();
            foreach ($temp_ids as $t) {
            	if (trim($t)) {
            		$temp_array[] = $t;
            	}
            	else {
            	  App::uses('CakeEmail', 'Network/Email');		
        			  			
        			  CakeEmail::deliver(Configure::read('admin_email'), '[MessagesSummary execute] bad config', 'Schedule id: ' . $s['id'], 'admin');

            	}
            }
            
            $temp_ids = implode(',', $temp_array);
            try {
              $query = "select c.contact, c.contact_type, e.name, c.id from ".OA_TBL_PREFIX."employees_contacts c left join ".OA_TBL_PREFIX."employees e on e.id=c.employee_id where c.id in ($temp_ids)";
              $emps = $this->Employee->query($query);
        		} catch (Exception $e) {
      				App::uses('CakeEmail', 'Network/Email');		
        			  			
        			CakeEmail::deliver(Configure::read('admin_email'), '[MessagesSummary execute] bad query', 'Schedule id: ' . $s['id'] . ' ' . $query, 'admin');
        			echo '{ERROR] Bad query';exit;
      
        		}            
            foreach ($emps as $e) {
              if ($e['c']['contact_type'] == CONTACT_EMAIL) {
                $temp = explode(';', str_replace(' ', '', $e['c']['contact']));
                $destination_emails = array_merge($destination_emails, $temp);
              }
              else
                $destination_faxes[] = $e['c']['contact'];                                    
            }
            
          }
                    
          // send summary as email to all email destinations
          if (sizeof($destination_emails) > 0) {
            // generate email body by getting the output of a rendered view.
            if ($messages) {
              $this->autoRender = false;  // make sure controller doesn't auto render
             
              /* Set up new view that won't enter the ClassRegistry */
              $view = new View($this, false);
              $view->set('messages', $messages);
              $view->set('account_num', $account_num);
              $view->set('company', $company);
              $view->set('include_cid', $include_cid);
              $view->set('did_number', $did_number);
               
              /* Grab output into variable without the view actually outputting! */
              $view_output = $view->render('email_messages');      
              
              $view2 = new View($this, false);
              $view2->set('messages', $messages);
              $view2->set('account_num', $account_num);
              $view2->set('company', $company);
              $view2->set('include_cid', $include_cid);
              $view2->set('did_number', $did_number);
               
              /* Grab output into variable without the view actually outputting! */
              $view_output_text = $view2->render('email_messages_text');                
            }
            else if ($send_no_message_notification) {  // check if we need to send out a 'no-messages' email
              $this->autoRender = false;  // make sure controller doesn't auto render
             
              /* Set up new view that won't enter the ClassRegistry */
              $view = new View($this, false);
              $view->set('account_num', $account_num);
              $view->set('did_number', $did_number);             
              $view->set('company', $company);
              /* Grab output into variable without the view actually outputting! */
              $view_output = $view->render('email_nomsg');      

              $view2 = new View($this, false);
              $view2->set('messages', $messages);
              $view2->set('account_num', $account_num);
              $view2->set('company', $company);
              $view2->set('did_number', $did_number);
               
              /* Grab output into variable without the view actually outputting! */
              $view_output_text = $view2->render('email_nomsg_text');                

            }
            if ($view_output) {
          		if (!$this->_sendemail("Live Answering Message Summary - Account # $account_num", $view_output, $destination_emails, $email_format, $view_output_text)) {
          		  $save_ok = false;
          		}        		
            }
          }
          // send summary as fax to all fax destinations, insert into queue to be processed by the fax converter
          $view_output = '';        
          foreach ($destination_faxes as $destination) {
            
            // generate fax body by getting the output of a rendered view
            if ($messages) {
              $this->autoRender = false;  // make sure controller doesn't auto render
               
              /* Set up new view that won't enter the ClassRegistry */
              $view = new View($this, false);
              $view->set('messages', $messages);
              $view->set('recipient', $destination);
              $view->set('company', $company);
               
              /* Grab output into variable without the view actually outputting! */
              $view_output = $view->render('fax_messages');        
              
              
            }
            else if ($send_no_message_notification) {  // check if we need to send out a 'no-messages' email
              $this->autoRender = false;  // make sure controller doesn't auto render          
              $view = new View($this, false);
              /* Grab output into variable without the view actually outputting! */
              $view->set('recipient', $destination);
              $view->set('company', $company);
              $view_output = $view->render('fax_nomsg');      
            }
          }
          if ($view_output) {
            if ($did_number == null) $did_number = '';
          		if (!$this->_fax($view_output, $destination, $account_num, $did_number)) {
          		  $save_ok = false;
          		}
          }        
  
        }
        $msg_ids = array();      
        if ($save_ok) {
        	// update the last_sent field of the message summary
          if ($messages || $send_no_message_notification) {
        	  $sql = "update ".OA_TBL_PREFIX."messages_summary set last_sent='$now_mysql' where id='".$schedule_id."'";
        	  $this->MessagesSummary->query($sql);
        	  $delivery = array();
            // log message summary that just got sent out, mark all messages as 'delivered'
            foreach ($messages as $k => $m) {
              $output .= "    SETTING delivery for " . $m['Message']['id'] . "\r\n";
              $msg_ids[] = $m['Message']['id'];
        		  $delivery['delivered_time'] = date("Y-m-d H:i:s");
          		$delivery['delivery_name'] = implode(', ', $destination_emails) . ' ' . implode(', ', $destination_faxes);
          		$delivery['delivery_contact'] = implode(', ', $destination_emails) . '  ' . implode(', ', $destination_faxes);
          		$delivery['delivery_contact_id'] = $s['employee_contact_ids'];
          		$delivery['delivery_contact_label'] = '';
        		  $delivery['employee_id'] = '';
        		  $delivery['message_id'] = $m['Message']['id'];
        		  $delivery['delivered_by_userid'] = '0';
        		  $delivery['delivered_by_ext'] = '';
        		  $delivery['delivery_method'] = '';
        		  $delivery['hold'] = '0';
        			$msg = array();
        		  $msg['id'] = $m['Message']['id'];
        		  $msg['delivered'] = '1';
                          $msg['hold'] = '0';
        		  $msg['summary_last_sent'] = $now_mysql;
              $this->Message->recursive = 0;
        		  $this->Message->save($msg);
              $this->MessagesDelivery->create();
              $this->MessagesDelivery->save($delivery);
              
              $msgsent['summary_last_sent'] = $now_mysql;
              $msgsent['message_id'] = $m['Message']['id'];
              $msgsent['messages_summary_id'] = $schedule_id;
        		  $this->MessagesSummarySent->create();
        		  $this->MessagesSummarySent->save($msgsent);              
            }
          }
       	  $sql = "update ".OA_TBL_PREFIX."messages_summary set last_run='$now_mysql' where id='".$schedule_id."'";
       	  $this->MessagesSummary->query($sql);
          
          
          $log = $s;
          $log['message_summary_id'] = $s['id'];
          unset($log['id']);
          $log['message_ids'] = implode(',', $msg_ids);
          $log['summary_sent'] = date('Y-m-d H:i:s');
          if ($messages || $send_no_message_notification) $log['summary_sent_to'] = implode(', ', array_merge($destination_emails,$destination_faxes));
          if ($send_no_message_notification) $log['no_message_sent'] = '1';
          else $log['no_message_sent'] = '0';
          $output .= "    FINISH num of msgs " . $log['message_ids'] . "\r\n";
          $this->MessagesSummaryLog->create();
          $this->MessagesSummaryLog->save($log);
        }
      }		  
		}
    echo $output; exit;
  }
  
  public function send() {
    $this->loadModel('DidNumber');
    $this->DidNumber->recursive = 0;
    $did = $this->DidNumber->findById($this->request->data['did_id']);
    if (empty($this->request->data['Misc']['recipient'])) {
      $this->Session->setFlash('You need to specify at least one recipient', 'flash_jsonbad');
      $this->render('/Elements/json_result');
      return;
    }
    $msg_ids = $this->request->data['Misc']['selector']; // array of msg ids to send out
    
    $destination_emails = array();
    $destination_faxes = array();
    foreach($this->request->data['Misc']['recipient'] as $r) {
      if (is_numeric($r)) $destination_faxes[] = trim($r);
      else $destination_emails[] = trim($r);
    }
    
    
    $fields = array('Message.call_id', 'CallLog.cid_number', 'Message.calltype', 'Message.created', 'Message.delivered');
    $this->loadModel('Message');
    $this->Message->recursive = 1;
 		// unbind unnecessary model
 		$this->Message->unbindModel(
      array(
        'hasMany' => array('Mistake', 'Complaint')
      )
   	);	
   	$messages = $this->Message->find('all', array('conditions' => array('Message.id in ('.implode(',', $msg_ids).')'), 'fields' => $fields));    
    $timezone = $did['DidNumber']['timezone'];
    $date1 = new DateTime();
    $date1->setTimezone(new DateTimeZone($timezone));      
    $now_time = $date1->format('H:i:s');
    $now_date = $date1->format('Y-m-d');
    $this->set('now_time', $now_time);
    $this->set('now_date', $now_date);
    
    foreach ($messages as $km => $m) {
      $date = new DateTime();
      $date->setTimestamp(strtotime($m['Message']['created']));
      $date->setTimezone(new DateTimeZone($timezone));
	    $messages[$km]['Message']['created'] = $date->format('m/d/Y g:i a');	        
    	foreach ($m['MessagesDelivery'] as $k => $delivery) {
    		// convert delivery time to client's timezone
	      $date = new DateTime();
	      $date->setTimestamp(strtotime($delivery['delivered_time']));
	      $date->setTimezone(new DateTimeZone($timezone));
	     	$messages[$km]['MessagesDelivery'][$k]['delivered_time'] = $date->format('m/d/Y g:i a');	        
    	}
    }
    
    if (empty($messages)) {
      $this->Session->setFlash('Cannot retrieve messages', 'flash_jsonbad');
      $this->render('/Elements/json_result');
      return;
    }
    $save_ok = true;
    $include_cid = true;
    if (sizeof($destination_emails) > 0) {
//            $destination = $e['contact'];
//            $destination_emails[] = $destination;
      // generate email body by getting the output of a rendered view.
      if ($messages) {
        $this->autoRender = false;  // make sure controller doesn't auto render
       
        /* Set up new view that won't enter the ClassRegistry */
        $view = new View($this, false);
        $view->set('messages', $messages);
        $view->set('include_cid', $did['DidNumber']['include_cid']);
        $view->set('company', $did['DidNumber']['company']);
        $view->set('account_num', $did['Account']['account_num']);
         
        /* Grab output into variable without the view actually outputting! */
        $view_output = $view->render('email_messages');      
        
        $view2 = new View($this, false);
        $view2->set('messages', $messages);
        $view2->set('include_cid', $did['DidNumber']['include_cid']);
        $view2->set('company', $did['DidNumber']['company']);
        $view2->set('account_num', $did['Account']['account_num']);
         
        /* Grab output into variable without the view actually outputting! */
        $view_output_text = $view2->render('email_messages_text');                
      }
      if ($view_output) {
    		if (!$this->_sendemail("Live Answering Message Summary", $view_output, $destination_emails, 'both', $view_output_text)) {
    		  $save_ok = false;
    		}        		
      }
    }
    // send summary as fax to all fax destinations, insert into queue to be processed by the fax converter
    $view_output = '';        
    foreach ($destination_faxes as $destination) {
      
      // generate fax body by getting the output of a rendered view
      $this->autoRender = false;  // make sure controller doesn't auto render
       
      /* Set up new view that won't enter the ClassRegistry */
      $view = new View($this, false);
      $view->set('messages', $messages);
      $view->set('recipient', $destination);
      $view->set('include_cid', $did['DidNumber']['include_cid']);
      $view->set('company', $did['DidNumber']['company']);
      $view->set('account_num', $did['Account']['account_num']);
       
      /* Grab output into variable without the view actually outputting! */
      $view_output = $view->render('fax_messages');        
        
        
    }
    if ($view_output) {
    		if (!$this->_fax($view_output, $destination, $did['Account']['account_num'], '')) {
    		  $save_ok = false;
    		}
    }        
    if ($save_ok) {
      $this->Session->setFlash('Messages sent', 'flash_jsongood');
      $this->render('/Elements/json_result');
      
    }
    else {
      $this->Session->setFlash('Cannot send or more of your messages', 'flash_jsonbad');
      $this->render('/Elements/json_result');
    }
  }
         
  public function printmsg() {
    $this->layout = 'standalone';
    $this->loadModel('DidNumber');
    $this->DidNumber->recursive = 0;
    $did = $this->DidNumber->findById($this->request->data['did_id']);
    $msg_ids = $this->request->data['Misc']['selector']; // array of msg ids to send out
    
    $fields = array('Message.call_id', 'CallLog.cid_number', 'Message.calltype', 'Message.created', 'Message.delivered');
    $this->loadModel('Message');
    $this->Message->recursive = 1;
 		// unbind unnecessary model
 		$this->Message->unbindModel(
      array(
        'hasMany' => array('Mistake', 'Complaint')
      )
   	);	
   	$messages = $this->Message->find('all', array('conditions' => array('Message.id in ('.implode(',', $msg_ids).')'), 'fields' => $fields));    
    $timezone = $did['DidNumber']['timezone'];
    $date1 = new DateTime();
    $date1->setTimezone(new DateTimeZone($timezone));      
    $now_time = $date1->format('H:i:s');
    $now_date = $date1->format('Y-m-d');
    $this->set('now_time', $now_time);
    $this->set('now_date', $now_date);
    
    foreach ($messages as $km => $m) {
      $appts = $this->_get_appointments($m['Message']['call_id']);
      $date = new DateTime();
      $date->setTimestamp(strtotime($m['Message']['created']));
      $date->setTimezone(new DateTimeZone($timezone));
	    $messages[$km]['Message']['created'] = $date->format('m/d/Y g:i a');	        
	    $messages[$km]['appointments'] = $appts;	        
    	foreach ($m['MessagesDelivery'] as $k => $delivery) {
    		// convert delivery time to client's timezone
	      $date = new DateTime();
	      $date->setTimestamp(strtotime($delivery['delivered_time']));
	      $date->setTimezone(new DateTimeZone($timezone));
	     	$messages[$km]['MessagesDelivery'][$k]['delivered_time'] = $date->format('m/d/Y g:i a');	        
    	}
    }
    if (empty($messages)) {
      $this->Session->setFlash('Cannot retrieve messages', 'flash_jsonbad');
      $this->render('/Elements/json_result');
      return;
    }
    $save_ok = true;
    $include_cid = true;
//            $destination = $e['contact'];
//            $destination_emails[] = $destination;
      // generate email body by getting the output of a rendered view.
    if ($messages) {
      $this->autoRender = false;  // make sure controller doesn't auto render
     
      /* Set up new view that won't enter the ClassRegistry */
       $this->set('messages', $messages);
      $this->set('include_cid', $did['DidNumber']['include_cid']);
      $this->set('company', $did['DidNumber']['company']);
      $this->set('account_num', $did['Account']['account_num']);
       
      /* Grab output into variable without the view actually outputting! */
      $this->render('email_messages');      
      
      
    }


  }              
  
  
	function _faxSummary($template_type, $recipient, $prompts,  $calltype_caption, $account_num='') {
		$this->layout = "plain";

		$recipient_array = explode(',', $recipient);
		foreach ($recipient_array as $r) {		
  		try {
  			App::uses('CakeEmail', 'Network/Email');		
  			$Email = new CakeEmail();
  			$Email->config('default');
  			$Email->template('deliver'.$template_type . 'Msg', 'default');
  			$Email->viewVars(array('calltype' => $this->calltype, 'prompts' => $prompts)); 
        $Email->to($r.'@fax.voicenation.com');
  			$Email->emailFormat('text');
  			$Email->subject("Account: " . $account_num);
  			$Email->send();
  
  			return true;
  		}	catch (Exception $e) {
  			return false;
  		}
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
	  
	  // check if data was posted		
		if ($this->request->is('post') || $this->request->is('put')) {
  	  
  	  // find timezone
  	  $this->loadModel('DidNumber'); 
  	  $this->DidNumber->recursive = 0;
  	  $did = $this->DidNumber->findById($did_id);
      $this->request->data['MessagesSummary']['account_id'] = $did['DidNumber']['account_id'];
      $this->request->data['MessagesSummary']['did_tz'] = $did['DidNumber']['timezone'];
      
      if (!isset($this->request->data['MessagesSummary']['start_time']) && !isset($this->request->data['MessagesSummary']['end_time']) && !isset($this->request->data['MessagesSummary']['send_time'])) {
        $this->request->data['MessagesSummary']['all_day'] = '1';
      }
      else {
        $this->request->data['MessagesSummary']['all_day'] = '0';
        $client_timezone = $did['DidNumber']['timezone'];
        $oa_timezone = Configure::read('default_timezone');
        
        // convert time to local OA timezone
        if (isset($this->request->data['MessagesSummary']['start_time']) && $this->request->data['MessagesSummary']['start_time']) {
          $this->request->data['MessagesSummary']['start_time'] = $this->time_format_mysql($this->request->data['MessagesSummary']['start_time']);
        }
        if (isset($this->request->data['MessagesSummary']['end_time']) && $this->request->data['MessagesSummary']['end_time']) {
          $this->request->data['MessagesSummary']['end_time'] = $this->time_format_mysql($this->request->data['MessagesSummary']['end_time']);
        }
  
        if (isset($this->request->data['MessagesSummary']['send_time']) && $this->request->data['MessagesSummary']['send_time']) {      
          $this->request->data['MessagesSummary']['send_time'] = $this->time_format_mysql($this->request->data['MessagesSummary']['send_time']);
        } 
        if (isset($this->request->data['MessagesSummary']['no_message_send_time']) && $this->request->data['MessagesSummary']['no_message_send_time']) {      
          $this->request->data['MessagesSummary']['no_message_send_time'] = $this->time_format_mysql($this->request->data['MessagesSummary']['no_message_send_time']);
        }         
      }     
      // create comma delimited list of employee contact ids
      if (isset($this->request->data['Misc']['employee_contact_ids']) && sizeof($this->request->data['Misc']['employee_contact_ids'])) {
        $this->request->data['MessagesSummary']['employee_contact_ids'] = implode(',', $this->request->data['Misc']['employee_contact_ids']);
      }
      else {
        $this->request->data['MessagesSummary']['employee_contact_ids'] = '';
      }
            
		  $this->MessagesSummary->create();
		  $saveok = $this->MessagesSummary->save($this->request->data['MessagesSummary']);
		  
		  if ($saveok) {
			  $this->Session->setFlash(__('Your schedule has been added'), 'flash_jsongood');
				$id = $this->MessagesSummary->getInsertID();

        /************** start logging changes */				  
        $e['user_id'] = AuthComponent::user('id');
        $e['user_username'] = AuthComponent::user('username');
        $e['old_values'] = '';
        $e['new_values'] = serialize($this->request->data);
        $e['account_id'] = $did['DidNumber']['account_id'];        
        $e['did_id'] = $this->request->data['MessagesSummary']['did_id'];
        $e['messages_summary_id'] = $id;
        $e['section'] = 'summary';
        $this->_formatTime($this->request->data['MessagesSummary']);
        $time_range = '';
  		  $day_ranges = $this->_getDayRanges($this->request->data['MessagesSummary'], $this->php_daysofweek);
        if ($this->request->data['MessagesSummary']['all_day']) $time_range .= ' all day';  		  
        else {
		      if (!empty($this->request->data['MessagesSummary']['start_time_f']) && $this->request->data['MessagesSummary']['end_time_f']) $time_range .= " from {$this->request->data['MessagesSummary']['start_time_f']} to {$this->request->data['MessagesSummary']['end_time_f']}";
		      if (!empty($this->request->data['MessagesSummary']['send_time_f'])) $time_range = " at " . $this->request->data['MessagesSummary']['send_time_f'];
		    }
        
        $e['description'] = 'Message summary created: ' . implode(', ', $day_ranges) .  $time_range . ' (ID: '.$id.')';
        $e['change_type'] = 'add';      
        $this->MessagesSummary->DidNumbersEdit->create();
        $this->MessagesSummary->DidNumbersEdit->save($e);				
        /************** finish logging changes */				  
          
		  }
		  else {
			  $this->Session->setFlash(__('Cannot save your changes, please try again later'), 'flash_jsonbad');
		  }
		  $this->render('/Elements/json_result');
		}
		else {
        $query = "select e.id, e.name, GROUP_CONCAT(c.label) as contact_labels, GROUP_CONCAT(c.contact) as contacts, GROUP_CONCAT(c.id) as contact_ids, GROUP_CONCAT(c.contact_type) as contact_types from ".OA_TBL_PREFIX."employees_contacts c left join ".OA_TBL_PREFIX."employees e on c.employee_id=e.id where c.did_id='$did_id' and c.flag='0' and (contact_type='".CONTACT_EMAIL."' OR contact_type='".CONTACT_FAX."') group by c.employee_id order by name, c.employee_id, c.contact_type";          
        
        $this->loadModel('Employee');
        $this->set('employees', $this->Employee->query($query));		  
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
    $this->MessagesSummary->recursive = 0;
		$old_data = $this->MessagesSummary->findById($id);
    $did_id = $old_data['MessagesSummary']['did_id'];
	  $this->loadModel('DidNumber'); 
	  $this->DidNumber->recursive = 0;
	  // get client timezone, all dates are stored in the OA default timezone
	  $d = $this->DidNumber->findById($old_data['MessagesSummary']['did_id']);		

	  $client_timezone = $d['DidNumber']['timezone'];		
    if (!$client_timezone) $client_timezone = Configure::read('default_timezone');
    $oa_timezone = Configure::read('default_timezone');
	  
	  $account_id = $d['DidNumber']['account_id'];	
    $old_data['MessagesSummary'] = $this->_convertTimes($old_data['MessagesSummary']);		  
    
		if ($this->request->is('post') || $this->request->is('put')) {
      // if time is specified, convert to OA default timezone
      if (!$this->request->data['MessagesSummary']['all_day'])  {
        // convert start time to OA local timezone
        if (isset($this->request->data['MessagesSummary']['start_time']) && $this->request->data['MessagesSummary']['start_time']) {        
          $this->request->data['MessagesSummary']['start_time'] = $this->time_format_mysql($this->request->data['MessagesSummary']['start_time']);
        }
  
         // convert end time to OA default timezone
        if (isset($this->request->data['MessagesSummary']['end_time']) && $this->request->data['MessagesSummary']['end_time']) {
          $this->request->data['MessagesSummary']['end_time'] = $this->time_format_mysql($this->request->data['MessagesSummary']['end_time']);
        }
  
        // convert send time to OA default timezone
        if (isset($this->request->data['MessagesSummary']['send_time']) && $this->request->data['MessagesSummary']['send_time']) {      
          $this->request->data['MessagesSummary']['send_time'] = $this->time_format_mysql($this->request->data['MessagesSummary']['send_time']);
        }      
        // convert send time to OA default timezone
        if (isset($this->request->data['MessagesSummary']['no_message_send_time']) && $this->request->data['MessagesSummary']['no_message_send_time']) {      
          $this->request->data['MessagesSummary']['no_message_end_time'] = $this->time_format_mysql($this->request->data['MessagesSummary']['no_message_send_time']);
        }              
      }
      else {
        $this->request->data['MessagesSummary']['start_time'] = '';
        $this->request->data['MessagesSummary']['end_time'] = '';
        $this->request->data['MessagesSummary']['send_time'] = '';
      }
      
      // create comma delimited list of employee contact ids
      if (isset($this->request->data['Misc']['employee_contact_ids']) && sizeof($this->request->data['Misc']['employee_contact_ids'])) {
        $this->request->data['MessagesSummary']['employee_contact_ids'] = implode(',', $this->request->data['Misc']['employee_contact_ids']);
      }
      else {
        $this->request->data['MessagesSummary']['employee_contact_ids'] = '';
      }
      
      // alter the creation timestamp if we're activating the message summary so that we're not sending out old messages
      if ($old_data['MessagesSummary']['active'] == '0' && $this->request->data['MessagesSummary']['active'] == '1') {
        $this->request->data['MessagesSummary']['created'] = date('Y-m-d H:i:s');
      }
			if ($this->MessagesSummary->save($this->request->data)) {
				$this->Session->setFlash(__('The changes have been saved'), 'flash_jsongood');

        /************** start logging changes */				  
				$id = $this->request->data['MessagesSummary']['id'];
        $e['user_id'] = AuthComponent::user('id');
        $e['user_username'] = AuthComponent::user('username');
        $e['old_values'] = serialize($old_data);
        $e['new_values'] = serialize($this->request->data);
        $e['account_id'] = $account_id;
        $e['did_id'] = $did_id;
        $e['messages_summary_id'] = $id;
        $e['section'] = 'summary';
        $time_range = '';
  		  $day_ranges = $this->_getDayRanges($this->request->data['MessagesSummary'], $this->php_daysofweek);
  		  
  		  $this->request->data['MessagesSummary'] = $this->_convertTimes($this->request->data['MessagesSummary']);
		    if ($this->request->data['MessagesSummary']['all_day']) $time_range .= ' all day';
		    else {
		      if ($this->request->data['MessagesSummary']['start_time'] && $this->request->data['MessagesSummary']['end_time']) $time_range .= " from {$this->request->data['MessagesSummary']['start_time_f']} to {$this->request->data['MessagesSummary']['end_time_f']}";
		      if ($this->request->data['MessagesSummary']['send_time']) $time_range = " at " . $this->request->data['MessagesSummary']['send_time_f'];
		    }
        $old_time_range = '';
        
  		  $old_day_ranges = $this->_getDayRanges($old_data['MessagesSummary'], $this->php_daysofweek);
		    if ($old_data['MessagesSummary']['all_day']) $old_time_range .= ' all day';
		    else {
		      if ($old_data['MessagesSummary']['start_time_f'] && $old_data['MessagesSummary']['end_time_f']) $old_time_range .= " from {$old_data['MessagesSummary']['start_time_f']} to {$old_data['MessagesSummary']['end_time_f']}";
		      if ($old_data['MessagesSummary']['send_time_f']) $old_time_range = " at " . $old_data['MessagesSummary']['send_time_f'];
		    }
		    
		    $changes = $this->_initChanges(); // initialize array containing changes
		    
		    // construct string representation of new and old schedules
        $new_schedule = implode(', ', $day_ranges) .  $time_range; 
        $old_schedule = implode(', ', $old_day_ranges) .  $old_time_range;
        
        // log change is schedule has been modified
        if ($new_schedule != $old_schedule) {
  		    $this->_setChanges('schedule', $old_schedule, $new_schedule, $changes);
        }
        
        // check for other changes and log if modified
        foreach ($this->request->data['MessagesSummary'] as $k => $v) {
          if (in_array($k, array('active', 'tx_interval', 'msg_type', 'all_day', 'employee_contact_ids', 'no_message', 'destination_email', 'destination_fax'))) {
            // log if value has changed
  		      if ( $old_data['MessagesSummary'][$k] != $v) {
              if ($k == 'active') {
                $options = array('0' => 'Inactive', '1' => 'Active');
  		          $this->_setChanges($k, $options[$old_data['MessagesSummary'][$k]],$options[$v], $changes);
              }
              else if ($k == 'msg_type') {
                $options = array('0' => '', '1' => 'Undelivered', '2' => 'All');
  		          $this->_setChanges($k, $options[$old_data['MessagesSummary'][$k]],$options[$v], $changes);
              }
              else if ($k == 'no_message') {
                $options = array('' => '', '0' => 'No', '1' => 'Yes');
  		          $this->_setChanges($k, $options[$old_data['MessagesSummary'][$k]], $options[$v], $changes);
              }                   
              else {   		        
  		          $this->_setChanges($k, $old_data['MessagesSummary'][$k],$v, $changes);
  		        }
  		      }
          }
        }        
        if (count($changes['label']) >= 1) {
          $e['description'] = serialize($changes);
          $e['change_type'] = 'edit';      
          $this->MessagesSummary->DidNumbersEdit->create();
          $this->MessagesSummary->DidNumbersEdit->save($e);
        }
         
        /************** finish logging changes */				  
				
			} else {
				$this->Session->setFlash(__('The changes could not be saved. Please, try again.'), 'flash_jsonbad');
			}
			$this->render('/Elements/json_result');
		} else {
      $query = "select e.id, e.name, GROUP_CONCAT(c.label) as contact_labels, GROUP_CONCAT(c.contact) as contacts, GROUP_CONCAT(c.id) as contact_ids, GROUP_CONCAT(c.contact_type) as contact_types from ".OA_TBL_PREFIX."employees_contacts c left join ".OA_TBL_PREFIX."employees e on c.employee_id=e.id where c.did_id='$did_id' and c.flag='0' and (contact_type='".CONTACT_EMAIL."' OR contact_type='".CONTACT_FAX."') and e.deleted='0' group by c.employee_id order by name, c.employee_id, c.contact_type";          
      
      $this->loadModel('Employee');
      $this->set('employees', $this->Employee->query($query));
		  $this->MessagesSummary->recursive = '1';
      $this->MessagesSummary->unbindModel(
        array(
         'hasMany' => array('MessagesSummaryLog'),
        )
      );				
		  
			$this->request->data = $this->MessagesSummary->findById($id);
			// convert times to client timezone
			if (!$this->request->data['MessagesSummary']['all_day']) {
        if ($this->request->data['MessagesSummary']['start_time']) {
          $this->request->data['MessagesSummary']['start_time'] = $this->time_format_user($this->request->data['MessagesSummary']['start_time']);
        }
        if ($this->request->data['MessagesSummary']['end_time']) {
          $this->request->data['MessagesSummary']['end_time'] = $this->time_format_user($this->request->data['MessagesSummary']['end_time']);
        }
        if ($this->request->data['MessagesSummary']['send_time']) {      
          $this->request->data['MessagesSummary']['send_time'] = $this->time_format_user($this->request->data['MessagesSummary']['send_time']);
        }  			
        if ($this->request->data['MessagesSummary']['no_message_send_time']) {      
          $this->request->data['MessagesSummary']['no_message_send_time'] = $this->time_format_user($this->request->data['MessagesSummary']['no_message_send_time']);
        }  			
      }
  
  		foreach ($this->request->data['DidNumbersEdit'] as $k => $e) {
  		  if ($e['change_type'] == 'edit') {
    		  if (strpos($e['description'], 'a:') !== false) $changes = unserialize($e['description']);
    		  $text = '';
    		  if (isset($changes['label'])) {
      		  foreach ($changes['label'] as $j => $label) {
      		  	$text .= '<b>' . $label . '</b> changed from <i>'.$changes['old_values'][$j].'</i> to <i>'.$changes['new_values'][$j].'</i><br>';
      		  }
      		}
    	  }
    		else $text = $e['description'];
    		
  	  	$this->request->data['DidNumbersEdit'][$k]['description'] = $text;
  
  		}
      
		}
	}

	function _fax($msg, $recipient, $account_num='', $did_number='') {
		$this->layout = "plain";
    $this->autoRender = false;  // make sure controller doesn't auto render
   
    /* Set up new view that won't enter the ClassRegistry */
    /* Grab output into variable without the view actually outputting! */
    $view = new View($this, false);
    $view->set('message', $msg);
    
    $view->set('faxnote', '* Live Answering Message Summary *');
    $view->set('faxstatus', 'For Review');
    $view->set('faxto', $recipient);
    $view->set('faxfrom', '(866) 766-5050');
    $view->set('faxnumber', $recipient);
    $view->set('faxphone', '(866) 766-5050');
    $view->set('faxdate', date('D M j, Y g:i a') . ' EST');
    $view->set('faxre', htmlspecialchars("Live Answering Message Summary - Account: " . $account_num));
    $view_output = $view->render('fax_summary_msg'); 

		// save fax into the fax queue to be processed in the background
    $data['FaxQueue']['fax_text'] = $view_output;
    $data['FaxQueue']['fax_processed'] = '0';
    $data['FaxQueue']['src_fax'] = $did_number;
    $data['FaxQueue']['dst_fax'] = $recipient;
    $data['FaxQueue']['format'] = 'html';
    $data['FaxQueue']['account_num'] = $account_num;
		$this->loadModel('FaxQueue');
		$this->FaxQueue->create();
		return $this->FaxQueue->save($data['FaxQueue']);
	}		
  // converts to mysql format (ex: 3:15pm converts to 15:15:00)
	function time_format_mysql($input) {
	  $ts = strtotime("today " . $input);
	  $mysql_time = date('H:i:s', $ts);
	  return $mysql_time;
	}
	
	function time_format_user($input) {
	  $ts = strtotime("today " . $input);
	  $mysql_time = date('g:i a', $ts);
	  return $mysql_time;
	}	

	public function delete($id = null) {
    $this->MessagesSummary->unbindModel(
      array(
       'hasMany' => array('MessagesSummaryLog', 'DidNumbersEdit'),
      )
    );				
		$old_data = $this->MessagesSummary->findById($id);
		if (!$old_data) {
		  $this->Session->setFlash(__('Cannot delete schedule, please try again later'), 'flash_jsonbad');
		  $this->render('/Elements/json_result');		  
		}


		if ($this->MessagesSummary->delete($id, true)) {		  
			$this->Session->setFlash(__('The schedule has been deleted'), 'flash_jsongood');
      $e['user_id'] = AuthComponent::user('id');
      $e['user_username'] = AuthComponent::user('username');
      $e['new_values'] = '';
      $e['old_values'] = serialize($old_data);
      $e['did_id'] = $old_data['MessagesSummary']['did_id'];
      $e['messages_summary_id'] = $old_data['MessagesSummary']['id'];
      $e['account_id'] = $old_data['MessagesSummary']['account_id'];
      $old_time_range = '';
		  $old_day_ranges = $this->_getDayRanges($old_data['MessagesSummary'], $this->php_daysofweek);
	    if ($old_data['MessagesSummary']['start_time_f'] && $old_data['MessagesSummary']['end_time_f']) $old_time_range .= " from {$old_data['MessagesSummary']['start_time_f']} to {$old_data['MessagesSummary']['end_time_f']}";
	    if ($old_data['MessagesSummary']['send_time_f']) $old_time_range = " at " . $old_data['MessagesSummary']['send_time_f'];
	    
      $old_schedule = implode(', ', $old_day_ranges) .  $old_time_range;
              
      $e['description'] = 'Message summary deleted:' . $old_schedule;
      $e['change_type'] = 'delete'; 
      $e['section'] = 'summary'; 
      $this->MessagesSummary->DidNumbersEdit->create();
      $this->MessagesSummary->DidNumbersEdit->save($e);			
		}
		else $this->Session->setFlash(__('Cannot delete schedule, please try again later'), 'flash_jsonbad');
		$this->render('/Elements/json_result');
	}
	
	// recover deleted schedule
	public function recover($edit_id) {
	  $recovery_data = $this->MessagesSummary->DidNumbersEdit->findById($edit_id);
	  if ($recovery_data) {
	    $summary = unserialize($recovery_data['DidNumbersEdit']['old_values']);
	    if ($this->MessagesSummary->findById($summary['MessagesSummary']['id'])) {

        $this->Session->setFlash(__('ERROR: This message summary already exists, cannot overwrite it'), 'flash_jsonbad');	 		      
        $this->render('/Elements/json_result');    
        return;
	    }
	    $ok = $this->MessagesSummary->saveAssociated($summary);
	    if ($ok) {
        $e['user_id'] = AuthComponent::user('id');
        $e['user_username'] = AuthComponent::user('username');
        $e['new_values'] = serialize($summary);;
        $e['old_values'] = '';
        $e['account_id'] = $summary['MessagesSummary']['account_id'];
        $e['did_id'] = $summary['MessagesSummary']['did_id'];
        $e['messages_summary_id'] = $summary['MessagesSummary']['id'];
        $e['description'] = 'Summary (ID: '.$summary['MessagesSummary']['id'].') recovered';
        $e['change_type'] = 'recover'; 
        $e['section'] = 'summary'; 
        $this->MessagesSummary->DidNumbersEdit->create();
        $this->MessagesSummary->DidNumbersEdit->save($e);	    
        $this->Session->setFlash(__('Summary schedule has been recovered'), 'flash_jsongood');	        
      }
      else {
        $this->Session->setFlash(__('Information cannot be recovered'), 'flash_jsonbad');	        
      }
	  }
    else {
      $this->Session->setFlash(__('Information cannot be found'), 'flash_jsonbad');	        
    }
    $this->render('/Elements/json_result');    
	}
		
  function _formatTime(&$schedule) {
    if (isset($schedule['start_time']) && $schedule['start_time']) $schedule['start_time_f'] = date('g:i A', strtotime("today " . $schedule['start_time']));
    if (isset($schedule['end_time']) && $schedule['end_time']) $schedule['end_time_f']  = date('g:i A', strtotime("today " . $schedule['end_time']));
    if (isset($schedule['send_time']) && $schedule['send_time']) $schedule['send_time_f']  = date('g:i A', strtotime("today " . $schedule['send_time']));
    if (isset($schedule['no_message_send_time']) && $schedule['no_message_send_time']) $schedule['no_message_send_time_f']  = date('g:i A', strtotime("today " . $schedule['no_message_send_time']));    
  }
  
	function _convertTimes($data) {
	  if (isset($data['end_time']) && $data['end_time']) {
      $data['end_time_f'] = $this->time_format_user($data['end_time']); 
    }
    
	  if (isset($data['start_time']) &&$data['start_time']) {
      $data['start_time_f'] = $this->time_format_user($data['start_time']); 
    }

	  if (isset($data['send_time']) && $data['send_time']) {
      $data['send_time_f'] = $this->time_format_user($data['send_time']); 
    }

	  if (isset($data['no_message_send_time']) && $data['no_message_send_time']) {
      $data['no_message_send_time_f'] = $this->time_format_user($data['no_message_send_time']); 
    }

    return $data;
	}	

  // returns an array of scheduling calendar appointments
  function _get_appointments($call_id, $deleted = false) {
    $appts = array('active' => array(), 'deleted' => array());
    if (Configure::read('calendar_enabled')) {
			$this->loadModel('Scheduling.EaAppointment');
			$appts = $this->EaAppointment->get_appointments($call_id, $deleted);
  	}
		return $appts;   
  }
}
