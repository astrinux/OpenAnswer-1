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
class DidNumbersEditsController extends AppController {
  public $paginate;
	public $components = array('RequestHandler');
	public $helpers = array('Js');

  protected $dictionary;
  
  function beforeFilter() {
    parent::beforeFilter();
    $this->dictionary = array(
      'did' => array(
        'calls_per_day' => array( 'values' => array('1' => '< 5 calls/day', '2' => '< 20 calls/day', '3' => '< 50 calls/day', '4' => '< 100 calls/day', '5' => '> 100 calls/day', '6' => 'Call volume fluctuates day to day')),
        'timezone' => array( 'values' => $this->global_options['timezone']),
        'include_cid' => array( 'values' => array('0' => 'No', '1' => 'Yes')),
        'email_format' => array( 'values' => array('0' => 'html', '1' => 'text')),
        'type' => array('1' => array( 'values' => 'Receptionist', '2' => 'Answering Service')),
        'primary_or_overflow' => array( 'values' => array('0' => 'Primary', '1' => 'Overflow')),
        'radio_advertising' => array( 'values' => array('0' => 'No', '1' => 'Yes')),
        'calls_per_day' => array( 'values' => array('1' => '< 5 calls/day', '2' => '< 20 calls/day', '3' => '< 50 calls/day', '4' => '< 100 calls/day', '5' => '> 100 calls/day', '6' => 'Call volume fluctuates day to day'))
      )
    );
  }

  function _getChangeText($section, $field, $old_value, $new_value) {
    $text = '';
    $old = (!empty($old_value))? $old_value:'(empty)';
    $new = (!empty($new_value))? $new_value:'(empty)';

    if (isset($this->dictionary[$section])) {
      if (isset($this->dictionary[$section][$field])) {
        $old = (!empty($old_value))? $this->dictionary[$section][$field]['values'][$old_value]:'(empty)';
        $new = (!empty($new_value))? $this->dictionary[$section][$field]['values'][$new_value]:'(empty)';
      }
    }
    $text = '<b>' . $field . '</b> changed from <i>'.$old.'</i> to <i>'.$new.'</i><br>';
    return $text;
  }
  
  function index($did_id=null) {
    if (!empty($did_id)) $conditions = array('did_id' => $did_id);
    else $conditions = array();
    if (!empty($this->request->data['Search']['edit_type'])) {
      $types = $this->request->data['Search']['edit_type'];
      $types = "'". str_replace(',', "','", $types). "'";
      
      $conditions[] = "DidNumbersEdit.section in ($types)";
    }    
		$this->paginate['limit'] = 20;
		$this->paginate['conditions'] = $conditions;
		$this->paginate['fields'] = array('DidNumbersEdit.*', "DATE_FORMAT(created, '%a %c/%d/%y %l:%i %p') as created_f");
		$this->paginate['order'] = array('DidNumbersEdit.id' => 'desc');
		$this->DidNumbersEdit->recursive = 0;

		$d = $this->paginate();

		foreach ($d as $k => $e) {
		  if ($e['DidNumbersEdit']['change_type'] == 'edit'){
  		  if (strpos($e['DidNumbersEdit']['description'], 'a:') !== false) $changes = @unserialize($e['DidNumbersEdit']['description']) ;
  		  $text = '';
  		  if (isset($changes['label'])) {
    		  foreach($changes['label'] as $j => $label) {
    		    if ($label != 'schedule ID') {
    		      $text .= $this->_getChangeText($e['DidNumbersEdit']['section'],  $label, trim($changes['old_values'][$j]), trim($changes['new_values'][$j]));
    		  	  //$text .= '<b>' . $label . '</b> changed from <i>'.(trim($changes['old_values'][$j] != '')? trim($changes['old_values'][$j]):'(empty)').'</i> to <i>'.(trim($changes['new_values'][$j] != '')? trim($changes['new_values'][$j]): '(empty)').'</i><br>';
    		  	}
    		  }
    		}
    		else $text = $e['DidNumbersEdit']['description'];
	  	  $d[$k]['DidNumbersEdit']['description'] = $text;
      }
		}		
		$this->set('edits', $d);
		$this->set('did_id', $did_id);    
  }
  
  function changes($section, $id) {
    if ($section == 'oncall') {
      $conditions = array('call_list_id' => $id, 'section' => $section);
    }
		$this->paginate['limit'] = 40;
		$this->paginate['conditions'] = $conditions;
		$this->paginate['fields'] = array('DidNumbersEdit.*', "DATE_FORMAT(created, '%a %c/%d/%y %l:%i %p') as created_f");
		$this->paginate['order'] = array('DidNumbersEdit.id' => 'desc');
		$this->DidNumbersEdit->recursive = 0;
		$d = $this->paginate();    
		$this->set('edits', $d);		
  }
}
	?>