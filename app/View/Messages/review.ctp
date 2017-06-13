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
$this->extend('/Common/view');
echo $this->Html->script('app');
/*$this->Paginator->options(array(
		'update' => '#msg_review' ,
		'evalScripts' => true
));*/
?>
<script>
	var recentsearch = [];

	var scrollbarWidth = getScrollbarWidth();
	var settings = <?php echo json_encode($settings) ?>;
	
	var msgDialogWinCallback = null ;
	var dialogWinCallback = null ;
	var msgWinLayout;
	var lastPopAttempt = null;

	var callId = null;
	var currentCall = null;
	var currentScheduleId = null;
	var currentInstructions;
	var pause_agent = false;
	var userprompts = ['Caller refused', 'N/A', 'Individual', 'Caller hung up'];
	var oncallTabs = null;
	var socket = null;
	var clockInterval = null;

	var dialogLayout;
	var msgWinLayout; 
	var dialogLayout_settings = {
			zIndex:				0		// HANDLE BUG IN CHROME - required if using 'modal' (background mask)
		,	resizeWithWindow:	false	// resizes with the dialog, not the window
		,	spacing_open:		6
		,	spacing_closed:		6
		, closable: false
		,	north__minSize:			120
		,	north__maxSize:			160
		,	south__size:			35 
		, west__size: 150
		, east__size: 200
		, east__children: {
			north__size:			300 
		}
		,	west__minSize:		150 
		,	east__minSize:		100 
		,	west__maxSize:		250 
		//,	south__size:		20 
		//, south__initClosed: true
		//,	applyDefaultStyles:		true // DEBUGGING
		}
	;   
	var msgClockInterval;
	var agentStatus = 'Off';
	var manualPop = false;

	var CONTACT_PHONE = '<?php echo CONTACT_PHONE ?>';
	var CONTACT_CELL = '<?php echo CONTACT_CELL ?>';
	var CONTACT_TEXT = '<?php echo CONTACT_TEXT ?>';
	var CONTACT_EMAIL = '<?php echo CONTACT_EMAIL ?>';
	var CONTACT_VMAIL = '<?php echo CONTACT_VMAIL ?>';
	var CONTACT_WEB = '<?php echo CONTACT_WEB ?>';
	var CONTACT_PAGER = '<?php echo CONTACT_PAGER ?>';
	var CONTACT_FAX = '<?php echo CONTACT_FAX ?>';
	var BUTTON_DISPATCH = '<?php echo BUTTON_DISPATCH ?>';
	var BUTTON_DELIVER = '<?php echo BUTTON_DELIVER ?>';
	var FAX_EMAIL = '<?php echo FAX_EMAIL; ?>';
	var CALLOUT_SUCCESS = '<?php echo CALLOUT_SUCCESS; ?>';
	var scrollbarWidth;
	scrollbarWidth = getScrollbarWidth();
	var checked_in = false;
	var error403_count = 0;
			
	var EVENT_MINDERCLICK = '<?php echo EVENT_MINDERCLICK; ?>';
	var EVENT_DIALOUT = '<?php echo EVENT_DIALOUT; ?>';
	var EVENT_PATCH = '<?php echo EVENT_PATCH; ?>';
	var EVENT_CALLTYPE = '<?php echo EVENT_CALLTYPE; ?>';
	var EVENT_DEBUG	 = '<?php echo EVENT_DEBUG; ?>';
	var EVENT_REPOP	 = '<?php echo EVENT_REPOP; ?>';
	var EVENT_FILL_PROMPT	 = '<?php echo EVENT_FILL_PROMPT; ?>';
	var break_id = '';
	var buttonClass = new Object();
	
	buttonClass[CONTACT_FAX] = 'c_fax';
	buttonClass[CONTACT_PHONE] = 'c_txf';
	buttonClass[CONTACT_CELL] = 'c_cell';
	buttonClass[CONTACT_TEXT] = 'c_text';
	buttonClass[CONTACT_EMAIL] = 'c_email';
	buttonClass[CONTACT_VMAIL] = 'c_vmail';
	buttonClass[CONTACT_WEB] = 'c_web';		

	var required = {};
	
	var operators = <?php echo json_encode($operators)?>;	  
	var myRole = '<?php echo AuthComponent::user('role'); ?>';
	var myName = "<?php echo trim(AuthComponent::user('firstname')) . ' ' . trim(AuthComponent::user('lastname')); ?>";
	var myFirstName = "<?php echo AuthComponent::user('firstname'); ?>";
	var myId = '<?php echo AuthComponent::user('id'); ?>';
	var myUsername = "<?php echo AuthComponent::user('username'); ?>";
	
<?php foreach($global_options['actions'] as $k => $a) {
	?>
	var <?php echo $a['type']?> = '<?php echo $k; ?>';
	<?php
}  
?>

 
</script>

<div class="msgdiv" id="msgdiv" style="height: 100%; width: 100%;">

<div id="msgindex" class="messages index ">
	<div class="panel-content tblheader">
	<?php 
	if (!empty($Messages)) {
	    if (!empty($global_options['timezone'][$Messages[0]['DidNumber2']['timezone']])) {
    	    $tz = ' - ' . $global_options['timezone'][$Messages[0]['DidNumber2']['timezone']];
    	}
    	else {
	        $tz = '';
	    }
	}
	else {
	    $tz = '';
	}
	?>
	<form method="POST" action="/Messages/review/<?php echo $did_id; ?>">
<h1>Messages <?php echo $tz; ?></h1>
<b>Msg#:</b> &nbsp;&nbsp;<input type="text" id="msg_id" size="8" name="data[Misc][message_id]" value="">&nbsp;&nbsp;&nbsp;&nbsp;


								<input type="checkbox" name="data[Search][m_type][]" value="delivered" <?php if ($search_delivered) echo 'checked'; ?>> Delivered &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="data[Search][m_type][]" id="chk_undelivered" value="undelivered" <?php if ($search_undelivered) echo 'checked'; ?>> Undelivered &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="data[Search][m_type][]" id="chk_unaudited" value="unaudited" <?php if ($search_audited) echo 'checked'; ?>> Not audited &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="data[Search][m_type][]" id="chk_hold" value="hold" <?php if ($search_hold) echo 'checked'; ?>> Save Hold&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="data[Search][m_type][]" value="minder" <?php if ($search_minder) echo 'checked'; ?>> Minder<br><br>
								<b>Start:</b> <input type="text" name="data[Search][m_start_date]" size="12" value="<?php echo $start_date; ?>" class="datepicker">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<b>End:</b> <input type="text" name="data[Search][m_end_date]" size="12" value="<?php echo $end_date; ?>" class="datepicker">&nbsp;&nbsp;<input type="submit" value="Go">
								</form>
	<?php
	echo $this->Element('paging');
	?>
	</div>
	<DIV class="tableWrapper tblheader" >
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">

			<COL class="col80">
			<COL class="col110">
			<COL class="col160">
			<COL class="col170">	
			<COL class="col60">	
			<COL class="col60">	
			<COL class="col60">	
			<COL class="col60">	
	<tr>
			<th class="col80"><?php echo $this->Paginator->sort('id', 'ID'); ?></th>
			<th class="col110" ><?php echo $this->Paginator->sort('operator'); ?></th>
			<th class="col160" ><?php echo $this->Paginator->sort('created'); ?></th>
			<th class="col170" align="left" ><?php echo $this->Paginator->sort('calltype'); ?></th>
			<th class="col60" ><?php echo $this->Paginator->sort('delivered'); ?></th>
			<th class="col60" ><?php echo $this->Paginator->sort('minder'); ?></th>
			<th class="col60" ><?php echo $this->Paginator->sort('hold'); ?></th>
			<th class="col60" ><?php echo $this->Paginator->sort('audited'); ?></th>
	</tr>
	</table>
	</div>
	<div class="data tableWrapper"><div class="innerWrapper">
	<table cellpadding="0" cellspacing="0" class="gentbl"  width="100%">

			<COL class="col80">
			<COL class="col110">
			<COL class="col160">
			<COL class="col170">	
			<COL class="col60">	
			<COL class="col60">	
			<COL class="col60">	
			<COL class="col60">	
	<?php
	$total_cnt = count($Messages);
	$msg_array = array();
	$msg_array_by_id = array();	
	foreach ($Messages as $k => $Message){ 
		 if ($k==0) $prev = '';
		 else $prev = $Messages[$k-1]['Message']['id'];
		 
		 if ($k == (count($Messages)-1)) $next = '';
		 else $next = $Messages[$k+1]['Message']['id'];
		 $msg_array[$k] = $Message['Message']['id'];
				
		?>
	<tr  onclick="currentIndex = '<?php echo $k; ?>'; loadMessage('<?php echo $Message['Message']['id']; ?>','<?php echo $Message['Message']['did_id']; ?>', null, true, 'current=<?php echo ($k+1); ?>&total=<?php echo $total_cnt;?>'); return false;">
		<td class="col80" align="center"><?php echo h($Message['Message']['id']); ?></td>
		<td class="col110" align="center"><?php echo h($Message['User']['username']); ?></td>
		<td class="col160" align="center"><?php echo h($Message[0]['createdf']); ?></td>
		<td class="col170"><?php echo h($Message['Message']['calltype']); ?></td>
		<td class="col60"  align="center"><?php if ($Message['Message']['delivered']) echo 'Yes'; else echo ''; ?></td>
		<td class="col60"  align="center"><?php if ($Message['Message']['minder']) echo 'Yes'; else echo ''; ?></td>
		<td class="col60"  align="center"><?php 
		if ($Message['Message']['hold'] == '1') echo 'Yes'; 
		else if ($Message['Message']['hold'] == '2') echo 'Until<br>'. $Message[0]['hold_until_f']; 
		else echo ''; ?>
		</td>
		<td class="col60"  align="center"><?php 
		if ($Message['Message']['audited'] == '1') echo 'Yes'; 
		else echo 'No'; ?>
		</td>
	</tr>
<?php } ?>
	</table>
	</div>
	</div>

</div>

</div>
		<div id="noteDialog" class="vnform">
		</div>  
		<div id="dialogWin">
		</div>    
		<div id="dialogWinSave">
		</div>     
		<div id="msgDialogWin">
		</div>
		<div id="dialog-confirm" title="Please confirm">
			<div id="confirm_message"></div>
		</div>  
		<div id="mistakeDialog" class="vnform">
		</div>    
		<div id="dialog-alert" title="Info">
			<div id="alert_message"></div>
		</div>   


		<div id="operatorScreen"> 
			<div class="ui-layout-north"> 
				<div class="ui-layout-content ">    
					<div id="screen_title">
					</div>
									<div id="account_info">
										<input type="hidden" name="test_time_save" id="test_time_save">    

										<!--<div class="textbox">
											Phone Number: <input type="text" name="did" id="a_did" value="" size="12" disabled>
										</div>   -->   
										<div class="textbox">
											<i class="fa fa-hourglass-2"></i> Call Timer: <input type="text" value="" id="call_time" size="4" disabled>
										</div>     
										<div class="textbox">
											<i class="fa fa-clock-o"></i> Local Time: <input type="text" id="local_time" value="" size="26" disabled> <i><span id="local_tz"></span></i>
										</div>
										<!--<div class="textbox">
											<input type="text" id="office_status" value="" size="10" disabled>
										</div>-->
										<div class="textbox test_box">
											<i class="fa fa-gear"></i> Test: <input type="text" style="width: 0; height: 0; top: -1000px; position: absolute;"><input type="text" style="width: 100px;" name="test_time" id="test_time" value="" title="View the operator screen for a different date/time by entering it here" >&nbsp;<input type="submit" value="GO" id="opscreen_test">
										</div>

										<div style="clear:both;"></div>         
									</div>
									<div id="answer_phrase">
									</div>    
				</div>
			</div>
			<div class="ui-layout-west"> 
				<!--<div class="pane-header ui-state-active">West Header</div>-->
				<div class="ui-layout-content sleft" style="position:relative;">
					<div id="op_notes" class="op_notes">
					</div>          
					<div id="op_notes_left" class="op_notes">
					</div>          
					<div id="calltypes">
					</div>
					
					<?php echo $this->element('button_options'); ?>					
				</div>
			</div> 
		
			<div class="ui-layout-center ui-corner-all"> 
				<!--<div class="pane-header ui-state-active">Center Header</div>-->
					<div class="ui-layout-content scenter" id="opscreen_main">
					    <ul>
    					    <li><a href="#tab-instructions">Instructions</a></li>
    					    <!--<li><a href="#tab-call-events">Call Events</a></li>
    					    <li><a href="#tab-deliveries">Deliveries</a></li>-->
					    </ul>
					    <div id="tab-instructions">
    						<div id="op_notes_center" class="op_notes">
    						</div>          
    						<div id="instructions" class="panel-content">
    						</div>
    						<div id="sandbox" class="hide"></div>
    					</div>
    					<!--<div id="tab-call-events">
    					</div>
    					<div id="tab-deliveries">
    					</div>-->

					</div>
					<div id="cb_emp" class="footer">
						<div id="show_dispatch" style="float:right; width: 100px;">
						<?php if (1) {
							?>
							<input type="checkbox" id="show_disp" value="1"> Show Disp.
							<?php
						}
						?>
						</div>					
						<div id="cb_emppicker">
						</div> 		      
						<div id="cb_empcontacts">
						</div>

					</div>  		      
			</div> 
			<div class="ui-layout-east ui-corner-all sright"> 
				<div class="ui-layout-center ui-corner-all"> 
					<div class="ui-layout-content" id="oncall_lists">
					</div>
					<div class="footer" id="oncall_footer"></div>
				</div>
				<div class="ui-layout-north ui-corner-all"> 
					<div id="op_notes_right" class="op_notes">
					</div>     	  
					<div class="ui-layout-content" id="company_content">
						<div id="acct_type" class="cinfo highlight">
						</div>
						<div id="acct_addr" class="cinfo">
						</div>
						<div id="acct_hours" class="cinfo highlight">
						</div>
						<div id="acct_info" class="cinfo">
						</div>
						<div style="clear:both;"></div>
						<div id="acct_files" class="cinfo2">
						</div>
					</div>
				</div>
			</div>   
			<div class="ui-layout-south ui-widget-header ui-corner-all" style="text-align:right;"> 
	      <button id="edit_msg" class="msg_edit is_hidden" style="float:left;">Edit</button>
	      <button id="save_msg" class="msg_edit is_hidden" style="float:left;">Save</button>
	      <button id="cancel_msg" class="msg_edit is_hidden" style="float:left;">Cancel</button>
        <div id="msg_dispatch">
	<?php 
	if ($this->Permissions->isAuthorized('MessagesReviewNote',$permissions)) {
	?>
  				<button id="opscreen_addnote">Add Note</button>
	<?php
	} 
	?>
  				<button id="opscreen_msgreview">Msg Review</button>
  				<button id="opscreen_addevent">Add Call Event</button>
  				<button id="opscreen_deliver">Mark Delivered</button>          
  				<button id="opscreen_dispatch">Dispatch</button>
  
  
  				
  				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="cancel_reason" id="cancel_reason_sel"><option value="" >Select reason
  				<?php
  				foreach ($global_options['cancel_reasons'] as $k=>$v) {
  					?>
  					<option value="<?php echo $k;?>"><?php echo $k;?></option>
  				<?php
  				}
  				?>
  				</select>
  				<button id="cancel_button" >Cancel</button>
				</div>

			</div>
	
		</div> 
		
						
<?php
//echo $this->Js->writeBuffer();
?>
<script>
msgListLength = <?php echo count($Messages); ?>;
msgArray = <?php echo json_encode($msg_array); ?>;
msgArrayById = <?php echo json_encode($msg_array_by_id); ?>;

$(document).ready(function() {
		$('#msgindex .datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true      
		});	
		$('#msgDialogWin').dialog({
			title:		'Message Review'
		,	width:		Math.floor($(window).width()  * .90)
		,	height:		Math.floor($(window).height() * .80)
		, dialogClass: 'no-close'		
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
		$('#msgDialogWin').siblings('.ui-dialog-titlebar').hide();

		$('#dialogWin').dialog({
			title:		'Generic Window'
		,	width:		Math.floor($(window).width()  * .90)
		,	height:		Math.floor($(window).height() * .90)
		, dialogClass: 'no-close'		
		, autoOpen: false
		, closeOnEscape: false
		,	modal: false
		, close: function() {
			$('#dialogWin').html('');
		}
		, buttons: {

				Close: function() {
					$( this ).dialog( "close" );
					if (dialogWinCallback) dialogWinCallback();
					dialogWinCallback=null;
					
				}
			}
		});

		$("#mistakeDialog").dialog({ 
			title:		'Mistakes'
		,	width:		780
		,	height:		400
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
		,	height:		650
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
		$('.tblheader').css('margin-right', scrollbarWidth);  
	// create the layout - with data-table wrapper as the layout-content element  
	/*$('#msgdiv').layout({
		center__contentSelector: '#msgdiv div.data',
		resizeWhileDragging: true
	});*/
	var resizeTimer;

	$(window).on('resize orientationChange', function(e) {
		<?php 
		// check target since jquery ui dialog resize will trigger the event also 
		?>
		if (e.target == window && !$('#operatorScreen').dialog('isOpen') ) {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(function() {
				$('#operatorScreen').dialog('option', 'width', Math.floor($(window).width()  * .95));
				$('#operatorScreen').dialog('option', 'height', Math.floor($(window).height()  * .95));
				if (dialogLayout) dialogLayout.resizeAll();
				$('#msgDialogWin').dialog('option', 'width', Math.floor($(window).width()  * .94));
				$('#msgDialogWin').dialog('option', 'height', Math.floor($(window).height()  * .94));
				
			}, 250);
		}
	});    
	
  $('#operatorScreen').dialog('option', 'width', Math.floor($(window).width()  * .95));
	$('#operatorScreen').dialog('option', 'height', Math.floor($(window).height()  * .95));	
	console.log(Math.floor($(window).height()  * .95)); console.log($(window).height());
});
</script>
