$(function() {
  
  // request a page from CakePHP once in a while to keep the PHP session alive
  var heartbeatTimer = setInterval(keepalive, settings['heartbeat_seconds'] *1000);
  var undeliveredTimer;
  undeliveredTimer = setInterval(flashUndelivered, settings['undelivered_minutes'] * 60 *1000);

  $.mask.definitions['p'] = "[A-Za-z0-9 \/\(\)\-]";  
  $.cookie.json = true;
  $.cookie.defaults = {'path': '/', 'expires': 365};
    
	$.toast.config.align = 'center';
	$.toast.config.width = 400;
	$.toast.config.closeForStickyOnly = false;
      
  $('#resizer_incr').on('click', function() {
    changeFontSize('incr'); 
    return false;  
  });
  
  $('#resizer_decr').on('click', function() {
    changeFontSize('decr'); 
    return false;  
  });  
  
  $('#resizer_admin').on('click', function() {
    window.open('/OpenAnswer/index/role:adminonly', '_blank','width=1210,height=700,scrollbars=1,resizable=1,location=yes,menubar=yes,toolbar=no'); 
    return false;    
  });
  
  $('#load_undelivered').on('click', function() {
    load_undelivered(); 
    return false;
  });
  
  $('#load_hold').on('click', function() {
    load_hold(); 
    return false;
  });  
  
  /** Account section buttons *******/
  $('#find_account_go').on('click', function() {
    if (accountSpecified()) {
      loadPage(this, '/Accounts/edit/'  + $('#find_account').val(), 'acct-content');
      acctLayout.center.children.layout1.close('west'); 
    } 
    else {
      loadPage(this, '/Accounts/', 'acct-list');
      acctLayout.center.children.layout1.open('west'); 
      $('#acct-content').html(''); 
    } 
    return false;
  });  
  
  $('#acct_add').on('click', function() {
    acctLayout.center.children.layout1.close('west');
    loadPage(this, '/Accounts/add', 'acct-content');
    return false;
  });   
  
  $('#acct_history').on('click', function() {
    if (accountSpecified()) {
      loadPage(null, '/AccountsEdits/index/'+$('#find_account').val(), 'acct-content'); 
    }
    else {
      alert('Please specify an account in the search field');
    }
    return false;    
  });
  
  $('#acct_cancel').on('click', function() {
    acctLayout.center.children.layout1.close('east'); 
    return false;
  });
  
  $('#acct_add_subacct').on('click', function() {
    addNumber($('#add_did_id').val());    
  });
  
  $('#acct_add_cancel').on('click', function() {
    $('#add-did').dialog('close');return false;    
  });  
  
  
  
  /** Phone number section handlers *******/
  $('#show_opscreen').on('click', function() { 
    manualPop = true; 
    if (didSpecified()) manualScreenPop($('#find_did').val(), null); 
    return false;  
  });
  
  $('#find_did_go').on('click', function() {
      $('#did_format').val('');
      document.didsearch.removeAttribute('target');          
      find_did_go_handler(this); 
      return false;    
  });
  
  $('#subacct_export').on('click', function() {
     $('#did_format').val('csv');
     document.didsearch.setAttribute('target', '_blank');    
  });
  
  $('#subacct_more').on('click', function() {
    if ($('#adv_filter').is(':hidden')) {
      $('#adv_filter').show();
      didLayout.sizePane('north', 110);
    } 
    else {
      $('#adv_filter').hide(); 
      didLayout.sizePane('north', 80);
    }    
  });
  
  $('#subacct_basic').on('click', function() {
    $.cookie('active-button', $(this).attr('id')); 
    if (didSpecified()) loadPage(this, '/DidNumbers/edit/'  + $('#find_did').val(), 'did-content'); 
    else alert('Please specify a phone number in the search field');
    return false;
  });
  
  $('#subacct_ct').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadCalltypes($('#find_did').val(), 'did-content', 'did-detail', 'didLayout'); 
    else alert('Please specify a phone number in the search field');
    return false;    
  });  
  
  $('#subacct_files').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadPage(this, '/Files/index/'  + $('#find_did').val(), 'did-content');
    else alert('Please specify a phone number in the search field');
    return false;    
  }); 
   
  $('#subacct_emp').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadPagePost(null, '/Employees/index/' + $('#find_did').val(), 'did-content', 'target=did-content&detail=did-detail', null);
    else alert('Please specify a phone number in the search field');
    return false;
  });  
  
  $('#subacct_ms').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadPage(this, '/MessagesSummary/index/'  + $('#find_did').val(), 'did-content'); else alert('Please specify a phone number in the search field');
      return false;    
  }); 
   
  $('#subacct_oncall').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadPage(this, '/CallLists/index/'  + $('#find_did').val(), 'did-content'); 
    else alert('Please specify a phone number in the search field');
    return false;    
  });  
  $('#subacct_crm').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadPage(this, '/Crms/index/'  + $('#find_did').val(), 'did-content'); 
    else alert('Please specify a phone number in the search field');
    return false;    
  });
  
  $('#subacct_cal').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadPage(this, '/Scheduling/EaServices/index/'  + $('#find_did').val(), 'did-content'); 
    else alert('Please specify a phone number in the search field');
    return false;    
  });
  
  $('#subacct_campaigns').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadPage(this, '/OutboundDialer/Campaigns/index/'  + $('#find_did').val(), 'did-content'); 
    else alert('Please specify a phone number in the search field');
    return false;    
  });  







    
  $('#subacct_notes').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadPage(null, '/Notes/index/' + $('#find_did').val() + '?target=did-content' , 'did-content');
    else alert('Please specify a phone number in the search field');
    return false;
  });  
  
  $('#subacct_history').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadPage(null, '/DidNumbersEdits/index/'+$('#find_did').val(), 'did-content');
    return false;
  });  
  
  $('#subacct_complaints').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadPage(this, '/Complaints/did_index/'  + $('#find_did').val(), 'did-content');
    else alert('Please specify a phone number in the search field');
    return false;
  });
    
  $('#subacct_calls').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadPagePost(null, '/CallLogs/messages/' + $('#find_did').val(), 'did-content', 'target=did-content&detail=did-detail', null);
    else alert('Please specify a phone number in the search field'); 
    return false;
  });  
  
  $('#subacct_msgs').on('click', function() {
    $.cookie('active-button', $(this).attr('id'));
    if (didSpecified()) loadPage(null, '/Messages/index/' + $('#find_did').val(), 'did-content'); 
    return false;
  }); 
   
  $('#subacct_cancel').on('click', function() {
    didLayout.center.children.layout1.close('east'); 
    $('#did_save_btn').prop('disabled', false); 
    return false;    
  });  

  /** Messages section handlers *******/
  $('#msgs_go').on('click', function() {
    loadPagePost(null, '/Messages/view/' + $('#find_acct').val(), 'msg-content', $(this).parent('form').serialize(), null);
    return false;
  });  
  
  $('#msgs_cancel').on('click', function() {
    msgLayout.center.children.layout1.close('east'); 
    return false;
  });

  /** Bulletins section handlers *******/
  $('#bulletin_index').on('click', function() {
    loadPage(this, '/Bulletins', 'bb-content'); 
    return false;
  });  

  $('#bulletin_create').on('click', function() {
    loadPage(this, '/Bulletins/add', 'bb-detail'); 
    bbLayout.center.children.layout1.open('east');
    return false;
  });  
  
  $('#bulletin_broadcast').on('click', function() {
    $('#broadcastWin').dialog('open');
    return false;
  });

  $('#broadcast_close').on('click', function() {
    $('#broadcast_msg').hide(); 
    return false;
  });
  
  $('#bulletin_cancel').on('click', function() {
    bbLayout.center.children.layout1.close('east'); 
    return false;    
  });
  

  /** Users section handlers *******/
  $('#user_cancel').on('click', function() {
    userLayout.center.children.layout1.close('east'); 
    return false;
  });

  /** Roles section handlers *******/
  $('#role_cancel').on('click', function() {
    roleLayout.center.children.layout1.close('east'); 
    return false;
  });

  

  /** Calls section handlers *******/
  $('#calls_go').on('click', function() {
    loadPagePost(null, '/CallLogs/index/' + $('#find_acct_call').val(), 'call-content', $(this).parents('form').serialize(), null);
    return false;
  });  
  
  $('#calls_cancel').on('click', function() {
    callsLayout.center.children.layout1.close('east'); 
    return false;  
  });
  
  /** Complaints section handlers *******/
  $('#complaints_go').on('click', function() {
    loadPagePost(null, '/Complaints/index/' + $('#find_acct_c').val(), 'complaints-content', $(this).parents('form').serialize(), null);
    return false;
  });  
  
  $('#complaints_add').on('click', function() {
			var parent_div = 'complaints-content';
			openDialogWindow('/Complaints/add', 'Add Complaint', null, function() {
					loadPage(this, '/Complaints/index', parent_div);
				}, 900, 600);
  });
  
  /** Mistakes section handlers *******/
  $('#mistakes_go').on('click', function() {
    loadPagePost(null, '/Mistakes/index/' + $('#find_acct_m').val(), 'mistakes-content', $(this).parents('form').serialize(), null);
    return false;
  });  
    
  $('#mistake_summary_check').on('click', function() {
    mistake_hide();
  });
  
  $('#mistake_export').on('click', function() {
    document.mistake_form.setAttribute('target', '_blank'); 
    document.getElementById('mistake_format').value='csv';
  });
  
  
  /** OpenAnswer handlers *******/
 
  
  $('#offbreakbtn').on('click', function() {
    $('#breakDialog').dialog('open');
  });    
  
  $('#onbreakbtn').on('click', function() {
		// check if pause_agent feature is enabled
		if (pause_agent) {
			// break_id shouldn't be blank when this button is clicked, but check for it anyway
			if (break_id == '') {
				leaveBreak(); 
				return;
			}
			
			// if on break only because operator is on a call, don't take operator off break
			// this situation is true when operator drags a call to oneself after putting themself on break
			if (break_id == 'on-call') {
				return;
			}
			
			// check if we are on a call
			if (callId !== null) {
				// if on a call, then we stay on break and reset break_id to 'on-call';
				break_id = 'on-call';
        localStorage.setItem('oa_onbreak', false); 				  
				$('#availbtn').prop('disabled', false);
				$('#onbreakbtn').hide();
				$('#offbreakbtn').show();
        postJson('/Users/leave_break', {'msg': '', 'break_id': break_id}, null); 							
			}
			else {
				leaveBreak();
			}
		}
		// we're not doing auto-answer and we're not on break 
		else {
    	leaveBreak();
    }
  });  
  
  $('#logout_btn').on('click', function() {
    logoutUser(); 
    return false;    
  });
  
  $('#opscreen_test').on('click', function() {
    manualScreenPop($('#find_did').val(), $('#test_time').val()); 
    return false;    
  });
  
  $('#break_save').on('click', function() {
    if ($('#breakDialog input:radio:checked').val()) {
      var reason=$('#breakDialog input:radio:checked').val(); 
      
      // text field only exists when 'Other' reason is available
      if (($('#breakDialog input[type=text]').length > 0) && $('#breakDialog input[type=text]').val() != '') {
        reason = reason + ' - ' + $('#breakDialog input[type=text]').val(); 
      }
      takeBreak( reason ); 
      
      // as long as this variable is true, break button has been pressed but OA hasn't received
      // OC confirmation that operator is on break.  Keep track so that we don't flag user as being in
      // wrap-up time.
			break_button_pressed = true;
    } 
    else alert('Please specify a reason for your break.'); 
    return false;  
  });
  
  $('#break_cancel').on('click', function() {
    $('#breakDialog').dialog('close'); 
    return false;
  });
  
  $('#clear_search').on('click', function() {
    $('#recentsel ul li:not(:last)').remove();
    localStorage.removeItem('recentsearch'); 
    return false;    
  });
  
  $('#app_settings').on('click', function() {
    openDialogWindow('/AppSettings/edit', 'Application Settings', null); 
    return false;    
  });
  
  /** Operator screen handlers *******/
  $('#opscreen_addnote').on('click', function() {
    openNoteDialog(currentInstructions['did']['account_id'], currentInstructions['did']['id'], 'message', '', function() {
      getJson('/Notes/operator/' + currentInstructions['did']['id'], null, function(data) {
        showOperatorNotes(data)
      });
    }); 
    return false;
  });
  
  
    $('#opscreen_security').on('click', function() {
        openSecurityDialog(currentInstructions['did']['account_id']);
        btnClickHandler(this);
    });
  
  $('#opscreen_msgreview').on('click', function() {
    var url = '/Messages/review/' + currentInstructions['did']['id'];
    window.open(url,'_blank','width=1024,height=700,scrollbars=1,resizable=1,location=no,menubar=yes,toolbar=yes'); 
  });  
  
  $('#opscreen_acctreview').on('click', function() {
    var url = '/ReviewRequests/add/' + currentInstructions['did']['id'];
			openDialogWindow(url, 'Request review', null, function() {			
				}, 700, 600);    
  });   
  
  $('#show_disp').on('click', function() {
    if (this.checked) {
      
      $('#operatorScreen .dispatcher').addClass('dispatcher2').removeClass('dispatcher');
    }
    else {
      $('#operatorScreen .dispatcher2').addClass('dispatcher').removeClass('dispatcher2');
    }
  });

  $('#opscreen_addevent').on('click', function() {
     addCustomEvent(callId, false); 
  });  
  
  $('#opscreen_deliver').on('click', function() {
      $(this).attr('btype', BUTTON_DELIVER);
      $(this).attr('call_id', callId);
      $(this).attr('emp_name', '');
      $(this).attr('contact_id', '');
      $(this).attr('employee_id', '');
      $(this).attr('ext', '');
      $(this).attr('action_id', '');
      $(this).attr('bdata', 'Mark Delivered');
      $(this).attr('bfulldata', 'Mark Delivered');
      $(this).attr('blabel', 'Mark Delivered');
      $(this).attr('bloc', '1');  			  
          
			btnClickHandler(this);    
  });  
  
  $('#opscreen_dispatch').on('click', function() {
    $(this).attr('btype', BUTTON_DISPATCH);
      $(this).attr('call_id', callId);
      $(this).attr('emp_name', '');
      $(this).attr('contact_id', '');
      $(this).attr('employee_id', '');
      $(this).attr('ext', '');
      $(this).attr('action_id', '');
      $(this).attr('bdata', 'Dispatch');
      $(this).attr('bfulldata', 'Dispatch');
      $(this).attr('blabel', 'Dispatch');
      $(this).attr('bloc', '1');  			  
      
			btnClickHandler(this);    
  });  
  
  $('#cancel_reason_sel').on('change', function() {
    if (!manualPop) {
      if (this.value != '') $('#cancel_button').prop('disabled', false); 
      else $('#cancel_button').prop('disabled', true);
    }    
  });  
  
  $('#cancel_button').on('click', function() {
    if (!manualPop) {
      logCallEvent(callId, '[CANCEL] ' + $('#cancel_reason_sel').val());        	
      closeOperatorScreen();      
    }
    else {
      user_confirm('Are you sure you want to exit the operator screen?', function() {
        if ($('#cancel_reason_sel').val() == '') logCallEvent(callId, '[CANCEL] Operator Screen' );        
        else logCallEvent(callId, '[CANCEL] ' + $('#cancel_reason_sel').val());               	
		    closeOperatorScreen();  				
        
      });
    }
  });  

  $('#actionBox .editable').on('click', function() {
    editText(this); 
    return false;  
  });  
  
  $('#callBox .actbtn').on('click', function() {
    callboxAction(this); 
    return false;    
  });  
  
  $('#actbtn_enable').on('click', function() {
    $('#callBoxCtrl .actbtn').button('enable'); 
    return false;    
  });
  
  
   // disable certain boxes depending on result of the outbound call
  $('#callBoxResult input[type=radio]').on('click', function() {
    if ($(this).val() == 1) {
      $('#callBoxResult .actbtn').button( "option", "disabled", true );        
      $('#btn_deliver').button('option', 'disabled', false);
    }
    if ($(this).val() > 4 && $(this).val() < 9) {
      $('#callBoxResult .actbtn').button( "option", "disabled", false );        
      $('#btn_deliver').button('option', 'disabled', true);
    }
    else 
      $('#callBoxResult .actbtn').button( "option", "disabled", false );        
  });

  //setup ajax error handling
  $( document ).ajaxError(function(evt, x, settings, err) {
          if (x.status == 403) {
            error403_count++;
            if (error403_count > 3) {
              window.location.href ="/OpenAnswer";
            }                
          }
          if (error403_count > 2) { 
            createToast('error', "Error: " + x.status + " - " + err);
          }
          $('#page-loading').hide();            
  });
  
  $( document ).ajaxSuccess(function() {
    error403_count = 0;
  });    
      
  $('.editable').focus( function(){
    if ($(this).html() == emptyText) $(this).html(' ');
  });
  $('.editable').blur( function(){
    if ( $.trim($(this).html()) == '') $(this).html(emptyText);
  });    
  $('.editUrl').focus( function(){
    if ($(this).val() == emptyUrl) $(this).val('');
    $(this).removeClass('emptyInput');      
  });
  $('.editUrl').blur( function(){
    if ( $.trim($(this)).val() == '') {
      $(this).val(emptyUrl);
      $(this).addClass('emptyInput');
    }
    else $(this).removeClass('emptyInput');
  });       


			
		$.getJSON('/Users/operators.json', function(json) {
			$(".find_user_sel2").select2({
				data:{ results: json.rows, text: 'label' },
				multiple: false,
		  	placeholder: 'Search by username or name',
		  	allowClear: true
			});
		});
					  
	  
    if ($.cookie('find_did_save') && $.cookie('find_did_save').id && $.isNumeric($.cookie('find_did_save').id)) {
      $('#find_did').val($.cookie('find_did_save').id);
    }

		$(".find_did_sel2").select2({
      initSelection : function (element, callback) {
        var id=$(element).val();
        if (id!=="") {        
          $.ajax("/DidNumbers/find/"+id, {
            dataType: "json"
          }).done(function(data) { 
            if (data.length > 0) {
              //click on the last button click to display last data
              //$('#' + $.cookie('active-button')).trigger('click');
              callback(data[0]); 
            }
            else {
              $(element).val('');
            }
          });
        }
/*        else {
          var data = {id: element.val(), text: element.attr('text')};
        }*/
      },		  
		  placeholder: 'Search by account number/ name/ phone number',
		  minimumInputLength: 3,
      allowClear: true,
      blurOnChange: true,
      openOnEnter: false,		  
		  ajax: {
			  url: "/DidNumbers/find/",
		    data: function(term, page) {
		      return {term: term, page: page};
		    },
			  dataType: 'json',
		    results: function (data, page) {
		      return {results: data};
		    }
		  }
		});
		
		$(".find_did_sel2all").select2({
		  data: recentsearch,
      initSelection : function (element, callback) {
        var id=$(element).val();
        if (id!=="") {        

          $.ajax("/DidNumbers/find2/"+id, {
            dataType: "json"
          }).done(function(data) { 
            if (data.length > 0) {
              $('.didbtns').show();
              callback(data[0]); 
            }
            else {
              $(element).val('');
            }
          });
        }
      },		  
		  placeholder: 'Search by account number/ name/ phone number',
		  minimumInputLength: 3,
      allowClear: true,
      blurOnChange: true,
      openOnEnter: false,		  
		  ajax: {
			  url: "/DidNumbers/find2/",
		    data: function(term, page) {
		      return {term: term, page: page};
		    },
			  dataType: 'json',
		    results: function (data, page) {
		      return {results: data};
		    }
		  }
		}).on('change', function(e) {
		  if (e.added) {
  		  recentsearch.unshift(e.added);
  		  if (recentsearch.length > 20) {
          $('#recentsel ul li').last().remove();		    
  		    recentsearch.shift();
  		  }
        localStorage.setItem("recentsearch",JSON.stringify(recentsearch));  		  
        $('#recentsel ul').prepend('<li><a href="#" onclick="editDidNumber(\''+e.added.id+'\', \''+e.added.value+'\');return false;">'+e.added.value+'</a></li>');		  
      }
		});		

    // load the last selected DID saved in a cookie
    $('#find_did').on("select2-selecting", function(e) {$.cookie('find_did_save', e.object);})		
    $('#find_did').on("select2-removed", function(e) {$.cookie('find_did_save', null);})		

    
		$(".find_account_sel2").select2({
      initSelection : function (element, callback) {
        var id=$(element).val();
        if (id!=="") {        
          /*var data = $.cookie('find_did_save');
          trigger_find_did = true;
          callback(data);*/
          $.ajax("/Accounts/find/"+id, {
            dataType: "json"
          }).done(function(data) { 
            if (data.length > 0) {
              //click on the last button click to display last data
              //$('#' + $.cookie('active-button')).trigger('click');
              $('.didbtns').show();
              callback(data[0]); 
            }
          });
        }
/*        else {
          var data = {id: element.val(), text: element.attr('text')};
        }*/
      },			  
		  placeholder: 'Search account number/ name or DID',
		  minimumInputLength: 3,
      allowClear: true,
      blurOnChange: true,
      openOnEnter: false,		  
		  ajax: {
			  url: "/Accounts/find/",
		    data: function(term, page) {
		      return {term: term, page: page};
		    },
			  dataType: 'json',
		    results: function (data, page) {
		      return {results: data};
		    }
		  }
		});		

    $(".find_account_sel2").on('change', function() { 
      if ($(this).hasClass('auto')) $(this).siblings('input[type=submit]').trigger('click');
    });

/*    $(".find_did_sel2").on('change', function() { 
      $(this).siblings('input[type=submit]').trigger('click');
    });*/
    
    $(".find_did_sel2all").on('change', function() { 
      $(this).next('input[type=submit]').trigger('click');
    });
        
    $(".find_user_sel2").on('change', function() { 
      if ($(this).hasClass('auto')) $(this).siblings('input[type=submit]').trigger('click');
    });
    


		$(document).ajaxStart(function() {
			$('#page-loading').show();
		});
		$(document).ajaxStop(function() {
			$('#page-loading').hide();
      $( document ).tooltip({
        open: function (event, ui) {
            setTimeout(function () {
                $(ui.tooltip).hide();
            }, 2000);
        }});			
		});
		
		$(document).ajaxError(function() {
			$('#page-loading').hide();
		});		
	
		$(document).error(function() {
			$('#page-loading').hide();
		});			


    // set up various jquery UI dialog boxes
    $('#add-did').dialog({
        title:        'Add Number',
        width:        400,
        height:        150,
        dialogClass: 'no-close',
        autoOpen: false,
        closeOnEscape: false,
        modal: true,
        close: function() {
            }
    }); 
    $('#dialogIntegration').dialog({
        title:        'CRM Integration',
        width:        900,
        height:        700,
        //dialogClass: 'no-close',
        autoOpen: false,
        closeOnEscape: false,
        modal: true,
        open: function() {
        },
        close: function() {
    }
    }); 
    $('#securityDialog').dialog({
        title:        'Account Security Questions',
        width:        400,
        height:        300,
        //dialogClass: 'no-close',
        autoOpen: false,
        closeOnEscape: false,
        modal: true,
        open: function() {
        },
        close: function() {
    }
    }); 
    $('#record-did').dialog({
			title:		'Record Company Name'
		,	width:		500
		,	height:		250
  	//, dialogClass: 'no-close'		
		, autoOpen: false
    , closeOnEscape: false
    ,	modal:		true
    , close: function() {
      $('#record-did-content').html('');
    }
    , open: function() {
    }
    });        
    $('#dialogWin').dialog({
			title:		'Generic Window'
		,	width:		Math.floor($(window).width()  * .90)
		,	height:		Math.floor($(window).height() * .90)
  	, dialogClass: 'no-close'		
		, autoOpen: false
    , closeOnEscape: false
    ,	modal: true
    , close: function() {
			if (dialogWinCallback) dialogWinCallback();
      dialogWinCallback=null;
      $('#dialogWin').html('');
    }
  	, buttons: {

  			Close: function() {
  				$( this ).dialog( "close" );
  				
  			}
  		}
    });
    
    $('#dialogWinSave').dialog({
			title:		'Add Event'
		,	width:		400
		,	height:		400
  	, dialogClass: 'no-close'		
		, autoOpen: false
    , closeOnEscape: false
    ,	modal: true
    , close: function() {
      $('#dialogWin').html('');
    }
  	, buttons: {
  			Save: function(event) {
  				$(event.target).parents('.ui-dialog-buttonpane').siblings('.ui-dialog-content').find('input[type=submit]').trigger('click');
  				$( this ).dialog( "close" );
  				
  			},

  			Cancel: function() {
  				if (dialogWinCallback) dialogWinCallback();
          dialogWinCallback=null;
  				$( this ).dialog( "close" );
  				
  			}
  		}
    });    
    
    $('#broadcastWin').dialog({
			title:		'Broadcast to all OpenAnswer Users'
		,	width:		500
		,	height:		280
		, autoOpen: false
    , closeOnEscape: false
    ,	modal:		true
    , close: function() {
      $('#dialogWin').html('');
    }
  	, buttons: {
  			Send: function() {
  				broadcastMessage();
          $('#broadcastWin').dialog( "close" );  				
  			},

  			Close: function() {
  				$('#broadcastWin').dialog( "close" );
  			}
  		}
    });
    
    $('#msgDialogWin').dialog({
			title:		'Message Review'
		,	width:		Math.floor($(window).width()  * .90)
		,	height:		Math.floor($(window).height() * .80)
		, autoOpen: false
    , closeOnEscape: false
    ,	modal:		true
    , close: function() {
		  clearInterval(msgClockInterval);
      if (msgWinLayout) msgWinLayout.destroy();
      $('#msgDialogWin').html('');
      msgWinLayout = null;
      if (msgDialogWinCallback) msgDialogWinCallback();
      msgDialogWinCallback = null;
    }
    });    
    //$('#msgDialogWin').siblings('.ui-dialog-titlebar').hide();
        
    $('#msgWin').dialog({
			title:		'Latest Message'
		,	width:		500
		,	height:		500
  	, dialogClass: 'no-close'		
		, autoOpen: false
    , closeOnEscape: false
    ,	modal:		true
    , close: function() {
    }
  	, buttons: {
  			'>> Click here to acknowledge you have seen this message <<': function() {
  			  var bulletin_id = $('#msgWin').find('input[name=br_id]').val();
      		var url = '/Bulletins/acknowledge/' + bulletin_id;
        	$.ajax({
        		url: url,
        		type: 'get',
        		dataType: 'json'
        	}).done(function(jsondata) {
        	  if (jsondata.success) {
        	    required = jsondata.required;
  				    $('#msgWin').dialog( "close" );
              checkRequiredMsgs();
        	  }
        	  else {
        	    alert(jsondata.msg);
        	  }
					});   			  
  			}
  		}
    });    

		$('#operatorScreen').dialog({ 
			title:		'Operator Screen'
		,	width:		Math.floor($('body').width()  * .90)
		,	height:		Math.floor($('body').height() * .90)
  	, dialogClass: 'no-close operator-screen'		
    , closeOnEscape: false
		, autoOpen: false
    ,	modal: false
		,	open:		function() {
        var d;
        $('#miscnotes').hide();
		    $('#test_time').blur();
  		  
				if (!dialogLayout) {
					// init layout *the first time* dialog opens
					dialogLayout = $("#operatorScreen").layout( dialogLayout_settings );
          $('#opscreen_main').tabs();
        }
				else {
					// just in case - probably not required
					dialogLayout.resizeAll();
				}

  		  if (manualPop) {
  		    $('#cancel_button').prop('disabled', false);
  		  }
  		  else {
  		    $('#cancel_button').prop('disabled', true);
  		  }
        $('#cancel_reason_sel').val('')  		  
		}
		, close: function() {
   		$('#operatorScreen input[type=text]').val('');
   		$('#company_content div, #calltypes, #instructions').html('');		  
		  clearInterval(clockInterval);
      $('#cb_empcontacts').html('');
		  manualPop = false;
  		currentCall = null;
  		callStatus = null;
  		msgId = null;
  		callId = null;		  
      incomingUniqueId = '';     
      $('#edit_msg, #save_msg, #cancel_msg').hide();   				
      $('#dispatch_msg').show();
      $('#dispatch_msg button').prop('disabled', false);
		}				
		,	resizeStop:		function() { 
		  console.log('opscreen resize');
		  if (dialogLayout) {
		    console.log('dialog resize');
		    dialogLayout.resizeAll(); 
		  }
		}
		});    

		$("#callBox").dialog({ 
			title:		'Dial Out Status'
		,	width:		360
		,	height:		370
		, position: {my: "center", at: "center", of: window}
  	, dialogClass: 'no-close'		
    , closeOnEscape: false
		, autoOpen: false
    ,	modal:		true
		,	open:		function(response) {
						}
		, close: function() {
      $('#callBoxResult').hide();		  
      $('#callBoxCtrl').show();     
      $('#callbox_did').val(''); 
      $('#callbox_did_id').val(''); 
		}			
		});  

    // initialize the datepickers 
		$('.datepicker').datepicker({
    	dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true
		});			
		
    // initialize the datepicker pairs
		$('.datepicker_pair').datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      onClose: function( selectedDate ) {
        if ($(this).attr('dtype') == 'start')
          $(this).siblings('input[dtype=end]').datepicker( "option", "minDate", selectedDate );
        else
          $(this).siblings('input[dtype=start]').datepicker( "option", "maxDate", selectedDate );
      }
		});
		
		$("#breakDialog").dialog({ 
			title:		'Break Description'
		,	width:		300
		,	height:		400
    , closeOnEscape: false
  	, dialogClass: 'no-close'		
		, autoOpen: false
    ,	modal:		true
		,	open:		function() {
		  $('#breakDialog input[type=text]').val('');
		}
					
		});  		
				
		$("#mistakeDialog").dialog({ 
			title:		'Mistakes'
		,	width:		780
		,	height:		500
  	, dialogClass: 'no-close'		
    , closeOnEscape: false
		, autoOpen: false
    ,	modal:		true
    , buttons: {
    	'Cancel': function() {
    		$( this ).dialog( "close" );
    	},
    	'Save': function() {
    	  var url = $(this).parents('.ui-dialog').find('input[name=url]').val();
    	  var formdata = $(this).parents('.ui-dialog').find('form').serialize();
    	  var dialogbox = this;

		  	jQuery.ajax({
		      url: url,
		      method: 'post',
		      dataType: 'json',
		      data: $('#mistake-form').serialize()
  		  }).done(function (response) {
  				if (response.success) {
  				  $(dialogbox).dialog( "close" );
  				  if (dialog_callback !== null) {
  				    dialog_callback();
  				    dialog_callback = null;
  				  }
  				}
  				alert(response.msg);
  		  }).fail(function () {
  		    alert('Cannot save changes, please try again later');     
  		  });     		
    		
    	}
    }
		,	open:		function() {
	
		}
		});  			
		
		
		$("#noteDialog").dialog({ 
			title:		'Notes'
		,	width:		700
		,	height:		600
  	, dialogClass: 'no-close'		
		, autoOpen: false
    , closeOnEscape: false
    ,	modal:		true
    , buttons: {
    	'Cancel': function() {
    		$( this ).dialog( "close" );
    	},
    	'Save': function() {
    	  var dialogbox = this;
    	  var url;
    	  if ($('#editnote_id').val() != '') url = '/Notes/edit';
    	  else url = '/Notes/add';
		  	jQuery.ajax({
		      url: url,
		      method: 'POST',
		      dataType: 'json',
		      data: $('#note-form').serialize()
		  }).done(function (response) {
  				if (response.success) {
  				  if (dialog_callback !== null) {
  				    dialog_callback();
  				    dialog_callback = null;
  				    $(dialogbox).dialog( "close" );
  				  }
  				}
  				alert(response.msg);		      
		  }).fail(function () {
  		    alert('Cannot save changes, please try again later');     
		      
		  });    		
    		
				$( this ).dialog( "close" );
    	}
    }
		,	open:		function() {
	
		}
					
		});		

  	$('#empsel').multiselect({
      selectedList: 10,
      noneSelectedText: 'Select employee/ oncall list',
      minWidth: 300
    });
    
    if (localStorage.getItem("recentsearch")) {
      recentsearch = JSON.parse(localStorage.getItem("recentsearch"));
      for (var i=0; i< recentsearch.length; i++) {
        $('#recentsel ul').prepend('<li><a href="#" onclick="editDidNumber(\''+recentsearch[i].id+'\', \''+recentsearch[i].value+'\');return false;">'+recentsearch[i].value+'</a></li>');    
      }
    }
    
    if (localStorage.getItem('oa_font_size')) {
      var the_font_size = localStorage.getItem('oa_font_size');
    }
    else the_font_size = '11px';
      
    if (localStorage.getItem('oa_sidebar') && localStorage.getItem('oa_sidebar') == 'expanded') {
      $('#sidebar_expander').trigger('click');
    }
            
    document.body.style.fontSize = the_font_size;
    document.onkeydown = function (e) {
      //if (e.keyIdentifier == 'U+0008' || e.keyIdentifier == 'Backspace') {
      /*JRW disable for now
      if (e.which === 13 && $(e.target).is(".jqte_editor")) {
        e.preventDefault();
      }*/
      // prevent backspace unless you are within input field     
      if (e.which === 8 && !$(e.target).is("input, textarea, .jqte_editor, .editable, .ceditable")) {
        e.preventDefault();
      }     
      else if ((e.which || e.keyCode) == 116) { // check for screen refresh
        
        user_confirm('Are you sure you want to refresh the screen?  You might lose your call or any unsaved changes', function() {
          postJson('/Users/leave_break', {'msg': '', 'break_id': break_id}, function() {
            jQuery.ajax('/Users/refresh_browser').done(function() {
                location.reload()
            });           
          }); 			
        

        },  function() {
            return false;
        });
        return false;
      }
      return true;
    };    
    



    $('#callBox .actbtn').button();
    
    $('#my_extension').html(localStorage.stationId);
    $('#screen').hide();    


  $( document ).tooltip({
    open: function (event, ui) {
        setTimeout(function () {
            $(ui.tooltip).hide();
        }, 2000);
    }
  });	
  
  $('#test_time').datetimepicker({
    timeFormat: "HH:mm:ss",
    dateFormat: "yy-mm-dd",
    controlType: 'select',
    stepHour: 1,
	  stepMinute: 5
  });    
  $('.timepicker').timepicker({'step': 5});
  
  // check to see if operator needs to acknowledge bulletin messages
  checkRequiredMsgs();
  
  $('input[type=button]').prop('disabled', false);

  $('input.didbtn ').prop('disabled', false);

  $('.didbtns').hide();
  $(".find_did_sel2").select2("enable", true);
  
  // don't allow return key in editable elements
  $('.editable').keydown(function(event) {
    if(event.keyCode == 13) return false;
  });
  



  // Make jQuery :contains Case-Insensitive
  $.expr[":"].contains = $.expr.createPseudo(function(arg) {
      return function( elem ) {
          return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
      };
  });
  
  // load my recent calls
  $('#recentmsg').load('/CallLogs/recent/'+myId);  
  
  // style active tabs
  $('.subtabs a').on('click', function(){
    $(this).parent('li').siblings('li').removeClass('active');
    $(this).parent('li').addClass('active');
  });
  
  $('#save_msg').on('click', function() {
    captureOperatorInstructions();
		var myform = $('#instructions form');
  	disableUnusedPrompts();
    var formdata = getFormData(myform);
    enableUnusedPrompts(); 
    
    $('#operatorScreen .ct, #operatorScreen .uprompt').addClass('disable_edits');
    $('#save_msg, #cancel_msg').hide();
    $('#edit_msg').show();    
	    
	    $.ajax({
	        url: '/Messages/save_prompts/'+callId,
	        type: 'POST',
	        dataType: 'json',
	        data: 'new_ct_title=' + $('#operatorScreen li.ct_sel').attr('ctitle') + '&old_ct=&new_ct=' + $('#operatorScreen li.ct_sel').attr('cid') + '&new_schedule_id='+ $('#operatorScreen li.ct_sel').attr('sid') + '&' + formdata
	    })
	    .done(function(data) {    
	      if (data.success) {
				 	createToast('info', data.msg);  				
        }
			 	else alert(data.msg);	        	
	
	    });
    if ($('#msg_dispatch').is(':hidden'))  {
    	closeOperatorScreen();
    }
  });

  $('#edit_msg').on('click', function() {
    $('#msg_dispatch button').prop('disabled', true);
    $('#save_msg, #cancel_msg').show();
    $('#edit_msg').hide();
    $('#operatorScreen .ct, #operatorScreen .uprompt').removeClass('disable_edits');
    if (!$('#msg_dispatch').is(':hidden')) $('#msg_dispatch button').prop('disabled', true);
  
  });
  
  $('#cancel_msg').on('click', function() {
    $('.ct, .uprompt').addClass('disable_edits');
    $('#save_msg, #cancel_msg').hide();
    $('#edit_msg').show();
    if ($('#msg_dispatch').is(':hidden')) {
    	closeOperatorScreen();
    }
    else $('#msg_dispatch button').prop('disabled', false);


  });
  
  
});