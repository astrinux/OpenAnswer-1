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
	$s_html .= '<input type="hidden" value="" name="transfer_status" id="message_action" />';
	$s_html .= '<input type="hidden" value="" name="message_action" id="message_action" />';
	$s_html2 = '';
	if (isset($json['ct_actions'][$schedule['id']])) $actions = $json['ct_actions'][$schedule['id']];
	else $actions = array();
	ksort($actions);
	$employee_select = false;

  $required = array();

	foreach ($actions as $ak => $a) {
		if ($a['eid'] == 'ALL') $employee_select = true;
		else $employee_select = false;
		$s_html .= '<div class="step">' . $this->element('calltype_schedule_callbox', array('idx' => $ak, 'actions' => $actions, 'action_id' => $a['id'], 'json' => $json)) . '</div>';
		$s_html2 .= $this->element('calltype_schedule_callbox_short', array('idx' => $ak, 'actions' => $actions, 'action_id' => $a['id'], 'json' => $json));
    if ($a['action_type'] == ACTION_TXF || $a['action_type'] == ACTION_BLINDTXF) $required['phone'] = true;
    else if ($a['action_type'] == ACTION_TXTMSG || $a['action_type'] == ACTION_TEXT_DELIVER) $required['text'] = true;
    else if ($a['action_type'] == ACTION_EMAIL || $a['action_type'] == ACTION_EMAIL_DELIVER) $required['email'] = true;
    else if ($a['action_type'] == ACTION_VMOFFER ) $required['vmail'] = true;
	}
	$s_html .= '<input type="hidden" value="'.$s_html2.'" name="c_instr" id="c_instr" >';
		if ($employee_select) {
				$emp_select = '<div class="step"><div class="action inline">Employee:</div> <input type="hidden" id="contact_picker" name="contact_picker" value=""><select id="employee_picker" name="emp_picker">';
				foreach ($json['employees'] as $k => $e) {
				  $reqcheck = $required;
				  $contacts = array();
				  $phone = $text = $vmail = $email = '';
				  foreach($e['contacts'] as $k) {
				    if (isset($reqcheck['phone']) && ($k['contact_type'] == CONTACT_PHONE || $k['contact_type'] == CONTACT_CELL)) {
				      $contacts[] = $this->element('formatPhone', array('num' => $k['contact']));
				      unset($reqcheck['phone']);
				      $phone = $k['id'] . '||' . $k['contact'];
				    }
				    else if (isset($reqcheck['text']) && $k['contact_type'] == CONTACT_TEXT ) {
				      $contacts[] = $this->element('formatPhone', array('num' => $k['contact']));
				      unset($reqcheck['text']);
				      $text = $k['id'] . '||' . $k['contact'];
				    }
				    else if (isset($reqcheck['vmail']) && $k['contact_type'] == CONTACT_VMAIL ) {
				      $contacts[] = $this->element('formatPhone', array('num' => $k['contact']));
				      unset($reqcheck['vmail']);
				      $vmail = $k['id'] . '||' . $k['contact'];				      
				    }
				    else if (isset($reqcheck['email']) && $k['contact_type'] == CONTACT_EMAIL ) {
				      $contacts[] = $k['contact'];
				      unset($reqcheck['email']);
				      $email = $k['id'] . '||' . $k['contact'];
				    }
				    if (!sizeof($reqcheck)) break;
				  }
					if (!sizeof($reqcheck)) $emp_select .= '<option value="'.$e['id'].'" data-gender="'.$e['gender'].'" data-phone="'.$phone.'" data-text="'.$text.'" data-vmail="'.$vmail.'" data-email="'.$email.'" data-gender="'.$e['gender'].'">' . $e['name'] . ' - ' . implode(', ', $contacts) . '</option>';
					else $emp_select .= '<option value="'.$e['id'].'" disabled>' . $e['name'] . ' - ' . implode(', ', $contacts) . '</option>';
				}
				$emp_select .= "</select></div>";
		}
		else {
			$emp_select = '';
		}
	$json['html'][$schedule['id']] = '<form class="cinstr" onsubmit="return false;">' . $emp_select . $s_html . '</form>';
	$json['shorthtml'][$schedule['id']] = $s_html2;
}
echo json_encode($json);

?>
