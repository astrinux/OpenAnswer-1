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
 * Messages Controller
 *
 * @property Message $Message
 */
class MessagesPromptsEditsController extends AppController {
	
  public $paginate = array(
  	'limit' => 100,
    'order' => array(
    	'MessagePromptsEdits.edit_time' => 'asc'
   	)
  );
	public $components = array('RequestHandler');
	public $helpers = array('Js');  
  
  public function view($msg_id) {
    $this->loadModel('MessagesPrompt');
    $fields = array("GROUP_CONCAT(caption order by sort asc SEPARATOR '|') as ptitles ", "GROUP_CONCAT(value order by sort asc SEPARATOR '|') as pvalues");    
    $fields2 = array("GROUP_CONCAT(caption order by sort asc SEPARATOR '|') as ptitles", "GROUP_CONCAT(value order by sort asc SEPARATOR '|') as pvalues", 'user_name', 'user_id', 'edit_time');    
    $conditions = array('message_id' => $msg_id);
    $group = array('edit_time');
    $order = array('edit_time' => 'asc');


    $current_prompts =  $this->MessagesPrompt->find('first', array('conditions' => array('message_id' => $msg_id), 'group' => array('message_id'), 'fields' => $fields));
    $current_prompts[0]['edit_time'] = '';
    //$current_prompts[0]['edit_title'] = 'Current values';

    
    $data = $this->MessagesPromptsEdit->find('all', array('conditions' => $conditions, 'group' => $group, 'fields' => $fields2));
    $maxlength = count($data);
    $current_prompts[0]['edit_title'] = ($data[$maxlength-1]['MessagesPromptsEdit']['user_name']? 'Edited by ' . $data[$maxlength-1]['MessagesPromptsEdit']['user_name']:'');
    $current_prompts[0]['edit_time'] = $data[$maxlength-1]['MessagesPromptsEdit']['edit_time'];

    for ($i=($maxlength-1); $i > 0; $i--) {
      $data[$i][0]['edit_title'] = ($data[$i-1]['MessagesPromptsEdit']['user_name']? 'Edited by ' . $data[$i-1]['MessagesPromptsEdit']['user_name']:'');
      $data[$i][0]['edit_time'] = $data[$i-1]['MessagesPromptsEdit']['edit_time'];
    } 
    $data[0][0]['edit_title'] = 'Original entry';
    $data[0][0]['edit_time'] = '';
    $data[] = $current_prompts;
    $this->set('edits', $data);
  }
  
	
}

