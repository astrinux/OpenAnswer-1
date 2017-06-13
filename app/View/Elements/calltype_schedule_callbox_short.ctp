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
	$prompts = '';
  if (isset($actions[$idx])) {
  	$action = $actions[$idx];
  }
  else $action = '';

	$employees = $json['employees']; 
	$employees_contacts = $json['contacts']; 
	$action_text = $action['action_text'];
	if ($action['action_type']) $action_text = str_replace('[a]', $global_options['actions'][$action['action_type']]['label'], $action_text);

	if (isset($action['eid']) && $action['eid']) {
		$emp_contacts = array();
		//get arrays of action recipients
		if ($action['eid'] == 'ALL') {
				$emp[] = 'Requested Staff';
				$emp_contacts[] = 'ALL';
		}
		else {
			$e_arr = explode(',', $action['eid']);
			$emp = array();
			foreach($e_arr as $eid) {
				$emp_contacts[] = $employees_contacts[$eid]['contact'];
				$emp[] = $employees[$employees_contacts[$eid]['employee_id']]['name'];
			}
		}
		
		$thehtml .=  strip_tags(str_replace('[e]', implode(', ', $emp), str_replace('[w]', $action['action_url'], $action_text) )) . "\r\n";
	}	
	else $thehtml .=  strip_tags(str_replace('[w]', $action['action_url'], $action_text)) . "\r\n";

	echo $thehtml;
?>