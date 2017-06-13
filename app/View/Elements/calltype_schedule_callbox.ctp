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
  if (isset($json['prompts'][$action_id])) {
  	$prompts = $json['prompts'][$action_id];
  }
  else $prompts = '';
  if (isset($actions[$idx])) {
  	$action = $actions[$idx];
  }
  else $action = '';
 	if ($action['action_type'] == '50') {
  		$thehtml .= '<div class="action">'.str_replace("\r\n", "<br>", $action['action_text']) . '</div>';
		  $buttonhtml = '';
   		if ($action['helper']) $thehtml .=  '<div class="helper">'. $action['helper'] . '</div>';
		  
  }
	else {
  	if (is_array($prompts) && sizeof($prompts)) {
  		$buttonhtml = '';
  		$thehtml .= '<div class="action">'.str_replace("\r\n", "<br>", $action['action_text']) . '</div><div id="prompts">';
  		if ($action['helper']) $thehtml .=  '<div class="helper">'. $action['helper'] . '</div>';
  		$sort = $action['sort'];
  		foreach ($prompts as $k => $p) {
  			$class = '';
  			if ($p['options'] == '{CID}') $class = ' fill_cid';
  			if ($p['options'] == '{CIDNAME}') $class = ' fill_cidname';
  			if (isset($p['value'])) $val = $p['value'];
  			else $val = '';
  			$thehtml .= '<div class="prompt"><label>'.$p['caption'].'</label><input type="hidden" value="'.$p['maxchar'].'" name="pmaxchar['.$sort.'][]" /><input type="hidden" value="'.$p['ptype'].'" name="ptype['.$sort.'][]" /><input type="hidden" value="'.$p['caption'].'" name="ptitle['.$sort.'][]" />';
  			if ($p['ptype'] == '1') $thehtml .= '<input type="text" size="30" spellcheck="true" class="uprompt'.$class.'" value="'.$val.'" maxlength="'.$p['maxchar'].'" name="pvalue['.$sort.'][]" /><div class="uprompt_max">0/'.$p['maxchar'].'</div>';
  			else if ($p['ptype'] == '2') $thehtml .= '<textarea rows="1" cols="30" class="uprompt" maxlength="'.$p['maxchar'].'"  name="pvalue['.$sort.'][]" >'.$val.'</textarea><div class="uprompt_max">0/'.$p['maxchar'].'</div>';
  			$thehtml .= '</div>';
  		  			//$thehtml .= '<li>' . $p['caption'] . '</li>';
  		//	if ($p['type'] == '1') echo $this->Form->input('username', array('label' => $p['caption'], 'size' => 30));
  		}
  		$thehtml .= '</div>';
  	}
  	else {
    	$buttonhtml = '';
    	$employees = $json['employees']; 
    	$employees_contacts = $json['contacts']; 
    	$emp_exts = array();
    	$emp_names = array();
    	//if (isset($action['action_type']) && $action['action_type'] > 0) {
    	if (1) {
   			$emp = array();
        $oncall_list = false;
    		$emp_contacts = array();
    		//get arrays of action recipients
    		if ($action['eid'] == 'ALL') {
    				$emp[] = 'Requested Staff';
    				$emp_contacts[] = 'ALL';
    				$emp_exts[] = '';
    				$emp_names[] = '';
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
          $oncall_list = true;
    			$emp_exts[] = '';
    			$emp_names[] = '';
    		}
    		else if ($action['eid']) {
    			$e_arr = explode(',', $action['eid']);
    			foreach($e_arr as $eid) {
    			  $contact = $employees_contacts[$eid]['contact'];
    			  $ext = $employees_contacts[$eid]['ext'];
    			  $emp_exts[] = $ext;
    			  if ($employees_contacts[$eid]['contact_type'] == CONTACT_TEXT) $contact = $contact . '@' . $employees_contacts[$eid]['carrier'];
    				$emp_contacts[] = $contact;
    				$emp_contacts_label[] = $employees_contacts[$eid]['label'];
    				if ($employees[$employees_contacts[$eid]['employee_id']]['gender'] == '1') 
    					$emp[] = '<span class="female">'.$employees[$employees_contacts[$eid]['employee_id']]['name'].'</span>';
    				else if ($employees[$employees_contacts[$eid]['employee_id']]['gender'] == '2')
    					$emp[] = '<span class="male">'.$employees[$employees_contacts[$eid]['employee_id']]['name'].'</span>';
            else
    					$emp[] = '<span>'.$employees[$employees_contacts[$eid]['employee_id']]['name'].'</span>';
    			  $emp_names[] = $employees[$employees_contacts[$eid]['employee_id']]['name'];
    			}
    		}
    	  if (isset($action['action_url']) && $action['action_url']) {
    		  $thehtml .=  '<div class="action">'. str_replace('[a]', $global_options['actions'][$action['action_type']]['label'], str_replace('[w]', '', str_replace("\r\n", "<br>", $action['action_text'])) ). '</div>';
    		  
    		}
    		else  {
    		  $thehtml .=  '<div class="action">'. str_replace('[a]', $global_options['actions'][$action['action_type']]['label'], str_replace('[e]', implode(', ', $emp), str_replace("\r\n", "<br>", $action['action_text'])) );
    		  if ($oncall_list) $thehtml .= '<div class="oncallbox" id="oncall_'.$action['id'].'"></div>';
    		  $thehtml .= '</div>';
    		}
    		if ($action['helper']) $thehtml .=  '<div class="helper">'. $action['helper'] . '</div>';
        
        $buttonhtml = '';

        if (!$oncall_list) {  				
          FireCake::log($action);
        	if ($action['action_type'] && $action['action_type'] == ACTION_TXF) {  // TXF
        		$buttonhtml = '<button class="actbtn c_txf" contact_id="'.$action['eid'].'" emp_name="'.$emp_names[0].'" btype="'.CONTACT_PHONE.'" action_type="btn_txfr" bdata="'.$emp_contacts[0].'" ext="'.$emp_exts[0].'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;" blabel="Transfer">Transfer</button><div class="action_chk"></div>';
        	}
        	else if ($action['action_type'] && $action['action_type'] == ACTION_TXF_DELIVER ) {  // TXF
        		$buttonhtml = '<button class="actbtn c_txf" contact_id="'.$action['eid'].'" emp_name="'.$emp_names[0].'" btype="'.CONTACT_PHONE.'" action_type="btn_txfr_deliver" bdata="'.$emp_contacts[0].'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Transfer">Transfer & Deliver</button><div class="action_chk"></div>';
        	}   
        	   	
        	else if ($action['action_type'] && $action['action_type'] == ACTION_BLINDTXF) {  // Blind TXF
        		$buttonhtml = '<button class="actbtn c_txf" contact_id="'.$action['eid'].'" emp_name="'.$emp_names[0].'" btype="'.CONTACT_PHONE.'" action_type="btn_txfr" bdata="'.$emp_contacts[0].'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Transfer">Blind Transfer</button><div class="action_chk"></div>';
        	}          	
        	else if ($action['action_type'] && $action['action_type'] == ACTION_BLINDTXF_DELIVER) {  // TXF
        		$buttonhtml = '<button class="actbtn c_txf" contact_id="'.$action['eid'].'" emp_name="'.$emp_names[0].'" btype="'.CONTACT_PHONE.'" action_type="btn_txfr_deliver" bdata="'.$emp_contacts[0].'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Transfer">Blind Transfer and Deliver</button><div class="action_chk"></div>';
        	}          	
    /*define('ACTION_TXF', '1');
    define('ACTION_BLINDTXF', '2');
    define('ACTION_TXTMSG', '3');
    define('ACTION_EMAIL', '4');
    define('ACTION_WEB', '5');
    define('ACTION_VMOFFER', '6');
    define('ACTION_HOLD', '7');
    define('ACTION_EMAIL_DELIVER', '8');
    define('ACTION_TEXT_DELIVER', '9');
    define('ACTION_DELIVER', '10');
    define('ACTION_EMAIL_MINDER', '13');
    */
        	else if ($action['action_type'] == ACTION_EMAIL) {  
      			$buttonhtml = '<button class="actbtn c_email" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_EMAIL.'" bdata="'. implode(',', $emp_contacts).'" action_type="btn_email" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;" blabel="Email">Email</button><div class="action_chk"></div>';
        	}
        	else if ($action['action_type'] == ACTION_DISPATCH) {  
      			$buttonhtml = '<button class="actbtn c_dispatch" bdata="" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" action_type="btn_dispatch" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;">DISPATCH</button><div class="action_chk"  blabel="Dispatch"></div>';
        	}    	
        	else if ($action['action_type'] == ACTION_TXTMSG) {  
      			$buttonhtml = '<button class="actbtn c_text" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_TEXT.'" bdata="'. implode(',', $emp_contacts).'" action_type="btn_text" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Text">Text</button><div class="action_chk"></div>';
        	}    	
        	else if ($action['action_type'] == ACTION_EMAIL_DELIVER) {  
      			$buttonhtml = '<button class="actbtn c_email" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_EMAIL.'" bdata="'. implode(',', $emp_contacts).'" action_type="btn_email_deliver" sschedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Email & Deliver">Email & Deliver</button><div class="action_chk"></div>';
        	}   
        	else if ($action['action_type'] == ACTION_EMAIL_MINDER) {  
      			$buttonhtml = '<button class="actbtn c_email" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_EMAIL.'" bdata="'. implode(',', $emp_contacts).'" action_type="btn_email_minder" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Email & Minder">Email & Minder</button><div class="action_chk"></div>';
        	}     	 	
        	else if ($action['action_type'] == ACTION_FAX_DELIVER) {  
      			$buttonhtml = '<button class="actbtn c_fax" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_FAX.'" bdata="'. implode(',', $emp_contacts).'" action_type="btn_fax_deliver" sschedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Fax & Deliver">Fax & Deliver</button><div class="action_chk"></div>';
        	}   
        	else if ($action['action_type'] == ACTION_FAX) {  
      			$buttonhtml = '<button class="actbtn c_fax" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_FAX.'" bdata="'. implode(',', $emp_contacts).'" action_type="btn_fax" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Fax">Fax</button><div class="action_chk"></div>';
        	}     	 	
        	else if ($action['action_type'] == ACTION_TEXT_DELIVER) {  
      			$buttonhtml = '<button class="actbtn c_text" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_TEXT.'" bdata="'. implode(',', $emp_contacts).'" action_type="btn_text_deliver" sschedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Text & Deliver">Text & Deliver</button><div class="action_chk"></div>';
        	}    	
        	else if ($action['action_type'] == ACTION_HOLD) {  // MSG
      			$buttonhtml = '<button class="actbtn c_hold" bdata="'. implode(',', $emp_contacts).'" emp_name="" action_type="btn_hold" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Hold Message">Hold Message</button><div class="action_chk"></div>';
        	} 
        	else if ($action['action_type'] == ACTION_VMOFFER) {  // MSG
      			$buttonhtml = '<button class="actbtn c_vmail" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_PHONE.'" bdata="'. implode(',', $emp_contacts).'" action_type="btn_voicemail" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Voicemail">Voicemail</button><div class="action_chk"></div>';
        	} 
        	else if ($action['action_type'] == ACTION_DELIVER) {  // MSG
      			$buttonhtml = '<button class="actbtn c_deliver" bdata="'. implode(',', $emp_contacts).'" emp_name=""  action_type="btn_deliver" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Deliver">Deliver</button><div class="action_chk"></div>';
        	} 
      	  else if ($action['action_type'] == ACTION_WEB) {  // WEB
      		  $buttonhtml = '<button class="actbtn c_web" contact_id="'.$action['eid'].'" emp_name=""  bdata="'. implode(',', $emp_contacts).'" action_type="btn_web" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Web">Web</button><div class="action_chk"></div>';
      	  }
    	      	   	
        }
    	}	
  
    	else {
    		if ($action['helper']) $thehtml .=  '<div class="helper">'. $action['helper'] . '</div>';
    	}
    }
  }
	
	    		if ($action['eid'] == 'ALL') echo $thehtml;
	    		else echo $buttonhtml . $thehtml;
?>