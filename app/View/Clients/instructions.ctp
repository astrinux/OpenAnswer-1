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

foreach ($json['schedules'] as $schedule) {
	$s_html = '<input type="hidden" value="" name="calltype_caption" id="calltype_caption" />';
	$s_html .= '<input type="hidden" value="" name="calltype_id" id="calltype_id" />';
	$s_html2 = '';
	if (isset($json['ct_actions'][$schedule['id']])) $actions = $json['ct_actions'][$schedule['id']];
	else $actions = array();
	ksort($actions);
	$employee_select = false;
	foreach ($actions as $ak => $a) {
		if ($a['eid'] == 'ALL') $employee_select = true;
		else $employee_select = false;
		$s_html .= '<div class="step">' . $this->element('calltype_schedule_callbox', array('idx' => $ak, 'actions' => $actions, 'action_id' => $a['id'], 'json' => $json)) . '</div>';
		$s_html2 .= $this->element('calltype_schedule_callbox_short', array('idx' => $ak, 'actions' => $actions, 'action_id' => $a['id'], 'json' => $json));
	}
	
	$s_html .= '<input type="hidden" value="'.$s_html2.'" name="c_instr" id="c_instr" >';
		if ($employee_select) {
				$emp_select = '<div class="step"><div class="action inline">Employee:</div> <select id="employee_picker" name="emp_picker">';
				foreach ($json['employees'] as $e) {
					$emp_select .= '<option value="'.$e['id'].'">' . $e['name'] . '</option>';
				}
				$emp_select .= "</select></div>";
		}
		else {
			$emp_select = '';
		}
	$json['html'][$schedule['id']] = '<form>' . $emp_select . $s_html . '</form>';
	$json['shorthtml'][$schedule['id']] = $s_html2;
}
echo json_encode($json);

?>