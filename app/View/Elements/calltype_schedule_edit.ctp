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
?>
	<?php
  $thehtml = '';
	if (sizeof($action['Prompt'])) {
		$thehtml .= $action['action_text'] . ': <ul>';
		foreach ($action['Prompt'] as $p) {
			$thehtml .= '<select>';
				$sel = false;
		    foreach ($global_options['prompts'] as $k1 => $val1) {
		    	$thehtml .= '<option value="' . $val1['caption'] . '"';
		    	if ($val1['caption'] == $p['caption']) {
		    		$thehtml .= ' selected';
		    		$sel = true;
		    	}
		    	$thehtml .= '>' . $val1['description'] . '</option>';
		    }
		    if (!$sel) 
		    	$thehtml .= '<option value="' . $p['caption'] . '" selected>'. $p['caption'] . '</option>';
		  	$thehtml  .= '</select><br>';
		  			//$thehtml .= '<li>' . $p['caption'] . '</li>';
		//	if ($p['type'] == '1') echo $this->Form->input('username', array('label' => $p['caption'], 'size' => 30));
		}
		$thehtml .= '</ul>';
	}
	
	if ($action['eid']) {
		//get arrays of action recipients
		$e_arr = explode(',', $action['eid']);
		$emp = array();
		foreach($e_arr as $e) {
			$temp = explode('|', $e);
			$emp[] = $employees[$temp[0]]['name'] . ' ('.$employees[$temp[0]]['contact'.$temp[1]].')';
		}
		$thehtml .=  str_replace('[e]', implode(', ', $emp), $action['action_text']);
	}	
	echo $thehtml;
?>