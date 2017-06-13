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
if (empty($this->request->data)) {
    echo '<br><br><br><center><h1>No messages were found</h1><br><br>
    <a href="#" onclick="$(\'#msgDialogWin\').dialog(\'close\'); return false;">(x) close this window</a></center>';
}
else {

    $account_name = $this->request->data['DidNumber']['company'] . ' (' .$account_num .')';
    $title = ' &nbsp;&nbsp;---- Message ID: ' . $this->request->data['Message']['id'] . ' ----'. $this->request->data[0]['createdf'];

?>
<div class="ui-layout-north" >
    <div id="msg_header" class="ui-layout-content">
        <div id="msg_nav">
        <?php 
        
        // create navigation buttons to browse through next/previous messages
        if ($navigation) {
                ?>
        <img src="/img/ajax-loader.gif" class="is_hidden" id="msg_loading" width="16" height="16" />&nbsp;
        <input type="text" value="<?php echo $current; ?>" size="3" onchange="return gotoMessage(this); " /> /<?php echo $total; ?> on page&nbsp;&nbsp;&nbsp;
        <input type="button" value="Prev" <?php if ($current == '0') echo 'disabled'; ?> onclick="$('#msg_loading').show();prevMessage(); return false;"></button>&nbsp;&nbsp;
    <input type="button" <?php if ($current == $total) echo 'disabled'; ?> value="Next" onclick="$('#msg_loading').show();nextMessage(); return false;"></button>
                                
        <?php
        }
        ?>
        &nbsp;&nbsp;<a href="#" id="refresh_msg" onclick="loadMessageUrl('<?php echo $_SERVER['REQUEST_URI']; ?>'); return false;"><i class="fa fa-lg fa-refresh"></i></a><br>
        <?php
        if ($this->request->data['Message']['audited']) {
                echo '<input type="checkbox" value="1" name="data[Message][audited]" id="audit_field" checked onclick="setAudit(this, \''.$this->request->data['Message']['id'].'\');"> Audited';
        }
        else {
                echo '<input type="checkbox" value="1" name="data[Message][audited]" id="audit_field" onclick="setAudit(this, \''.$this->request->data['Message']['id'].'\');"> Audited';
        }   
                                
        
        echo $this->element('message_edit_options');
                                
                                
        ?>
        
        </div>
        <form>
            <input type="text" id="msg_find_did" value="<?php echo htmlspecialchars($account_name); ?>" size="30" disabled>
            <?php 
            // add visual indicator that number is bilingual if necessary
            if ($this->request->data['DidNumber']['bilingual']) echo ' &nbsp;<i title="This account is bilingual" class="fa fa-lg fa-globe"></i> '; 
            ?>
            <input type="hidden" id="msg_find_did_id" value="<?php echo $this->request->data['DidNumber']['id']; ?>">
        <?php
            echo $title;
        ?>
        </form>
                                        
        <input type="hidden" id="msg_edit_id"  value="<?php echo $this->request->data['Message']['id']; ?>">    
        <b>Taken by</b>: <?php echo $this->request->data['Message']['user_name']. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
        <b>Duration</b>: <?php echo $this->element('formatDuration', array('t' => $this->request->data[0]['duration'])); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <b>STATUS</b>: <span id="msg_deliver"><?php
        if ($this->request->data['Message']['hold'] && $this->request->data['Message']['hold_until']) echo 'HOLD until '. $this->request->data['Message']['hold_until'];
        else if ($this->request->data['Message']['hold']) echo 'HOLD';
        else if ($this->request->data['Message']['delivered']) echo 'DELIVERED'; else echo 'UNDELIVERED';
        ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <b>Caller ID</b>: <?php echo $this->element('formatPhone', array('num' => $this->request->data['CallLog']['cid_number'])). '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
        <b>Local Time</b>: <span id="msglocal_time"></span>

        

    </div>
</div>
<div class="ui-layout-south">
    <div class="ui-layout-content" id="msg_actions">
        <input id="operatorScreenBtn" type="button" onclick="manualPop = true; manualScreenPop('<?php echo $this->request->data['Message']['did_id']; ?>', null); return false;" class="is_hidden" value="Operator Screen" /> 
                                
        <?php

        // create buttons at the bottom of the edit pane (DELIVER/ UNDELIVER/ MINDER/ etc)
        if ($this->request->data['Message']['delivered']) {
            echo '<input type="button" value="DELIVER BY LMR" id="lmr_deliver" onclick="toggleLMRDelivery(\''.$this->request->data['Message']['id'].'\', \''.$this->request->data['Message']['call_id'].'\', $(\'#msg_emp\').val(), $(\'#msg_emp option:selected\').text()); return false;" class="is_hidden important">&nbsp;';
            echo '<input type="button" value="UNDELIVER" id="deliver_btn" onclick="toggleDelivery(this, $(this).val(), \''.$this->request->data['Message']['id'].'\', \''.$this->request->data['Message']['call_id'].'\'); return false;">&nbsp;';
        }
        // create a different set of buttons if message has not been marked as delivered
        else {
            echo '<input type="button" value="DELIVER BY LMR" id="lmr_deliver" onclick="toggleLMRDelivery(\''.$this->request->data['Message']['id'].'\', \''.$this->request->data['Message']['call_id'].'\', $(\'#msg_emp\').val(), $(\'#msg_emp option:selected\').text()); return false;" class="important">&nbsp;';
            echo '<input type="button" id="deliver_btn" value="DELIVER"  onclick="toggleDelivery(this, $(this).val(), \''.$this->request->data['Message']['id'].'\', \''.$this->request->data['Message']['call_id'].'\'); return false;">&nbsp;';
        }
        // check if the message has been sent to dispatch 
        if ($this->request->data['Message']['minder']) {
            echo '<input type="button" value="UNMINDER" onclick="toggleMinder(this, \'unminder\', \''.$this->request->data['Message']['id'].'\', \''.$this->request->data['Message']['call_id'].'\'); return false;">&nbsp;';
        }
        else {
            echo '<input type="button" value="MINDER"  onclick="toggleMinder(this, \'minder\', \''.$this->request->data['Message']['id'].'\', \''.$this->request->data['Message']['call_id'].'\'); return false;">&nbsp;';
        }
        
        // create a button for flagging a mistake on this message
        echo '&nbsp;<input type="button" value="Mistake"  onclick="openMistakeDialog(\''.$this->request->data['Message']['id'].'\', \''.$did_id.'\', \'add\', \'\', function() {document.getElementById(\'audit_field\').checked = true; loadMsgMistakes(\''.$this->request->data['Message']['id'].'\');}); return false;">';
        
        // create a button for creating a complaint on this message
        echo '&nbsp;<input type="button" value="Complaint"  onclick="openDialogWindow(\'/Complaints/add/'.$this->request->data['Message']['id'].'/'.$did_id.'\', \'Add Complaint\', null, function() {loadMsgComplaints(\''.$this->request->data['Message']['id'].'\');}, 900, 600); return false;" >';
        
        // create a button for entering a note on this message
        if ($this->Permissions->isAuthorized('MessagesEditNote',$permissions)) {                        
            echo '&nbsp;<input type="button" value="Note"  onclick="openNoteDialog(\''.$this->request->data['DidNumber']['account_id'].'\',\''.$did_id.'\', \'message\', \''.$this->request->data['Message']['id'].'\', function() {loadMsgNotes(\''.$this->request->data['Message']['id'].'\');}); return false;" >';
        }

        // create a button to enter a custom event into the call log
        echo '&nbsp;<input type="button" value="Custom Event"  onclick=" addCustomEvent(\''.$this->request->data['Message']['call_id'].'\', true); return false;">';

        // create a button to put message on hold until a certain time
        echo '&nbsp;<input type="button" value="Hold Til"  onclick=" addHoldTil(\''.$this->request->data['Message']['id'].'\', \''.$this->request->data['Message']['call_id'].'\'); return false;">';
        ?>
        <input type="button" value="CLOSE" onclick="$('#msgDialogWin').dialog('close'); return false;" />
    </div>
</div>
<div id="msg_center" class="ui-layout-center">
    <div class="ui-layout-center">
        <div class="ui-layout-north " id="tabs2">
            <ul>
                <li><a href="#msg_instructions">Instructions</a></li>
            </ul>     
            <div class="ui-layout-content">
                <div id="msg_instructions" mid="<?php echo $this->request->data['Message']['id']; ?>">
                    <?php
                    if ($this->request->data['Message']['calltype_instructions']) echo str_replace("\r", "<br>", $this->request->data['Message']['calltype_instructions']);
                    ?>
                </div>
            </div>
        </div>
        <div class="ui-layout-center">
                    <?php 
                    $prompt_select = array();
                    echo $this->Form->create('Message', array('id'=>"msg_edit_form", 'method' => 'post')); ?>
            
            <div class="ui-layout-north">
                <div class="header" id="msg_edit_header">
                    <?php 
                    $options = array();
                    foreach ($data['calltypes'] as $i => $ct) {
                        if (isset($data['schedules'][$ct['id']])) {
                            $options[$ct['id']] = $ct['title'];
                        }
                    }
                    $emp_options = array();
                    foreach ($data['employees'] as $eid => $emp) {
                        $emp_options[$eid] = $emp['name'];
                    }
                    
                    $cal_options = array();
                    foreach ($data['calendars'] as $cid => $cal) {
                        $cal_options[$cid] = $cal['name'];
                    }           
                    echo $this->Form->input('Message.old_calltype_id', array('type' => 'hidden', 'value'=> $this->request->data['Message']['calltype_id'], 'id' => 'msg_ct_old'));
                                                            
                                                                                    
                    // create a drop down to switch the call types                              
                    echo $this->Form->input('Message.calltype_id', array('label' => '<b>Call type</b> ',  'disabled' => true, 'onselect' => 'return false', 'options' => $options, 'div' => false, 'id' => 'msg_ct'));

                    echo '&nbsp;&nbsp;&nbsp;&nbsp;';

                    // create an employee picker drop down to display employee contact buttons          
                    if (isset($this->request->data['MessagesDelivery'][0]) && $this->request->data['MessagesDelivery'][0]['employee_id']) {
                            $emp_id = $this->request->data['MessagesDelivery'][0]['employee_id'];
                    }
                    else $emp_id = $this->request->data['Message']['last_eid'];
                    echo $this->Form->input('Message.last_eid', array('label' => false, 'options' => $emp_options, 'empty' => 'Select employee', 'title' => 'Select an employee', 'div' => false, 'id' => 'msg_emp', 'value' => $emp_id ));

                    // Now display the drop down for displaying calendars if calendars are configured for the account
                    if (sizeof($cal_options) > 0) {
                        echo '&nbsp;&nbsp;<select title="Select calendar to display and click on GO"><option value="">Select calendar</option>';
                        foreach ($cal_options as $key => $val) {
                            echo '<option value="'.$key.'">'.$val.'</option>';
                        }
                        echo '</select> <button onclick="if ($(this).prev().val() != \'\') {var myWindow=window.open(\'/Scheduling/EaServices/schedule_from_msg/0/'.$this->request->data['Message']['call_id'].'/\' + $(this).prev().val(),\'_blank\',\'width=900,height=700,scrollbars=1,resizable=1,location=yes,menubar=yes,toolbar=yes\'); myWindow.focus();} return false;">Go</button>';
                    }


                    ?>
                    <div id="emp_buttons">
                    </div>                      
                </div>
        
                
            </div>
            <div class="ui-layout-center">
                
                            
    
                <div class="content ui-layout-content">
                    <div id="msg_prompts" >
                        <table class="prompts" cellpadding="2" cellspacing="0" width="100%">
                        <?php
                        $old_action_num = '';
                        $first = true;
                        foreach ($this->request->data['MessagesPrompt'] as $k => $p) {
                            $rclass = '';
                            if ($p['action_num'] != $old_action_num) {
                                    if (!$first) $rclass =' section';
                                    $first = false;
                            }
                            
                            echo '<input type="hidden" name="ptitle[1][]"  value="'.$p['caption'].'">';
                            echo '<input type="hidden" name="ptype[1][]"  value="'.$p['ptype'].'">';
                            echo '<input type="hidden" name="action_num[1][]"  value="'.$p['action_num'].'">';
                            echo '<input type="hidden" name="pmaxchar[1][]"  value="'.$p['maxchar'].'">';
                            echo '<input type="hidden" name="poptions[1][]"  value="'.$p['options'].'">';
                            if ($p['caption'] == 'Phone Number') $class = " phone_field";
                            else $class = '';
                                    
                            if ($p['ptype'] == '2' ||$p['ptype'] == '3') {
                                    echo '<tr class="'.$rclass.'"><td>' . $p['caption'] . '</td><td><textarea class="is_hidden old_val">'.$p['value'].'</textarea><textarea  class="uprompt" name="pvalue[1][]" rows="2" cols="30" onchange="if (isEditable(this)) logCallEvent(\''.$this->request->data['Message']['call_id'].'\', \'[PROMPT] \' + $(this).parents(\'td\').siblings(\'td\').html() + \': \' + $(this).val(), \'23\');">'.$p['value'].'</textarea></td></tr>';
                            }
                            else if ($p['ptype'] == '4' ) {
                                    echo '<tr class="'.$rclass.'"><td>' . $p['caption'] . '</td><td>';
                                    
                                    $temp = explode('||', $p['options']);
                                    
                                    $poptions = explode('|', $temp[0]);
                                    $pactions = explode('|', $temp[1]);
                                    
                                    echo '<textarea class="is_hidden old_val">'.$p['value'].'</textarea><select class="conditional uprompt " ';
                                    if ($element_id) echo 'id="'. $element_id . '" ';
                                    echo 'onchange="if (isEditable(this)) logCallEvent(\''.$this->request->data['Message']['call_id'].'\', \'[PROMPT] \' + $(this).siblings(\'label\').html() + \': \' + $(this).val(), \''.EVENT_FILL_PROMPT.'\');"';  
                                    echo ' class="uprompt'.$input_class.'" name="pvalue[1][]"><option value="">Select</option>';
                                    foreach ($poptions as $k=> $o) {
                                            echo '<option value="'.$o.'" data-action="'.$pactions[$k].'"';
                                            if ($p['value'] == $o) echo ' selected';
                                            echo '>'.$o.'</option>';
                                    }
                                    echo '</select>';                                
                                    echo '</td></tr>';
                            }
                            else {
                                    echo '<tr class="'.$rclass.'"><td>' . $p['caption'] . '</td><td><textarea class="is_hidden old_val">'.$p['value'].'</textarea><input class="uprompt'.$class.'" type="text" name="pvalue[1][]" size="30" value="'.$p['value'].'" onchange="if (isEditable(this)) logCallEvent(\''.$this->request->data['Message']['call_id'].'\', \'[PROMPT] \' + $(this).parents(\'td\').siblings(\'td\').html() + \': \' + $(this).val(), \'23\');"></td></tr>';
                            }
                            $old_action_num = $p['action_num']; 
                        }
                        echo '</table>';
                     ?>
                    </div>
                    <div id="appts">
                        <?php
                        if (sizeof($appts['active']) > 0 || sizeof($appts['deleted']) > 0) {
                            echo '<h3><center>Appointment(s)</center></h3>';
                            echo '<table class="prompts" cellpadding="2" cellspacing="0" width="100%">';
                    
                            foreach ($appts['active'] as $row):
                                foreach ($row as $p) {
                                    echo '<tr><td align="right">'.$p['caption'].':</td><td> ' . $p['value']. "</td></tr> \t\r\n";
                                }
                                echo '<tr><td align="right">&nbsp;</td><td>&nbsp;</td></tr>'." \t\r\n";
                            endforeach;
                            foreach ($appts['deleted'] as $row):
                                echo '<tr class="cancelled"><td align="right">&nbsp;</td><td><span class="cancelled">CANCELLED</span></td></tr>'." \t\r\n";
                                foreach ($row as $p) {
                                    echo '<tr class="cancelled"><td align="right">'.$p['caption'].':</td><td> ' . $p['value']. "</td></tr> \t\r\n";
                                }
                            endforeach;
                            echo '<tr><td align="right">&nbsp;</td><td>&nbsp;</td></tr>'." \t\r\n";
                            echo '</table>';
                        }             
                        
                        ?>
                    </div>  
                    <div id="msg_edits" class="is_hidden">
                        <?php
                        echo 'This message has been edited, click <a href="#" data-msg-id="'.$this->request->data['Message']['id'].'" onclick="return false;">here</a> to view the edits';
                        ?>
                    </div>

                    <div id="dialog-edits" title="Message edits">
                    </div>      
                 
                </div>
                <div class="footer">
                    <div id="edit_buttons" <?php if ($this->request->data['Message']['delivered']) echo ' class="is_hidden"'; ?>>
                                 
                        &nbsp;&nbsp;&nbsp;&nbsp;<button class="edit_msg_toggle" id="edit_message" >edit message/ calltype</button>
                    </div>
                </div>   

            </div>
                
            
            
            </form>
            
            
            
            

        </div>
    </div>
    <div class="ui-layout-east">
        <div class="ui-layout-north">
            <div class="ui-layout-content" id="m_oncall_lists" >
                <?php
                $tabs = '<ul>';
                $cnt = 1;
                $divs = '';
                if (sizeof($data['call_lists']) > 0) {
                    foreach ($data['call_lists'] as $title => $l) {
                        $tabs .= '<li><a href="#oc-tabs-'.$cnt.'">'. $title.'</a></li>' . "\r\n";
                        if ($l['legacy']) $divs .= '<div id="oc-tabs-'.$cnt.'">'.str_replace("\r\n", "<br>", $l['legacy_list']).'</a>' . "</div>\r\n";
                        else {
                            $divs .= '<div id="oc-tabs-'.$cnt.'">';
                            $divs .= 'Click <a href="#" onclick="openDialogWindow(\'/CallLists/view_all/'.$did_id.'/all\', \'Oncall list\', null, null, 450, 700);">here</a> to see <b>all</b> oncall lists<br><br>';              
                            $temp = explode(',', $l['employee_ids']);
                            foreach($temp as $t) {
                                if (!empty($data['employees'][$t])) {
                                                        
                                    $divs .= '<a href="#" onclick="$(\'#msg_emp\').val(\''.$data['employees'][$t]['id'].'\');$(\'#msg_emp\').trigger(\'change\'); return false;">'.$data['employees'][$t]['name'].'</a><br>';
                                }
                            }
                            $divs .= "</div>\r\n";
                        }
                        $cnt++;
                    }
                }
                else {
                    $tabs .= '<li><a href="#oc-tabs-'.$cnt.'"><i>(None found)</i></a></li>' . "\r\n";           
                    $divs .= '<div id="oc-tabs-'.$cnt.'">'; 
                    $divs .= 'Click <a href="#" onclick="openDialogWindow(\'/CallLists/view_all/'.$did_id.'/all\', \'Oncall list\', null, null, 450, 700);">here</a> to see <b>all</b> oncall lists</div>';  
                }
                $tabs .= '</ul>' . "\r\n";
                echo $tabs . $divs;
                ?>
            </div>
        </div>
        <div class="ui-layout-center" id="tabs1">
            <ul>
                <li><a href="#tab-events">Events</a></li>
                <li><a href="#tab-mistakes">Mistakes</a></li>
                <li><a href="#tab-notes">Notes</a></li>
                <li><a href="#tab-complaints">Complaints</a></li>
                <li><a href="#tab-deliveries">Deliveries</a></li>

            </ul>   
            <div class="ui-layout-content">


                <div id="tab-mistakes">
                </div>
                
                <div id="tab-notes">
                </div>
        
                <div id="tab-events">
                </div>
                
                <div id="tab-complaints">
                </div> 
                
                <div id="tab-deliveries">
                </div>     
            </div>
        
        </div>
    </div>
        

</div>

<script>

$(function() {
    var msg_schedules = <?php echo json_encode($data['schedules']); ?>;
    //callId = <?php echo $this->request->data['Message']['call_id']; ?>;
    var msg_employees = <?php echo json_encode($data['employees']); ?>;

    var msg_did_id = '<?php echo $this->request->data['Message']['did_id'] ; ?>';
    var msg_message_id = '<?php echo $this->request->data['Message']['id'] ; ?>';
    var msg_call_id = '<?php echo $this->request->data['Message']['call_id'] ; ?>';
    var msg_schedule_id = '<?php echo $this->request->data['Message']['schedule_id'] ; ?>';
    
            
    
    $('#edit_message').on('click', function() {
        openFromMsgReview(msg_did_id, msg_call_id, msg_message_id, msg_schedule_id);
        return false;
    });
    
    $('#msg_edits a').on('click', function() {
    
        var url = '/MessagesPromptsEdits/view/' + $(this).attr('data-msg-id');
        $.ajax({
            url: url,
            type: 'GET'
        })
        .done(function(data) {
            $('#dialog-edits').html(data);
        });
        $('#dialog-edits').dialog('open'); return false;      
    });

    $('#msg_emp').on('change', function() {
        $('#emp_buttons').html(getEmployeeButtons(this.value, msg_employees, true, msg_call_id, msg_did_id, '<?php echo $this->request->data['CallLog']['did_number']?>'));
        msgWinLayout.center.children.layout1.center.children.layout1.center.children.layout1.sizePane('north', $('#msg_edit_header').height()+12);
        
    });
    
    if ($('#operatorScreen').length) $('#operatorScreenBtn').show();
    
    msgWinLayout = $('#msgDialogWin').layout({
        south__size:    26,
        south__resizable: false,
        south__closable: false,
        north__size:    60,
        north__resizable: false,
        north__closable: false,
        center__children: {
            east__size:         .45,
            east__children: {
                north__size: .3
            },
            center__children: {
                north__size: .5,
                center__children: {
                    north__size: 100,
                    north__spacing_open: 0,
                    north__spacing_closed: 0
                }
            }
        }     
    });
    

    $( "#tabs1" ).tabs({
        activate: function( event, ui) {
            if (ui.newPanel.attr('id') == 'tab-notes') {
                $('#msg_notes .descr').readmore({
                    speed: 75,
                    maxHeight: 72          
                });
            }
        }
    });    

    $( "#tabs2" ).tabs({
    });    


    <?php
    if ($edited) {
    ?>
    $('#msg_edits').show();
    <?php
    }

    ?>
    
    $( "#dialog-edits" ).dialog({
        autoOpen: false,
        height: 600,
        width: 500,
        modal: true,
        buttons: {
            'Cancel': function() {
                $( this ).dialog( "close" );
            }        
        }
    });

    $('#emp_buttons').html(getEmployeeButtons($('#msg_emp').val(), msg_employees, true, msg_call_id, msg_did_id, '<?php echo $this->request->data['CallLog']['did_number']; ?>'));
    msgWinLayout.center.children.layout1.center.children.layout1.center.children.layout1.sizePane('north', $('#msg_edit_header').height()+12);


    loadCallEvents(<?php echo $this->request->data['Message']['call_id']; ?>);
    loadMsgMistakes(<?php echo $this->request->data['Message']['id']; ?>);
    loadMsgNotes(<?php echo $this->request->data['Message']['id']; ?>);    
    loadMsgComplaints(<?php echo $this->request->data['Message']['id']; ?>);    
    loadMsgDeliveries(<?php echo $this->request->data['Message']['id']; ?>);

    $('#m_oncall_lists').tabs();  
    clearInterval(msgClockInterval);
    
    // set timer to update local time every second
    msgClockInterval = setInterval( function() {
        $('#msglocal_time').html(moment().tz('<?php echo $client_tz; ?>').format('dd MM/D/YYYY h:mm:ss a z'));
                                             
    },1000);         

    autosize.destroy(document.querySelectorAll('#msg_prompts textarea.uprompt'));
    autosize(document.querySelectorAll('#msg_prompts textarea.uprompt'));    
    
    $('#msg_prompts .ct, #msg_prompts .uprompt').addClass('disable_edits');
});


</script>
<?php
}

?>