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
  if (isset($json['prompts'])) {
  	$prompts = $json['prompts'];
  }
  else $prompts = '';
  if (isset($actions[$idx])) {
  	$action = $actions[$idx];
  }
  else $action = '';
	if ($action['dispatch_only']) $dispatch_class = " dispatcher";
	else $dispatch_class = "";	
	if (!$action['action_type']) {
		$buttonhtml = '';
		$thehtml .= '<div class="action'.$dispatch_class.'">'.str_replace("\r\n", "<br>", $action['action_text']). '</div>';
	}
	else {
  	$buttonhtml = '';
  	$employees = $json['employees']; 
  	$employees_contacts = $json['contacts']; 
  	//if (isset($action['action_type']) && $action['action_type'] > 0) {
  	$emp = array();
  	if (1) {
  	  $action['helper'] = str_replace(array("<br>", "<br />", "<br/>"), "\r\n", $action['helper']);
      $action['helper']	= str_replace("\r\n", "<br>", strip_tags($action['helper']));
  	  
      $dispatch = false;
  		$emp_contacts = array();
  		//get arrays of action recipients
  		if ($action['eid'] == 'ALL') {
  				$emp[] = 'Requested Staff';
  				$emp_contacts[] = 'ALL';
  		}
  		else if (substr($action['eid'], 0, 6) == 'ONCALL') {
  		  $oncall_id = str_replace('ONCALL_', '', $action['eid']);
  		  foreach ($json['call_lists'] as $k => $l) {
  		    
  		    if ($l['call_list_id'] == $oncall_id) {
  		      $list_title = '('.$k.')';
  		      break;
  		    }
  		    else $list_title = '';
  		  }
        $emp[] = 'ON-CALL List ' . $list_title;
        $dispatch = true;
  		}  	
  		else if (substr($action['eid'], 0, 8) == 'CALENDAR') {
  		  $calendar_id = str_replace('CALENDAR_', '', $action['eid']);
        if (isset($json['calendars'][$calendar_id])) $emp[] = $json['calendars'][$calendar_id]['name'];

  		}  			
  		else if (!empty($action['eid']) && $action['eid'] != 'null') {
  			$e_arr = explode(',', $action['eid']);
  			
  			foreach($e_arr as $eid) {
  				$emp_contacts[] = $employees_contacts[$eid]['contact'];
  				$label = $employees[$employees_contacts[$eid]['employee_id']]['name'] . ' ('.$employees_contacts[$eid]['label'].')';
  				if ($employees[$employees_contacts[$eid]['employee_id']]['gender'] == '1') 
  					$emp[] = '<span class="female">'.$label.'</span>';
  				else if ($employees[$employees_contacts[$eid]['employee_id']]['gender'] == '2')
  					$emp[] = '<span class="male">'.$label.'</span>';
          else
  					$emp[] = '<span>'.$label.'</span>';
  			}
  		}

//  	  if (isset($action['action_url']) && $action['action_url']) {
			if (0) {
  		  $thehtml .=  '<div class="action'.$dispatch_class.'">'. str_replace('[a]', $global_options['actions'][$action['action_type']]['label'], str_replace('[w]', $action['action_url'], str_replace("\r\n", "<br>", $action['action_text'])) ). '</div>';
  		}
  		else {
  		  $thehtml .=  '<div class="action'.$dispatch_class.'">'. str_replace('[a]', $global_options['actions'][$action['action_type']]['label'], str_replace('[e]', implode(', ', $emp), str_replace("\r\n", "<br>", $action['action_text'])) ). '</div>';
  		}
  		if (trim(strip_tags($action['helper']))) $thehtml .=  '<div class="helpertxt">'. $action['helper'] . '</div>';
  		
    	$buttonhtml = '';
  		if (!$dispatch) {		
  			/*
    	if ($action['action_type'] <= ACTION_TXF || $action['action_type'] <= ACTION_BLINDTXF) {  // TXF
    		$buttonhtml = '<button class="actbtn c_txf msg" btype="btn_txfr" num="'.$emp_contacts[0].'" sid="'.$action['schedule_id'].'" aid="'.$action['id'].'" onclick="btnClickHandler(this); return false;">Transfer</button><div class="action_chk"></div>';
    	}
    	
    	else if ($action['action_type'] == ACTION_EMAIL) {  
  			$buttonhtml = '<button class="actbtn c_email msg" addr="'. implode(',', $emp_contacts).'" btype="btn_email" sid="'.$action['schedule_id'].'" aid="'.$action['id'].'" onclick="executeAction(this); return false;">Email</button><div class="action_chk"></div>';
    	}
    	else if ($action['action_type'] == ACTION_TXTMSG) {  
  			$buttonhtml = '<button class="actbtn c_text msg" addr="'. implode(',', $emp_contacts).'" btype="btn_text" sid="'.$action['schedule_id'].'" aid="'.$action['id'].'" onclick="executeAction(this); return false;">Text</button><div class="action_chk"></div>';
    	}    	
    	else if ($action['action_type'] == ACTION_EMAIL_DELIVER) {  
  			$buttonhtml = '<button class="actbtn c_email msg" addr="'. implode(',', $emp_contacts).'" btype="btn_email_deliver" sid="'.$action['schedule_id'].'" aid="'.$action['id'].'" onclick="executeAction(this); return false;">Email & Deliver</button><div class="action_chk"></div>';
    	}    	
    	else if ($action['action_type'] == ACTION_TEXT_DELIVER) {  
  			$buttonhtml = '<button class="actbtn c_text msg" addr="'. implode(',', $emp_contacts).'" btype="btn_text_deliver" sid="'.$action['schedule_id'].'" aid="'.$action['id'].'" onclick="executeAction(this); return false;">Text & Deliver</button><div class="action_chk"></div>';
    	}    	
    	else if ($action['action_type'] == ACTION_HOLD) {  // MSG
  			$buttonhtml = '<button class="actbtn c_hold msg" addr="'. implode(',', $emp_contacts).'" btype="btn_hold" sid="'.$action['schedule_id'].'" aid="'.$action['id'].'" onclick="executeAction(this); return false;">Hold Message</button><div class="action_chk"></div>';
    	} 
    	else if ($action['action_type'] == ACTION_VMOFFER) {  // MSG
  			$buttonhtml = '<button class="actbtn c_vmail msg" addr="'. implode(',', $emp_contacts).'" btype="btn_voicemail" sid="'.$action['schedule_id'].'" aid="'.$action['id'].'" onclick="executeAction(this); return false;">Voicemail</button><div class="action_chk"></div>';
    	} 
    	else if ($action['action_type'] == ACTION_DELIVER) {  // MSG
  			$buttonhtml = '<button class="actbtn c_deliver msg" addr="'. implode(',', $emp_contacts).'" btype="btn_deliver" sid="'.$action['schedule_id'].'" aid="'.$action['id'].'" onclick="executeAction(this); return false;">Deliver</button><div class="action_chk"></div>';
    	} 
  	  else if ($action['action_type'] == ACTION_WEB) {  // WEB
  		  $buttonhtml = '<button class="actbtn c_web msg" id="btn_web" url="'.$action['action_url'].'" btype="btn_web" sid="'.$action['schedule_id'].'" aid="'.$action['id'].'" onclick="executeAction(this); return false;">Web</button><div class="action_chk"></div>';
  	  }*/
  	      	   	
      }
  	}	

  	else {
  		if ($action['helper']) $thehtml .=  '<div class="helper">'. $action['helper'] . '</div>';
  	}
  }
	
	//echo $buttonhtml . $thehtml;
  echo $thehtml;	
?>