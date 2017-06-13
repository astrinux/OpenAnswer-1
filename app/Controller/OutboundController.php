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


class OutboundController extends AppController {
  	public function end($outbound_id) {
	  if (empty($outbound_id)) {
			$this->Session->setFlash(__('Invalid outbound call id'), 'flash_jsonbad');
	    $this->render('/Elements/json_result');	    
	  }
	  $d['id'] = $outbound_id;
	  $d['call_end'] = date('Y-m-d H:i:s');
	  if ($this->Outbound->save($d)) {
			$this->Session->setFlash('Flagged end of outbound call', 'flash_jsongood');	    
	  }
	  else {
			$this->Session->setFlash('Cannot flag end of outbound', 'flash_jsonbad');	    
	  }
    $this->render('/Elements/json_result');
	}
}
