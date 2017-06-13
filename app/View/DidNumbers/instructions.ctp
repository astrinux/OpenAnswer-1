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
if ($json['success']) {
    $tabs = '<ul id="oncall_lists">';
    $cnt = 1;
    $divs = '';
    
    
    //Build HTML for call lists
    
    //Remove any call lists that should not be visible to the operators
    foreach($json['call_lists'] as $title => $l) {
        if ($l['hide_from_operator']) unset($json['call_lists'][$title]);
    }
    
    //for all remaining call lists
    if (sizeof($json['call_lists']) > 0) {
        foreach ($json['call_lists'] as $title => $l) {
            //Create the tab for this call list
            $tabs .= '<li><a href="#oc-tabs-'.$cnt.'">'. $title.'</a></li>' . "\r\n";
            if ($l['legacy'] && trim($l['legacy_list'])) {
                $divs .= '<div id="oc-tabs-'.$cnt.'">'.str_replace("\r\n", "<br>", $l['legacy_list']).'</a>' .  '<br><br>Click <a onclick="openDialogWindow(\'/CallLists/view_all/'.$did_id.'/all\', \'Oncall list\', null, null, 450, 700);" href="#">here</a> to see all lists' ."</div>\r\n";
            }
            else {
                $divs .= '<div id="oc-tabs-'.$cnt.'">';
                if (trim($l['employee_ids'])) {
                    $temp = explode(',', $l['employee_ids']);
                }
                else $temp = array();
                
                //Create an entry on the call list tab for each employee on the call list
                foreach($temp as $t) {
                    if (!empty($json['employees'][$t]['gender'])) {
                        $gendercls = strtolower($global_options['gender'][$json['employees'][$t]['gender']]);
                    }
                    else $gendercls = '';
                    $divs .= '<span class="'.$gendercls.'"></span>&nbsp;<a href="#" eid="'.$json['employees'][$t]['id'].'" onclick="$(\'#emp_picker_wrapper\').show();$(\'#show_emp_picker\').val(\''.$json['employees'][$t]['id'].'\'); $(\'#show_emp_picker\').trigger(\'change\'); $(\'#gender_div\').removeClass().addClass(\''.$gendercls.'\');return false;">' .$json['employees'][$t]['name']. '</a><br>';
                    //'<a href="">'.$json['employees'][$t]['name'].'</a><br>';
                }
                $divs .= '</a>';
                $divs .= '<br>Click <a onclick="openDialogWindow(\'/CallLists/view_all/'.$did_id.'/all\', \'Oncall list\', null, null, 450, 700);" href="#">here</a> to see all lists';        
                $divs .= "</div>\r\n";
            }
            $cnt++;
        }
    }
    else {
        //If there are no call lists, create a single tab of "(None)", with no entries on it.
            $tabs .= '<li><a href="#oc-tabs-'.$cnt.'">(None)</a></li>' . "\r\n";
            $divs .= '<div id="oc-tabs-'.$cnt.'">Click <a onclick="openDialogWindow(\'/CallLists/view_all/'.$did_id.'/all\', \'Oncall list\', null, null, 450, 700);" href="#">here</a> to see all lists</div>';
            $tabs .= "</li>";
    }
    
    
    //close the html for the on-call tabs
    $tabs .= '</ul>' . "\r\n";
    //store the html to display the oncall section in the json response variable
    $json['oncall_html'] = $tabs . $divs;
    
    
    $counter = 0;
    $prompt_select = array();
    //for each calltype schedule
    foreach ($json['schedules'] as $schedule) {
        $s_html = '<input type="hidden" value="" name="calltype_caption" id="calltype_caption" />';
        $s_html .= '<input type="hidden" value="" name="calltype_id" id="calltype_id" />';
        $s_html .= '<textarea  name="instructions" id="opinstr" class="is_hidden" ></textarea>';
        $s_html .= '<input type="hidden" value="" name="schedule_id" id="schedule_id" />';
        $s_html .= '<input type="hidden" value="" name="transfer_status" id="transfer_status" />';
        $s_html .= '<input type="hidden" value="" name="message_action" id="message_action" />';
        if (isset($json['ct_actions'][$schedule['id']])) {
            $actions = $json['ct_actions'][$schedule['id']];
        }
        else {
            $actions = array();
        }
        ksort($actions);
        $employee_select = '0';
        $oncall_list_select = '0';
        $required = array();
        $oncall_lists = array();
        $transfer_required = '0';
        $has_calendar = false;
        
        $section_num = 1;
        $sections = $json['sections'][$schedule['id']];
        // set first section to be visible by default
        $sections[1]['visible'] = 1; 

        // mark which section should be visible initially
        while (isset($sections[$section_num]) && $sections[$section_num]['section_action'] == '1') {
            $goto_section = $sections[$section_num]['section_num'];
            $sections[$goto_section]['visible'] = 1;
            $section_num = $goto_section;
        }
               
        $old_section = 0;
        $cnt = 0;
        $promptnum = 1;
        foreach ($actions as $ak => $a) {
            $thehtml = '';
            // check if it is the start of a new section and mark as hidden if necessary
            if ($a['section'] != $old_section) {
                if ($sections[$a['section']]['visible'] == 0) $temp = " is_hidden";
                else {
                    $temp = '';
                }

                $s_html .= ('<div id="section_'.($a['section']).'" class="script_section'.$temp.'" data-section="'.$a['section'].'" data-action="'.$sections[$a['section']]['section_action'].'" data-section-num="'.$sections[$a['section']]['section_num'].'"');
                $s_html .= '>'; 
                $hide_steps = false;
            }
            
            if (1) {
                
                // check if we need to display employee picker
                if ($a['eid'] == 'ALL') $employee_select = '1';  // if calltype is generic
                if (substr($a['eid'], 0, 6) == 'ONCALL') {  // if calltype needs access to oncall list
                    $oncall_list_select = '1';
                    $oncall_lists[] = array('action_id' => $a['id'], 'list_id' => str_replace('ONCALL_', '', $a['eid']));
                }
                
                // check if action is only visible to dispatchers
                if ($a['dispatch_only']) {
                    $dispatch_class = " dispatcher";
                }
                else $dispatch_class = '';
				if ($a['action_label']) $label = ' data-label="'.$a['action_label'].'" ';
				else $label = '';
                if ($hide_steps) $extra_class = ' is_hidden ';
                else $extra_class = '';
                $s_html .= '<div class="step'.$dispatch_class. $extra_class.'"'.$label.'>';
                
    
    
                //$this->element('calltype_schedule_callbox', array('idx' => $ak, 'actions' => $actions, 'action_id' => $a['id'], 'json' => $json)) . '</div>';
                $action_id = $a['id'];
                if (isset($json['prompts'][$action_id])) {
                    $prompts = $json['prompts'][$action_id];
                }
                else $prompts = '';
    
                $action = $actions[$ak];
    
                if ($action['action_type'] == '50') {
                        $thehtml .= '<div class="action atype'.$action['action_type'].'">'.str_replace("\r\n", "<br>", $action['action_text']) . '</div>';
                        $buttonhtml = '';
                        if ($action['helper']) $thehtml .=  '<div class="helpertxt">'. $action['helper'] . '</div>';
                        
                }
                else {
                    // create prompts 
                    if (is_array($prompts) && sizeof($prompts)) {
                        $buttonhtml = '';
                        
                        $thehtml .= '<div class="action atype'.$action['action_type'].'">'.str_replace("\r\n", "<br>", $action['action_text']) . '</div><div class="prompts">';
                        if ($action['helper']) $thehtml .=  '<div class="helpertxt">'. $action['helper'] . '</div>';
                        $sort = $action['sort'];
                        foreach ($prompts as $k => $p) {
                            $class = $input_class = '';
                            if (isset($p['value'])) $val = $p['value'];
                            else $val = '';
                            if ($p['required']) {
                                $class .= " required-div";
                                $input_class .= " required";
                            }
                            $element_id = '';
                            $img = '';
                            if (trim($p['verification']) == '1') $input_class .= ' phone_field';
                            else if (trim($p['verification']) == '2') $input_class .= ' email_field';
                            else if (trim($p['verification']) == '3' && !empty($google_api_key)) {
                                $input_class .= ' street_field';
                            $img = ' <img src="/themes/vn/google.png" width="80";>';
                            }
                            else if (trim($p['verification']) == '4' && !empty($google_api_key)) {
                                $input_class .= ' citystate_field';
                            $img = ' <img src="/themes/vn/google.png" width="80">';
                            }
                            else if (trim($p['verification']) == '5' && !empty($google_api_key)) {
                                $input_class .= ' fulladdr_field';
                            $img = ' <img src="/themes/vn/google.png" width="80">';
                            }
                            
    
                            
                            $thehtml .= '<div class="prompt '.$class.'"><label>'.$p['caption'].'</label><input type="hidden" value="'.$p['maxchar'].'" name="pmaxchar['.$sort.'][]" /><input type="hidden" value="'.$p['ptype'].'" name="ptype['.$sort.'][]" /><input type="hidden" value="'.$p['caption'].'" name="ptitle['.$sort.'][]" /><input type="hidden" value="'.$p['options'].'" name="poptions['.$sort.'][]" />';
                            
                            // contruct the caller prompts depending on the type
                            // 1 = Single line input, 2 = Multi-line, 3 = drop down, 4 = conditional
                            if ($p['ptype'] == '1' || $img != '') {
                                $thehtml .= '<input ';
                                if ($element_id) $thehtml .= 'id="'. $element_id . '" ';
                                $thehtml .= ' prompt="'.$p['caption'].'" onfocus="return checkEditable(this);" type="text" size="40" spellcheck="true" class="uprompt'.$input_class.'" value="'.$val.'" maxlength="'.$p['maxchar'].'" name="pvalue['.$sort.'][]" /><div class="uprompt_max">0/'.$p['maxchar'].'</div>' . $img;
                            }
                            else if ($p['ptype'] == '2') {
                                $thehtml .= '<textarea ';
                                if ($element_id) $thehtml .= 'id="'. $element_id . '" ';
                                $thehtml .= ' prompt="'.$p['caption'].'"onfocus="return checkEditable(this);" rows="1" cols="40" class="uprompt'.$input_class.'" maxlength="'.$p['maxchar'].'"  name="pvalue['.$sort.'][]" >'.$val.'</textarea><div class="uprompt_max">0/'.$p['maxchar'].'</div>' . $img;
                            }
                            // text field with dropdown suggesions
                            else if ($p['ptype'] == '3') {
                                $thehtml .= '<input prompt="'.$p['caption'].'" id="sel_'.$schedule['id'].'_'.$ak.'_'.$k.'"onclick="return checkEditable(this);"  type="text" size="40" spellcheck="true" class="prompt_dd uprompt'.$class.'" value="'.$val.'" maxlength="'.$p['maxchar'].'" name="pvalue['.$sort.'][]" /><div class="uprompt_max">0/'.$p['maxchar'].'</div>' . $img;
                            }
                            // conditional select box
                            else if ($p['ptype'] == '4') {
                                $temp = explode('||', $p['options']);
                                $hide_steps = true;
                                
                                $poptions = explode('|', $temp[0]);
                                $pactions = explode('|', $temp[1]);
                                
                                $thehtml .= '<select data-promptnum="prompt'.$promptnum.'" class="conditional uprompt'.$input_class.'" ';
                                if ($element_id) $thehtml .= 'id="'. $element_id . '" ';
                                $thehtml .= 'onfocus="return checkEditable(this);"  class="uprompt'.$input_class.'"  name="pvalue['.$sort.'][]"><option value="">Select</option>';
                                foreach ($poptions as $k2=> $o) {
                                    $thehtml .= '<option value="'.$o.'" data-action="'.$pactions[$k2].'">'.$o.'</option>';
                                }
                                $thehtml .= '</select>';
                            }
                            
                            $thehtml .= '</div>';
                            $prompt_select["sel_".$schedule['id']."_".$ak."_".$k] = explode('|', $p['options']);
                            $promptnum++;
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
                                $list_title = '';
                                foreach ($json['call_lists'] as $k => $l) {
                                    
                                    if ($l['call_list_id'] == $oncall_id) {
                                        $list_title = '('.$k.')';
                                        break;
                                    }
                                }
                                $emp[] = 'ON-CALL List ' . $list_title;
                                $oncall_list = true;
                                $emp_exts[] = '';
                                $emp_names[] = '';
                            }
                            else if ($action['eid'] == 'CALENDAR_ALL') {
                                $emp[] = 'Requested Calendar';
                                $emp_contacts[] = 'ALL';
                                $emp_exts[] = '';
                                $emp_names[] = '';
                            }                   
                            else if (substr($action['eid'], 0, 8) == 'CALENDAR') {
                                $calendar_id = str_replace('CALENDAR_', '', $action['eid']);
                                if (isset($json['calendars'][$calendar_id])) $emp[] = $json['calendars'][$calendar_id]['name'];
                                $has_calendar = true;
                                $emp_exts[] = '';
                                $emp_names[] = '';
                            }               
                            else if ($action['eid']) {
                                $e_arr = explode(',', $action['eid']);
                                foreach($e_arr as $eid) {
                                    if (isset($employees_contacts[$eid])) {
                                        $contact = $employees_contacts[$eid]['contact'];
                                        $ext = $employees_contacts[$eid]['ext'];
                                        $emp_exts[] = $ext;
                                        if ($employees_contacts[$eid]['contact_type'] == CONTACT_TEXT) {
                                            $contact = substr($contact, -10);
                                            if (isset($employees_contacts[$eid]['addr'])) {
                                                $contact = $employees_contacts[$eid]['prefix'] . $contact . '@' . $employees_contacts[$eid]['addr'];
                                            }
                                            else {
                                                $contact = $contact . '@' . $employees_contacts[$eid]['carrier'];
                                            }
                                        }
                                        $emp_contacts[] = $contact;
                                        $emp_contacts_label[] = $employees_contacts[$eid]['label'];
                                        if ($employees[$employees_contacts[$eid]['employee_id']]['gender'] == '1') 
                                            $emp[] = '<span class="female">'.$employees[$employees_contacts[$eid]['employee_id']]['name'].'</span>';
                                        else if ($employees[$employees_contacts[$eid]['employee_id']]['gender'] == '2')
                                            $emp[] = '<span class="male">'.$employees[$employees_contacts[$eid]['employee_id']]['name'].'</span>';
                                        else
                                            $emp[] = $employees[$employees_contacts[$eid]['employee_id']]['name'];
                                        $emp_names[] = $employees[$employees_contacts[$eid]['employee_id']]['name'];
                                    }
                                    else {
                                        if ($eid != null && $eid != '') {
                                            mail(Configure::read('admin_email'), 'bad EID for action # ' . $action['id'], $action['schedule_id'] . ' action id: ' . $eid);
                                        }
                                    }
                                }
                            }
                            if (isset($action['action_url']) && $action['action_url']) {
                                $thehtml .=  '<div class="action atype'.$action['action_type'].'">'. str_replace('[a]', $global_options['actions'][$action['action_type']]['label'], str_replace('[w]', '', str_replace("\r\n", "<br>", $action['action_text'])) ). '</div>';
                                
                            }
                            else  {
                                //if ($action['action_type'] == 45) { //TLC don't force formatting for text/info, so it will keep the WYSIWYG from the editor
                                if (0) {
                                    $thehtml .=  '<div>'. str_replace('[a]', $global_options['actions'][$action['action_type']]['label'], str_replace('[e]', implode(', ', $emp), str_replace("\r\n", "<br>", $action['action_text'])) );
                                }
                                else {
                                    $thehtml .=  '<div class="action atype'.$action['action_type'].'">'. str_replace('[a]', $global_options['actions'][$action['action_type']]['label'], str_replace('[e]', implode(', ', $emp), str_replace("\r\n", "<br>", $action['action_text'])) );
                                }
                                if ($oncall_list) $thehtml .= '<div class="oncallbox" id="oncall_'.$action['id'].'"></div>';
                                $thehtml .= '</div>';
                            }
                            if ($action['helper']) $thehtml .=  '<div class="helpertxt">'. $action['helper'] . '</div>';
                            
                            $buttonhtml = '';
                            if ($action['eid'] == 'ALL') {
                                    $buttonhtml = '<button class="actbtn c_deliver" action_type="picker" dlv="no" txf="no" onclick="actionClickHandler(this); return false">Pick Employee</button><div class="action_chk"></div>';
                            }
                            else if ($action['eid'] == 'CALENDAR_ALL') {
                                    $buttonhtml = '<button class="actbtn c_deliver" action_type="picker" dlv="no" txf="no" onclick="actionClickHandler(this); return false">Pick Calendar</button><div class="action_chk"></div>';
                            }
                            else if ($action['action_type'] == ACTION_DELIVER) {  
                                    $buttonhtml = '<button class="actbtn c_deliver" txf="no" dlv="yes" did_id="'.$did_id.'" bdata="'. implode(',', $emp_contacts).'" emp_name=""   schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Deliver" action_type="'.ACTION_DELIVER.'">Deliver</button><div class="action_chk"></div>';
                            } 
            else if ((!$oncall_list && isset($emp_names[0])) || 
                $action['action_type'] == ACTION_HOLD || 
                $action['action_type'] == ACTION_DISPATCH || 
                $action['action_type'] == ACTION_CRM
                ) {
                                if ($action['action_type'] && $action['action_type'] == ACTION_TXF) {  // TXF
                                    $buttonhtml = '<button class="actbtn c_txf" txf="yes" dlv="no" did_id="'.$did_id.'" did="'. $did_number.'"  contact_id="'.$action['eid'].'" emp_name="'.$emp_names[0].'" btype="'.CONTACT_PHONE.'" action_type="'.ACTION_TXF.'" bdata="'.$emp_contacts[0].'" ext="'.$emp_exts[0].'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;" blabel="Transfer">Transfer</button><div class="action_chk"></div>';
                                }
                                else if ($action['action_type'] && $action['action_type'] == ACTION_TXF_DELIVER ) {  // TXF
                                    $buttonhtml = '<button class="actbtn c_txf"  txf="yes" dlv="yes" did_id="'.$did_id.'" did="'. $did_number.'" contact_id="'.$action['eid'].'" emp_name="'.$emp_names[0].'" ext="'.$emp_exts[0].'" btype="'.CONTACT_PHONE.'" action_type="'.ACTION_TXF.'" bdata="'.$emp_contacts[0].'" ext="'.$emp_exts[0].'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Transfer">Transfer & Deliver</button><div class="action_chk"></div>';
                                }   
                                else if ($action['action_type'] && $action['action_type'] == ACTION_LMR_DELIVER ) {  
                                    $buttonhtml = '<button class="actbtn c_txf" txf="yes" dlv="yes" did_id="'.$did_id.'" did="'. $did_number.'" contact_id="'.$action['eid'].'" emp_name="'.$emp_names[0].'" btype="'.CONTACT_PHONE.'" action_type="'.ACTION_LMR.'" bdata="'.$emp_contacts[0].'" ext="'.$emp_exts[0].'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="LMR">LMR & Deliver</button><div class="action_chk"></div>';
                                }               
                                else if ($action['action_type'] && $action['action_type'] == ACTION_LMR ) {  
                                    $buttonhtml = '<button class="actbtn c_txf"  txf="yes" dlv="no" did_id="'.$did_id.'" did="'. $did_number.'" contact_id="'.$action['eid'].'" emp_name="'.$emp_names[0].'" btype="'.CONTACT_PHONE.'" action_type="'.ACTION_LMR.'" bdata="'.$emp_contacts[0].'" ext="'.$emp_exts[0].'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="LMR">LMR</button><div class="action_chk"></div>';
                                }                   
                                else if ($action['action_type'] && $action['action_type'] == ACTION_BLINDTXF) {  // Blind TXF
                                    $buttonhtml = '<button class="actbtn c_txf"  txf="yes" dlv="no" did_id="'.$did_id.'" did="'. $did_number.'" contact_id="'.$action['eid'].'" emp_name="'.$emp_names[0].'" btype="'.CONTACT_PHONE.'" action_type="'.ACTION_BLINDTXF.'" bdata="'.$emp_contacts[0].'" ext="'.$emp_exts[0].'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Blind Transfer">Blind Transfer</button><div class="action_chk"></div>';
                                }           
                                else if ($action['action_type'] && $action['action_type'] == ACTION_BLINDTXF_DELIVER) {  // TXF
                                    $buttonhtml = '<button class="actbtn c_txf"  txf="yes" dlv="yes" did_id="'.$did_id.'" did="'. $did_number.'" contact_id="'.$action['eid'].'" emp_name="'.$emp_names[0].'" btype="'.CONTACT_PHONE.'" action_type="'.ACTION_BLINDTXF.'" bdata="'.$emp_contacts[0].'" ext="'.$emp_exts[0].'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Transfer">Blind Transfer and Deliver</button><div class="action_chk"></div>';
                                }           
                                else if ($action['action_type'] && $action['action_type'] == ACTION_TXF_NO_ANNOUNCEMENT) {  // TXF w/o announcement
                                    $buttonhtml = '<button class="actbtn c_txf"  txf="yes" dlv="no" did_id="'.$did_id.'" did="'. $did_number.'" contact_id="'.$action['eid'].'" emp_name="'.$emp_names[0].'" btype="'.CONTACT_PHONE.'" action_type="'.ACTION_TXF_NO_ANNOUNCEMENT.'" bdata="'.$emp_contacts[0].'" ext="'.$emp_exts[0].'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Transfer w/o announcement">Transfer</button><div class="action_chk"></div>';
                                }           
                                else if ($action['action_type'] && $action['action_type'] == ACTION_TXF_NO_ANNOUNCEMENT_DELIVER) {  // TXF w/o announcement
                                    $buttonhtml = '<button class="actbtn c_txf"  txf="yes" dlv="yes" did_id="'.$did_id.'" did="'. $did_number.'" contact_id="'.$action['eid'].'" emp_name="'.$emp_names[0].'" btype="'.CONTACT_PHONE.'" action_type="'.ACTION_TXF_NO_ANNOUNCEMENT.'" bdata="'.$emp_contacts[0].'" ext="'.$emp_exts[0].'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Transfer w/o announcement">Transfer and Deliver</button><div class="action_chk"></div>';
                                }        
                                else if ($action['action_type'] == ACTION_EMAIL) {  
                                    $buttonhtml = '<button class="actbtn c_email"  txf="no" dlv="no" did_id="'.$did_id.'" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_EMAIL.'" bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_EMAIL.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;" blabel="Email">Email</button><div class="action_chk"></div>';
                                }
                                else if ($action['action_type'] == ACTION_DISPATCH) {  
                                    $buttonhtml = '<button class="actbtn c_dispatch" bdata=""  txf="no" dlv="no" did_id="'.$did_id.'" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" action_type="'.ACTION_DISPATCH.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;" blabel="Dispatch">DISPATCH</button><div class="action_chk"></div>';
                                }       
                                else if ($action['action_type'] == ACTION_TXTMSG) {  
                                    $buttonhtml = '<button class="actbtn c_text" txf="no" dlv="no" did_id="'.$did_id.'" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_TEXT.'" bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_TXTMSG.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Text">Text</button><div class="action_chk"></div>';
                                }       
                                else if ($action['action_type'] == ACTION_EMAIL_DELIVER) {  
                                    $buttonhtml = '<button class="actbtn c_email" txf="no" dlv="yes" did_id="'.$did_id.'" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_EMAIL.'" bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_EMAIL.'" sschedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Email & Deliver">Email & Deliver</button><div class="action_chk"></div>';
                                }   
                                else if ($action['action_type'] == ACTION_EMAIL_MINDER) {  
                                    $buttonhtml = '<button class="actbtn c_email" txf="no" dlv="no" did_id="'.$did_id.'" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_EMAIL.'" bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_EMAIL_MINDER.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Email & Minder">Email & Minder</button><div class="action_chk"></div>';
                                }           
                                else if ($action['action_type'] == ACTION_FAX_DELIVER) {  
                                    $buttonhtml = '<button class="actbtn c_fax" txf="no" dlv="yes" did_id="'.$did_id.'" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_FAX.'" bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_FAX.'" sschedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Fax & Deliver">Fax & Deliver</button><div class="action_chk"></div>';
                                }   
                                else if ($action['action_type'] == ACTION_FAX) {  
                                    $buttonhtml = '<button class="actbtn c_fax" txf="no" dlv="no" did_id="'.$did_id.'" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_FAX.'" bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_FAX.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Fax">Fax</button><div class="action_chk"></div>';
                                }           
                                else if ($action['action_type'] == ACTION_TEXT_DELIVER) {  
                                    $buttonhtml = '<button class="actbtn c_text" txf="no" dlv="yes" did_id="'.$did_id.'" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_TEXT.'" bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_TXTMSG.'" sschedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Text & Deliver">Text & Deliver</button><div class="action_chk"></div>';
                                }       
                                else if ($action['action_type'] == ACTION_HOLD) {  // MSG
                                    $buttonhtml = '<button class="actbtn c_hold" txf="no" dlv="no" did_id="'.$did_id.'" bdata="'. implode(',', $emp_contacts).'" emp_name="" action_type="'.ACTION_HOLD.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Hold Message">Hold Message</button><div class="action_chk"></div>';
                                } 
                                else if ($action['action_type'] == ACTION_VMOFFER) {  // MSG
                                    $buttonhtml = '<button class="actbtn c_vmail" txf="yes" dlv="no" did_id="'.$did_id.'" did="'. $did_number.'" contact_id="'.$action['eid'].'" ext="'.$emp_exts[0].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_PHONE.'" bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_VMOFFER.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Voicemail">Voicemail</button><div class="action_chk"></div>';
                                } 
                                else if ($action['action_type'] == ACTION_VMOFFER_DELIVER) {  // MSG
                                    $buttonhtml = '<button class="actbtn c_vmail" ext="'.$emp_exts[0].'" txf="yes" dlv="yes" did_id="'.$did_id.'" did="'. $did_number.'" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_PHONE.'" bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_VMOFFER.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Voicemail">Voicemail and Deliver</button><div class="action_chk"></div>';
                                } 
                                else if ($action['action_type'] == ACTION_VM) {  // MSG
                                    $buttonhtml = '<button class="actbtn c_vmail" ext="'.$emp_exts[0].'" txf="yes" dlv="no" did_id="'.$did_id.'" did="'. $did_number.'" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_PHONE.'" bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_VM.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Voicemail">Voicemail</button><div class="action_chk"></div>';
                                } 
                                else if ($action['action_type'] == ACTION_VM_DELIVER) {  // MSG
                                    $buttonhtml = '<button class="actbtn c_vmail" ext="'.$emp_exts[0].'" txf="yes" dlv="yes" did_id="'.$did_id.'" did="'. $did_number.'" contact_id="'.$action['eid'].'" emp_name="'.implode(',', $emp_names).'" btype="'.CONTACT_PHONE.'" bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_VM.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Voicemail">Voicemail and Deliver</button><div class="action_chk "></div>';
                                } 
                                else if ($action['action_type'] == ACTION_WEB) {  // WEB
                                    $buttonhtml = '<button class="actbtn c_web" txf="no" dlv="no" did_id="'.$did_id.'" contact_id="'.$action['eid'].'" emp_name=""  bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_WEB.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Web">Web</button><div class="action_chk"></div>';
                                }
                                else if ($action['action_type'] == ACTION_CALENDAR) {  // scheduling calendar
                                    $buttonhtml = '<button class="actbtn c_web" txf="no" dlv="no" did_id="'.$did_id.'" contact_id="'.str_replace('CALENDAR_', '', $action['eid']).'" emp_name=""  bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_CALENDAR.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Calendar">Calendar</button><div class="action_chk"></div>';
                                }             
                                else if ($action['action_type'] == ACTION_CALENDAR_DELIVER) {  // scheduling calendar
                                    $buttonhtml = '<button class="actbtn c_web" txf="no" dlv="yes" did_id="'.$did_id.'" contact_id="'.str_replace('CALENDAR_', '', $action['eid']).'" emp_name=""  bdata="'. implode(',', $emp_contacts).'" action_type="'.ACTION_CALENDAR.'" schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" onclick="actionClickHandler(this); return false;"  blabel="Calendar">Calendar</button><div class="action_chk"></div>';
                                }             
                                else if ($action['action_type'] == ACTION_CRM) {  
                                    $buttonhtml = '<button class="actbtn c_web" txf="no" dlv="no" did_id="'.$did_id.'" did="'. $did_number.'"    action_type="'.ACTION_CRM.'"   schedule_id="'.$action['schedule_id'].'" action_id="'.$action['id'].'" crm_id="'.$action['action_opt'].'" onclick="actionClickHandler(this); return false;"  blabel="CRM-'.$action['name'].'">'.$action['name'].'</button><div class="action_chk"></div>';
                                }
                            }
                        }   
                
                        else {
                            if ($action['helper']) $thehtml .=  '<div class="helpertxt">'. $action['helper'] . '</div>';
                        }
                    }
                }
                
      
                 
                //if ($action['eid'] == 'ALL' || $action['eid'] == 'CALENDAR_ALL') $s_html .= $thehtml;
                if (0) {}
                else $s_html .= ($buttonhtml . $thehtml);
                $s_html .= "</div>";
                if ($a['action_type'] == ACTION_TXF || $a['action_type'] == ACTION_TXF_DELIVER) $transfer_required = '1';
                // check which fields are required for this picker
                if ($a['action_type'] == ACTION_TXF || $a['action_type'] == ACTION_TXF_DELIVER || $a['action_type'] == ACTION_BLINDTXF|| $a['action_type'] == ACTION_BLINDTXF_DELIVER) $required['phone'] = true;
                else if ($a['action_type'] == ACTION_TXTMSG || $a['action_type'] == ACTION_TEXT_DELIVER) $required['text'] = true;
                else if ($a['action_type'] == ACTION_EMAIL || $a['action_type'] == ACTION_EMAIL_DELIVER) $required['email'] = true;
                else if ($a['action_type'] == ACTION_VMOFFER || $a['action_type'] == ACTION_VMOFFER_DELIVER) $required['vmail'] = true;
            }

            // close off section if necessary
            if ($cnt == (sizeof($actions)-1) || ($action['section'] != $actions[$ak+1]['section'])) {
                $s_html .= '</div>';
            }
            $cnt++;
            $old_section = $action['section'];              
            
        }
        $extra_msg = '<div class="addnotes">+ <a href="#" onclick="$(\'#operatorScreen #misc_div\').toggle();$(\'#operatorScreen #miscnotes\').prop(\'disabled\', false);return false;">add notes</a></div><div id="misc_div" class="is_hidden step"><div class="prompts"><div class="prompt"><label>Misc</label><input type="hidden"  class="miscnotes" value="255" name="pmaxchar[99][]" /><input  class="miscnotes" type="hidden" value="2" name="ptype[99][]" /><input  class="miscnotes" type="hidden" value="" name="poptions[99][]" /><input type="hidden"  class="miscnotes" value="Misc" name="ptitle[99][]" />';
        
        $extra_msg .= '<textarea id="miscnotes" class="miscnotes" onfocus="return checkEditable(this);"  rows="1" cols="40" class="uprompt" maxlength="255"  name="pvalue[99][]" ></textarea><div class="uprompt_max">0/255</div></div></div></div>';

        if (1) { // always show employee picker for now
                $emp_select = '<a href="#" class="a_noul" onclick="$(\'#emp_picker_wrapper\').toggle();dialogLayout.sizeContent(\'center\');return false;">&nbsp;&nbsp;&raquo;</a>&nbsp;&nbsp;<span id="emp_picker_wrapper">Employee: <select style="width:200px" id="show_emp_picker" onchange="$(\'#cb_empcontacts\').html(getEmployeeButtons(this.value, currentInstructions[\'employees\'], false, callId, \''.$did_id.'\', \''.$did_number.'\')); showGender(this, \''.$did_id.'\'); return false;"';
                $emp_select .= '>';
                $emp_select .= '<option value="">Select</option>';
                foreach ($json['employees'] as $k => $e) {
                if (!empty($e['gender'])) $gendercls = strtolower($global_options['gender'][$e['gender']]);
                else $gendercls = '';                 
                     $emp_select .= '<option data-class="'.$gendercls.'" value="'.$e['id'].'">' . $e['name'] . '</option>';
                }
                $emp_select .= "</select></span><span id=\"gender_div\">&nbsp;</span>";
        }       
        else {
            $emp_select = '';
        }
        if (sizeof($json['calendars']) > 0) {   
                    $cal_select = '&nbsp;&nbsp;&nbsp;Calendar: <select id="cal_picker" style="min-width: 120px;">';
                    $cal_select .= '<option value="">Select</option>';
                    foreach ($json['calendars'] as $k => $e) {
                         $cal_select .= '<option value="'.$e['id'].'">' . $e['name'] . '</option>';
                    }
                    $cal_select .= "</select> &nbsp;<input type=\"submit\" value=\"Go\" onclick=\"var url = '/Scheduling/EaServices/schedule_from_call/0/'+ callId +'/' + $('#cal_picker').val();
                myWindow=window.open(url,'_blank','width=800,height=700,scrollbars=1,resizable=1,location=yes,menubar=yes,toolbar=yes');
                myWindow.focus();\">";
        }
        else $cal_select = '';
        $json['html'][$schedule['id']] = '<form class="cinstr"  autocomplete="off" onsubmit="return false;">'  . $extra_msg . $s_html;
        $json['html'][$schedule['id']] .= '</form>';
        //$json['html'][$schedule['id']] .= '<form id="opinstr_form"><textarea class="is_hidden" name="opinstr" id="opinstr"></textarea></form>';
        $json['html'][$schedule['id']] .= '<div class=""><div class="appt_time"></div></div>';
        $json['transfer_required'][$schedule['id']] = $transfer_required;
        $json['oncall_list_select'][$schedule['id']] = $oncall_list_select;
        $json['prompt_select'] = $prompt_select;
        $json['employee_select'][$schedule['id']] = $employee_select;
        $json['emp_picker'] = $emp_select;
        $json['cal_picker'] = $cal_select;
        if (1) $json['oncall_lists'][$schedule['id']] = $oncall_lists;
    }
}
unset($json['ct_actions']);
unset($json['prompts']);
unset($json['calendars']);
echo json_encode($json);

?>
