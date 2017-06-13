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
	.invalid {color: red;font-style: italic;}
	.invalid:before {
	  content: '{'
	}
	.invalid:after {
	  content: '}'
	}
	
@media print {
		.noprint {
				display: none;
		}
}   
</style>
ID: <?php echo $s['Schedule']['id']; ?><br><br>
<?php
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
								  if (!empty($contacts[$eid])) {
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
  								else {
  								   $emp[] = '<span class="invalid">ERROR</span>';
  								}
								}
							}
							else {
							  $emp[] = '<span class="invalid">ERROR</span>';
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

		?>