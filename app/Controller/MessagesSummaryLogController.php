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
class MessagesSummaryLogController extends AppController {
  public $paginate;
	public $components = array('RequestHandler');
	public $helpers = array('Js');
	  
  function beforeFilter() {
    parent::beforeFilter();
  }
/**
 * index method
 *
 * @return void
 */
	public function index($summary_id) {

    if (empty($summary_id)) {
			$this->Session->setFlash(__('You cannot access this page'), 'flash_jsonbad');
      $this->render('/Elements/json_result');      
    }
    $this->paginate['order'] = array('id' => 'desc');
    $this->paginate['limit'] = 15;
    
    $this->paginate['fields'] = array('MessagesSummaryLog.*', "DATE_FORMAT(CONVERT_TZ(MessagesSummaryLog.summary_sent, '".Configure::read('default_timezone')."', MessagesSummaryLog.did_tz), '%c/%d/%Y %l:%i %p') as summary_sent_f" );
    $this->paginate['conditions'] = array('message_summary_id' => $summary_id);
    
		$data = $this->paginate();
    $this->set('data', $data);    
	}
	
	
  function msg_summaries($did_id) {
    if (empty($did_id)) {
			$this->Session->setFlash(__('You cannot access this page'), 'flash_jsonbad');
      $this->render('/Elements/json_result');      
    }
    $this->paginate['order'] = array('id' => 'desc');
    $this->paginate['limit'] = 40;
    
    $this->paginate['fields'] = array('MessagesSummaryLog.*', "DATE_FORMAT(CONVERT_TZ(MessagesSummaryLog.summary_sent, '".Configure::read('default_timezone')."', MessagesSummaryLog.did_tz), '%c/%d/%Y %l:%i %p') as summary_sent_f" );
    $this->paginate['conditions'] = array('did_id' => $did_id);
    
		$data = $this->paginate();
    $this->set('data', $data);    
	}    

}
