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

class EmailQueuesController extends AppController {
  function beforeFilter() {
    parent::beforeFilter();
    $this->Auth->allow('process');
  }
 
	public function index($did_id) {
	}
  
    public function process() {
        
        //load necessary models
        $this->loadModel('DidNumber');
        $this->loadModel('Message');
        
        //get a list of all emails in the queue that have not yet been processed, limited to a max of 100
        //at a time to keep from running out of memory
        $emails = $this->EmailQueue->find('all', array('limit' => 100, 'conditions' => array('processed' => 0)));
        
        //for each email in the queue
        foreach ($emails as $e) {
            
            //recipient is stored as a serialized string or array, so unserialize it
            $recipients = unserialize($e['EmailQueue']['recipients']);
            
            //if it was an array, we convert it into a semicolon delimited string
            if (is_array($recipients)) {
                $recipients = implode(';', $recipients);
            }
            
            //We need to know what SMTP profile we should be using for this specific email, so
            //we pull the subaccount id from the queue and look up the setting for this account
            $did = $this->DidNumber->findById($e['EmailQueue']['did_id']);
            $this->DidNumber->id = $e['EmailQueue']['did_id'];
            $smtp_profile = $this->DidNumber->field('smtp_profile');
            
            //Attempt to send the email out
            $send_ok = $this->_sendemail($e['EmailQueue']['subject'], $e['EmailQueue']['content_html'], $recipients, $e['EmailQueue']['format'], $e['EmailQueue']['content_text'],$smtp_profile);
            
            //Take action based on whether or not the send was successful.
            if ($send_ok) { //email was successful
                //mark the queued email as processed successfully
                $e['EmailQueue']['processed'] = '1';
                
                //if we know the call id, add an entry to the call event log that it was successful.
                if ($e['EmailQueue']['call_id']) {
                    $this->logEvent($e['EmailQueue']['call_id'], $send_ok, EVENT_OTHER, '');
                }
            }
            else  { //email failed
                $this->DidNumber->recursive = 0;
                $this->Message->recursive = 0;
                $did = $this->DidNumber->findById($e['EmailQueue']['did_id']);
                
                //mark the queue entry as failed
                $e['EmailQueue']['processed'] = '2';
                
                //if we know the call id, we can log an entry for it and send a detailed failure email
                if ($e['EmailQueue']['call_id']) {
                    $msg = $this->Message->findByCallId($e['EmailQueue']['call_id']);
                    $this->logEvent($e['EmailQueue']['call_id'], "Unable to send message: " . $recipients, EVENT_ERROR, '');
                    $content =  'Invalid email in queue ID #' . $e['EmailQueue']['id'] . "\r\n\r\nRecipients: \r\n" . implode(',', unserialize($e['EmailQueue']['recipients'])) . "\r\n\r\n" . 'Account #' . $did['Account']['account_num'] . " " .$did['DidNumber']['company'] . "\r\n\r\n" . "Message #" . $msg['Message']['id'] . "\r\n\r\nPlease note that the message will have to be resent out after the employee information is corrected" ;
                    $email = new CakeEmail();
                    $email->config('default');
                    $email->to(Configure::read('admin_email2'));
                    $email->subject("[OA EmailQueues.process] Invalid Employee Info for Acct# " . $did['Account']['account_num']);
                    $email->send($content);
                }
                else { //send generic failure email
                    
                    //
                    
                    //$content =  'Invalid email in queue ID #' . $e['EmailQueue']['id'] . "\r\n\r\nRecipients: \r\n" . implode(',', unserialize($e['EmailQueue']['recipients'])) . "\r\n\r\n" . 'Account #' . $did['Account']['account_num'] . " " .$did['DidNumber']['company'] . "\r\n\r\n" . "Message #" . $msg['Message']['id'] . "\r\n\r\nPlease note that the message will have to be resent out after the employee information is corrected" ;
                    //$email = new CakeEmail();
                    //$mail->config('default');
                    //$email->to(Configure::read('admin_email2'));
                    //$email->subject("[OA EmailQueues.process] Invalid Employee Info for Acct# " . $did['Account']['account_num']);
                    //$email->send($content);
                }
            }
            
            //save whatever the results were, to the queue.
            $this->EmailQueue->save($e);
        }
    echo 'done'; exit;
    }
}
