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
$this->Paginator->options(array(
    'update' => '#did-content' ,
    'evalScripts' => true
));
?>
<div class="msgdiv" id="msgdiv" style="height: 100%; width: 100%;">

<div id="msgindex" class="messages index ui-layout-center">
  <div class="panel-content tblheader">
  <?php 
  if (!empty($Messages[0]) && !empty($global_options['timezone'][$Messages[0]['DidNumber2']['timezone']])) $tz = ' - ' . $global_options['timezone'][$Messages[0]['DidNumber2']['timezone']];
  else $tz = '';
  ?>
  <form>
<h1>Messages <?php echo $tz; ?></h1>
<b>Msg#:</b> &nbsp;&nbsp;<input type="text" id="mmsg_id" size="8" name="data[Misc][message_id]" value="">&nbsp;&nbsp;&nbsp;&nbsp;


								<input type="checkbox" name="data[Search][m_type][]" value="delivered" <?php if ($search_delivered) echo 'checked'; ?>> Delivered &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="data[Search][m_type][]" id="mchk_undelivered" value="undelivered" <?php if ($search_undelivered) echo 'checked'; ?>> Undelivered &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="data[Search][m_type][]" id="mchk_unaudited" value="unaudited" <?php if ($search_audited) echo 'checked'; ?>> Not audited &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="data[Search][m_type][]" id="mchk_hold" value="hold" <?php if ($search_hold) echo 'checked'; ?>> Save Hold&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="data[Search][m_type][]" value="minder" <?php if ($search_minder) echo 'checked'; ?>> Minder&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Start: <input type="text" name="data[Search][m_start_date]" size="12" value="<?php echo $start_date; ?>" class="datepicker">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								End: <input type="text" name="data[Search][m_end_date]" size="12" value="<?php echo $end_date; ?>" class="datepicker">&nbsp;&nbsp;<input type="submit" value="Go" onclick="loadPagePost(null, '/Messages/index/<?php echo $did_id; ?>' , 'did-content', $(this).parent('form').serialize(), null);return false;">
								</form>
								<div style="clear:both"></div>
	<?php
	echo $this->Element('paging');
	?>
	<br>
	<i class="fa fa-print"></i> <a href="#" id="printbtn">print</a> | <i class="fa fa-send"></i> <a href="#" id="sendbtn">send</a>	
  </div>
	<DIV class="tableWrapper tblheader" >

	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">

			<COL class="col30">
			<COL class="col80">
			<COL class="col110">
			<COL class="col160">
			<COL class="col170">	
			<COL class="col60">	
			<COL class="col60">	
			<COL class="col60">	
			<COL class="col60">	
	<tr>
			<th class="col30"><input type="checkbox" onclick="if (this.checked) $('#msgdiv .selector').prop('checked', true); else $('#msgdiv .selector').prop('checked', false); "></th>
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
  <form name="msgselect" id="msgselect">
	<table cellpadding="0" cellspacing="0" class="gentbl"  width="100%">

			<COL class="col30">
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
	<tr >
		<td class="col30" align="center"><input type="checkbox" value="<?php echo h($Message['Message']['id']); ?>" class="selector" name="data[Misc][selector][]"></td>
		<td class="col80 msgsel" align="center" onclick="currentIndex = '<?php echo $k; ?>';  loadMessage('<?php echo $Message['Message']['id']; ?>','<?php echo $Message['Message']['did_id']; ?>', null, true, 'current=<?php echo ($k+1); ?>&total=<?php echo $total_cnt;?>'); return false;"><?php echo h($Message['Message']['id']); ?></td>
		<td class="col110 msgsel" align="center" onclick="currentIndex = '<?php echo $k; ?>';  loadMessage('<?php echo $Message['Message']['id']; ?>','<?php echo $Message['Message']['did_id']; ?>', null, true, 'current=<?php echo ($k+1); ?>&total=<?php echo $total_cnt;?>'); return false;"><?php echo h($Message['User']['username']); ?></td>
		<td class="col160 msgsel" align="center" onclick="currentIndex = '<?php echo $k; ?>';  loadMessage('<?php echo $Message['Message']['id']; ?>','<?php echo $Message['Message']['did_id']; ?>', null, true, 'current=<?php echo ($k+1); ?>&total=<?php echo $total_cnt;?>'); return false;"><?php echo h($Message[0]['createdf']); ?></td>
		<td class="col170 msgsel" onclick="currentIndex = '<?php echo $k; ?>';  loadMessage('<?php echo $Message['Message']['id']; ?>','<?php echo $Message['Message']['did_id']; ?>', null, true, 'current=<?php echo ($k+1); ?>&total=<?php echo $total_cnt;?>'); return false;"><?php echo h($Message['Message']['calltype']); ?></td>
		<td class="col60 msgsel"  align="center" onclick="currentIndex = '<?php echo $k; ?>';  loadMessage('<?php echo $Message['Message']['id']; ?>','<?php echo $Message['Message']['did_id']; ?>', null, true, 'current=<?php echo ($k+1); ?>&total=<?php echo $total_cnt;?>'); return false;"><?php if ($Message['Message']['delivered']) echo 'Yes'; else echo ''; ?></td>
		<td class="col60 msgsel"  align="center" onclick="currentIndex = '<?php echo $k; ?>';  loadMessage('<?php echo $Message['Message']['id']; ?>','<?php echo $Message['Message']['did_id']; ?>', null, true, 'current=<?php echo ($k+1); ?>&total=<?php echo $total_cnt;?>'); return false;"><?php if ($Message['Message']['minder']) echo 'Yes'; else echo ''; ?></td>
		<td class="col60 msgsel"  align="center" onclick="currentIndex = '<?php echo $k; ?>';  loadMessage('<?php echo $Message['Message']['id']; ?>','<?php echo $Message['Message']['did_id']; ?>', null, true, 'current=<?php echo ($k+1); ?>&total=<?php echo $total_cnt;?>'); return false;"><?php 
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
	</form>
	</div>
	</div>

</div>

</div>
<div id="sendDialog" class="is_hidden">
<center><br><br>
<form id="recipient_form">
Send to:<br>
<select name="data[Misc][recipient][]" id="recipients" multiple style="width:350px;">
<?php
$first_fax = true;
$first_email = true;
$first = true;
foreach ($recipients as $k=>$r) {
	if ($first_email && $r['EmployeesContact']['contact_type'] == CONTACT_EMAIL) {
		$first_email = false;
		if (!$first) echo '</optgroup>';
		echo ' <optgroup label="Email addresses">';
	}
	if ($first_fax && $r['EmployeesContact']['contact_type'] == CONTACT_FAX) {
		$first_fax = false;
		if (!$first) echo '</optgroup>';
		echo ' <optgroup label="Fax numbers">';
	}
	echo '<option value="'. $r['EmployeesContact']['contact'].'">' .$r['Employee']['name'] . '  -  ' . $r['EmployeesContact']['contact']. '</option>';
	$first = false;
}
?></optgroup>
</select> &nbsp;
</center>
</form>

</div>
<?php
echo $this->Js->writeBuffer();
?>
<script>
msgListLength = <?php echo count($Messages); ?>;
msgArray = <?php echo json_encode($msg_array); ?>;
msgArrayById = <?php echo json_encode($msg_array_by_id); ?>;



$(document).ready(function() {
  
  function reloadPage() {
        if ($('#msgdiv').is(':visible')) {
          if (!$("#operatorScreen").dialog('isOpen') && !$("#msgDialogWin").dialog('isOpen') && !sendDialog.dialog('isOpen')) {
            <?php
            if ($sort) {
              ?>
            var url = '/Messages/index/' + $('#find_did').val() + '/sort:<?php echo $sort; ?>/direction:<?php echo $sort_dir; ?>';
              <?php
            }
            else {
              ?>
            var url = '/Messages/index/' + $('#find_did').val();
              <?php
            }
            ?>
            loadPage(null,url , 'did-content');
          }
          else {
            if (msgCheckTimer) clearInterval(msgCheckTimer);
            msgCheckTimer = setTimeout(reloadPage, settings['msg_update_seconds'] * 1000);
          }
        }
        else {
          if (msgCheckTimer) clearInterval(msgCheckTimer);
        }        
  
  }  
  

	$('#recipients').select2();  // create jQuery style select box
	var sendDialog;

	$('#sendbtn').on('click', function() {
	  if ($('#msgdiv input.selector:checked').length <1) {
	    alert('You must select at least 1 message to send');
	    return false;
	  }
	  else {
    	sendDialog = $( "#sendDialog" ).dialog({
    		autoOpen: true,
    		height: 300,
    		width: 550,
    		modal: true,
    		buttons: {
    		'Send': function() {
          postJson('/MessagesSummary/send/<?php echo $did_id; ?>/', $('#recipient_form').serialize() + '&' + $('#msgselect').serialize() + '&did_id=' + '<?php echo $did_id; ?>',function () {sendDialog.dialog('close');});						
    		},
    		'Cancel': function() {
    			sendDialog.dialog( "close" );
    		}},
    		close: function() {
    			sendDialog.dialog('destroy');
    		}
    	});	  	
		}
		return false;
	});

	$('#printbtn').on('click', function() {
    var w = window.open('about:blank', '_blank','width=1024,height=700,scrollbars=1,resizable=1,location=no,menubar=yes,toolbar=yes'); 
	  if ($('#msgdiv input.selector:checked').length <1) {
	    alert('You must select at least 1 message to send');
	    return false;
	  }
	  else {
	    $.ajax({
	        url: '/MessagesSummary/printmsg',
	        type: 'post',
	        dataType: 'html' ,
	        data: $('#recipient_form').serialize() + '&' + $('#msgselect').serialize() + '&did_id=' + '<?php echo $did_id; ?>'
			}).done(function(html) {    
            w.document.write(html);
            w.document.close();
            w.print();
			}).fail(ajaxCallFailure);   
		}
		return false;
	});	
	

		
	$('#msgindex .datepicker').datepicker({
  	dateFormat: 'yy-mm-dd',
    changeMonth: true,
    changeYear: true,
    showButtonPanel: true    
	});	
		  
  // manually compensate width of the header with the scrollbar width to header lines up with data rows
  $('.tblheader').css('margin-right', scrollbarWidth);  
  
  // create the layout - with data-table wrapper as the layout-content element  
  $('#msgdiv').layout({
    center__contentSelector: '#msgdiv div.data',
    resizeWhileDragging: true
  });
/*    if (msgCheckTimer) clearInterval(msgCheckTimer);
    msgCheckTimer = setTimeout(reloadPage,settings['msg_update_seconds'] * 1000);	  */
    
});
</script>
