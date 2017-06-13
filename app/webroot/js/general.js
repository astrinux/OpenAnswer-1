    // convert seconds to hh:mm:ss format
    
    /***
    the object 'currentInstructions' has the following properties:
        'call_id'
        'call_lists' indexed by call list title
        'calltypes' array of all calltypes
        'contacts' indexed by contact_id
        'did' - DID object
        'employees' - indexed by employee id
        'files' - array of files
        'html' - indexed by schedule id
        'notes' - array of notes
        'oncall_html' 
        'oncall_lists' => indexed by schedule_id
        'prompts' - indexed by action_id, each entry is an array of prompts for each calltype action
        'schedules' - indexed by calltype id
    
    ***/
    function secondsToTime(value) {
        var secs = value;
        var hours = Math.floor(secs / 3600);
        secs %= 3600;
        var minutes = Math.floor(secs / 60);
        var seconds = Math.floor(secs % 60);            
        var result = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds  < 10 ? "0" + seconds : seconds);     
        return result;  
    }
    
    
    function check_conditional(t) {
        if (!$(t).is(':visible')) return; 
        var next_section = '';

        // find current section
        var current_section = $(t).parents('.script_section').attr('data-section');
        var branch = '';
        
        // hide all sections for now
        $('#instructions #section_' + current_section).nextAll('.script_section').hide();
        
        // fetch the action we need to take
        var cond_action_txt = $(t).find('option:selected').attr('data-action');
        if (typeof(cond_action_txt) == 'undefined') {
            $(t).parents('div.step').nextAll('div.step').addClass('is_hidden');
            return;
        }
        
        
        var action = cond_action_txt.split('_'); 
        cond_action = action[0].toString();
        if (action[1]) branch = action[1].toString();
        
        // cond_action: 0 = do nothing, 1=branch to section, 2=branch to action, 3=execute until specified label
        var data_promptnum = $(t).attr('data-promptnum');
        if (cond_action != '0') {
            if (cond_action == '2') {
                // branch to specified label, hide everything between current step and specified label
//                  $(t).parents('.step').siblings().show();

                $(t).parents('.step').nextUntil('.step[data-label="'+branch+'"]', '.step').each(function() {
                    if (!$(this).hasClass('is_hidden')) $(this).addClass('is_hidden');
                });
                $('#instructions #section_' + current_section + ' .step[data-label="'+branch+'"]').removeClass('is_hidden');
                $('#instructions #section_' + current_section + ' .step[data-label="'+branch+'"]').nextAll('.step').removeClass('is_hidden');
                
                $('.step[data-label="'+branch+'"]').each(function() {
                    $(this).find('.conditional').each(function() {
                        
                        if (!$(this).hasClass('is_hidden')) {
                            check_conditional(this);
                            return false;
                        }
                    });
                });                 
            }
            else if (cond_action == '3') {
                // execute until we reach the specified label
                $(t).parents('.step').nextUntil('.step[data-label="'+branch+'"]', '.step').each(function() {
                     $(this).removeClass('is_hidden');
                });
                
                // hide all steps after the current step
                $('#instructions #section_' + current_section + ' .step[data-label="'+branch+'"]').addClass('is_hidden');
                $('#instructions #section_' + current_section + ' .step[data-label="'+branch+'"]').nextAll('.step').addClass('is_hidden');
            }
            else if (cond_action == '') {           
                $(t).parents('div.step').nextAll('div.step').addClass('is_hidden');
            }               
            // else it's a skip to a section
            else {
                //$(t).parents('.step').siblings().show();
                // not branching to a section, just check section action to see where we go from this section
                next_section = branch;
                $(t).parents('div.step').nextAll('div.step').addClass('is_hidden');
            }
        }

        else {
            // no branching to take, show next steps of current section and check the section action
            if ($(t).parents('.script_section').attr('data-action') > 0) {
                // set next section to check
                next_section = $(t).parents('.script_section').attr('data-section-num');
            }
            else {
                // execution ends at the end of this section, set next_section to ''
                $('#instructions #section_' + current_section).nextAll('.script_section').hide();
                next_section = '';
            }
            $(t).parents('div.step').nextAll('div.step').removeClass('is_hidden');
            $(t).parents('.step').nextAll('.step').each(function() {
                if ($(this).is(':visible')) {
                    if ($(this).find('.conditional').length > 0) {
                        check_conditional($(this).find('.conditional')[0]);
                        return false;
                    }
                }
            });                     
        }
        var recursion_count = 0; 
        
        // recursively check this section, then check the section action
        while (next_section != '' && recursion_count < 50) {
            $('#instructions #section_' + current_section).nextAll('.script_section').hide();
            $('#instructions #section_' + next_section).appendTo('.cinstr');
            $('#instructions #section_' + next_section).show();
            $('#instructions #section_' + next_section + ' .conditional').each(function() {
                if ($(this).is(':visible')) {
                    check_conditional(this);
                }
            });
            
            current_section = next_section;
            if ($('#instructions #section_' + next_section).attr('data-action') > 0) {
                next_section = $('#instructions #section_' + next_section).attr('data-section-num') ;
            }
            else {
                next_section = '';
            }
            recursion_count++;
            
        }            
        

        
       
         $('#instructions .unused').appendTo('.cinstr');
    }   
    
    // used to check required fields assigned 'required' class inside the element identified by '#sel'
    function checkMissingInfo(sel) 
    {
        var missing_info = false;
        $('#' + sel + ' .required').removeClass('missing');
        $('#' + sel).find('.required').each(function()
        {
            if ($(this).val() == '' && !$(this).parents('tr').hasClass('tbd')) 
            {
                $(this).addClass('missing');
                missing_info = true;
            }
        });
        return missing_info;
    }
    
    
    function editDidNumber(id, title) 
    {
        $('#find_did').attr('text', title);
        $('#sidebar a[data-id=tabs-did]').trigger('click');    
        $('.find_did_sel2all').select2('val', id);
        loadPage(this, '/DidNumbers/edit/'  + $('#find_did').val(), 'did-content');   
        didLayout.center.children.layout1.close('west'); 
        didLayout.center.children.layout1.close('east'); 
    }
    
    function addEmployeeSubmit(did_id) 
    {
        var url = '/Employees/add/' + did_id;
        if ($('#empcontacts').find('tr').not('.tbd').length < 2) 
        {
            alert('You must specify at least one contact for this employee');
        }
        else 
        {
            var missing_info = checkMissingInfo('emp_add');
            if (!missing_info) 
            {
                $.ajax({
                    url: url,
                    dataType: 'json',
                    data: $('#emp_edit').serialize(),
                    type: 'POST'
                    }).done(function(data) 
                    {
                        if (data.success) 
                        {
                            loadPagePost(null, '/Employees/index/' + $('#find_did').val(), 'did-content', 'target=did-content&detail=did-detail', null);               
                            didLayout.center.children.layout1.close('east');
                        }
                        alert(data.msg);
                    });
            }
            else alert('You must fill in the required fields');
        }
    }

    // retrieve the call buttons for a specific call type action
    function getOncallCallbox(schedule_id, container, action_id) 
    {
        var url = '/CallLists/callbox_view/'+ schedule_id+'/' + action_id + '/' + callId;
        loadPagePost(null, url, container, {test_time: $('#test_time').val()}, function(res) { $('#'+container).show(); });
    }
    
    function showGender(t, did_id) 
    {
        dialogLayout.sizeContent('center');
        var gender = $(t).find(' option:selected').attr('data-class');
        var cls = gender;
        $('#gender_div').removeClass().addClass(cls);
    }
    
    
    
    
    // builds the html to render for an employee on the operator screen
    // this should be called for each employee that needs to be rendered
    function getEmployeeButtons(emp, employees, show_all, btn_call_id, btn_did_id, btn_did) 
    {
        if (emp == '') return '';
        var the_emp = employees[emp];
        var html = '';
        var c;
        var c_class;
        var btn_text;
        var btn_text_full;
        action_id = '';
        btn_class = '';
        if (emp == '') return '';
        $('#cb_emp').removeClass('alertbg');
        if (typeof(show_all) == 'undefined') show_all = false;
        for (var j=0; j < the_emp['contacts'].length; j++) 
        {
            c = the_emp['contacts'][j];
            if (!c['ext']) c['ext'] = '';
            btn_text = c['contact'];
            btn_text_full = btn_text;
            if (btn_text.length > 25) 
            {
                btn_text=btn_text.substr(0,25) + '...';
            }
            // only show button if there is contact info specified.  Only show visible buttons or all if 'show_all' is true
            if (btn_text != '' && (c['visible'] == '1' || show_all)) 
            {
                btn_text = phoneFormat(btn_text);
                if (c['contact_type'] == CONTACT_TEXT) 
                {
                    if (!c['addr'] && !c['carrier']) continue;
                    else if (!c['addr']) 
                    {
                        btn_text_full = c['contact'] + "@" + c['carrier'];
                    }
                    else 
                    {
                        if (c['prefix']) btn_text_full = c['prefix'] + c['contact'].substr(c['contact'].length-10) + "@" + c['addr'];
                        else btn_text_full = c['contact'] + "@" + c['addr'];
                    }
                }
                else if (c['contact_type'] == CONTACT_EMAIL) 
                {
                    btn_text_full = btn_text_full.replace(/;/g, ', ');
                }
                else if (c['contact_type'] == CONTACT_FAX || c['contact_type'] == CONTACT_PHONE) 
                {
                    btn_text = phoneFormat(btn_text);
                }
                if (c['contact_type'] == CONTACT_PHONE || c['contact_type'] == CONTACT_VMAIL) 
                {
                    if (c['ext']) 
                    {
                        btn_text += ' EXT: ' + c['ext'];
                        btn_text_full += ' EXT: ' + c['ext'];
                    }
                }
                if (buttonClass.hasOwnProperty(c['contact_type'])) c_class = buttonClass[c['contact_type']];
                else c_class = '';
                c_class = c_class + ' ' + btn_class; 
                html = html + '<button title="'+btn_text+'" emp_name="'+the_emp['name'] +'" blabel="' + c['label']+'" bfulldata="'+btn_text_full+'" class="c_btn '+c_class+'" action_id="'+action_id+'" call_id="'+btn_call_id+'" did_id="'+btn_did_id+'" did="'+btn_did+'" bdata="' + c['contact']+ '" ext="'+c['ext']+'" contact_id="' + c['id'] + '" btype="' + c['contact_type'] + '" onclick="btnClickHandler(this); return false;">'+ c['label']+ '</button>';
            }
        }       
        if (the_emp['special_instructions'] != '' && the_emp['special_instructions'] != null) 
        {
            if (the_emp['special_instructions'] != '') html = '<div class="emp_notes">' + the_emp['special_instructions'] + '</div>' + html;
        }
        return html;
    }
    
    function checkAction() 
    {
        var action = $('#actsel').val();
    
        if (action == ACTION_TXF || action == ACTION_TXF_DELIVER || action == ACTION_WEB || action == ACTION_BLINDTXF_DELIVER || action == ACTION_BLINDTXF || action == ACTION_LMR || action == ACTION_LMR_DELIVER || action == ACTION_VMOFFER|| action == ACTION_VMOFFER_DELIVER || action == ACTION_VM || action == ACTION_VM_DELIVER) 
        {
            // disable multi-employee selection
            $('#empsel').multiselect(
            {
                selectedList: 1,
                noneSelectedText: 'Select employee/ oncall list',
                minWidth: 220,
                position: {
                    my: 'center',
                    at: 'center',
                    collision: 'fit'                
                },                   
                multiple: false
            });
        }
        else if (action == ACTION_CALENDAR) 
        {
            // disable multi-employee selection
            $('#empsel').multiselect(
            {
                selectedList: 1,
                noneSelectedText: 'Select employee/ oncall list',
                minWidth: 220,
                multiple: false
            });
        }
        else {
            // enable multi-employee selection
            $('#empsel').multiselect({
                selectedList: 10,
                noneSelectedText: 'Select employee/ oncall list',
                minWidth: 220,
                multiple: true,
                close: function() {
                    $('#actionbox_msg').html('');
                    
                },
                click: function(event, ui) {
                    if (ui.value == 'ALL' && ui.checked) {

                        if ($("#empsel").multiselect("widget").find(":checkbox:checked").length > 1) {
                            $('#actionbox_msg').html('Cannot select \'Requested Staff\' if another employee is selected');
                            return false;
                        }
                        else {
                            $('#actionbox_msg').html('');
                        }
                    }
                    else {
                        if ($("#empsel").multiselect("widget").find((":checkbox[value=ALL]")).prop('checked')) {
                            $('#actionbox_msg').html('Cannot select another employee if \'Requested Staff\' is selected');
                            return false;
                        }
                        else {
                            $('#actionbox_msg').html('');
                        }
                    }              
                }
                
            });     
        }
        if ( action == ACTION_DISPATCH || action == ACTION_HOLD || action == ACTION_DELIVER) {  // info or dispatch
            $('#empseldiv').hide();
            $('#optseldiv').hide();
            
        }
        else if (action == ACTION_CRM) {
            $('#empseldiv').hide();
            getCrmOptions();
            $('#optseldiv').show();
        }
        else {
            $('#empseldiv').show();
            $('#optseldiv').hide();
             getEmployees();          
            $('#empsel').multiselect('refresh');        
        }           
        
    }
    
    function getCrmOptions() {
        //console.log(crms);
    }    
    
    // function to render call type instructions in the operator callbox 
    function displayCallType(idx, target) 
    {
        // idx is index into call type array (sorted by sort order)
        var ctype = currentInstructions['calltypes'][idx];
        var cid = ctype['id'];
        var selected_li = $('#action'+idx);
        var action;
        var delivery = false;
        var transfer = false;
        var web = false;
        var buttonhtml = '';
        //JRW       var actions = currentInstructions['ct_actions'][cid];
        var schedule_id = currentInstructions['schedules'][cid]['id'];
        var prompts_auto;
        $('#cb_emp').removeClass('alertbg');    
        logCallEvent(callId, '[Calltype] ' + selected_li.attr('ctitle'), EVENT_CALLTYPE);
        
        //replace the html of the target location with the html for the selected call type.
        $(target).html(currentInstructions['html'][schedule_id]);
        
        // prefill any caller id fields within the instructions
        if ((settings['show_callerid'] == '1') && currentCall.event['calleridnum'] && jQuery.isNumeric(currentCall.event['calleridnum'])) {
            //$(target + ' input.fill_cid').val(currentCall.event['connectedcallnum']);
            //$('#instructions input.fill_cidname').val(currentCall.event['calleridname']);
            prompts_auto = userprompts.concat();            
            prompts_auto.push(currentCall.event['connectedlinenum']);
            prompts_auto.push('Caller ID ' + currentCall.event['connectedlinenum']);
        }
        else {
            prompts_auto = userprompts.concat();
        }
        // if the employee selector is shown, then show the buttons for the default employee
        $('#cb_emppicker').html(currentInstructions['emp_picker'] + currentInstructions['cal_picker']);
        
        if (0) {
            $('#show_emp_picker')[0].selectedIndex = 1;
            $('#cb_empcontacts').html(getEmployeeButtons($('#show_emp_picker').val(), currentInstructions['employees']));
        }
        else {
            $('#show_emp_picker')[0].selectedIndex = 0;
        }
        if (currentInstructions['transfer_required'][schedule_id] == '1' || currentInstructions['oncall_list_select'][schedule_id] == '1' || currentInstructions['employee_select'][schedule_id] == '1') {
            $('#emp_picker_wrapper').show();
        }
        else {
            $('#emp_picker_wrapper').hide();
        }
        $('.helpertxt a').on('click', function() {
            var link = $(this).attr('href');
                myWindow=window.open(link,'_blank','width=800,height=700,scrollbars=1,resizable=1,location=yes,menubar=yes,toolbar=yes');
                myWindow.focus();           
                return false;  
        });
        $('#cb_empcontacts').html('');
        dialogLayout.sizeContent('center');     
        var $uprompts = $(target).find('.uprompt');
        $uprompts.autocomplete({
            source: prompts_auto, 
            delay: 100,
            minLength: 1,
            select: function(event, ui) {
                logCallEvent(callId, '[AUTOPROMPT] ' + $(event.target).siblings('label').html() + ': ' + ui.item.value, '23');
            }
        });
        $uprompts.on("keyup focus", function() {
            setCount(this);
        });


        $('#actions li').removeClass('ct_sel');    
        $('#action' + idx).addClass('ct_sel');        
        
        $('#calltype_caption').val(selected_li.attr('ctitle'));
        $('#calltype_id').val(selected_li.attr('cid'));
        $('#schedule_id').val(selected_li.attr('sid'));
        currentScheduleId = selected_li.attr('sid');
        
        // put cursor on first prompt so we're ready to go
        $(target + ' #instructions input:text:first').focus();
        var $inp = $(target + ' #instructions input:text');
        $inp.bind('keydown', function(e) {
            var key = e.which;
            if (key == 13) {
                e.preventDefault();
                var nextIndex = $('#instructions input:text').index(this) + 1;
                var maxIndex = $('#instructions input:text').length;
                if (nextIndex < maxIndex) {
                    $('#instructions input:text:eq(' + nextIndex+')').focus();
                }
                if  (nextIndex == maxIndex ) {
                    $('#instructions textarea').focus();
                }
            }
        });
        
        autosize(document.querySelectorAll('#instructions textarea'));    
        
        // populate dropdowns 
        $('#instructions .prompt_dd').each(function() {
            var el_id = $(this).attr('id');
            if (currentInstructions['prompt_select'].hasOwnProperty(el_id)) {
                $( "#"+el_id ).autocomplete({
                    minLength: 0,             
                    source: currentInstructions['prompt_select'][el_id]
                });
            }
        });


        $('#operatorScreen .uprompt, #miscnotes').on('change', function() {
            logCallPrompt(callId, this);
            if ($(this).hasClass('conditional')) check_conditional(this);
            else if ($(this).hasClass('phone_field')) {
                //phone number masking
                var the_val = $(this).val();
                
                // exclude part of the field that specifies extension number, which is assumed to be preceded by 'ext'
                var temp_array = the_val.split('ext');  
                var matches = temp_array[0].match(/\b[0-9]{10,}/g); 
                var temp2;
                if (matches) {
                    for (var i=0; i < matches.length; i++) {
                        if (matches[i].length == 10) {
                            temp2 =  matches[i].substr(0, 3) + '-' + matches[i].substr(3, 3) + '-' + matches[i].substr(6, 4)
                            the_val = the_val.replace(matches[i], temp2);
                            $(this).val(the_val);
                        }
                        else if (matches[i].length == 11 && matches[i].substr(0, 1) == '1') {
                            temp2 = '1-' + matches[i].substr(1, 3) + '-' + matches[i].substr(4, 3) + '-' + matches[i].substr(7, 4)
                            the_val = the_val.replace(matches[i], temp2);
                            $(this).val(the_val);
                        }
                    }      
                }
            }
            else if ($(this).hasClass('email_field')) {
            // check fields that require email validation
                
                if (validateEmails(this)) {
                    $(this).removeClass('missing'); 
                }
                else {
                    $(this).removeClass('shake animated').addClass('shake missing animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                        $(this).removeClass('shake animated');
                    });          
                }               
            }
            
        });
    
    
        // check_conditional($('#instructions .script_section:first .conditional:first')[0]);
        // check fields that require address validation
        $('#instructions .street_field').each(function(index) {
            var input_field = this;
         
            var search_string;
            $(this).on('blur', function () {
                $(this).addClass('missing');
                search_string = $(this).val();
            });
            var autocomplete = new google.maps.places.Autocomplete(this).addListener('place_changed', function() {
                populatePlaceField(this, input_field, 'street', search_string);
                var citystatefields = $('#instructions .citystate_field');
                var this_field = this; 
                
                    // check if city/state/zip field exists and autopopulate if blank
                    if (citystatefields.length == 1 && citystatefields.val() == '') {
                        populatePlaceField(this_field, citystatefields[0], 'citystatezip', search_string);
                    }
                    else if (citystatefields.length == 1) { // if not blank, ask if user wants to overwrite
                        user_confirm('Overwrite the city/state/zip prompt with the new selection?', function() {
                            populatePlaceField(this_field, citystatefields[0], 'citystatezip', search_string);
                        });
                    }
            });
        });

        $('#instructions .fulladdr_field').each(function(index) {
            var input_field = this;
        
            var search_string; 
            $(this).on('blur', function () {
                $(this).addClass('missing');
                search_string = $(this).val();
            });
            var autocomplete = new google.maps.places.Autocomplete(this).addListener('place_changed', function() {populatePlaceField(this, input_field, 'fulladdr', search_string);});
        });

        $('#instructions .citystate_field').each(function(index) {
            var input_field = this;

            var search_string;
            $(this).on('blur', function () {search_string = $(this).val();});
            var autocomplete = new google.maps.places.Autocomplete(this).addListener('place_changed', function() {populatePlaceField(this, input_field, 'citystatezip', search_string);});
        });
        $('#instructions .fulladdr_field, #instructions .street_field, #instructions .citystate_field, #instructions prompt_dd').keydown(function (e) {
        if (e.which == 13 && $('.pac-container:visible').length) return false;
        });     

                
        $('#employee_picker').select2({
            formatResult: formatOption,
            formatSelection: formatOption
        });
        if (currentInstructions['oncall_lists'].hasOwnProperty(schedule_id)){ 
                var lists = currentInstructions['oncall_lists'][schedule_id];
                for (var j=0; j < lists.length; j++) {
                    var list = lists[j];
                    getOncallCallbox(list['list_id'], 'oncall_'+list['action_id'], list['action_id']);
                }
        }
        $('#instructions .uprompt:first').focus();
        if ($('#show_disp').is(':visible')) {
            if ($('#show_disp').is(':checked')) {
            
                $('#operatorScreen .dispatcher').addClass('dispatcher2').removeClass('dispatcher');
            }
            else {
                $('#operatorScreen .dispatcher2').addClass('dispatcher').removeClass('dispatcher2');
            }       
        }
    }

    function removePrompt(t) {
        user_confirm('Are you sure you want to remove this prompt? ', function() {
            var num_of_prompts = $(t).parents('div.sortable-el').find('ul.userprompts li').length;
            if (num_of_prompts == 1) {
                alert('You cannot remove the last prompt.  If you no longer require this prompt, delete the entire step from the agent script.');
            }
            else {
                $(t).parent('li').remove();
            }
        });
    }
    
    function hideCustomCalltype(t) {
         $(t).siblings('input.input').val('');$(t).hide(); $(t).siblings('input.other').hide(); $(t).siblings('select.other').show();$(t).siblings('select.other').val(''); return false;
    }
    
    function checkPromptType(t) {
        if (t.value=='3') {
            $(t).siblings('.p_options').show();
        }
        else $(t).siblings('.p_options').hide();
    }
    
    
    function getEmployees() {
        // will repopulate the employee picker on the action box edit with new selection depending on what contact type 
        // is needed (phone/ email/ web/ etc..)
        
                
        var type = $('#actsel').val();
        var index = $('#el_index').val();

        
        var children = $('#agent_script').find('div.sortable-el');
        var li = children[index];
        var employee_ids = $(li).find('.emp_id').val();
        var e_arr = new Array();
        if (employee_ids != '') {
                //get arrays of action recipients
            e_arr = employee_ids.split(',');
        }
    
        // only add employee to dropdown if they have contact information that's applicable to the action
        $('optgroup.contact').detach().appendTo('#unused_select');
        $('optgroup.contact').removeAttr('selected');
        
        if (type == ACTION_EMAIL || type == ACTION_EMAIL_DELIVER) {
//          $('.contact' + CONTACT_EMAIL).show();
            $('#unused_select optgroup.contact' + CONTACT_EMAIL).detach().appendTo('#empsel');
            $('#unused_select optgroup.contactALL').detach().appendTo('#empsel');
            $('#unused_select optgroup.contactOncall').detach().appendTo('#empsel');
            $("#empsel").val(e_arr);        
            
        }
        else if (type == ACTION_WEB) {
//          $('.contact' + CONTACT_WEB).show();
            $('#unused_select optgroup.contact' + CONTACT_WEB).detach().appendTo('#empsel');
            $("#empsel").val(employee_ids);     

        }
        else if (type == ACTION_CALENDAR) {
//          $('.contact' + CONTACT_WEB).show();
            $('#unused_select optgroup.contactCalendar').detach().appendTo('#empsel');
            $("#empsel").val(employee_ids);     

        }       
        else if ((type == ACTION_FAX || type == ACTION_FAX_DELIVER)) typestr = 'fax';
        else if ((type == ACTION_TXF_DELIVER || type == ACTION_TXF || type == ACTION_LMR || type == ACTION_LMR_DELIVER || type == ACTION_TXF_DELIVER || type == ACTION_BLINDTXF_DELIVER|| type == ACTION_BLINDTXF)) {
            $('#unused_select optgroup.contact' + CONTACT_PHONE).detach().appendTo('#empsel');
            $('#unused_select optgroup.contact' + CONTACT_CELL).detach().appendTo('#empsel');
            $('#unused_select optgroup.contactALL').detach().appendTo('#empsel');
            $('#unused_select optgroup.contactOncall').detach().appendTo('#empsel');
            $("#empsel").val(employee_ids);     
        }
        else if ((type == ACTION_TXTMSG || type == ACTION_TEXT_DELIVER)) {
            $('#unused_select optgroup.contact' + CONTACT_TEXT).detach().appendTo('#empsel');
            $('#unused_select optgroup.contactALL').detach().appendTo('#empsel');
            $('#unused_select optgroup.contactOncall').detach().appendTo('#empsel');
            $("#empsel").val(e_arr);        
        }
        else if ((type == ACTION_VMOFFER || type == ACTION_VMOFFER_DELIVER)) {
            $('#unused_select optgroup.contact' + CONTACT_PHONE).detach().appendTo('#empsel');
            $('#unused_select optgroup.contact' + CONTACT_CELL).detach().appendTo('#empsel');
            $('#unused_select optgroup.contactALL').detach().appendTo('#empsel');
            $('#unused_select optgroup.contactOncall').detach().appendTo('#empsel');
            $("#empsel").val(employee_ids);     
        }
        else if ((type == ACTION_VM || type == ACTION_VM_DELIVER)) {
            $('#unused_select optgroup.contact' + CONTACT_PHONE).detach().appendTo('#empsel');
            $('#unused_select optgroup.contact' + CONTACT_CELL).detach().appendTo('#empsel');
            $('#unused_select optgroup.contactALL').detach().appendTo('#empsel');
            $('#unused_select optgroup.contactOncall').detach().appendTo('#empsel');
            $("#empsel").val(employee_ids);     
        }
        else if ((type == ACTION_TXF_NO_ANNOUNCEMENT || type == ACTION_TXF_NO_ANNOUNCEMENT_DELIVER) ) {
            $('#unused_select optgroup.contact' + CONTACT_PHONE).detach().appendTo('#empsel');
            $('#unused_select optgroup.contact' + CONTACT_CELL).detach().appendTo('#empsel');
            $('#unused_select optgroup.contactALL').detach().appendTo('#empsel');
            $('#unused_select optgroup.contactOncall').detach().appendTo('#empsel');
            $("#empsel").val(employee_ids);     
        }
        else typestr = '';
    }           
    
    function unsaved_changes(editor) {
      if ($('.options_editor:visible').length < 1) return false;
      var cnt = $('.options_editor:visible').find('.modified').length;

      if (cnt > 0) {
        return !confirm('You might have some unsaved changes, are you sure you want to proceed?');

      }
      else {
          return false;
      } 
    }
    
    function close_options_editor() {
      var cnt = $('.options_editor:visible').find('.modified').length;

      if (cnt) {
        user_confirm('You might have some unsaved changes, are you sure you want to proceed?', function() {
          $('.options_editor').hide(); 
          $('.options_editor input, .options_editor select, .options_editor span').removeClass('modified');
        });

      }
      else {
          $('.options_editor input, .options_editor select, .options_editor span').removeClass('modified');
          $('.options_editor').hide(); 
      }
    }
        
    function editAction(el) {
        // find the action text of the clicked action
        var actiontxt = el.find('.action_text').val().trim();
        
        // find the action option (if set)
        //var actopt = $(el).find('input[name^=action_opt]').val().trim();
        
        // keep track of which action we're editing
        var idx = $('#agent_script div.sortable-el').index(el);  
        $('#el_index').val(idx); // keep track of which action we're editing 
        
        

        $('#caption1').html(el.find('.caption1').html());
        $('#caption2').html(el.find('.caption2').html());
        $('#caption3').html(el.find('.caption3').html());      

        // set the action type in the edit box
        $('#actsel').val(el.find('.action_type').val().trim());
        $('#optsel').val(el.find('.action_opt').val().trim());
        var eids = el.find('.emp_id').val().trim();
        var e_arr = [];
        if (eids != '') {
                //get arrays of action recipients
                e_arr = eids.split(',');

        }
        checkAction();
        $("#empsel").multiselect('refresh');   

        close_options_editor();
        $('#actionEditor').show();
            
    }   
    
    function checkLabel(t) {
        close_options_editor();
        var new_value = $.trim(t.value);
        var old_value = $(t).siblings('span').html();
        if (new_value != '') {
            if (new_value.length < 4) {
                alert('Make sure your label is at least 4 characters long');
                t.value = $(t).siblings('span').html();
                t.focus();
                return;
            }
        }
        
        $(t).siblings('span').html(t.value);
        
        // as the label changes, make sure that references to the label are updated too.
        $('input.poptions').each(function() {
        
            var options = $(this).val().split('||');
            if (options[1]) {
                var cond_actions = options[1].split('|');
                for (var i=0; i < cond_actions.length; i++) {
                    var temp = cond_actions[i].split('_');
                    if ((temp[0] == '3' || temp[0] == '2') && temp[1] == old_value) {
                        cond_actions[i] = temp[0] + '_' + new_value;
                    }
                }
                
                $(this).val(options[0] + '||' + cond_actions.join('|'));
            }
        });
    }

    function loadSchedule(url) {
                    $('#did-detail').html('');
                    didLayout.center.children.layout1.open('east');
                    $.ajax({
                        url: url,
                        dataType: 'html',
                        type: 'GET'
                    }).done(function(data) {
                        $('#did-detail').html(data);
                    }); 
    }

    function deleteCalltype(id) {
                    var url = '/Calltypes/delete/' + id;
                    $.ajax({
                        url: url,
                        dataType: 'json',
                        type: 'GET'
                    }).done(function(data) {
                        if (data.success) {
                            loadCalltypes($('#find_did').val(), 'did-content', 'did-detail', 'didLayout');          
                        }
    
                    }); 
    }
    
    function deleteSchedule(id, calltype_id) {
                    var url = '/Schedules/delete/' + id + '/' + calltype_id;
                    $.ajax({
                        url: url,
                        dataType: 'json',
                        type: 'GET'
                    }).done(function(data) {
                        if (data.success) {
                            loadCalltypes($('#find_did').val(), 'did-content', 'did-detail', 'didLayout');          
                        }
    
                    }); 
    }  
    
    function saveCalltype(sid) {
        var action_text;
        var eid;
        var action_type;
        var save_calltype = true;
        var section_id = 0;
        var action_num = 0;
        $('#agent_script').children('.script_section').each(function(index, val) {
            section_id++;
            var prompt_num = 0;
            $(this).find('.sortable-el').each(function(index, val) {                
                $(this).find('select, input, textarea').each(function(index, val) {
                    var input_name = $(this).attr("name").replace(/\[Action\]\[\d*\]/, "[Action]["+action_num+"]");
                    $(this).attr("name", input_name);               
                });
                $(this).find('input.sort_order').val(action_num);           
                action_num++;
                $(this).find('input.section').val(section_id);           
            });
            
            $(this).find('input.section_sort').val(section_id);           
            $(this).find('input.section_title').val($(this).find('div.section_title').html());           
            
        });  
        
         $('#agent_script').find('ul.userprompts').each(function(index, val) {
            var sort_order = 0;
            $(this).find('li.prompt').each(function(index, val) {                
                $(this).find('input.psort').val(sort_order);
                sort_order++;
            });
                        
        }); 
        var data = $('#AgentScripting').serialize();
        
        
        if (save_calltype) {
            var data = $('#AgentScripting').serialize(); 
            
            $.ajax({
                type: 'POST',
                url: '/Schedules/edit/' + sid, 
                data: data,
                dataType: 'json'
            }).done(function(data) {
                if (data.success) {
                    alert('Your changes have been saved');
                    didLayout.center.children.layout1.close('east');      
                    loadCalltypes($('#find_did').val(), 'did-content', 'did-detail', 'didLayout'); return false;
                }
                else alert(data.msg);
            }).fail(function (j, textStatus) {
                alert('Failed to save your changes, try again later - ' + textStatus);        
            });
        }
        return false;
    }
    
    function saveUser() {
        var $form = $('#user-detail form');
        var id = $form.find('input[name="data[User][id]"]').val();
        var url;
        if (id)
            url = '/Users/edit/' + id ;
        else
            url = '/Users/add/';

        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: $form.serialize()
        }).done(function(data) {    
                    if (data.success) {
                        loadPage(this, '/Users/', 'user-content');  
                        userLayout.center.children.layout1.close('east');                               
                    }
            alert(data.msg);
        });                 
        return false;
    }
    
    function saveQueue(id) {
        var url = '/Queues/edit/' + id ;
        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: $('#editQueue').serialize()
        }).done(function(data) {    
                    if (data.success) {
                        loadPage(this, '/Queues/', 'user-content'); 
                        userLayout.center.children.layout1.close('east');                               
                    }
            alert(data.msg);
        });                 
        return false;
    }  


    function addBulletin() {
        var url = '/Bulletins/add/' ;
        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: $('#addBulletin').serialize()
        }).done(function(data) {    
                    if (data.success) {
                        loadPage(this, '/Bulletins/', 'bb-content');    
                        bbLayout.center.children.layout1.close('east');                             
                    }
            alert(data.msg);
        });                 
        return false;  
    }
    function saveBulletin() {
        var url = '/Bulletins/edit/' ;
        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: $('#editBulletin').serialize()
        }).done(function(data) {    
                    if (data.success) {
                        loadPage(this, '/Bulletins/', 'bb-content');    
                        bbLayout.center.children.layout1.close('east');                             
                    }
            alert(data.msg);
        });                 
        return false;  
    }
        
        function saveAccountAdd(t) {
            var myform = $(t).parents('form');
            var url = '/Accounts/add' ;
            $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: myform.serialize()
            }).done(function(data) {    
/*                      if (data.success) {
                            loadPage(this, '/Accounts/edit/'  + data.new_id, 'acct-content');                       
                        }*/
                        alert(data.msg);
            });                 
            return false;
            
        }   
                        
        function loadMessage(message_id, did_id, callback, modal, data) {
            if (typeof(modal) == 'undefined') modal = true;
            if (callback) msgDialogWinCallback = callback;
            else msgDialogWinCallback = null;
            var url = '/Messages/edit/' + message_id + '/' + did_id;
            openMsgDialogWindow(url, 'Message Review', data, modal);        
            
        }  
        
        // loads message from message review 'prev' and 'next' link
        function loadMessageUrl(url) {
            openMsgDialogWindow(url, 'Message Review', null, true);            
        }      
        
    function checkPrompt(t) {
        if ($(t).val() == 'other') {
            $(t).siblings('input.other').show();
            $(t).siblings('input.other').val('');
            $(t).siblings('a.other').show();
            $(t).hide();
        }
        if ($(t).val() == 'Phone Number') $(t).siblings('.p_ver').val('1');
        else if ($(t).val() == 'Email Address') $(t).siblings('.p_ver').val('2');
        else if ($(t).val() == 'Street Address') $(t).siblings('.p_ver').val('5');
        else if ($(t).val() == 'Property Address') $(t).siblings('.p_ver').val('5');
        else if ($(t).val() == 'City, State, Zip') $(t).siblings('.p_ver').val('4');
        else $(t).siblings('.p_ver').val('0');        
        
    }
    
    function checkChoice(t) {
        $('.choices input').prop('disabled', true);
        $('.choices').addClass('cdisabled');
        $(t).parents('div.input').find('div.choices input').prop('disabled', false);
        $(t).parents('div.input').find('div.choices').removeClass('cdisabled');
    }  
    
    function keepalive () {
        $.ajax({
            url: "/OpenAnswer/keepalive",
            success: function(data) {
            }
        });             
    }

    function consoleLog(data) {
        //console.log(data);
    }
    
//pops up a confirmation dialog window with a Yes and No button
//executes a callback function if Yes is selected, and an optional callback if No is selected
    function user_confirm(msg, callback, callback_no) {
        var the_callback = callback;
        var other_callback = null;
        if (typeof(callback_no) != 'undefined') other_callback = callback_no;
        
        $('#confirm_message').html(msg);
        $('#dialog-confirm').dialog({
            resizable: false,
            autoOpen: false,
            height:240,
            modal: true,
            buttons: {
                "Yes": function() {
                    $( this ).dialog('close');
                    the_callback();
                },
                "No": function() {
                    $( this ).dialog('close');
                    if (other_callback) other_callback();
                }
            }, 
            close: function() {
                $( this ).dialog('destroy');
            }
        });  
        $('#dialog-confirm').dialog('open');
    }       
    
    function alert(msg) {

        $('#alert_message').html(msg);
        $('#dialog-alert').dialog({
            resizable: true,
            autoOpen: false,
            height:240,
            modal: true,
            buttons: {
                "OK": function() {
                    $( this ).dialog('close');
                }
            }, 
            close: function() {
                $( this ).dialog('destroy');
            }
        });  
        $('#dialog-alert').dialog('open');
        $('#dialog-alert').dialog('moveToTop');        
    }         

    function changeFontSize(direction) {
        var font_size = document.body.style.fontSize;
        font_size = font_size.replace(/[^0-9\.]/g, '');
        if (direction == 'incr') {
            font_size = Number(font_size) +1;
            if (font_size > 14) font_size = 14;
        }
        else {
            font_size = Number(font_size) -1 ;
            if (font_size < 10) font_size = 10;
        }
        document.body.style.fontSize = font_size + 'px';
        localStorage.setItem('oa_font_size', font_size + 'px');
    }
    
    function openFromMinder(did_id, call_id, msg_id, schedule_id) {
        var url = '/Messages/edit/' + msg_id +'/target:dialogWin';
        
        logCallEvent(call_id,'Opened from minders', EVENT_MINDERCLICK);    
        openMsgDialogWindow(url, 'Message Review', null, true);     
        
        if (0) {    // disable dispatching from operator screen for now.        
        logCallEvent(call_id,'Opened from minders', EVENT_MINDERCLICK);    
        //openMsgDialogWindow(url, 'Message Review', null, true);           
        minderScreenPop(did_id, call_id, schedule_id, function() {
            $('#tab-deliveries').load( "/Messages/msg_deliveries/" + msg_id, function(response) {
                $('#operatorScreen .ct, #operatorScreen .uprompt').addClass('disable_edits');   
            });  
            $('#instructions input:text:first, #instructions textarea:first').blur();           
            $('#operatorScreen #save_msg, #cancel_msg').hide();
            $('#operatorScreen #edit_msg').show();
            $('#operatorScreen #msg_dispatch').show();

        });
        }
    }

    function openFromMsgReview(did_id, call_id, msg_id, schedule_id) {
        $('#save_msg, #cancel_msg').show();
        $('#edit_msg').hide();
        logCallEvent(call_id,'Opened from msg review', EVENT_REPOP);    
        minderScreenPop(did_id, call_id, schedule_id, function() {
            $('#tab-deliveries').load( "/Messages/msg_deliveries/" + msg_id, function(response) {

            });  
            editMessage();            
        });
    }


    function updateMinders(rows) {
        var url;
        var rclass = '';
        var html = '<table class="mindertbl" cellspacing="0" width="100%"><tr><th>Account</th><th align="left">Age</th></tr>';
        
        for (var i=0; i<rows.length; i++) {
            if (rows[i]['minder_age'] > parseInt(settings['minder_warn_time_3'])) rclass = 'minder_warn_color_3';
            else if (rows[i]['minder_age'] > parseInt(settings['minder_warn_time_2'])) rclass = 'minder_warn_color_2';
            else if (rows[i]['minder_age'] > parseInt(settings['minder_warn_time_1'])) rclass = 'minder_warn_color_1';
            else rclass = '';
            url = '/Messages/edit/' + rows[i]['id'] +'/target:dialogWin';
            html += '<tr onclick="openFromMinder(\''+rows[i]['did_id']+'\', \''+rows[i]['call_id']+'\', \''+rows[i]['id']+'\', \''+rows[i]['schedule_id']+'\'); return false;" ';
            if (rclass != '') html += ' class="'+rclass+'"';
            
            html += '><td>'+rows[i]['account_num']+'</td><td>'+secondsToTime(rows[i]['minder_age'])+'/'+ secondsToTime(rows[i]['msg_age']) +'</td></tr>';
        }
        html += '</table>';
        $('#minders-content').html(html);
    }
    

    function logCallPrompt(callId, t) {
                logCallEvent(callId, '[PROMPT] ' + $(t).siblings('label').html() + ': ' + t.value, EVENT_FILL_PROMPT);        
                // find other prompts that contains the the same title
                $('#instructions').find('label:contains("'+$(t).siblings('label').html()+'")').each(function() {
                    // make sure it's exact match
                    if ($(t).siblings('label').html() == $(this).html()) {
                        $(this).siblings('.uprompt').val(t.value);
                    }
                });             
    }
    
    
    function updateServerStatus(status, classname) {
        $('#serverStatus').html('<span class="'+classname+'">'+status+'</span>');   
    }
    function updateServerDelay(delay) {
        $('#serverDelay').html(delay);  
    }

    
    
    function setMinder(msg_id, state, call_id) {
        var url;
        var txt;
        if (state === true) {
            url = '/Messages/minder/' + msg_id + '/' + call_id;
            txt = 'Mindered';
        }
        else {
            url = '/Messages/unminder/' + msg_id+ '/' + call_id;
            txt = 'Un-Mindered';
        }
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json'
        }).done(function(jsondata) {
            if (jsondata.success == true) {
                         
            }
            else alert('Message cannot be ' + txt);
        }).fail(ajaxCallFailure);           
    }

    function setDeliver(msg_id, state, call_id) {
        var url;
        var txt;
        if (state === true) {
            url = '/Messages/deliver/' + msg_id + '/' + call_id;
            txt = 'Delivered';
        }
        else {
            url = '/Messages/undeliver/' + msg_id+ '/' + call_id;
            txt = 'Un-delivered';
        }
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json'
        }).done(function(jsondata) {
            if (jsondata.success == true) {
                         
            }
            else alert('Message cannot be ' + txt);
        }).fail(ajaxCallFailure);               
    }

    function phoneFormat(value) {
        value = $.trim(value);
        var temp = value.replace(/[^0-9]/g,"");         
        if (value == '(null)') return '';
        else if (temp.length < 10) return value;
        else if (temp.length == 10 && value.length <= 15) return  temp.substr(0, 3) + "-" + temp.substr(3,3) + "-" + temp.substr(6, 4);
        else return value;
    }
    
    function connFormatter(row, cell, value, columnDef, dataContext) {
        if (typeof(allAgents[value]) != 'undefined') {
            return allAgents[value].name;
        }
        else return value;
    }

    function refreshLog(t) {
        if ($(t).hasClass('msg')) {
            var msg_id = $(t).attr('mid');
            if (msg_id) $('#msg_log').load('/Messages/msg_event_log/' + msg_id);
        }
    }
    
    function openCallBox(did_id, did, num_to_dial, ext, deliver, call_id, contact_id, schedule_id, opened_by) {
        if (typeof(deliver) !== 'undefined' && deliver) $('#success_action').val('deliver');
        else $('#success_action').val('');                        
        $('#callBoxCtrl').show();
        $('#callBoxResult').hide(); 
        $('#callbox_did').val(did);
        $('#callbox_did_id').val(did_id);
        $('#opened_by').val(opened_by);
        if (typeof(call_id) !== 'undefined') $('#callbox_call_id').val(call_id);
        else $('#callbox_call_id').val('');
        if (typeof(contact_id) !== 'undefined') $('#callbox_contact_id').val(contact_id);
        else $('#callbox_contact_id').val('');
        if (typeof(schedule_id) !== 'undefined') $('#callbox_schedule_id').val(schedule_id);
        else $('#callbox_schedule_id').val('');
        $('#callBox').dialog( "open" );    
        $('#num_to_dial').val(num_to_dial);    
        $('#org_num_to_dial').val(num_to_dial);    
        $('#num_to_dial_ext').html('EXT: ' + ext);    
    }
    
    function clearCall() {
        currentCall = null;
        callStatus = null;
        callId = null;      
    }
    

    // click handler for action buttons within the call type instructions        
    // always originates from the operator screen
    function actionClickHandler( t ) {
        captureOperatorInstructions();
        var contact_id = '';
        var did_id = '';
        var did_number = '';
        var call_id = callId;
        var ext = '';
        var crm_id = '';
        var buttonObj;
        var action_type = $(t).attr('action_type');
        var dialout = $(t).attr('txf');
        var deliver = $(t).attr('dlv');
        
        if ($(t).attr('contact_id')) contact_id = $(t).attr('contact_id');
        if ($(t).attr('did_id')) did_id = $(t).attr('did_id');
        if ($(t).attr('did')) did_number = $(t).attr('did');
        if ($(t).attr('ext')) ext = $(t).attr('ext');
        if ($(t).attr('crm_id')) crm_id = $(t).attr('crm_id');
        
        // find out if previous buttons have been executed
        var action_buttons = $('#instructions .actbtn');
        var button_idx = action_buttons.index(t);
        var exitClick = false;
        var myform = $(t).parents('form');
        
        // disable unused prompts so they're not sent to the server and saved as part of message
        disableUnusedPrompts(); 
        var formdata = getFormData(myform);
        
        
        // check if required prompts from previous steps are filled in        
        $(t).parents('.step').prevAll('.step').find('.uprompt').each(function(){
            if ($(this).hasClass('required') && $(this).is(':visible') && $(this).val() == '') {
                alert('You must fill out the required prompts');
                enableUnusedPrompts();          
                exitClick = true;      
                return false;
            }
        });

        if (exitClick) return false;
                    
        // mark action button as having been clicked by placing checkmark next to it
        $(t).siblings('.action_chk').html($(t).siblings('.action_chk').html() + '&#10003');   

        if (action_type == "picker") {
            $('#cb_emp').removeClass('rubberBand animated').addClass('alertbg rubberBand animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){$(this).removeClass('rubberBand animated'); });
            enableUnusedPrompts();             
            return false;      
        }
        
        buttonObj = logButtonClick(call_id, t);  // log button click;
        
        if (dialout == 'yes'){
            var number_to_dial = $(t).attr('bdata');
//            $('#contact_picker').val(contact_id);
            
            if (deliver == 'yes') {
                openCallBox(did_id, did_number, number_to_dial, ext, true, call_id, contact_id, $(t).attr('schedule_id'), 'operator_screen');
            }
            else {
                openCallBox(did_id, did_number, number_to_dial, ext, false, call_id, contact_id, $(t).attr('schedule_id'), 'operator_screen');
            }
            //$('#callbox_did').val('');
            //$('#callBox').dialog( "open" );      
//            $('#contact_picker').val(contact_id);
            $('#cb_action_id').val($(t).attr('aid'));
            checkButtons(callStatus);
            refreshLog(t);
            enableUnusedPrompts();            
            return false;
        }

        else if (action_type == ACTION_WEB) {
            //open webform in a separate window
            var myWindow=window.open($(t).attr('bdata'),'_blank','width=800,height=700,scrollbars=1,resizable=1,location=yes,menubar=yes,toolbar=yes');
            myWindow.focus();       

            var url = '/Messages/execute/' + $(t).attr('action_id') + '/'+ call_id;
                        
        }
        else if (action_type == ACTION_CALENDAR) {
            //open calendar in a separate window
            var url = '/Scheduling/EaServices/schedule_from_call/' + $(t).attr('action_id') + '/'+ call_id +'/' + $(t).attr('contact_id');
            myWindow=window.open(url,'_blank','width=800,height=700,scrollbars=1,resizable=1,location=yes,menubar=yes,toolbar=yes');
            myWindow.focus();       
            
        }       
        /*else if (action_type == ACTION_CALENDAR_DELIVER) {
            //open calendar in a separate window
            var url = '/EaServices/schedule_from_call/' + $(t).attr('action_id') + '/'+ call_id +'/' + $(t).attr('action_id') + '/deliver';
            myWindow=window.open(url,'_blank','width=800,height=700,scrollbars=1,resizable=1,location=yes,menubar=yes,toolbar=yes');
            myWindow.focus();       
        }       */
        else if (action_type == ACTION_DELIVER) {
                $.ajax({
                    url: '/Messages/delivery_check/'+ call_id,
                    dataType: 'html'
                })
                .done(function(data) {
                    if (data > 0) {
                        confirm_msg = 'Are you sure you want to mark this message as DELIVERED?';
                    }
                    else {
                        confirm_msg = '<span style="color:red"><b>There are no deliveries recorded on this message.</b></span>  <br><br>Are you sure you want to mark this as DELIVERED ? ';
    
                    }
                    user_confirm(confirm_msg, function() {
                        postJson('/Messages/execute/' + $(t).attr('action_id') + '/'+ call_id,  formdata + '&' + $.param(buttonObj), function() {
                            refreshLog(t2); 
                            closeOperatorScreen();
                        });                                                                       
                    });                 
                }) 
        }
        else if (action_type == ACTION_DISPATCH || action_type == ACTION_HOLD || action_type == ACTION_DELIVER) {
            postJson('/Messages/execute/' + $(t).attr('action_id') + '/'+ call_id,  formdata + '&' + $.param(buttonObj), function() {
                refreshLog(t2); 
                closeOperatorScreen();
            });                                     
        }
            else if (action_type == ACTION_CRM) {
                
                var prompts = new Object;
                var url = '/Crms/crmaction/'+ crm_id;
                
                $("[prompt]").each(function () {
                    prompts[$(this).attr('prompt')] = $(this).val();
                });
                //var request = new Object;
                JSON.stringify(prompts);
                
                $.ajax({
                    url: url,
                    dataType: 'html',
                    type: 'post',
                    data: JSON.stringify(prompts),
                    })
                    .done(function(data) {
                            $('#dialogIntegration').html(data);
                            $('#dialogIntegration').dialog('open');
                    });
            }
        else if (dialout == 'no' && deliver == 'yes'){

//            contact_id = '';

//            $('#contact_picker').val(contact_id);

            var t2 = t;
            user_confirm('Are you sure you want to send this message?', function() {

                var url = '/Messages/execute/' + $(t).attr('action_id') + '/'+ call_id;
                postJson('/Messages/execute/' + $(t).attr('action_id') + '/'+ call_id,  formdata + '&' + $.param(buttonObj), function() {
                    refreshLog(t2);
                    closeOperatorScreen();
                });                 
            }); 
        }       
        else if (dialout=='no' && deliver=="no"){
//            contact_id = '';
//            $('#contact_picker').val(contact_id);
            var t2 = t;
            var user_prompt;
            if (action_type == ACTION_TXTMSG) user_prompt = 'send the text';
            else user_prompt = 'send the email';
            user_confirm('Are you sure you want to '+user_prompt+'?', function() {
                postJson('/Messages/execute/' + $(t).attr('action_id') + '/'+ call_id,  formdata + '&' + $.param(buttonObj), function() {refreshLog(t2);});                 
                    refreshLog(t2);
                                                                    
            });     
        }       
        
        enableUnusedPrompts();
                
        return false;
    }   
    
    function updateCallList(form, did_id, target, view) {
        if (typeof(view) == 'undefined') view='index';
        loadPagePost(form, '/CallLists/'+view+'/' + did_id, target, null, null);
    }
            
    function createToast(t, message, sticky, duration){
        if (typeof(sticky) == 'undefined') var sticky = false;
        if (typeof(duration) == 'undefined') var duration = 10000;
        var options = {
            duration: duration,
            sticky: sticky,
            type: t
        };
        
        $.toast(message, options);
    }
    
    function requiredPromptsCompleted() {
        var missingFields = false;
        
        $('#instructions .step').find('.uprompt').each(function(){
            if ($(this).hasClass('required') && $(this).is(':visible') && $(this).val() == '') {
                alert('You must fill out the required prompts');
                missingFields = true;
                return false;
            }
        });      
        if (missingFields) return false;
        else return true;
    }
    function captureOperatorInstructions() {
        $('#sandbox').html($('.cinstr').html());
        $('#sandbox input, #sandbox .uprompt_max, #sandbox select, #sandbox textarea, #sandbox button, #sandbox .script_section:hidden').remove();
        $('#opinstr').val($('#sandbox').html());      
        $('#sandbox').html('');
    }

    function disableUnusedPrompts() {
        $('#operatorScreen .step:hidden .prompts input[type=hidden], #operatorScreen .step:hidden .prompts .uprompt').each(function() {
            if ($(this).parents('.step').hasClass('is_hidden') || $(this).parents('.script_section').is(':hidden')) $(this).prop('disabled', true);
        });             
    }
    
    function enableUnusedPrompts() {
        $('#operatorScreen .step:hidden .prompts input[type=hidden], #operatorScreen .step:hidden .prompts .uprompt').prop('disabled', false);
    }
    
    function getFormData(f) {
        var formdata;
        // don't include miscnotes if blank;
        if ($('#operatorScreen  #miscnotes').val() == '') $('.miscnotes').prop('disabled', true);
        formdata =  f.serialize();
        $('.miscnotes').prop('disabled', false);
        return formdata;
    }
    
    // click handler for employee buttons, can originate from either the message review or operator screen
    function btnClickHandler( t, callback ) {
        captureOperatorInstructions();
        var from_operator_screen;
        var from_msg_review;
        if ($(t).parents('#msgDialogWin').length > 0) {
            from_msg_review = true;
            from_operator_screen = false;
        }
        else {
            from_operator_screen = true;
            from_msg_review = false;
        }
        var formdata;
        if (typeof(callback) === 'undefined')  callback = null;
        var contact_id = '';
        var call_id = '';
        var did_id = '';
        var did_number = '';
        var ext = '';
        var msg_edit = false;
        var myform;
        
        
        if ($(t).hasClass('msg_edit')) msg_edit = true;
        
        if ($(t).attr('did_id')) did_id = $(t).attr('did_id');
        if ($(t).attr('did')) did_number = $(t).attr('did');
        if ($(t).attr('contact_id')) contact_id = $(t).attr('contact_id');
        if ($(t).attr('call_id')) call_id = $(t).attr('call_id');
        if ($(t).attr('ext')) ext = $(t).attr('ext');
        
        // log button click 
        var buttonObj = logButtonClick(call_id, t);  // log button click;

        // disable invisible prompt fields except for dispatcher fields
        if (from_msg_review) {
            myform = $('#msg_edit_form');
            formdata = getFormData(myform);
        }
        else {
            myform = $('#instructions form');
            disableUnusedPrompts();
            formdata = getFormData(myform);
            enableUnusedPrompts();
        }           
        


        
        if ($(t).attr('btype') == CONTACT_PHONE || $(t).attr('btype') == CONTACT_CELL){
            $(t).html($(t).html() + '&#10003');         
            
            //if (from_msg_review || requiredPromptsCompleted()) {
            if (1) {
                var recipient;
                recipient = $(t).attr('bdata');
//                $('#contact_picker').val(contact_id);
                if (from_operator_screen) {
                    openCallBox(did_id, did_number, recipient, ext, false, call_id, contact_id, currentScheduleId, 'operator_screen');
                }
                else {
                    openCallBox(did_id, did_number, recipient, ext, false, call_id, contact_id, '', 'message_review');
                }
                $('#cb_action_id').val($(t).attr('aid'));
                checkButtons(callStatus);
                refreshLog(t);
            }
            return false;
        }
        else if ($(t).attr('btype') == CONTACT_EMAIL || $(t).attr('btype') == CONTACT_TEXT || $(t).attr('btype') == CONTACT_FAX || $(t).attr('btype') == BUTTON_DISPATCH || $(t).attr('btype') == BUTTON_DELIVER){
            if ($(t).attr('btype') != BUTTON_DELIVER) {
                if (!from_msg_review && !requiredPromptsCompleted()) {
                    return false;
                }
            }

            recipient = $(t).attr('bdata');
            var confirm_msg = 'Are you sure you want to send this message?';
            if ($(t).attr('btype') == BUTTON_DISPATCH) {
                confirm_msg = 'Are you sure you want to send this message to dispatch?';
                user_confirm(confirm_msg, function() {
                    var action_id = 'none';
                    var button_type;
                    
                    button_type = $(t).attr('btype');
                    // dispatch button is a dialog box button

                    var url = '/Messages/btnClick/' + call_id;
                    
                    postJson('/Messages/btnClick/' + call_id,  formdata + '&' + $.param(buttonObj), function() {
                        if (msg_edit) loadMsgDeliveries($('#msg_edit_id').val());
                        if (button_type == BUTTON_DISPATCH || button_type == BUTTON_DELIVER) {
                            closeOperatorScreen(); 
                        }
                    });                 
                                                                    
                });                 
            }
            else if ($(t).attr('btype') === BUTTON_DELIVER) {
                $.ajax({
                    url: '/Messages/delivery_check/'+ call_id,
                    dataType: 'html'
                })
                .done(function(data) {
                    if (data > 0) {
                        confirm_msg = 'Are you sure you want to mark this message as DELIVERED?';
                    }
                    else {
                        confirm_msg = '<span style="color:red"><b>There are no deliveries recorded on this message.</b></span>  <br><br>Are you sure you want to mark this as DELIVERED ? ';
    
                    }
                    user_confirm(confirm_msg, function() {
                        var action_id = 'none';
                        var button_type;
                        
                        button_type = $(t).attr('btype');
                        // dispatch button is a dialog box button
    
                        var url = '/Messages/btnClick/' + call_id;
                        
                        postJson('/Messages/btnClick/' + call_id,  formdata + '&' + $.param(buttonObj), function() {
                            if (msg_edit) loadMsgDeliveries($('#msg_edit_id').val());
                            if (button_type == BUTTON_DISPATCH || button_type == BUTTON_DELIVER) {
                                closeOperatorScreen(); 
                            }
                        });                 
                                                                        
                    });                 
                })                  
            }
            else {
                    $(t).html($(t).html() + '&#10003');         
                
                    user_confirm(confirm_msg, function() {
                        var action_id = 'none';
                        var button_type;
                        
                        button_type = $(t).attr('btype');
                        // dispatch button is a dialog box button
    
                        var url = '/Messages/btnClick/' + call_id;
                        
                        postJson('/Messages/btnClick/' + call_id,  formdata + '&' + $.param(buttonObj), function() {
                            if (msg_edit) loadMsgDeliveries($('#msg_edit_id').val());
                            if (button_type == BUTTON_DISPATCH || button_type == BUTTON_DELIVER) {
                                closeOperatorScreen(); 
                            }
                        });                 
                                                                        
                    });                 
            }
                                                                                                                                            

        }
        else if ($(t).attr('btype') == CONTACT_WEB) {
            $(t).html($(t).html() + '&#10003');         
            
            var myWindow=window.open($(t).attr('bdata'),'_blank','width=800,height=700,resizable=1,scrollbars=1,location=yes,menubar=yes,toolbar=yes');
            myWindow.focus();       
            logCallEvent(call_id,'Going to web form: ' + $(t).attr('bdata'));    
//        window.setTimeout(function() {refreshLog(t)}, 1000);
                /*postJson('/Messages/btnClick/' + call_id,  formdata + '&' + $.param(buttonObj), function() {
                    if (msg_edit) loadMsgDeliveries($('#msg_edit_id').val());
                }); */                      
        }
    

        //see if we need to refresh message log on message review screen

        return false;
    }   

    
    function closeOperatorScreen() {
        endCall(callId);
        manualPop = false;
        currentCall = null;
        callStatus = null;
        msgId = null;
        callId = null;          
        currentScheduleId = null;           
        incomingUniqueId = '';      
        
        $('#test_time_save').val('');
        $('#operatorScreen #msg_dispatch').show();          
        $('#operatorScreen').dialog( "close" );       

        // if another screen pop attempt had come in, while the operator screen was last open, then pop it
        if (lastPopAttempt !== null) {
            screenPop(lastPopAttempt); 
            lastPopAttempt = null;
        }
        else {
            if (pause_agent && (break_id == 'on-call')) {
                leaveBreak();
            }
        }
        
        if ($('#callBox').dialog('isOpen')) {
            $('#callBox').dialog('close')             
        }

        if ($('#msgDialogWin').dialog('isOpen')) {
            $('#msgDialogWin #refresh_msg').trigger('click');             
        }

    }
    
    function endOutboundCall() {
        if (outboundId) {
            var url = '/Outbound/end/' + outboundId;
            
            $.ajax({
                    url: url,
                    type: 'get',
                    dataType: 'json'
            }).done(function(jsondata) {
                if (jsondata.success !== true) alert(jsondata.msg);
            }).fail(ajaxCallFailure);                 
            outboundId = '';
        }
    }
    
    // Dialout box buttons click handler
    function callboxAction( t ) 
    {
        var button_id = $(t).attr('id');
        if ($('#opened_by').val() == 'operator_screen') from_operator_screen = true;
        else from_operator_screen = false;
        var myform;
        var formdata;         
        var call_id = $('#callbox_call_id').val();
        
        $(t).siblings('.action_chk').html($(t).siblings('.action_chk').html() + '&#10003');
        if ($(t).siblings('.action').length) {
            var event = $(t).siblings('.action').html();
//          logCallEvent(call_id, 'Clicked on action: ' + event);
        }
        if (button_id == 'in_hold'){
            socket.emit('call_hold_in');
            logCallEvent(call_id, '[HOLD] Incoming call put on hold');
        }
        else if (button_id == 'in_hangup'){
            socket.emit('call_hangup_in');
            socket.emit('call_hangup_agent');               
            logCallEvent(call_id, '[HANGUP] Hang up incoming call');
        }
        else if (button_id == 'in_talk'){
            socket.emit('call_talk_in');
            logCallEvent(call_id, '[TALK] Pick up incoming call');
        }   
        else if (button_id == 'out_talk'){
            socket.emit('call_talk_out');
            logCallEvent(call_id, '[TALK] Pick up outgoing call');
        }       
        else if (button_id == 'out_hold'){
            socket.emit('call_hold_out');
            logCallEvent(call_id, '[HOLD] Outgoing call put on hold');
        }                       
        else if (button_id == 'out_hangup'){
            $('#out_hangup').button("disable");
            socket.emit('call_hangup_out');
            logCallEvent(call_id, '[HANGUP] Hangup outgoing call');
            $('#out_dial').button("enable");
            endOutboundCall();
        }      
        else if (button_id == 'btn_patch'){
            socket.emit('call_patch');
            logCallEvent(call_id, '[PATCH] Call patched', EVENT_PATCH);
            
        }                       
        else if (button_id == 'btn_cancel'){
            $('#callBoxCtrl').show();
            $('#callBoxResult').hide();                             
            $('#callBox').dialog('close');                  
            logCallEvent(call_id, '[CANCEL] Transfer cancelled');
        }                       
        else if (button_id == 'btn_cancel2'){
            user_confirm('Are you sure you want to cancel and not enter a call result?', function() {
                $('#callBox').dialog('close');                  
                $('#callBoxResult').hide();                             
                logCallEvent(call_id, '[CANCEL] Dialout status cancelled');
            });
        }                       
        else if (button_id == 'btn_ret'){
            $('#callBoxCtrl').hide();
            $('#callBoxResult').show();                             
            //logCallEvent(call_id, '[RETURN] from dialout box');
        }
                    
        else if (button_id == 'out_dial'){
            var d = new Date(); 
            var emsg = '';
            if ($('#num_to_dial').val() != $('#org_num_to_dial').val()) emsg = '[DIAL] Dialing ' + $('#org_num_to_dial').val() + ' Actual Dialed: ' + $('#num_to_dial').val();
            else emsg = '[DIAL] Dialing ' + $('#num_to_dial').val(); 
            socket.emit('call_dial_out', {'dialstr': $('#num_to_dial').val(), 'did': $('#callbox_did').val(), 'did_id': $('#callbox_did_id').val() });
            logCallEvent(call_id, emsg);
            $('#out_dial').button("disable");
            $('#out_hangup').button("enable");              
                    if (!from_operator_screen) {
                        myform = $('#msg_edit_form');
                        formdata = myform.serialize();
                    }
                    else {
                        myform = $('#instructions form');                   
                        formdata = getFormData(myform);
                    }                   
                    var url = '/Messages/dial/' + call_id;
                    
                    $.ajax({
                            url: url,
                            type: 'post',
                            dataType: 'json',
                            data: formdata + '&unique_id='+incomingUniqueId+'&num_dialed='+$('#num_to_dial').val()+'&ext_dialed='+$('#num_to_dial_ext').html()+'&contact_id=' + $('#callbox_contact_id').val() + '&schedule_id=' + $('#callbox_schedule_id').val() + '&did_id=' + $('#callbox_did_id').val()
                    }).done(function(jsondata) {
                        if (jsondata.success !== true)  alert('Transfer result cannot be recorded');
                        else {
                            outboundId = jsondata.outbound_id;
                        }
                    }).fail(ajaxCallFailure);                   
            
        
        }
        else if (button_id == 'btn_ret2') {
            var msg_status;
            
            // check if we need to mark the message as delivered if callout was successfull
            if ($('#success_action').val() == 'deliver' && $('#callBoxResult input:checked').val() == CALLOUT_SUCCESS) msg_status = 'deliver';
            else msg_status = 'return';
                
            /*if (button_id == 'btn_ret2') msg_status = 'return';
            else if (button_id == 'btn_minder') msg_status = 'minder';
            else if (button_id == 'btn_deliver') msg_status = 'deliver';*/
            var result = $('#callBoxResult input:checked').val();
            $('#callBox').dialog('close'); 
            logCallEvent(call_id, '[RETURN]:  ' + callResults[$('#callBoxResult input:checked').val()]);            
            
            // if transfer is a success check, record as a delivery
            if (result == '1') {
                $('#message_action').val(callResults[$('#callBoxResult input:checked').val()]);
                    if (!from_operator_screen) {
                        myform = $('#msg_edit_form');
                        formdata = myform.serialize();
                    }
                    else {
                        myform = $('#instructions form');                   
                        formdata = getFormData(myform);
                    }
                    var url = '/Messages/transfer/' + call_id + '/'  + msg_status;
                    $.ajax({
                            url: url,
                            type: 'post',
                            dataType: 'json',
                            data: formdata + '&contact_id=' + $('#callbox_contact_id').val() + '&schedule_id=' + $('#callbox_schedule_id').val() + '&did_id=' + $('#callbox_did_id').val()
                    }).done(function(jsondata) {
                            if (jsondata.success == true) {
                                if ( $('#success_action').val() == 'deliver' || button_id == 'btn_deliver') {
                                    closeOperatorScreen();
                                }
                            }
                            else alert('Transfer result cannot be recorded');
                    }).fail(ajaxCallFailure);   
            }
            else {
                var url = '/Messages/reset_minder/' + call_id;
                $.ajax({
                        url: url,
                        dataType: 'json'
                }).done(function(jsondata) {
                        if (jsondata.success == false) {
                                alert(jsondata.msg);
                        }
                }).fail(ajaxCallFailure);
                                    
                logCallEvent(call_id, 'Transfer not successful:  ' + callResults[$('#callBoxResult input:checked').val()]);             
            }
            
        }
        else if (button_id == 'btn_minder') {
            $('#callBox').dialog('close');          
            logCallEvent(callId, '(MINDER btn from call screen):  ' + callResults[$('#callBoxResult input:checked').val()]);            
            setMinder(msgId, true, callId)
        }
        else if (button_id == 'btn_deliver') {
            $('#callBox').dialog('close');
            logCallEvent(callId, '(DELIVER btn from call screen):  ' + callResults[$('#callBoxResult input:checked').val()]);               
            $('#message_action').val(callResults[$('#callBoxResult input:checked').val()]);
                    var myform;
                    var formdata;
                    if (!from_operator_screen) {
                        myform = $('#msg_edit_form');
                        formdata = myform.serialize();
                    }
                    else {
                        myform = $('#instructions form');                   
                        formdata = getFormData(myform);
                    }           
                var url = '/Messages/execute/' + $('#cb_action_id').val() + '/'+ callId;
                $.ajax({
                        url: url,
                        type: 'post',
                        dataType: 'json',
                        data: formdata
                }).done(function(jsondata) {
                        if (jsondata.success == true) {
                                closeOperatorScreen();
                        }
                        else alert('Message cannot be delivered');
                }).fail(ajaxCallFailure);       
            //setDeliver(msgId, true, callId)        
            
        }
        else if (button_id == 'btn_next') {
            $('#callBox').dialog('close');
            logCallEvent(callId, '(NEXT btn from call screen):  ' + callResults[$('#callBoxResult input:checked').val()]);              
        }

        return false;
    }       

    function getTime(start, end) {
        var tstart, tend;
        var tmer1, tmer2;
        var retval;
        if (start == null || end == null) return 'Closed';
        tstart = start.split(':');
        tend = end.split(':');
        
        tmer1 = (tstart[0]/12)? 'pm': 'am';
        tmer2 = (tend[0]/12)? 'pm': 'am';
        
        retval =  (tstart[0]%12) + ':' + tstart[1];
        
        if (tmer1 != tmer2) retval += tmer1;
        retval +=  '-' + (tend[0]%12) + ':' + tend[1];
        retval += tmer2;
        
        return retval;
        
        
        
    }
    
    function attachNavListener() {
        $('.ajaxlink').on('click', function() {
            var tabId = $( this ).closest( "div.tab" ).attr('id');
            $('#'+tabId+' .rightcol').load($(this).attr('href'));
            $('ul.leftnav li').addClass('ct');
            $('ul.leftnav li').removeClass('ct_sel');
            $(this).addClass('ct_sel');
        
        });         
    }
            
    function takeBreak(reason) {
        // send break request to OC
        socket.emit('operator_break',{'onBreak': true, 'reason': reason, 'ext': myExtension, 'queues': myQueues});    
        if ($('#breakDialog').dialog('isOpen')) { 
            $('#breakDialog').dialog('close');          
        }
    }

    function leaveBreak() {
        socket.emit('operator_break',{'onBreak': false, 'ext': myExtension, 'queues': myQueues});

    }
    
    function logoutUser() {
        if (socket) takingCalls(false, false);
        localStorage.setItem('oa_taking_calls', false); 
        if (socket) socket.disconnect();
        localStorage.setItem('oa_onbreak', false); 
        // mark end of break if user is on break before logging htem out
        if (localStorage.getItem('break_id') != '') {
            postJson('/Users/leave_break', {'msg': '', 'break_id': localStorage.getItem('break_id')}, function() {
                window.location='/Users/logout';
                localStorage.setItem('break_id', '');
            }); 
        }
        else {
            window.location='/Users/logout';          
        }
    }
    
    function takingCalls(status, force) {
        checked_in = false;
        if (typeof(status) == 'string') {
            if (status == 'true') status = true;
            else status = false;
        }
        socket.emit('operator_status', {'takingCalls': status, 'forceLogout': force, 'queues': myQueues, 'penalties': myPenalties, 'ext': myExtension, 'membername': myName, 'role': myRole});          
        if (status) jQuery.ajax('/Users/status/1');
        else jQuery.ajax('/Users/status/0');
    }
    
    
    function openDialogWindow(url, title, data, callback, winwidth, winheight) {
        $('#dialogWin').html('<br><br><br><br><br><br><br><br><br><center><img src="/img/loadera32.gif">');
        if (typeof(callback) != 'undefined' && callback) dialogWinCallback=callback;
        else dialogWinCallback = null;
        var posted;
        if (!data) posted = new Object();
        else posted = data;
        $.ajax({
            url: url,
            type: 'GET',
            data: posted
        }).done(function(data) {
            $('#dialogWin').html(data);
        }).fail(ajaxCallFailure);
    
    
        if (title) {
            $('#dialogWin').dialog( "option", "title", title );
            $('#dialogWin').siblings('.ui-dialog-titlebar').show();
        }
        else $('#dialogWin').siblings('.ui-dialog-titlebar').hide();
            
        if (typeof(winwidth) !== 'undefined' && typeof(winheight) !== 'undefined') {
            $('#dialogWin').dialog('option', 'width', winwidth);
            $('#dialogWin').dialog('option', 'height', winheight);
            $('#dialogWin').dialog('open');
        }
        else {
            $('#dialogWin').dialog('option', 'width', Math.floor($(window).width()  * .90));
            $('#dialogWin').dialog('option', 'height', Math.floor($(window).height()  * .90));
            $('#dialogWin').dialog('open');
        
        }
    }       
    function prevMessage() {

        if (currentIndex >= 1) {
            currentIndex--;
            loadMessage(msgArray[currentIndex], 'null', null, true, 'current='+(currentIndex+1)+'&total='+msgArray.length);
            //checkAuditButtons();
        }
    }
    
    function nextMessage() {
        if (currentIndex < msgListLength) {
            currentIndex++;
            loadMessage(msgArray[currentIndex], 'null', null, true, 'current='+(currentIndex+1)+'&total='+msgArray.length);
            
            //checkAuditButtons();
        }
                
    }

    function gotoMessage(t) {
        var idx = t.value;
        if (idx <= msgArray.length) {
            $('#msg_loading').show();
            currentIndex = idx-1;
            loadMessage(msgArray[currentIndex],'null', null, false, 'current='+(currentIndex+1)+'&total='+msgArray.length); 
        }
    }
    
    function openMsgDialogWindow(url, title, data, modal) {
        if (typeof(modal) == 'undefined') modal = true;
        var posted;
        if (!data) posted = new Object();
        else posted = data;
        $.ajax({
            url: url,
            type: 'GET',
            data: posted
        }).done(function(data) {
            if (msgWinLayout) msgWinLayout.destroy();       
            $('#msgDialogWin').html(data);
         
        }).fail(ajaxCallFailure);
    
    
        $('#msgDialogWin').dialog('option', 'modal', modal);
        if (!$('#msgDialogWin').dialog('isOpen')) {
            $('#msgDialogWin').dialog('option', 'width', Math.floor($(window).width()  * .94));
            $('#msgDialogWin').dialog('option', 'height', Math.floor($(window).height()  * .94));
            $('#msgDialogWin').dialog('open');
        }
        else {
            $('#msgDialogWin').dialog('moveToTop');
        }
        
    }       
    function setButtons(data) {
                if (data.canTakeCalls) {
                    var operatorTakingCalls = localStorage.getItem('oa_taking_calls');
                    $('#operator_btns').show();
                    $('#force_logout').hide();      
                    if (operatorTakingCalls == 'true') {
                        $('#unavailbtn').hide();
                        $('#availbtn').show();
                        $('#availbtn').prop('disabled', false);      
                    }
                    else {
                        $('#unavailbtn').show();
                        $('#availbtn').hide();
                    }
                }
                else {
                    $('#operator_btns').hide();
                    $('#force_logout').show();
                }       
    }
    function broadcastMessage() {
        socket.emit('broadcast', {'msg': $('#broadcast').val() + '<br> - ' + myUsername + '(' + moment().format('h:mm a') + ')'});
    }
    function disconnectServer() {
        socket.removeAllListeners();
        socket.disconnect();
        socket.destroy();
    }
    
    function connectToServer() 
    {
        var serverConnectRetry = true;
        
        if (reconnectCount > 200) {
            // console.log('count > 200, not opening socket');
            //clearInterval(reconnectTImer);
        }
        
        
        updateServerStatus(new Date().toString() + 'Trying to connect to server', 'info');          
        //if (socket) {
        if (0) 
        {
            socket.socket.connect();
        }
        else 
        {
            socket = io(ocServer, 
            {
                'reconnect': true,
                'reconnection delay': 300,
                'max reconnection attempts': 20,
                'timeout': 30000,
                'sync disconnect on unload': true
            });
            
            socket.on('user_status', function(data) 
            {
                setButtons(data);
            });
            
            socket.on('keepalive', function(data) 
            {
                socket.emit("keepalivereply",data);
            });
            socket.on('keepalivedelay', function(data) 
            {
                updateServerDelay(data.delay);
            });
            
            socket.on('user_checkin_response', function(data) 
            {
                // only attempt to take calls if allowed by OC server
                if (data.canTakeCalls) 
                {
                    // check the last known state of previous OA session to see if we should be taking calls
                    var operatorTakingCalls = localStorage.getItem('oa_taking_calls');
/*                      if ((operatorTakingCalls == 'false' || operatorTakingCalls == null) && data.queues != '') 
                    {
                        operatorTakingCalls = "true";
                        localStorage.setItem('oa_taking_calls', true);
                    }*/
                    takingCalls(operatorTakingCalls, false);
                }
                setButtons(data);
            });
            
            socket.on('connect', function (reason)
            {
                socket.emit('user_checkin', {'ext': myExtension, 'user_id': myId, 'username': myUsername})
                
                reconnectCount = 0;
                updateServerStatus('Connected to server', 'info');
                websocketConnected = true;
                
            });         
            
            socket.on('operator_msg', function(data) 
            {
                alert(data.msg);
            });
            
            socket.on('status_update', function(data) {
                if (checked_in) {
                    if (data.agents.hasOwnProperty('SIP/' + myExtension)) {
                        my_status = data.agents['SIP/' + myExtension];
                        localStorage.setItem('oa_taking_calls', true);  
                        if (my_status.paused == '1') {
                            $('#operator_btns').show();//
                            $('#unavailbtn').hide();//
                            $('#availbtn').show();
                            $('#offbreakbtn').hide();
                            $('#onbreakbtn').show();
                            $('#availbtn').prop('disabled', true);
                            var onBreak = localStorage.setItem('oa_onbreak', true);
                        
                        }
                        else {
                            $('#operator_btns').show();//
                            $('#unavailbtn').hide();//
                            $('#availbtn').show();
                            $('#offbreakbtn').show();
                            $('#availbtn').prop('disabled', false);
                            $('#onbreakbtn').hide();
                            var onBreak = localStorage.setItem('oa_onbreak', false);
                        }
                    }
                    else {
                        $('#unavailbtn').show();//
                        $('#unavailbtn').prop('disabled', false);
                        
                        $('#availbtn').hide();//
                        $('#offbreakbtn').hide();
                        $('#onbreakbtn').hide();
                        localStorage.setItem('oa_taking_calls', false); 
                        
                    }
                }
            });
            
            socket.on('updateAgents', function (data) {
                var html = '<div id="operatordiv" style="padding: 10px 20px;">' + new Date().toString() + ' (<strong>This list will update every ' +settings['agent_update_seconds']+ ' seconds)</strong></div><br><br><table class="gentbl" width="100%" cellspacing="0" ><tr><th width="80" align="left">Extension</th><th align="left" width="110">Name</th><th align="left" width="230">Queues</th><th width="100">IP Address</th><th width="150">Socket</th><th>Status</th></tr>';
                var oa_users = data.oa_users;
                var all_agents = data.allagents;
                var break_reasons = data.breakReasons;
                var users = new Array();
                for (var i in oa_users) {
                    users.push(oa_users[i]);
                }
                users.sort(function(a, b){
                    var a1= a.username, b1= b.username;
                    if(a1== b1) return 0;
                    return a1> b1? 1: -1;
                });         
                for (var i=0; i < users.length; i++) {
                    var ext = 'SIP/' + users[i]['extension'];
                    html += '<tr><td>'+users[i]['extension']+'</td><td>'+users[i]['username']+'</td><td>';
                    if (data.allagents.hasOwnProperty(ext)) {
                        html += data.allagents[ext]['queues'].join(', ');
                    }
                    html += '</td>';
                    html += '<td align="center">'+users[i]['ip_address'] + '</td><td align="center">'+ i + '</td>';
                    html += '<td>';
                    if (users[i]['takingCalls'] === true) {
                        if (all_agents.hasOwnProperty(ext)) {
                        if (all_agents[ext]['paused'] == '1') {
                            if (break_reasons.hasOwnProperty(users[i]['extension'])) html += 'On Break - '+ break_reasons[users[i]['extension']];
                            else html += 'On Break';
                        }
                        else {
                            html += 'Taking calls';
                        }
                        }
                        else {
                            html += 'Unknown';
                        }
                    
                    }
                    else html += 'Not taking calls';
                    html += '</td></tr>';
                }
                html += '</table>';
                //html += '<br><br>' + localStorage.getItem('oa_taking_calls') + typeof(localStorage.getItem('oa_taking_calls'));
                $('#report-detail').html(html);                 
            });           
            
            
            socket.on('dispatch_update', function(data) {
                updateMinders(data.rows);
                updateBreaks(data);
            });
            
            socket.on('msgcount_update', function(data) {
                $('#undel_msg').html(data['rows'][0]['undelivered']);

                $('#held_msg').html(data['rows'][0]['hold']);
            });
            
            // requires OC version 0.103 or greater
            socket.on('call_patched', function(data) {
                $('#callBoxCtrl').hide();
                $('#callBoxResult').show();                     
            });
            
            socket.on('call_not_patched', function(data) {
                alert('failed to patch call');
            });         
            
            socket.on('on_break', function(data) {
                break_button_pressed = false;  // reset this flag once OC confirms we're on break

                // check if we're pausing agent because agent is on-call or wrap-up
                if (data.reason != 'on-call' && data.reason != 'wrapup') {
                    localStorage.setItem('oa_onbreak', true);                 
                    localStorage.setItem('oa_onbreak_reason', data.reason);                 
                    startBreakTimer();
                    $('#availbtn').prop('disabled', true); //
                    $('#onbreakbtn').show();
                    $('#offbreakbtn').hide();
                    postJson('/Users/enter_break', {'msg': data.msg, 'reason': data.reason}, function(data) {
                        break_id = data.break_id;
                        localStorage.setItem('break_id', break_id); 
                    });             
                }
                else {
                    if (break_id == '') {           
                        break_id = "on-call";
                        localStorage.setItem('break_id', 'on-call'); 
                    }
                    logCallEvent(callId, '[DEBUG] agent paused ' + data.reason, EVENT_DEBUG);                   
                }
            });
            
            socket.on('off_break', function(data) {
                if (break_id != 'on-call') {
                    stopBreakTimer();
                    
                    // only execute if operator is truly on break
                    localStorage.setItem('oa_onbreak', false);                
                    $('#availbtn').prop('disabled', false);
                    $('#onbreakbtn').hide();
                    $('#offbreakbtn').show();
                    if (break_id != '') postJson('/Users/leave_break', {'msg': data.msg, 'break_id': break_id}, null);          
                }
                else {
                    logCallEvent(callId, '[DEBUG] agent resumed', EVENT_DEBUG);                 

                }
                localStorage.setItem('break_id', ''); 
                break_id = '';          
            });
                                    
            socket.on('ready_to_take_calls', function() {
                consoleLog('got ready to take calls');
                checked_in = true;
                
                $('#operator_btns').show();//
                $('#unavailbtn').hide();//
                $('#availbtn').show();
                $('#offbreakbtn').show();
                $('#onbreakbtn').hide();
                var onBreak = localStorage.getItem('oa_onbreak');
                var onBreakReason = localStorage.getItem('oa_onbreak_reason');
                
                // only take break if it's not an on-call or call wrapup break
                if (onBreak == 'true') {
                    takeBreak(onBreakReason);
                }
                // take operator off break just in case we're on break from a call.  Takes care of instances when browser gets refreshed
                //  while operator screen is still open, therefore operator never taken off on-call/ wrapup break
                else if ( !$('#operatorScreen').dialog( "isOpen" ) && pause_agent) {
                    leaveBreak();
                }
                /*if (myRole == 'A' || myRole == 'S') { 
                    bodyLayout.open('east');
                    $('#minders-content').html('');
                }*/
            });

            socket.on('ext_already_logged_in', function() {
                $('#unavailbtn').show();//
                $('#unavailbtn').prop('disabled', false);                   
                $('#availbtn').hide();//
                $('#offbreakbtn').hide();
                $('#onbreakbtn').hide();
                
                localStorage.setItem('oa_taking_calls', false); 
                localStorage.setItem('oa_onbreak', false);                  
                localStorage.setItem('oa_onbreak_reason', '');                  
                                
                user_confirm('Extension ' + myExtension + ' is already logged in.  Would you like to force a logout?', function() {
                    takingCalls(true, true)
                    localStorage.setItem('oa_taking_calls', true);      
                                
                });
                /*if (myRole == 'A' || myRole == 'S') { 
                    bodyLayout.open('east');
                    $('#minders-content').html('');
                }*/
            });
            
            socket.on('not_ready_to_take_calls', function() {
                consoleLog('got not ready to take calls');
                checked_in = true;
                $('#unavailbtn').show();//
                $('#unavailbtn').prop('disabled', false);
                
                $('#availbtn').hide();//
                $('#offbreakbtn').hide();
                $('#onbreakbtn').hide();
                checked_in = true;
                /*bodyLayout.close('east');
                $('#minders-content').html('');*/
            });


            
            socket.on('oa_login', function (data) {
                alert('getting oa_login');
                    //window.location = '/users/logout';
            });
            
            socket.on('operator_logout', function (data) {
                alert('You have been logged in elsewhere, only one active login is allowed.');
                $('#unavailbtn').show();//
                $('#unavailbtn').prop('disabled', false);
                localStorage.setItem('oa_taking_calls', false);                 
                localStorage.setItem('oa_onbreak', false);                  
                $('#availbtn').hide();//
                $('#offbreakbtn').hide();
                $('#onbreakbtn').hide();
            });
            
            
            socket.on('error', function (reason) 
            {
                updateServerStatus('Unable to connect to server:'+ ocServer, 'alert');
                $('#unavailbtn').show();        //
                $('#availbtn').hide();          
                $('#unavailbtn').prop("disabled", true);      
                    
//                  reconnectTimer = setTimeout(connectToServer, reconnectInterval);                    
            });
            


            socket.on('disconnect', function (reason) {
                consoleLog('Event: disconnect');
                websocketConnected = false;
                
                if (typeof(reason) === 'undefined') reason = '';
                updateServerStatus('Disconnected from server:'+ ocServer + reason, 'info');
                $('#unavailbtn').show();
                $('#availbtn').hide();          
                $('#unavailbtn').prop("disabled", true);
//                  reconnectTimer = setTimeout(connectToServer, reconnectInterval);                    
            });
                
            socket.on('connect_failed', function (reason) {
                consoleLog('Event: connect_failed');
                websocketConnected = false;
                
                if (typeof(reason) === 'undefined') reason = '';
                updateServerStatus('Cannot connect to server'+ ocServer + reason, 'alert');
                $('#unavailbtn').prop("disabled", true); //
//                  reconnectTimer = setTimeout(connectToServer, reconnectInterval);                    
            });    
            
            socket.on('anything', function (reason) {
                consoleLog('Event: anything');
                if (typeof(reason) === 'undefined') reason = '';
                updateServerStatus('Anything' + reason, 'alert');
                $('#unavailbtn').prop("disabled", true); //
//                  reconnectTimer = setTimeout(connectToServer, reconnectInterval);                    
            });       
            
            socket.on('reconnect_failed', function (reason) {
                websocketConnected = false;
                
                consoleLog('Event: reconnect_failed');
                if (typeof(reason) === 'undefined') reason = '';
                updateServerStatus('Reconnect failed to server'+ ocServer + reason, 'alert');
                $('#unavailbtn').prop("disabled", true); //
//                  reconnectTimer = setTimeout(connectToServer, reconnectInterval);                    
            });        
                        
            socket.on('reconnect', function (reason) {
                consoleLog('Event: reconnect');
                websocketConnected = true;
                
                if (typeof(reason) === 'undefined') reason = '';
                updateServerStatus('Reconnected to server:'+ ocServer + reason, 'info');
                $('#unavailbtn').prop("disabled", false); //
            });        
            

            socket.on('broadcast_msg', function(data) {
                $('#broadcast_msg_content').html(data.msg);
                $('#broadcast_msg').show();
            });
            
            socket.on('call_update', function(data) {
                if (data) {
                    callStatus = data;
                    if (data.inboundStatus == '') data.inboundStatus = 'Off';
                    if (data.outboundStatus == '') data.outboundStatus = 'Off';
                    if (data.agentStatus == '') data.agentStatus = 'Off';
                    
                    if (callId && (data.inboundStatus != '' || data.agentStatus != '' || data.outboundStatus != '')) logCallEvent(callId, '[STATUS] Inbound: ' + data.inboundStatus + ', Agent: ' + data.agentStatus + ', Outbound: ' + data.outboundStatus, EVENT_DEBUG);                          
                    
                    if (agentStatus != data.agentStatus) {
                        // only flag the end of call if the agent 'off' status is received when agent was previously in 'talk' status
                        if (data.agentStatus == 'Off' && agentStatus == 'Talk') {
                            // agent is no longer part of call, set end time of call
                            if (callId) {
                                logCallEvent(callId, '[CALL END] Operator Offline');                
        
                                // reset the break timer and set break reason to 'wrap-up' so we have a way to keep track of call-wrapup time. 
                                // OQ flashes the wrap-up break if it exceeds a max length
                                // also check if break button has been clicked before getting this update and yet haven't received OC confirmation
                                if (pause_agent && break_id == 'on-call' && !break_button_pressed) takeBreak('wrapup');  
                                                
                                endCall(callId);
                            }
                            endOutboundCall();
                        }
                        agentStatus = data.agentStatus;
                    }
                    if ($('#callBox').dialog('isOpen')) {
                        
                        checkButtons(data);
                        updateStatus(data);
                    }
                    else if ($('#outboundBox').dialog('isOpen')) {
                        $('#outboundBox').trigger('update', data);
                    }
                    
                    else {
                        checkButtons(data);
                        updateStatus(data);
                    }
                    consoleLog('SENDING call_update_confirm');
                    // confirm that we've received update so that OC doesn't resend
                    socket.emit('call_update_confirm', {ts: data.ts});              
                }
            });

            socket.on('call_hangup', function(data) {
            });
            socket.on('call_incoming', liveScreenPop);    
                        
        }
        

    }           

    function endCall(call_id) {
        $.ajax({
            url: '/CallLogs/end_call/'+call_id,
            type: 'GET',
            dataType: 'json'
        }).done(function(data) {


        }).fail(ajaxCallFailure);
                
    }
            
    // opens up operator screen for a specified DID, if test_time is specified, the callsceen for 
    // that particular time is shown, useful for time-sensitive accounts
    function manualScreenPop(did_id, test_time) {         
        incomingUniqueId = '';
        $.ajax({
            url: '/DidNumbers/view/'+did_id,
            type: 'GET',
            dataType: 'json'
        }).done(function(jsondata) {
            manualPop = true;        
            $('#operatorScreen').dialog( "close" );
            var dataObj = new Object();
            var number;
            if (jsondata.DidNumbersEntry.length > 0) number = jsondata.DidNumbersEntry[0]['number'];
            else number = '';
            dataObj = {'oa_did': number, 'did': jsondata.DidNumber, 'test_time' : test_time, 'event': {'test_time': test_time}, test_mode: true};
            screenPop(dataObj, 0, function() {
                $('#operatorScreen #save_msg, #cancel_msg').hide();
                $('#operatorScreen #edit_msg').hide();
                $('#operatorScreen #msg_dispatch').show();                  
            });
            $('#test_time').val(test_time);
        }).fail(ajaxCallFailure);
    }
    
    // recreate operator screen for a specified DID
    function recreateScreenPop(phonenumber, did_id, unique_id) {          
        if (pause_agent && break_id == '') takeBreak('on-call'); // mark operator as unvailable to take calls 
        if ($('#operatorScreen').dialog( "isOpen" )) {
            alert('The operator screen is already open');
            return false;
        }
        
        $.ajax({
            url: '/DidNumbers/view/'+did_id,
            type: 'GET',
            dataType: 'json'
        }).done(function(jsondata) {
            manualPop = true;        
            $('#operatorScreen').dialog( "close" );
            var dataObj = new Object();
            var number;

            dataObj = {'oa_did': phonenumber, 'did': jsondata.DidNumber, 'test_time' : '', 'event': {'uniqueid': unique_id}, test_mode: false};
            screenPop(dataObj, 0, function() {
                $('#operatorScreen #save_msg, #cancel_msg').hide();
                $('#operatorScreen #edit_msg').hide();
                $('#operatorScreen #msg_dispatch').show();                      
            });
        }).fail(ajaxCallFailure);         
    }       
    
    // recreate operator screen processing a minder
    function minderScreenPop( did_id, call_id, schedule_id, callback) {       
        phonenumber = 'lookup';
        if (pause_agent && break_id == '') takeBreak('on-call'); // mark operator as unvailable to take calls 
        if ($('#operatorScreen').dialog( "isOpen" )) {
            alert('The operator screen is already open');
            return false;
        }
        
        $.ajax({
            url: '/DidNumbers/view/'+did_id,
            type: 'GET',
            dataType: 'json'
        }).done(function(jsondata) {
            manualPop = true;        
            $('#operatorScreen').dialog( "close" );
            var dataObj = new Object();
            var number;

            dataObj = {'oa_did': phonenumber, 'did': jsondata.DidNumber, 'test_time' : '', 'event': {'call_id': call_id, 'schedule_id': schedule_id}, test_mode: false};
            screenPop(dataObj, true, callback);
        }).fail(ajaxCallFailure);         
    }           
    
    
    // screen pop for outbound campaign
    function campaignScreenPop(phonenumber, did_id, calltype, contact_id) {       
//        if (pause_agent) takeBreak('on-call'); // mark operator as unvailable to take calls 
        if ($('#operatorScreen').dialog( "isOpen" )) {
            alert('The operator screen is already open');
            return false;
        }
        
        $.ajax({
            url: '/DidNumbers/view/'+did_id,
            type: 'GET',
            dataType: 'json'
        }).done(function(jsondata) {  
            $('#operatorScreen').dialog( "close" );
            var dataObj = new Object();
            var number;

            dataObj = {'oa_did': phonenumber, 'did': jsondata.DidNumber, 'test_time' : '', 'event': {'uniqueid': 'CAMPAIGN', 'calltype': calltype, 'contact_id': contact_id}, test_mode: false};
            screenPop(dataObj);
        }).fail(ajaxCallFailure);         
    }       
    
    function liveScreenPop(data) {
        agentStatus = 'Off';
        if (pause_agent && (break_id == '' || break_id == 'on-call')) takeBreak('on-call'); // mark operator as unvailable to take calls 
        
        incomingUniqueId = data.event.uniqueid;
        // only replace the operator screen if it had been opened manually instead of through a screen pop triggered by a call
        if (!$('#operatorScreen').dialog( "isOpen" ) || ($('#operatorScreen').dialog( "isOpen" ) && manualPop)) {
            screenPop(data);
        }
        else {
            // check if operator screen is already open
            if ($('#operatorScreen').dialog( "isOpen" )) {
                // if operator screen is already open then save newest screen pop data
                lastPopAttempt = data;
                user_confirm('Another call is coming in, would you like to close the current window?', function() {
                    
                    // save callStatus since closing operator screen will null it out
                    var callStatusSave = callStatus;
                    logCallEvent(callId, '[CANCEL] Incoming call');                 
                    closeOperatorScreen();
                    
                    // reinstate callStatus of new call
                    callStatus = callStatusSave;
                });
            }
        }
    }
    
    function didSpecified() {
        if ($('#find_did').val() == '') {
//          alert('Please specify a DID number to retrieve');
            $('#find_did').focus();
            return false;
        }
        else {
            return true;
        }
    }
    
    function accountSpecified() {
        if ($('#find_account').val() == '') {
            //alert('Please specify an account number to retrieve');
            $('#find_did').focus();
            return false;
        }
        else {
            return true
        }
    }       

    function refreshOperatorScreen(instrdata) {


    }       
    
    function showOperatorNotes(data) {
        var notes = '';
        if (data.hasOwnProperty('left')) {
            $('#op_notes_left').html(data['left']);
            $('#op_notes_center').html(data['center']);
            $('#op_notes_right').html(data['right']);
        }
        else {
            $.each(data, function(key, value) {
                notes += '<div>'+value['description'] +'</div>';
            })        
            $('#op_notes').html(notes);

        }         
    }
    
    function renderOperatorScreen(instrdata, callback) {
        $( "#opscreen_main" ).tabs({
            active: 0
        });       
        currentInstructions = instrdata;        
        callId = instrdata.call_id;
        msgId = instrdata.msg_id;
         
        var files = '';
        showOperatorNotes(instrdata['notes']);
            
        setInterval(function(){  $("#op_notes").toggleClass("op_notes_blink");  },1000)
        $.each(instrdata['files'], function(key, value) {
            files += '<div class="file_div"><a href="/Files/view/'+value['id']+'/'+value['file_name']+'" target="_blank"><img src="/img/icons/'+value['file_extension']+'.png" width="32" height="32"><br>'+value['file_name']+'</a></div>';
        })
        if (files != '') {
            files = files + '<div style="clear:both">&nbsp;</div>'; 
        }
        $('#acct_files').html(files);
        
        var schedules = instrdata['schedules'];
//      var ct_actions = instrdata['ct_actions'];
        //callbox_employees = instrdata['employees'];
        
        var html = '<ul id="actions">';
        var cid;
        var savedPrompts; // use to store value of user prompts already entered
        var current_calltype = 0;
        // create list of calltypes available for this account
        for (var i=0; i<instrdata['calltypes'].length; i++) {
            cid = instrdata['calltypes'][i]['id'];
            if (schedules[cid]) {
                if (instrdata['calltypes'][i]['title'] == instrdata['current_calltype']) current_calltype = i;
                html += '<li class="ct" id="action'+i+'" sid="'+schedules[cid]['id']+'" cidx="'+i+'" cid="'+cid+'" ctitle="'+instrdata['calltypes'][i]['title']+'">' + instrdata['calltypes'][i]['title'] + ' - ' + instrdata['calltypes'][i]['type'];
                if (instrdata['calltypes'][i]['desc'] != null && instrdata['calltypes'][i]['desc'] != '') {
                    html += '<div>'+instrdata['calltypes'][i]['desc']+'</div>';
                }
                html += '</li>';
            }
        }
        html += '</ul>';
        $('#calltypes').html(html);
        
        if (instrdata['schedule_id'] != '') {
        
             displayCallType($('#operatorScreen .ct[sid='+instrdata['schedule_id']+']').attr('cidx'), '#instructions');
        }
        else {
            displayCallType(current_calltype, '#instructions');     
        }

        var saved_prompts = [];
        var saved_labels = [];
        var label;
        var idx;

        // prefill prompts if this is re-pop of an existing call    
                    var entered_prompts = instrdata['entered_prompts'];
                    if (!($.isEmptyObject(entered_prompts))) {
                            $('#instructions input.uprompt, #instructions textarea.uprompt, #instructions select.uprompt, #operatorScreen textarea.miscnotes').each(function(index, value) {
                                    label = $(this).siblings('label').html().replace('* ', '').trim();
                                    if (entered_prompts.hasOwnProperty(label)) {
                                        $(this).val(entered_prompts[label]);
                                        if (label == 'Misc') {
                                            $('#operatorScreen #misc_div').show();
                                        }
                                    }
                                    if ($(this).hasClass('conditional')) {
                                        check_conditional(this);
                                    }
                            });
                            
                            for (var key in entered_prompts) {
                                    saved_labels.push(key);
                                    saved_prompts.push(entered_prompts[key].trim());
                            }
                    }           
        
        $('#calltypes li').on('mouseover', function() {$(this).addClass('ct_over');});        
        $('#calltypes li').on('mouseout', function() {$(this).removeClass('ct_over')});        
        // attach click event listeners to calltype buttons
        $('#calltypes li').on('click', function() {
            if ($(this).hasClass('disable_edits')) {
                alert('You cannot change the calltype unless you are in edit mode');
                return false;
            }
            // save existing prompts and labels
            $('#instructions .uprompt').each(function(index, value) {
                label = $(this).siblings('label').html().replace('* ', '').trim();
                if ($(this).val() != '') {
                    // see if a prompt by this name already exists
                    idx = $.inArray(label, saved_labels);
                    if (idx > -1) {
                        if ($(this).val()) {
                            saved_prompts[idx] = $(this).val().trim();
                            saved_labels[idx] = label;
                        }
                    }
                    else {
                        idx = saved_prompts.length;
                        if ($(this).val()) {
                            saved_prompts[idx] = $(this).val().trim();
                            saved_labels[idx] = label;
                        }
                    }
                }
                
            });
            var misc_saved_prompt = $('#operatorScreen #miscnotes').val();

            // display newly selected calltype and pre-fill prompts that have already been filled in
            displayCallType($(this).attr('cidx'), '#instructions')
            var count = 0;
            var used_index = new Array();
            $('#instructions select.uprompt, #instructions input.uprompt, #instructions textarea.uprompt, #instructions select.uprompt').each(function() {
                label = $(this).siblings('label').html().replace('* ', '');
                idx = $.inArray(label, saved_labels);
                
                if (idx > -1) {
                    $(this).val(saved_prompts[idx]);
                    used_index.push(idx);
                    // check conditional statements
                    if ($(this).hasClass('conditional')) {
                        check_conditional(this);
                    }
                }
                
            });
            var newhtml = '<div class="step unused"><div class="action">Extra Prompts (<i>not required by this call type</i>)</div><div class="prompts">';
            var extra_prompts = false;
            for (var j=0; j < saved_labels.length; j++) {
                if ($.inArray(j, used_index) < 0) {
                    if (saved_prompts[j] != '') {
                        extra_prompts = true;
                        newhtml += '<div class="prompt">';
                        newhtml += '<input type="hidden" name="ptype[99][]" value="2">';
                        newhtml += '<input type="hidden" name="poptions[99][]" value="2">';
                        newhtml += '<input type="hidden" name="pmaxchar[99][]" value="255">';
                        newhtml += '<input type="hidden" name="ptitle[99][]" value="'+saved_labels[j]+'"><label>'+saved_labels[j]+'</label>';
                        newhtml += '<textarea cols="40" rows="1" class="uprompt" name="pvalue[99][]" onchange="if (this.value==\'\') $(this).parent(\'.prompt\').remove();">'+saved_prompts[j]+'</textarea> &nbsp;<a href="#" onclick="$(this).parent(\'.prompt\').remove();">x</a>';
                        newhtml += '</div>';
                    }
                }
            }
            newhtml += '</div></div>';
            if (extra_prompts) $('#instructions form:first-child').append(newhtml);
            
            $('#operatorScreen #miscnotes').val(misc_saved_prompt);       
            // show misc notes if not blank

            if ($('#operatorScreen #miscnotes').val() != '') $('#operatorScreen #misc_div').show();     
            else $('#operatorScreen #misc_div').hide(); 
            
            // trigger to resize textareas
            autosize.destroy(document.querySelectorAll('#instructions textarea'));  
            autosize(document.querySelectorAll('#instructions textarea'));  

                
        });                     
        
        // display oncall lists, organized in tabs if there is more than one
        $('#oncall_lists').html(instrdata['oncall_html']);      
        // existing tabs must be destroyed so that they can be rebuilt 
        if (oncallTabs) {
            $('#oncall_lists').tabs("destroy");
            oncallTabs = false;
        }
        
        // show any oncall lists
        $('#oncall_lists').tabs();
        oncallTabs = true;
        $('#operatorScreen .ui-layout-content').scrollTop(0);
        if (typeof(callback) !== 'undefined') {
            callback();
        }
            $('#tab-call-events').load( "/CallLogs/events/"+callId, function(response) {
            });  
        
    }
    function checkEditable(t) {
        if ($(t).hasClass('disable_edits')) $(t).blur();
        return true;
    }
    
    // function for creating the operator screen 
    function screenPop(data, skip_call_log, callback) {       
        if (typeof(skip_call_log) == 'undefined') {
            skip_call_log = 0;
        }
        
        // in test mode, show a test box that can be used to select the time to test time sensitive calltypes
        if (data.test_mode) $('.test_box').show();
        else $('.test_box').hide(); // don't show test box for live calls
        
        if (data.event.test_time != '') $('#test_time_save').val(data.event.test_time);
        if (data.event.hasOwnProperty('connectedlinenum')) $('#op_caller_id').html(phoneFormat(data.event['connectedlinenum']));
        else $('#op_caller_id').html('NONE');
        //set our timezone to be what the client timezone is, so that saved dates/times will look accurate to THEM rather than to us.
        client_tz = data.did.timezone;
        //open up a modal dialog that contains the operator 'screen'
        $('#operatorScreen').dialog( "open" );

        currentCall = data; // save the current call info in a global variable
        var callInstructions = data.instructions;
        var oa_did;
        // check if we need to fetch account instructions. Overflow calls that are handed off already contains instructions
        if (!data.hasOwnProperty('instructions')) {
            if (data.hasOwnProperty('oa_did')) oa_did = data['oa_did'];
            else oa_did = '';
            //if (data.event !== null) $('#inboundStatus').html('Connected to ' + data.event.channel);
            var url = '/DidNumbers/instructions/' + data['did']['id'] + '/' + oa_did + '/' + skip_call_log;
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {'event': data.event, 'queue': data.queue}
            }).done(function(instrdata) {
                instrdata['did_number'] = oa_did;
                if (instrdata['success'] === false) {
                    alert('This account has not been completely provisioned yet, make sure there is at least one calltype schedule defined that will match the current time');
                    $('#operatorScreen').dialog( "close" );
                }
                else  {
                    renderOperatorScreen(instrdata, callback);              
                }
            }).fail(ajaxCallFailure);           
        }
        else {
            var url = '/DidNumbers/store_instructions/';
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {'event': data.event, 'instructions': callInstructions}
            }).done(function(instrdata) {
                if (instrdata['success'] === false) {
                    alert('The instructions cannot be saved');
                    $('#operatorScreen').dialog( "close" );
                }
                else  renderOperatorScreen(callInstructions, callback);                 
            }).fail(ajaxCallFailure);           
        }
            
        // flag that operator has a call in progress so that call timer is updated
        callInProgress = true;
        callStart = new Date().getTime();
        var hours = moment().tz(client_tz).format('H');
        var timeOfDay = '';
        if (hours >= 0 && hours < 12) timeOfDay = 'Morning';
        else if (hours >= 12 && hours < 18) timeOfDay = 'Afternoon';
        else if (hours >= 18 && hours <= 23) timeOfDay = 'Evening';      

        // fill out basic account info for the initial screen pop
        data['did']['answerphrase'] = data['did']['answerphrase'].replace(/\[Company Name\]/i, data['did']['company']);
        data['did']['answerphrase'] = data['did']['answerphrase'].replace(/\[o\]/i, myFirstName);
        
        var icons = '';
        
        if (data['did']['bilingual'] == '1')  {
            icons += '<i class="fa fa-globe fa-lg"></i> &nbsp;&nbsp;';
        }
        if (data['did']['hipaa'] == '1')  {
            icons += '<i class="fa fa-medkit fa-lg"></i> &nbsp;&nbsp;';
        }


        $('#answer_phrase').html(icons + data['did']['answerphrase'].replace(/\[m\]/i, timeOfDay));
        $('#answer_phrase').attr('class', 'bg'+data['did']['did_color']);

        $('[name="account_num"]').val(data['did']['account_num']);
        $('[name="did"]').val(data['did']['did_number']);
        var service_type = ((data['did']['type'] == '1')? '<span><i class="fa fa-smile-o"></i> RECEPTIONIST </span>': '<img src="/themes/vn/icon-headset.png" width="15" height="17" /> &nbsp;<span>ANSWERING SERVICE </span>');
        $("#screen_title").html(service_type + ' ' + data['did']['account_name'] + ' <span>'+ data['did']['account_num']+'</span>' );
        var address = '';
        var info = '';
        var t = {'oldval': '', 'val' : '', 'start': '', 'end': '', 'day': '', 'hours': '', 'first': true};
        // only display information that should be displayed
        if (data['did']['company_visible'] && data['did']['company'] != '') address += '<h1>' + data['did']['company'] + '</h1>';
        var state = '';
        if (data['did']['address_visible']) {
            if (data['did']['state']) state = data['did']['state'];
            else if (data['did']['province']) state = data['did']['province'];
            
            if (data['did']['address1'] != '') address +=  data['did']['address1'];
            if (data['did']['address2'] != '') address += '<br>' + data['did']['address2'];
            if (data['did']['city'] != '') address += '<br>' + data['did']['city'] + ', ' + state + ' ' + data['did']['zip'];
        }

            if (data['did']['main_phone_visible'] && data['did']['main_phone'] != '') info += '<br>' + phoneFormat(data['did']['main_phone']) + ' (main)';
            if (data['did']['alt_phone_visible'] && data['did']['alt_phone'] != '') info += '<br>' + phoneFormat(data['did']['alt_phone']) + ' (alt)';
            if (data['did']['main_fax_visible'] && data['did']['main_fax'] != '') info += '<br>' + phoneFormat(data['did']['main_fax']) + ' (fax)';
            var prefix;
            if (data['did']['website'] != null) {
                if (data['did']['website'].indexOf('http://') < 0) prefix = 'http://';
                else prefix = '';
                if (data['did']['website_visible'] && data['did']['website'] != '') info +=  '<br><a href="' + prefix + data['did']['website'] + '" target="_blank">' + prefix + data['did']['website'] + '</a>';          
            }
            if (data['did']['email_visible'] && data['did']['email'] != '') info +=  '<br><a href="mailto:' + prefix + data['did']['website'] + '">' +  data['did']['email'] + '</a>';
        
            if (data['did']['industry'] != '' && data['did']['industry'] != null) info += "<br><b><i>" + data['did']['industry'] + '</i></b>';

        
        $('#acct_type').html(service_type);
        $('#acct_addr').html(address);
        $('#acct_info').html(info);
        if (data['did']['hours_visible'] && data['did']['hours'] != '') $('#acct_hours').html(data['did']['hours'].replace("\r\n", "<br>"));
        
        
        // confirm to OC that we've screen-popped
        if (!data.test_mode && socket != null) socket.emit('screen-popped', {'ext': myExtension});    
        //if (!data.test_mode) socket.emit('screen-popped', {'ext': myExtension});    
            var diff_sec;
            var test_time_save = $('#test_time_save').val();
            if (test_time_save) {
                diff_sec = moment().format('X') - moment(test_time_save).format('X');
            }
        
        clearInterval(clockInterval);
        clockInterval = setInterval( function() {
            if (callInProgress) {
                if (test_time_save) {
                    $('#local_time').val(moment().subtract(diff_sec, 'seconds').format('dd MM/D h:mm:ss a z'));
                    tday = moment().subtract( diff_sec, 'seconds').format('d'); 
                }
                else {
                    $('#local_time').val(moment().tz(client_tz).format('dd MM/D h:mm:ss a z'));
                    tday = moment().tz(client_tz).format('d'); 
                }

                // update current time and call duration timer
                var nowTs = new Date().getTime();
                var callTime = (nowTs-callStart)/1000;
                $('#call_time').val(Math.round(callTime));
                             
            }
        // Add a leading zero to seconds value
        },1000); 
                    
    }

    // function to update the status bar at the bottom of the screen
    function updateStatus(data) {
        if (typeof(data) !== 'undefined' && (data !== null)) {
            $('#inboundStatus').html(data.inboundStatus);
            $('#outboundStatus').html(data.outboundStatus);
            $('#agentStatus').html(data.agentStatus);

        }
        if (data.inboundStatus == 'Off' || data.inboundStatus == '') $('#in_status').html('No Call');
        else if (data.inboundStatus == 'Hold') $('#in_status').html('Holding');
        else if (data.inboundStatus == 'Talk') $('#in_status').html('Talking');
            
            
        if (data.outboundStatus == 'Hold') $('#out_status').html('Holding');
        else if (data.outboundStatus == 'Talk') $('#out_status').html('Talking');
        else if ((data.outboundStatus == '' || data.outboundStatus == 'Off') && data.agentStatus == 'Talk') $('#out_status').html('Calling');
        else if (data.outboundStatus == '' || data.outboundStatus == 'Off') $('#out_status').html('No Call');
        
    }

    // update the number of certain types of breaks displayed in header of page
    function updateBreaks(data) {
        var count = 0;
        if (!data.hasOwnProperty('breakReasons') || break_count_reasons.length == 0) return;
        exts = '';
        for (var ext in data['breakReasons']) {
            if ($.inArray(data['breakReasons'][ext], break_count_reasons) > -1) {
                count = count +1;
                exts = exts + ' ' + ext;
            }
        }
        $('#num_on_break').html('<i class="fa-coffee fa" title="Exts: '+exts+'"></i> ' + count);
    }
    
    // enable/ disables various buttons depending on the status of the call returned from OC    
    function checkButtons(data) {
        $('#btn_cancel').prop("disabled", false);           
        if (typeof(data) !== 'undefined' && (data !== null)) {
            $('#inboundStatus').html(data.inboundStatus);
            $('#outboundStatus').html(data.outboundStatus);
            $('#agentStatus').html(data.agentStatus);

            if (data.inboundStatus == 'Hold' ) {
                $('#in_hold').button("disable");
                $('#in_talk').button("enable");
                $('#in_hangup').button("disable");

                if (data.agentStatus == 'Hold' ) $('#out_dial').button("enable");
            }       
            else if (data.inboundStatus == 'Talk') {
                $('#in_hold').button("enable");
                $('#in_talk').button("disable");
                $('#out_dial').button("disable");
                $('#in_hangup').button("enable");
            }       
            else if (data.inboundStatus == 'Off' && data.agentStatus == 'Hold' && (data.outboundStatus == 'Off' || data.outboundStatus == '')) {
                socket.emit('call_hangup_agent');             
            }               
            else if (data.inboundStatus == 'Off' || data.inboundStatus == '') {
                $('#in_talk').button("disable");
                $('#in_hold').button("disable");
                $('#in_hangup').button("disable");
            }
                    
            if (data.outboundStatus == 'Hold') {
                $('#out_hold').button("disable");
                $('#out_talk').button("enable");
                $('#out_hangup').button("disable");
            }               
            else if (data.agentStatus == 'Talk out') {
                $('#in_talk').button("disable");
                $('#out_hold').button("enable");
                $('#out_talk').button("disable");
                $('#btn_patch').button("enable");
                $('#out_hangup').button("enable");
            }               
            else if (data.agentStatus == 'Dial out') {
                $('#out_hold').button("enable");
                $('#in_talk').button("disable");
                $('#out_talk').button("disable");
                $('#btn_patch').button("enable");
                $('#out_hangup').button("enable");
            }
            else if (data.outboundStatus == '' || data.outboundStatus == 'Off') {
                $('#out_talk').button("disable");
                $('#out_hold').button("disable");
                $('#btn_patch').button("disable");
                $('#out_hangup').button("disable");
                if (data.inboundStatus != 'Talk') $('#out_dial').button("enable");
                if (data.agentStatus == '' || data.agentStatus == 'Off') $('#out_hangup').button("disable");
            }

            if ((data.inboundStatus == '' || data.inboundStatus == 'Off') && (data.outboundStatus == '' || data.outboundStatus == 'Off') && currentCall !== null && currentCall.hasOwnProperty('channel')) {
                $('#callBoxCtrl').hide();
                $('#callBoxResult').show();
            }
        }
        else {
                $('#in_talk').button("disable");
                $('#in_hold').button("disable");
                $('#out_talk').button("disable");
                $('#out_hold').button("disable");
                $('#btn_patch').button("disable");
                $('#in_hangup').button("disable");
                $('#out_hangup').button("disable");
                $('#out_dial').button("enable");
                $('#in_status').html('No Call');
                $('#out_status').html('No Call');
        }
    }
    
    function getHours(t, last) {
        if (t.first) t.oldval = t.val;
        t.first = false;
        if (t.oldval != t.val || last) {
            if (last) t.end = t.day;
            if (t.start === t.end) {
                t.hours += t.start + ' ' + t.val + '<br>';;
            }
            else {
                t.hours += t.start + '-' + t.end + " " + t.oldval + '<br>';
            }
            t.oldval = t.val;
            t.start = t.day;
        }
        else {
            t.end = t.day;
        }
    }
    function logTakingCalls(taking_calls) {
        if (taking_calls) {
            $.ajax('/Users/taking_calls');
        }
        else {
            $.ajax('/Users/not_taking_calls');
        }
    }
    
    function logButtonClick(call_id, buttonObj) {
        if (call_id < 1) return;
        var attrs = buttonObj.attributes;
        var attrsObj = {};
     
        if ($(buttonObj).attr('action_type')) attrsObj['action_type'] = $(buttonObj).attr('action_type');
        attrsObj['btype'] = $(buttonObj).attr('btype');
        attrsObj['emp_name'] = $(buttonObj).attr('emp_name');
        attrsObj['contact_id'] = $(buttonObj).attr('contact_id');
        attrsObj['employee_id'] = $(buttonObj).attr('employee_id');
        attrsObj['did'] = $(buttonObj).attr('did');
        attrsObj['ext'] = $(buttonObj).attr('ext');
        attrsObj['action_id'] = $(buttonObj).attr('action_id');
        attrsObj['bdata'] = $(buttonObj).attr('bdata');
        attrsObj['bfulldata'] = $(buttonObj).attr('bfulldata');
        attrsObj['blabel'] = $(buttonObj).attr('blabel');
        attrsObj['bloc'] = $(buttonObj).attr('bloc');
        if ($(buttonObj).hasClass('c_btn'))
            attrsObj['button_type'] = 'Employee';
        else
            attrsObj['button_type'] = 'Action';
        
        var url = '/CallEvents/buttonClick/' + call_id;
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {'callId': call_id, 'attrs': attrsObj, 'event': 'Button Click'}
            }).done(function(jsondata) {
                if (!jsondata.success) alert(jsondata.msg);
            }).fail(ajaxCallFailure);   
        return attrsObj;   
    }       
    
    // logs a call event to the OA DB
    function logCallEvent(call_id, event_txt, event_type) {
        if (call_id < 1) return;
        var url;
        if (typeof(event_type) !== 'undefined') 
            url = '/CallEvents/add/' + call_id + '/' + event_type + '.json';
        else 
            url = '/CallEvents/add/' + call_id + '.json';
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {'callId': callId, 'event_txt': event_txt}
            }).done(function(jsondata) {
                if (!jsondata.success) alert(jsondata.msg);
                else $('.calllog_eventlist').append(jsondata.row);
            }).fail(ajaxCallFailure);
        
    }
    
            
    // set maximum count on the message prompt
    function setCount(src) {
        var chars = src.value.length;
        var limit;
        if ($(src).hasClass('uprompt')) limit = $(src).attr('maxlength');
        if (limit) {
            if (chars > limit) {
                    src.value = src.value.substr(0, limit);
                    chars = limit;
            }
            $(src).siblings('.uprompt_max').html( (chars) + '/' + limit );
        }
    }
    
    // adds visual cues for employee gender
    function formatOption(opt) {
        var originalOption= opt.element;
        var gender;
        if ($(originalOption).data('gender') == '1') gender = 'female';
        else if ($(originalOption).data('gender')) gender = 'male';
        else gender='';
        
        return '<span class="'+gender+'">&nbsp;&nbsp;</span>&nbsp; ' + opt.text;
    }
    


    function disconnectFromServer() {
    }
         

    
    function loadClient(url) {
        $('#did-content').load(url);
        $('.didbtns').show();
    }

    function loadPage(t, url, target, callback) {
        if (typeof(callback) == 'undefined') callback = null;
        //var myform = $(t).parents('form');        
            $.ajax({
                    url: url,
                    type: 'get',
                    dataType: 'html' 
            }).done(function(data) {    
                if (callback !== null) callback();
                $('#' + target).html(data);
            }).fail(ajaxCallFailure);   
    }    
    
    function loadPagePost(form, url, target, data, callback) {
        var post_data
        if (form) post_data = $(form).serialize() + '&' + data;
        else post_data = data;
        //var myform = $(t).parents('form');        
            $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'html' ,
                    data: post_data
            }).done(function(html) {    
                $('#' + target).html(html);
                if (callback) callback();
            }).fail(ajaxCallFailure);   
    }    

        function ajaxCallFailure(j, textStatus) {
            if (j.hasOwnProperty('status')) {
                var msg;
                if (j.hasOwnProperty('responseText')) {
                msg = 'Cannot communicate to the OpenAnswer Server, contact Technical Support - ' + textStatus + ' - ' + j.status + ' ' + j.responseText;
                }
                else 
                msg = 'Cannot communicate to the OpenAnswer Server, contact Technical Support - ' + textStatus + ' - ' + j.status;
            }
            else msg = 'Cannot communicate to the OpenAnswer Server, contact Technical Support - ' + textStatus;
            alert(msg);
        }

    function getJson(url, data, callback) {
        //var myform = $(t).parents('form');        
        var formdata;
        if (typeof(data) == 'undefined' || data == null) formdata = '';
        else formdata = data;
            
        $.ajax({
                url: url,
                type: 'get',
                dataType: 'json' ,
                data: formdata
        }).done(function(json) {    
             if (json.success) createToast('info', json.msg);               
             else alert(json.msg);
            if (callback !== null) callback(json);
        }).fail(ajaxCallFailure);       
    }
    
    function postJson(url, data, callback) {
        //var myform = $(t).parents('form');        
        var formdata;
        if (typeof(data) == 'undefined' || data == null) formdata = '';
        else formdata = data;
            
        $.ajax({
                url: url,
                type: 'post',
                dataType: 'json' ,
                data: formdata
        }).done(function(json) {    
            if (json.success) {
                if (callback !== null) callback(json);
                if (data != null && data.hasOwnProperty('msg'))
                    createToast('info', data.msg);                  
                else
                    createToast('info', json.msg);                  
            }
            else {
                alert(json.msg);                
            }
        }).fail(ajaxCallFailure);       
    }
    


    function checkCategory(t) {
    
        if (t=='21' || t == '22' || t == '23' || t == '24') {
            $('#waiverdiv').show();
            getWaiver(document.getElementById('incident_operator').value);   
        }
        else {
            $('#waiverdiv').hide();
            document.getElementById('penalty').value='0';
            document.getElementById('waiversel').value='0';
            
        }
    }
    
    function getWaiver(t) {
        if (t) {
        $('#waiver-div').load('/Voicenation/OperatorIncidents/waivers/' + t + '/' + document.getElementById('incident_date').value.replace('/', '_'));
        }
        else $('#waiver-div').html('');
    }
    function loadCalltypes(acct, target, detail_target) {
        var url = "/Calltypes/view/" + acct;
        var myform = $(this).parents('form');       
        
        $.ajax({
                url: url,
                type: 'post',
                /*,dataType: 'json',*/
                data: 'target=' + target + "&detail=" + detail_target 
        }).done(function(data) {    
            $('#' + target).html(data);
        }).fail(ajaxCallFailure);
    }
    
     
    function openNoteDialog(account_id, did_id, action, id, save_callback) {
        var url;
        dialog_callback = save_callback;
        if (action == 'message') {
            url = '/notes/add/' + account_id + '/' + did_id + '/' + id;
        }
        else if (action == 'edit') {
            url = '/notes/'+action+'/' + id;
        }
        else {
            url = '/notes/'+action+'/' + account_id + '/' + did_id;
        }
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'html'
            }).done(function(html) {
                $('#noteDialog').html(html);
                $('#noteDialog').dialog('open');                    
            });
        
    }       
    
    function openSecurityDialog(account_id) {
        var url;
        url = '/accounts/security/' + account_id;
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html'
        }).done(function(html) {
            $('#securityDialog').html(html);
            $('#securityDialog').dialog('open');
        });
    }
    
    function isAuthorized(shortname) {
        if (permissions.indexOf(shortname)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    
    
    function loadCallEvents(call_id) {
        $('#tab-events').load( "/CallLogs/events/"+call_id, function(response) {
            var html = $('#tab-events h3:first').html();
            $('#msg_center li[aria-controls=tab-events] a').text(html);    
            msgWinLayout.resizeAll();
        } );  
    }           

    function loadMsgMistakes(msg_id) {
        $('#tab-mistakes').load( "/Mistakes/msg_mistakes/"+msg_id, function(response) {
            var html = $('#tab-mistakes h3:first').html();
            $('#msg_center li[aria-controls=tab-mistakes] a').text(html);    
        } );  
    }           

    function loadMsgNotes(msg_id) {
        $('#tab-notes').load( "/Notes/msg_notes/"+msg_id, function(response) {
            var html = $('#tab-notes h3:first').html();
            $('#msg_center li[aria-controls=tab-notes] a').text(html);    
        } );  
    }           

    function loadMsgComplaints(msg_id) {
        $('#tab-complaints').load( "/Complaints/msg_complaints/"+msg_id, function(response) {
            var html = $('#tab-complaints h3:first').html();
            $('#msg_center li[aria-controls=tab-complaints] a').text(html);    
        } );  
    }           

    
    function loadMsgDeliveries(msg_id) {
        $('#tab-deliveries').load( "/Messages/msg_deliveries/" + msg_id, function(response) {
            var html = $('#tab-deliveries h3:first').html();
            $('#msg_center li[aria-controls=tab-deliveries] a').text(html);    
        });  
    }
    
    function loadMsgSummaries(did_id) {
        $('#tab-summaries').load( "/MessagesSummary/msg_summaries/" + did_id, function(response) {
        });  
    }

    function editText(t) {
        var html = $(t).html().trim();
//          $(".helpereditor").jqte({br: false});
        if (html ==  emptyText) html = '';
        $('#helpereditor').jqteVal(html);      
$("#helperform .jqte_editor").keydown(function(e){
    // Enter was pressed without shift key
    if (e.keyCode == 13 && !e.shiftKey)
    {
            // prevent default behavior
            e.preventDefault();
    }
});       
        $('#helperedit-dialog').dialog({
            resizable: true,
            autoOpen: true,
            height:350,
            width:540,
            modal: true,
            buttons: {
                "Done": function() {
                    var newhtml = $('#helperform .jqte_editor').html().trim().replace('<br>', '');
                    $(t).html(newhtml);
        
                    $( this ).dialog('close');
                },
                "Cancel": function() {
                    $( this ).dialog('close');
                }
            }, 
            open: function() {
//          $('#helperform').show();
                $('#helperform .jqte_editor').focus();
            },
            close: function() {

//          $('#helperform').hide();
                $( this ).dialog('destroy');
            }
        });      
    }
    function editHelper(t) {
        $('#helpereditor').jqteVal($(t).html());
        $("#helperform .jqte_editor").off('keydown');
        $('#helperedit-dialog').dialog({
            resizable: true,
            autoOpen: true,
            height:350,
            width:540,
            modal: true,
            buttons: {
                "Save": function() {
                    var newhtml = $('#helperform .jqte_editor').html();
                    $(t).html(newhtml);
//          $(t).prev().val(newhtml.replace(/\"/g, '&quot;'));          
                    $(t).prev().val(newhtml);             
                    $( this ).dialog('close');
                },
                "Cancel": function() {
                    $( this ).dialog('close');
                }
            }, 
            open: function() {
//          $('#helperform').show();
                $('#helperform .jqte_editor').focus();

            },
            close: function() {

//          $('#helperform').hide();
                $( this ).dialog('destroy');
            }
        });       
        
    }
    
    function openMistakeDialog(msgId, accountId, action, id, success_callback) {
        var url;
        dialog_callback = success_callback;
        if (action == 'edit') {
            url = '/mistakes/'+action+'/' + id;
        }
        else {
            url = '/mistakes/'+action+'/' + accountId + '/' + msgId;
        }
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'html'
            }).done(function(html) {
                $('#mistakeDialog').html(html);
                $('#mistakeDialog').dialog('open');                 
            });
        
    }
    
    function changeLabel(t) {
        var thelabel = $(t).html().replace(/\"/g, '&quot;');
        var duplicate = false;
        $('#emp_edit span.ceditable').each(function() {
            if ($(this).html() == thelabel && t != this) {
                duplicate = true;
            }  
        });      
        if (duplicate) {
            alert('A contact with the label \''+thelabel+'\' already exists!');
            $(t).html($(t).parent().next().find('.clabel').val());
            $(t).focus();
        }
        else {
            if (thelabel != '') $(t).parent().next().find('.clabel').val(thelabel);
            else {
            }
        }
    }
    
    function addContact(type, table_id) {
        var count = $('#' + table_id).find('tr').length;
        var duplicate = false;
        var loop = true;
        var label = labels[type];
        var cnt = 2;
        var classname = "";
        var fieldsize = '';
        
        while (loop && cnt < 100) {
            loop = false;    
            $('#emp_edit input.clabel').each(function() {
                if ($(this).val() == label) {
                    label = labels[type] + cnt;
                    cnt++;
                    loop = true;
                }  
            });
        }
        if (type == CONTACT_PHONE || type == CONTACT_FAX || type == CONTACT_TEXT) {
            classname="phone"
            fieldsize = '15';
        }
        else {
            fieldsize= '30';
        }
        html =  '<tr onmouseover="showDel(this);" onmouseout="hideDel(this);"><td width="15"><a href="#" class="handle" onclick="return false;" title="Click and drag to reorder">&equiv;</a></td><td><input type="hidden" name="data[Contact][sort]['+count+']" value="'+count+'"><input type="hidden" name="data[Contact][visible]['+count+']" value="0"><input type="checkbox" name="data[Contact][visible]['+count+']" value="1" checked>&nbsp;visible</td><td align="right"><span class="ceditable" contenteditable=true>';
        html += (label + '</span>&nbsp;&nbsp;<img src="'+icons[type]+'" align="absmiddle">');
        html +=     '</td><td><input type="hidden" size="20" class="clabel" name="data[Contact][label]['+count+']" value="'+label+'"><input type="hidden" name="data[Contact][id]['+count+']" value=""><input type="hidden" name="data[Contact][contact_type]['+count+']" value="'+type+'"><input type="text" name="data[Contact][contact]['+count+']" size="'+fieldsize+'" value="" class="mycontact required '+classname+'">';
    
        if (type == CONTACT_PHONE) {
            html +='&nbsp;&nbsp;&nbsp;Ext: <input type="hidden" name="data[Contact][carrier]['+count+']" value="" ><input type="text" name="data[Contact][ext]['+count+']" size="10" value="" >';
        }
        else if (type == CONTACT_TEXT) {
            html += '&nbsp;&nbsp;&nbsp;Carrier: <input type="hidden" name="data[Contact][ext]['+count+']" value="" ><select class="required carrier" name="data[Contact][carrier]['+count+']"><option value=""></select> <a href="#" onclick="testText(this); return false;" title="Not sure if you picked the right carrier?  Click \'test\' to send a quick test message to your text device">unsure?</a>';
        }
        else {
            html += '<input type="hidden" name="data[Contact][ext]['+count+']" value="" ><input type="hidden" name="data[Contact][carrier]['+count+']"value="" >';
        }
         
        html += '&nbsp;<span class="trash is_hidden"><a href="#" onclick="deleteContact(this, null)" title="Remove this contact">';
        html += '<img src="/img/icons/delete.png" width="12" height="12" align="absmiddle"></a></span>';
        html += '</td></tr>';
        $('#empcontacts tbody').sortable('destroy');  
        $('#empcontacts tbody').append(html);
        $('.phone').mask("(999) ?999-99999");
        $('#empcontacts tbody').sortable({ handle: ".handle" });        
        if (type == CONTACT_TEXT) {
            populateSelect();
        }
         $('#emp_edit .ceditable').blur(function() {
            changeLabel(this);
            })
    }               
    
    // to use, add onkeypres="return validateNumber(event);" to input field
    function validateNumber(event) {
        var key = window.event ? event.keyCode : event.which;

        if (event.keyCode == 8 || event.keyCode == 46
         || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 9) {
                return true;
        }
        else if ( (key < 48 || key > 57) && key != 45 ) {
                return false;
        }
        else return true;
    };

 
        
    function addCustomEvent(call_id, reload) {
        if (!reload) 
            url = "/CallEvents/operator_custom/" + call_id;
        else
            url = "/CallEvents/custom/" + call_id;

            $.ajax({
                url: url,
                type: 'GET'
            }).done(function(data) {
                $('#dialogWinSave').html(data);
                $('#dialogWinSave').dialog('open');
            }).fail(ajaxCallFailure);
    }    

    function addHoldTil(msg_id, call_id) {
        url = "/Messages/hold_until/" + msg_id + '/' + call_id;
        openDialogWindow(url, 'Hold Until...', null);
    }    

    

    function checkTxInterval(t) {
        if ($(t).val() === '0') {
            $('#send_time').show();
            $('#time_range').hide();
            $('#alldaydiv').hide();
            document.getElementsByName("data[MessagesSummary][all_day]")[1].checked = false;    
            $('#time_range input').prop('disabled', true);
            $('#time_range input').prop('disabled', true);
            $('#send_time input').prop('disabled', false);    
            $('#notallday').show();      
            $('#allday').hide();      
        }
        else {
            $('#send_time').hide();
            $('#time_range').show();
            $('#alldaydiv').show();
            $('#time_range input').prop('disabled', false);
            $('#send_time input').prop('disabled', true);    
            //$('#notallday').hide();      
            $('#allday').show();      
        }
        
    }
    
    function checkAllDay(t) {
        if (t.checked) {
            $('#time_range input').prop('disabled', true);
            $('#send_time input').prop('disabled', true);    
            $('#notallday').hide();
        
        }
        else {
            $('#time_range input').prop('disabled', false);
            $('#send_time input').prop('disabled', false);    
            $('#notallday').show();
        }
    }
    
    function flashUndelivered() {
        var undelivered_count =  $('#undel_msg').html();
        if (undelivered_count > 0) {
            $('#undel_cont').addClass('undel_cont_blink');
            createToast('danger', 'Please check undelivered messages!', false, 120000);
        }
        else $('#undel_cont').removeClass('undel_cont_blink');    
        bodyLayout.open('east');      
    }
    
function getScrollbarWidth() {
        var outer = document.createElement("div");
        outer.style.visibility = "hidden";
        outer.style.width = "100px";
        outer.style.msOverflowStyle = "scrollbar"; // needed for WinJS apps

        document.body.appendChild(outer);

        var widthNoScroll = outer.offsetWidth;
        // force scrollbars
        outer.style.overflow = "scroll";

        // add innerdiv
        var inner = document.createElement("div");
        inner.style.width = "100%";
        outer.appendChild(inner);        

        var widthWithScroll = inner.offsetWidth;

        // remove divs
        outer.parentNode.removeChild(outer);

        return widthNoScroll - widthWithScroll;
}

function searchFilter(tblname, t) {
                if (t.length < 1) {
                        $("#"+tblname+" tr").css("display", "");
                } else {
                        $("#"+tblname+" tbody tr:not(:contains('"+t+"'))").css("display", "none");
                        $("#"+tblname+" tbody tr:contains('"+t+"')").css("display", "");
                }
}

    function updateOperatorStats() {
                if ($('#operatordiv').is(':visible')) {
                    consoleLog('getting agents');
                        socket.emit('updateAgents', {queue: 'all'}); 
                }
                else {
                    if (agentCheckTimer) clearInterval(agentCheckTimer);
                }        
    
    }
    
    function setAudit(t, msg_id) {
        var url;
        if (t.checked) {
            url = '/Messages/set_audit/'+msg_id +'/1';
            msg = 'Message has been flagged as audited';
        }
        else {
            url = '/Messages/set_audit/'+msg_id+'/0';
            msg = 'Audit flag has been cleared';
        }
        postJson(url, 'Message has been flagged as audited', null);
    }

    function load_undelivered() {
        $('#msg-filter input:checkbox').prop('checked', false);
        $('#find_msg_user').select2("data", null);    
        $("#find_acct").select2("val", "");    
        $('#tabs-msgs').trigger('click');
        $('#msg-filter input[type=text]').val('');
        $('#chk_undelivered').prop('checked', true);
        $('#msg-filter input[type=submit]').trigger('click');
        var date_start = moment().subtract(20, 'days').format('YYYY-MM-DD');
        var date_end = moment().format('YYYY-MM-DD');
        $('#msg-filter input[name="data[Search][m_start_date]"]').val(date_start);
        $('#msg-filter input[name="data[Search][m_end_date]"]').val(date_end);
        $('#undel_cont').removeClass('undel_cont_blink');    
    }
    
        function load_hold() {
        $('#tabs-msgs').trigger('click');
        $('#find_msg_user').select2("data", null);    
        $("#find_acct").select2("val", "");    
        $('#msg-filter input[type=text]').val('');
        $('#msg-filter input:checkbox').prop('checked', false);
        $('#chk_hold').prop('checked', true);
        $('#find_msg_user').select2("data", null);
        var date_start = moment().subtract(20, 'days').format('YYYY-MM-DD');
        var date_end = moment().format('YYYY-MM-DD');
        $('#msg-filter input[name="data[Search][m_start_date]"]').val(date_start);
        $('#msg-filter input[name="data[Search][m_end_date]"]').val(date_end);
        $('#msg-filter input[type=submit]').trigger('click');
    }
    
    function fetchMessages(user_id, form_id, label) {
        $('#tabs-msgs').trigger('click');
        $('#msg-filter input[type=text]').val('');    
        $('#msg-filter input:checkbox').prop('checked', false);
        $('#find_msg_user').select2("data", {id: user_id, text: label});
        var date_start = $('#audit_form input[name="Search[start_date]"]').val();
        var date_end = $('#audit_form input[name="Search[end_date]"]').val();
        if (date_start == '') date_start = moment().subtract(20, 'days').format('YYYY-MM-DD');
        if (date_end == '') date_end = moment().format('YYYY-MM-DD');
        $('#msg-filter input[name="data[Search][m_start_date]"]').val(date_start);
        $('#msg-filter input[name="data[Search][m_end_date]"]').val(date_end);
        $('#msg-filter input[type=submit]').trigger('click');
    }
    
    function fetchMistakes(user_id, label) {
        $('#tabs-mist').trigger('click');
        $('#mistake-filter input').val('');
        $('#mistake-filter input:checkbox').prop('checked', false);
        $('#find_user3').select2("data", {id: user_id, text: label});
        var date_start = $('#audit_form input[name="Search[start_date]"]').val();
        var date_end = $('#audit_form input[name="Search[end_date]"]').val();
        if (date_start == '') date_start = moment().subtract(20, 'days').format('YYYY-MM-DD');
        if (date_end == '') date_end = moment().format('YYYY-MM-DD');
        $('#mistake-filter input[name="data[Search][start_date]"]').val(date_start);
        $('#mistake-filter input[name="data[Search][end_date]"]').val(date_end);
        $('#mistake-filter input[type=submit]').trigger('click');
    }  
    function deliverCheck(call_id) {
    }
    
    function toggleMinder(btn, state, mid, call_id) {
        user_confirm('Are you sure you want to set the status to \''+state+'\'', function() {
            if (state == 'minder' || state == 'unminder') {
                var mybtn = btn;
                $.ajax({
                    url: '/Messages/'+state+'/'+ mid + '/' + call_id,
    
                })
                .done(function(data) {
                        var jsondata = jQuery.parseJSON(data);
                        if (jsondata.success === true) {
                            if (mybtn.value == 'MINDER') {
                                mybtn.value = 'UNMINDER';
                            }
                            else {
                                mybtn.value = 'MINDER';
                            }
                            loadCallEvents(call_id);                            
                            
                        }
                        else {
                            alert(jsondata.msg);
                        }
                 })
                 .always(function() {
                    });
            }
        });
    }  
    
    function toggleOncallHide(t, list_id) {
            var val;
            if (t.checked) val = 1;
            else val = 0;
                $.ajax({
                    url: '/CallLists/setHide/' +list_id+'/'+ val,
                    dataType: 'json',
                })
                .done(function(data) {
                    if (!data.success) {
                        alert(data.msg);
                        if (t.checked) t.checked = false;
                        else t.checked = true;
                    }
                })
    }    
    
    function markDelivery(btn, state, mid, call_id, msg) {
        state = btn.value.toLowerCase();
        if (typeof(msg) == 'undefined') {
            msg = 'Are you sure you want to set the status to \''+state+'\'';
        }
        user_confirm(msg, function() {
            $('#toggle_delivery').show();
            if (state == 'deliver' || state == 'undeliver') {
                var mybtn = btn;
                $.ajax({
                    url: '/Messages/'+state+'/'+ mid + '/' + call_id,
                    fail: function () {
                        $('#toggle_delivery').hide();
                    }
                })
                .done(function(data) {
                        var jsondata = jQuery.parseJSON(data);
                        if (jsondata.success === true) {
                            if (mybtn.value == 'DELIVER') {
                                mybtn.value = 'UNDELIVER';
                                $('#msg_deliver').html('DELIVERED');
                                 $('#lmr_deliver').hide();
                                $('#edit_buttons').hide();
                            }
                            else {
                                mybtn.value = 'DELIVER';
                                $('#lmr_deliver').show();
                                $('#msg_deliver').html('UNDELIVERED');
                                $('#edit_buttons').show();
                                msgWinLayout.resizeAll();
                            }
                            loadCallEvents(call_id);                            
                            loadMsgDeliveries(mid);
                        }
                        else {
                            alert(jsondata.msg);
                        }
                 })
            }
        });    
    }
    function toggleDelivery(btn, state, mid, call_id) {
        if (state == 'DELIVER') {
            $.ajax({
                url: '/Messages/delivery_check/'+ call_id,
                dataType: 'html'
            })
            .done(function(data) {
                if (data > 0) {
                    markDelivery(btn, state, mid, call_id);                                                               
                }
                else {
                    markDelivery(btn, state, mid, call_id, '<span style="color:red"><b>There are no deliveries recorded on this message.</b></span>  <br><br>Are you sure you want to mark this as DELIVERED instead of <b style="color:red;">DELIVER BY LMR</b>?'); 

                }
            })       
        }
        else {
            markDelivery(btn, state, mid, call_id);      
        }
        

    }

    function toggleLMRDelivery(mid, call_id, employee_id, employee_name) {
        if (employee_id == '') {
            alert('You do not have an employee selected');
            return;
        }
        user_confirm('Are you sure you want to set LMR delivery for ' + employee_name + '?', function() {
            $.ajax({
                url: '/Messages/deliver_lmr/'+ mid + '/' + call_id + '/' + employee_id,
                data: 'name=' + employee_name,
                type: 'POST'
    
            })
            .done(function(data) {
                    var jsondata = jQuery.parseJSON(data);
                    if (jsondata.success === true) {
                            $('#deliver_btn').val('UNDELIVER');
                            $('#msg_deliver').html('DELIVERED');
                            $('#lmr_deliver').hide();
                            $('#edit_buttons').hide();
                        loadCallEvents(call_id);                    
                        loadMsgDeliveries(mid);
                    }
                    else {
                        alert(jsondata.msg);
                    }
             })
    
        });
    }






    function appointmentScheduled(appt_time, deliver_status) {
        createToast('info', 'Appointment has been scheduled')       
        var the_html = $('#operatorScreen').find('.appt_time').html();
        if (the_html == '') the_html = '<b>Appointments:</b>';
        $('#operatorScreen').find('.appt_time').html( the_html + '<br>' + appt_time);
        
    }
    
    function testText(t) {
        var num = $(t).siblings('.mycontact').val();
        if (confirm('Are you sure you want to send a test text message to: ' +num+ '?')) {
        var number = num.replace(/[^0-9]/g, '');
        var carrier = $(t).siblings('select').val();
        if (number && carrier) {
        
            $.ajax({
                    url: '/EmployeesContact/testText/'+number+'/'+carrier,
                    dataType: 'json',
                    type: 'POST',
                }).done(function(data) {
                    if (data.success) {
                        alert('Test text message was sent to your number: ' + num);      
                    }
                    else {
                        alert(data.msg);
                    }
                    
                });     
        }
        else alert('You must specify both a number and carrier');
        }
    }


    function isEditable(t) {
        if ($(t).hasClass('disable_edits')) {
            $(t).val($(t).siblings('textarea').val());
            alert('You must enable editing before changing this prompt');
            return false;
        }
        else {
            $(t).siblings('textarea').val($(t).val());
            return true;
        }
    }
    
    
    function getCalltype(supressEdit, schedule_id, message_id, call_id, did_id, callback) {

        var new_ct = $('#msg_ct').val();
        var old_ct = $('#msg_ct_old').val();

        var url =  '/Messages/get_calltype_instructions/' + schedule_id +'/'+message_id+'/'+call_id + '/' + did_id + '.json';
        
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {'new_ct':new_ct, 'old_ct': old_ct}
        })
        .done(function(data) {
            if (data.success == true) {
                //$('#msg_ct').val(new_ct);
                $('#msg_instructions').html(data.instructions_html);

                // swap out user prompts with ones from the new calltype
                $('#msg_prompts').html(data.prompts_html);
                if (!supressEdit) editMessage();
                callback();
            }
            else alert('Unable to save your changes, please try again later');
        })
        .fail(function() {
            alert('Unable to save your changes, please try again later');
            $('#msg_ct').val($('#msg_ct_old').val());
                        
        });                 


    }    
    
    
    function checkprompts() {
    }


    function editMessage() {
        //$('#msg_prompts input.uprompt, #msg_prompts textarea.uprompt').prop('disabled', false);
    $('#save_msg, #cancel_msg').show();
    $('#msg_dispatch').hide();
    }   
        
    function setEmployee(emp, container) {
        $('#'+container).val(emp);
        $('#'+container).trigger('change');
    }

        // hide/unhide the mistakes export button (only allowed for summary screen)
    function mistake_hide() {
        if(document.getElementById('mistake_summary_check').checked) {
            $('#mistake_export').show();
        } else {
            $('#mistake_export').hide();
        }
    }
    
    // parse google place return value to populate a street address field
    function getPlaceParts(place) {
        
        var parts = {snumber: '', street: '', city: '', state: '', zip: ''};
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            var placeTypes = place.address_components[i].types;
            if ($.inArray('street_number', placeTypes) > -1 ) {
                parts['snumber'] =  place.address_components[i]['short_name'];
            }
            if ($.inArray('route', placeTypes)  > -1) {
                parts['street'] = place.address_components[i]['short_name'];
            }
            if (($.inArray('locality', placeTypes)  > -1) || ($.inArray('neighborhood', placeTypes) > -1 && parts['city'] == '') || ($.inArray('sublocality', placeTypes)  > -1 && parts['city'] == '') ) {
                parts['city'] =  place.address_components[i]['long_name'];
            }      
            if ($.inArray('administrative_area_level_1', placeTypes)  > -1) {
                parts['state'] =   place.address_components[i]['short_name'];
            }       
            if ($.inArray('postal_code', placeTypes) > -1 ) {
                parts['zip'] = place.address_components[i]['short_name'];
            }       
        }
        return parts;     
    }
    
    function populatePlaceField(t, input_field, ftype, search_string) {
        var parts = getPlaceParts(t.getPlace());
        var addr = '';
        var geocode_ok = true;
        if (ftype == 'street') {
            if (parts['snumber'] == '') { // exact location not found
                geocode_ok = false;
                if (parts['street']) {
                    parts['snumber'] = search_string.trim().split(' ')[0];
                    addr = parts['snumber'] + ' ' + parts['street'];
                }
                else addr = search_string;
            }
            else { // exact location found
                addr = parts['snumber'] + ' ' + parts['street'];
            }
        }
        else if (ftype == 'fulladdr') {
            
            if (parts['snumber'] == '') {// exact location not found
                geocode_ok = false;
                if (parts['street']) {
                    parts['snumber'] = search_string.trim().split(' ')[0];
                    addr = parts['snumber'] + ' ' + parts['street'];
                    addr = addr + ", " + parts['city'] + ' ' + parts['state'] + ' ' + parts['zip'];      
                }
                else addr = search_string;        
            }
            else { // exact location found
                addr = parts['snumber'];      
                if (addr != '') addr = addr + " ";
                if (parts['street'] != '') addr = addr + ' ' + parts['street'];
        
                if (addr != '') addr = addr + ", ";
                addr = addr + parts['city'] + ' ' + parts['state'] + ' ' + parts['zip'];      
            }
        }
        else if (ftype == 'citystatezip') {
            // make sure zipcode is returned in the autocomplete selection
            if (parts['zip'] == '') {
                geocode_ok = false;
            }
            
            addr = parts['city'] + ' ' + parts['state'] + ' ' + parts['zip'];      
        }
        input_field.value = addr;
        if (geocode_ok == false) {
                $(input_field).removeClass('shake animated').addClass('shake missing animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                    $(input_field).removeClass('shake animated');
                });          
        }
        else {
            $(input_field).removeClass('missing')
        }
        autosize.destroy(input_field);  
        autosize(input_field);       
        if (geocode_ok) {
            $(input_field).trigger('change');
            return true;
        }
        else {
            return false;
        }
    }


    
    // email format validation
    function validateEmail(email) {
        if (email == '') return true;
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    // validate multiple emails delimited by ',', ';' or ' '
    function validateEmails(t) {
        var emails = $(t).val(),
        emailArray = emails.split(/\,|;|\s|\r\n|\r|\n/);
        var result = true;
        for (var i = 0; i < emailArray.length; i++) {
            
            if( ! this.validateEmail(emailArray[i].trim())) {
                result = false;
            }
        };      
        return result;
    }     
    
    function checkRequiredMsgs() {
        if (required.length > 0) {
            $('#msgWin .content').html(required[0]['Bulletin']['note'] + '<input type="hidden" name="br_id" value="'+required[0]['BulletinRecipient']['id']+'">');
            $('#msgWin').dialog('open');   
        }    
    }
    function checkPageReload(e) {
        /*e.stopPropagation();
        e.preventDefault();  */
        return 'Make sure you have no active calls and have saved any changes you made before leaving OpenAnswer.';
    }  

    function startBreakTimer() {
    $('#ts_start').val(new Date().getTime());   
        
    $('#break_timer').addClass('break_cont_blink');     
    breakTimer = setInterval(function() {
        var h=0;
        var m=0;
        var s=0;
        var temp=0;
        $('#break_timer').children().show();
            
        var start_ts;
        start_ts = $('#ts_start').val();
        stop_ts =  new Date().getTime();    
        var td = stop_ts - start_ts;
        td = Math.round(td/1000);
    
        h = Math.floor(td/(60*60));
        temp = td%(60*60);
        m = Math.floor(temp/60);
        s = temp%(60);

        if (m<10) m = '0' + m;
        if (s<10) s = '0' + s;
        if (h<10) h = '0' + h;

        $('#sw_h').html(h + ':');
        if (h<1) $('#sw_h').hide();
        else $('#sw_h').show();
        $('#sw_m').html(m);
        $('#sw_s').html(s);
    }, 2000);
    
    
    }
    
    function stopBreakTimer() {
    clearInterval(breakTimer);
    $('#break_timer').children().hide();
    }
