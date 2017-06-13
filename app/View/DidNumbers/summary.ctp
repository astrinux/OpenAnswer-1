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
<style>
	body {font-size: 12px; padding:30px; line-height: 140%; color: #333; font-family: Verdana;}
	table td{font-size: 12px}
	h3 {font-size:12px; display:inline;}
	h2 {font-size:16px; margin:45px 0px 10px 0px;}
	h1 {font-size:20px;font-weight: bold;}
	label {font-weight: bold; display: inline-block; width:150px; text-align:right; margin-right: 10px;}
	.unknown {font-style:italic; color: #ccc; font-size:11px;}
	.employees {margin: 20px;}
	.contacts {margin:5px 0px; border-bottom: 1px solid #ccc;padding:5px 0px;}
	.contacts label {width: 150px;}
	.schedule {margin: 10px 20px 10px 20px;}
	.actions {margin: 5px 20px 20px 20px;}
	.action {border-bottom: 1px dotted #aaa;padding: 5px; margin:0px;}
	.prompts {margin-left: 20px;margin-top:10px;}
	input[type=text] {border:1px solid #ccc; padding:2px 4px;}
	form {margin:0px;}
	.helpertxt {margin: 20px 0px; font-style: italic;}
	.script_section {border: 1px solid #aaa; border-radius: 1px; margin-bottom:20px;padding: 10px;}
	.section_title { text-align:left; color: #777;margin-bottom: 10px;}
	.section_action {text-align:right; text-decoration: italic; color: #888}

	.action_label { text-align:right; color: #777;}
	
@media print {
		.noprint {
				display: none;
		}
}   
</style>
<?php if (!isset($emailed)) {
	?>
<div class="noprint">
<center><a href="#" onclick="$('#email_send').show(); return false;"><img src="/img/email.png" alt="email" title="email" width="24" height="16"></a>&nbsp;&nbsp;&nbsp;<a href="#" onclick="window.print(); return false;"><img src="/img/print.png" alt="print" title="print" width="24" height="21"></a></center><br>
	<div style="height: 50px;">
	<form id="email_send" method="POST" action="/DidNumbers/email_summary/<?php echo $this->request->data['DidNumber']['id']; ?>" style="display: none; text-align:center;">Email address: <input type="text" name="email" value="<?php echo $this->request->data['DidNumber']['contact_email']; ?>" size="30">&nbsp;<input type="submit" value="Go">
	</form>
	</div>
</div>
<?php
} ?>

<div id="did_edit" class="did_numbers form">
<h1><?php echo $this->request->data['Account']['account_num']; ?> - <?php echo $this->request->data['DidNumber']['company']; ?></h1>
	<table class="gentbl" width="100%" cellpadding="4" cellspacing="0" border="0">
		<tr>
			<td width="50%"><h2>General Information</h2>
				<div class="input">
					<label>Contact Name</label><?php echo $this->request->data['DidNumber']['contact_name']; ?>
				</div>  
				<div class="input">
					<label>Contact Phone</label><?php echo $this->element('formatPhone', array('num' => $this->request->data['DidNumber']['contact_phone'])); ?>
				</div>  
				<div class="input">
					<label>Contact Email</label><?php echo $this->request->data['DidNumber']['contact_email']; ?>
				</div>  
				<div class="input">
					<label>Timezone</label><?php echo $global_options['timezone'][$this->request->data['DidNumber']['timezone']]; ?>
				</div>
				<div class="input">
					<label>Service Type</label><?php echo $global_options['type'][$this->request->data['DidNumber']['type']]; ?>
				</div>
						<div class="input">
					<label>Industry</label><?php echo $this->request->data['DidNumber']['industry'] ; ?>
				</div>        
			</td>
			<td width="50%"><h2>Billing Information</h2>
				<div class="input">
					<label>Billing Address 1</label><?php echo $this->request->data['Account']['billing_address1']; ?>
				</div>  
				<div class="input">
					<label>Billing Address 2</label><?php echo $this->request->data['Account']['billing_address2']; ?>
				</div>  
				<div class="input">
					<label>Billing City</label><?php echo $this->request->data['Account']['billing_city'] . ', ' . $this->request->data['Account']['billing_state'] . ' ' . $this->request->data['Account']['billing_zip']; ?>
				</div>  
				
			</td>
		</tr>
</table>
	
	<h2>Operator Screen Info</h2>
	<div class="input">
		<label>Answer Phrase</label><?php echo str_replace(array('[c]', '[o]'), array($this->request->data['DidNumber']['company'], '[Operator]'), $this->request->data['DidNumber']['answerphrase']); ?>
	</div>  
	<?php
	if ($this->request->data['DidNumber']['address_visible']) {
		if (!empty($this->request->data['DidNumber']['address1'])) {
			?>
			<div class="input"><label>Address 1</label><?php echo $this->request->data['DidNumber']['address1'];?>
			</div>
			<?php
		}
		if (!empty($this->request->data['DidNumber']['address2'])) {
			?>
			<div class="input"><label>Address 2</label><?php echo $this->request->data['DidNumber']['address2'];?>
			</div>
			<?php
		}
		if (!empty($this->request->data['DidNumber']['city'])) {
			?>
			<div class="input"><label>City/State/Zip</label><?php echo $this->request->data['DidNumber']['city'] . ' ' . $this->request->data['DidNumber']['state'] . ' ' . $this->request->data['DidNumber']['zip'];?>
			</div>
			<?php
		}
	}
	else {
			?>
			<div class="input"><label>Address1</label><i>(none specified)</i>
			</div>
			<div class="input"><label>Address2</label><i>(none specified)</i>
			</div>
			<div class="input"><label>City</label><i>(none specified)</i>
			</div>
	<?php
	}
	
	
	if ($this->request->data['DidNumber']['main_phone_visible']) {
			?>
			<div class="input"><label>Main Phone</label><?php echo $this->element('formatPhone', array('num' => $this->request->data['DidNumber']['main_phone'])); ?>
			</div>
	<?php
	}
	else {
			?>
			<div class="input"><label>Main Phone</label><i>(none specified)</i>
			</div>
	<?php
	}
	
	if ($this->request->data['DidNumber']['alt_phone_visible']) {
			?>
			<div class="input"><label>Alt Phone</label><?php echo $this->element('formatPhone', array('num' => $this->request->data['DidNumber']['alt_phone'])); ?>
			</div>
		<?php
	}
	else {
			?>
			<div class="input"><label>Alt Phone</label><i>(none specified)</i>
			</div>
	<?php
	}

	
	if ($this->request->data['DidNumber']['main_fax_visible']) {
			?>
			<div class="input"><label>Fax</label><?php echo $this->element('formatPhone', array('num' => $this->request->data['DidNumber']['main_fax'])); ?>
			</div>
	<?php
	}
	else {
			?>
			<div class="input"><label>Fax</label><i>(none specified)</i>
			</div>
	<?php
	}
	
	if ($this->request->data['DidNumber']['website_visible']) {
			?>
			<div class="input"><label>Website</label><?php echo $this->request->data['DidNumber']['website'];?>
			</div>
	<?php
	}
	else {
			?>
			<div class="input"><label>Website</label><i>(none specified)</i>
			</div>
	<?php
	}
	
	if ($this->request->data['DidNumber']['email_visible']) {
			?>
			<div class="input"><label>Email</label><?php echo $this->request->data['DidNumber']['email'];?>
			</div>
	<?php
	}
	else {
			?>
			<div class="input"><label>Email</label><i>(none specified)</i>
			</div>
	<?php
	}
	
	if ($this->request->data['DidNumber']['hours_visible']) {
			?>
			<div class="input"><label>Hours</label><?php echo $this->request->data['DidNumber']['hours'];?>
			</div>
	<?php
	}
	else {
			?>
			<div class="input"><label>Hours</label><i>(none specified)</i>
			</div>
	<?php
	}   
			
	?>
	<h2>Calltypes & Instructions</h2>
	<div class="calltypes">
	<?php
	
	foreach ($calltypes as $c) {
		if (count($c['Schedule']) > 0) {
		?>
		<div class="calltype">
		<h3><?php echo $c['Calltype']['title'];?></h3>
		<?php
		
		foreach ($c['Schedule'] as $s) {
			$actions = $s['Action'];
			echo '<div class="schedule">';
			echo '<i>' . $s['schedule'] . '</i>';
			echo '<div class="actions">';
				
			$current_section = '';
			foreach ($actions as $ak => $action) {
				$thehtml = '';          
				if ($current_section == '' || $current_section != $action['section']) {
					if (!empty($sections[$s['id']][$action['section']])) $section_title = $sections[$s['id']][$action['section']]['title'];
					else $section_title = '';
					$thehtml .= '<div class="script_section"><div class="section_title">SECTION: '.$section_title.'</div>'; 
				}
				$current_section = $action['section'];
				$prompts = $action['Prompt']; 
				// check if action is only visible to dispatchers
				if ($action['dispatch_only']) $dispatch_class = " dispatcher";
				else $dispatch_class = '';
					
				if ($action['action_label'] != '') {
					$thehtml .= '<div class="action_label">'.$action['action_label'].'</div>';
				}		
				$thehtml .= '<div class="step'.$dispatch_class.'">';
				$prompts = $action['Prompt'];

				if ($action['action_type'] == '50') {
					$thehtml .= '<div class="action">'.str_replace("\r\n", "<br>", $action['action_text']);
					if ($action['helper']) $thehtml .=  '<div class="helpertxt">'. $action['helper'] . '</div>';
					$thehtml .= '</div>';

						
				}
				else {
					if (is_array($prompts) && sizeof($prompts)) {
						$thehtml .= '<div class="action">'.str_replace("\r\n", "<br>", $action['action_text']) . '<div class="prompts">';
						$sort = $action['sort'];
						foreach ($prompts as $k => $p) {
							$class = '';
							if (isset($p['value'])) $val = $p['value'];
							else $val = '';
							if ($p['required']) {
									$title = '* ' . $p['caption'];
									$class .= " required";
							}
							else {
									$title = $p['caption'];
							}
							if (trim($p['caption']) == 'Phone Number') $class .= ' phone_field';
										$extra = '';
							if ($p['ptype'] == '3') {
									$options = explode('|', $p['options']);
									$extra = ' - Dropdown: ' .  implode(', ', $options);
							}
							else if ($p['ptype'] == '4') {
								$temp = explode('||', $p['options']);
								$options = explode('|', $temp[0]);
								$temp4 = explode('|', $temp[1]);
								
						 
								$extra = ' - Conditional: <blockquote>';
								foreach ($options as $k => $v) {
									$temp3 = explode('_', $temp4[$k]);
									//print_r($temp3);
									
									if ($temp3[0] == 1) $temp2[] = "$v -> Go to section: <i>" . $sections[$s['id']][$temp3[1]]['title'] . '</i>';
									else if ($temp3[0] == 2) $temp2[] = "$v -> Go to action label: <i>" . $temp3[1] . '</i>';
									else if ($temp3[0] == 3) $temp2[] = "$v -> Execute until action label: <i>" . $temp3[1] . '</i>';
									else $temp2[] = "$v -> <i>(no action)</i>";
								}
								$extra .= implode('<br>', $temp2) . '</blockquote>';
								$temp2 = array();
							}
							$thehtml .= '<div class="prompt '.$class.'">' . $title . $extra;
							$thehtml .= '</div>';
						}
						$thehtml .= '</div>';
						if ($action['helper']) $thehtml .=  '<div class="helpertxt">'. $action['helper'] . '</div>';                
						$thehtml .= '</div>';
						
					}
					else {
						$emp_exts = array();
						$emp_names = array();
						//if (isset($action['action_type']) && $action['action_type'] > 0) {
						if (1) {
							$emp = array();
							$oncall_list = false;
							// recipient of action is the requested employee instead of a specific employee
							if ($action['eid'] == 'ALL') {
									$emp[] = 'Requested Staff';
									$emp_exts[] = '';
									$emp_names[] = '';
							}
							// Recipient of action is an on-call list
							else if (substr($action['eid'], 0, 6) == 'ONCALL') {
								$oncall_id = str_replace('ONCALL_', '', $action['eid']);
								$list_title = $oncall[$oncall_id]['CallList']['title'];
								$emp[] = 'ON-CALL List (' . $list_title . ')';
								$emp_exts[] = '';
								$emp_names[] = '';
							}
							// Recipient of action is an on-call list
							else if (substr($action['eid'], 0, 8) == 'CALENDAR') {
								$cal_id = str_replace('CALENDAR_', '', $action['eid']);
								if ($cal_id == 'ALL') {
									$emp[] = 'Requested Calendar';
								}
								else {
									$cal_title = $calendars[$cal_id]['EaService']['name'];
									$emp[] = 'CALENDAR (' . $cal_title . ')';
								}
								$emp_exts[] = '';
								$emp_names[] = '';
							}               
							// recipient of action is a specific employee
							else if ($action['eid']) {
								$e_arr = explode(',', $action['eid']);
								foreach($e_arr as $eid) {
									$contact = $contacts[$eid]['contact'];
									$ext = $contacts[$eid]['ext'];
									$emp_exts[] = $ext;
									
									// construct email address for text message
									if ($contacts[$eid]['contact_type'] == CONTACT_TEXT) {
										$contact = $contact . ' - ' . $contacts[$eid]['carrier'];
									}

									if ($employees[$contacts[$eid]['employee_id']]['Employee']['gender'] == '1') 
										$emp[] = '<span class="female">'.$employees[$contacts[$eid]['employee_id']]['Employee']['name'].'</span> ('.$contact.')';
									else if ($employees[$contacts[$eid]['employee_id']]['Employee']['gender'] == '2')
										$emp[] = '<span class="male">'.$employees[$contacts[$eid]['employee_id']]['Employee']['name'].'</span> ('.$contact.')';
									else
										$emp[] = '<span>'.$employees[$contacts[$eid]['employee_id']]['Employee']['name'].'</span> ('.$contact.')';
									$emp_names[] = $employees[$contacts[$eid]['employee_id']]['Employee']['name'];
								}
							}
							if (isset($action['action_url']) && $action['action_url']) {
								$thehtml .=  '<div class="action">'. str_replace('[a]', $global_options['actions'][$action['action_type']]['label'], str_replace('[w]', '', str_replace("\r\n", "<br>", $action['action_text'])) );
								if ($action['helper']) $thehtml .=  '<div class="helpertxt">'. $action['helper'] . '</div>';
								$thehtml .= '</div>';
								
							}
							else  {
								$thehtml .=  '<div class="action">'. str_replace('[a]', $global_options['actions'][$action['action_type']]['label'], str_replace('[e]', implode(', ', $emp), str_replace("\r\n", "<br>", $action['action_text'])) );
								if ($oncall_list) $thehtml .= '<div class="oncallbox" id="oncall_'.$action['id'].'"></div>';
							 if ($action['helper']) $thehtml .=  '<div class="helpertxt">'. $action['helper'] . '</div>';
								$thehtml .= '</div>';
								
							}
						 
						}   
				
						else {
							if ($action['helper']) $thehtml .=  '<div class="helpertxt">'. $action['helper'] . '</div>';
							
						}
					}
				}
		
		
				$thehtml .= "</div>";
				// check if we need to close off current script section
				if (($ak == (count($actions) -1)) || $action['section'] != $actions[$ak+1]['section']) {
					if (isset($sections[$s['id']])) {
						$schedule_sections = $sections[$s['id']];
						if (!empty($schedule_sections[$action['section']])) {
							$section_action = $schedule_sections[$action['section']]['section_action'] ;
							if ($section_action > 0) {
								$section_num = $schedule_sections[$action['section']]['section_num'];
								$thehtml .= '<div class="section_action">Go to '. $schedule_sections[$section_num]['title'].'</div>';
							}
							else {
								$thehtml .= '<div class="section_action">Stop here</div>';
								
							}
						}
					}
					$thehtml .= '</div>';
				}
				
				echo $thehtml;
			}       
			echo '</div>';
			echo '</div>';

		}
		?>
		</div>
	</div>
		<?php
		}
	}
	?>
	</div>
	
	<h2>Call Summary</h2>
	<div class="calltypes">
	<?php
	if (sizeof($summaries) < 1) echo '<i>None found</i>';
	$destinations = array();
	foreach ($summaries as $m) {
		echo '<div class="">';
		if ($m['MessagesSummary']['destination_email']) $destinations = $destinations + explode(';', $m['MessagesSummary']['destination_email']);
		if ($m['MessagesSummary']['destination_fax']) $destinations[] = phoneFormat($m['MessagesSummary']['destination_fax']);
		if ($m['MessagesSummary']['employee_contact_ids']) {
			$eids = explode(',', $m['MessagesSummary']['employee_contact_ids']);
			foreach ($eids as $c) {
				if ($contacts[$c]['contact_type'] == CONTACT_FAX) $contact = $this->element('formatPhone', array('num' => $contacts[$c]['contact']));
				else $contact = $contacts[$c]['contact'];
				$destinations[] = implode(', ', explode(';', $contact)) . ' - ' . $contacts[$c]['label'];
			}
		}    
			$time_range = '';
			if ($m['MessagesSummary']['tx_interval']) $time_range = ", every {$m['MessagesSummary']['tx_interval']} minutes";
			if ($m['MessagesSummary']['all_day']) $time_range .= ", all day";
			else {
				if ($m['MessagesSummary']['start_time_f'] && $m['MessagesSummary']['end_time_f']) $time_range .= ", from {$m['MessagesSummary']['start_time_f']} to {$m['MessagesSummary']['end_time_f']}";
				if ($m['MessagesSummary']['send_time_f']) $time_range = ", at " . $m['MessagesSummary']['send_time_f'];
			}
			echo '<i>' . implode(', ', $m['MessagesSummary']['day_range']) . $time_range . '</i>';
			echo '<br><b>Send to:</b> ' . implode(', ', $destinations);
			echo '<br><b>Send \'No-message\' notification:</b> ';
			echo $m['MessagesSummary']['no_message']? 'Yes': 'No';
		echo '</div>';

	}
	?>
	</div>

	<h2>Employee Directory</h2>
	<div class="employees">
	<?php
	foreach ($employees as $e) {
		
		echo '<h3>' . $e['Employee']['name'] . '</h3>';
		if (!empty($e['Employee']['title'])) echo '&nbsp; &nbsp;' . $e['Employee']['title'];
		else echo '&nbsp; &nbsp;' . '<span class="unknown">(Unknown title)</span>';
		if (!empty($e['Employee']['gender'])) echo '&nbsp; &nbsp;' . $global_options['gender'][$e['Employee']['gender']];
		else echo '&nbsp; &nbsp;' . '<span class="unknown">(Unknown gender)</span>';
		echo '<div class="contacts">';
		foreach ($e['EmployeesContact'] as $c) {
			echo '<label>' . $c['label'] . ':</label>';
			if ($c['contact_type'] == CONTACT_PHONE || $c['contact_type'] == CONTACT_CELL || $c['contact_type'] == CONTACT_VMAIL ||$c['contact_type'] == CONTACT_TEXT || $c['contact_type'] == CONTACT_FAX) echo $this->element('formatPhone', array('num' => $c['contact']));
			else echo $c['contact'];
			if (!empty($c['ext'])) echo '&nbsp;&nbsp;Ext: ' . $c['ext'];
			if ($c['contact_type'] == CONTACT_TEXT) echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Carrier:</b> ' . (isset($carriers[trim($c['carrier_id'])])? $carriers[trim($c['carrier_id'])] : trim($c['carrier']));
			echo '<br>';
		}
		echo '</div>';
	}
	?>
	</div>
	
	<h2>Oncall Lists</h2>
	<div class="calltypes">
	<?php

	if (sizeof($oncall) < 1) echo '<i>None found</i>';
	
	foreach ($oncall as $c) {
		echo '<h3>'.$c['CallList']['title'].'</h3>';
		if (count($c['CallListsSchedule']) > 0) {
		?>
		<div class="calltype">
		<?php

		foreach ($c['CallListsSchedule'] as $s) {
			echo '<div class="schedule">';
			echo '<i>' . $s['schedule'] . '</i>';
			$ids = explode(',', $s['employee_ids']);

			$names = array();
			foreach ($ids as $id) {
				//print_r($employees[$id]);
				if (!empty($employees[$id])) $names[] = $employees[$id]['Employee']['name'];
			}
			if (sizeof($names) > 0) {
				echo '<br>';
				echo implode(', ', $names);
			}
			else if (trim($s['legacy_list'])) echo '<br>' . str_replace(array('\n', "\r\n"), ', ', $s['legacy_list']);
			
			echo '</div>';      
		}
		echo '</div>';
		}
		else {
			echo '<div class="schedule">';
			echo '<i>No schedules for this list</i></div>';
		}
	}
?>
	<h2>Calendars</h2>
	<div class="calltype">
	<?php
	foreach ($calendars as $c) {
		
		echo '<h3>' . $c['EaService']['name'] . '</h3>';
		
		if (!empty($c['EaService']['description'])) echo '<p><i>' . $c['EaService']['description'] . '</i></p>';
		echo '<div class="schedule">';
		echo '<i>Providers</i><br>';
		foreach ($c['EaProvider'] as $p) {
			//print_r($p);
			echo $p['employee']['e']['name'] . ' - ' . $p['employee']['c']['contact'];
			if ($p['notification']) echo ' (NOTIFY)<br>';
			else echo '<br>';
		}
		echo '</div>';
	}
	?>
	</div>
</div>
		
