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
    'update' => '#did-content',
    'evalScripts' => true
));

	function phoneFormat($num) {
    if ($num) {
      $num = preg_replace('/[^0-9]/', '', $num);
      if (strlen($num) == 11 && substr($num, 0, 1) == '1') $num = substr($num, -10);
      if (strlen($num) == 10)
        $num2 =  '(' . substr($num, 0, 3) . ') ' . substr($num, 3, 3) . '-' . substr($num, 6); 
      else $num2 = $num . ' <span class="mistake">?</span>';
    }
    else $num2 = '';	  
    return $num2;
	}
?>

<div class="MessagesSummary index ">
  <div class="panel-content tblheader fg_grey">
	<h2><i class="fa <?php echo $global_options['icons']['setup']; ?>"></i> <?php echo __('Messages Summary') . '<i> - ' . $timezone . '</i>'; ?></h2>
<a href="#" onclick="addMsgSchedule(); return false;"><i class="fa fa-plus"></i> Add new schedule</a>
</div>
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%" >
	<tr>
			<th width="50" align="left">ID</th>
			<th width="40" align="center">Active</th>
			<th width="120" align="center">Created</th>
			<th width="200" align="left"><?php echo $this->Paginator->sort('destination'); ?></th>
			<th width="260" align="left"><?php echo $this->Paginator->sort('start_time'); ?></th>
			<th width="80"><?php echo $this->Paginator->sort('msg_type'); ?></th>
			<th width="70"><?php echo $this->Paginator->sort('no_message', 'Send \'No-Message\''); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	if (sizeof($MessagesSummary)) {
	foreach ($MessagesSummary as $m): 
	  $destinations = array();
	  
	  if ($m['MessagesSummary']['destination_email']) $destinations = $destinations + explode(';', $m['MessagesSummary']['destination_email']);
	  if ($m['MessagesSummary']['destination_fax']) $destinations[] = phoneFormat($m['MessagesSummary']['destination_fax']);
	  if ($m['MessagesSummary']['employee_contact_ids']) {
	    $contacts = explode(',', $m['MessagesSummary']['employee_contact_ids']);
	    foreach ($contacts as $c) {
	      if (isset($contact_ids[$c])) {
	        if ($contact_ids[$c]['c']['contact_type'] == CONTACT_FAX) $contact = $this->element('formatPhone', array('num' => $contact_ids[$c]['c']['contact']));
	        else $contact = $contact_ids[$c]['c']['contact'];
	        $destinations[] = implode(', ', explode(';', $contact)) . ' - ' . $contact_ids[$c]['c']['label'];
	      }
	      else $destinations[] = '<i>None</i>';
	    }
	  }
	?>
	<tr>
		<td onclick="editMsgSchedule('<?php echo $m['MessagesSummary']['id']; ?>'); return false;"><?php echo  $m['MessagesSummary']['id']?></td>
		<td align="center"  onclick="editMsgSchedule('<?php echo $m['MessagesSummary']['id']; ?>'); return false;"><?php echo  $m['MessagesSummary']['active']? 'Yes': 'No'; ?></td>
		<td align="center" onclick="editMsgSchedule('<?php echo $m['MessagesSummary']['id']; ?>'); return false;"><?php echo  $m['MessagesSummary']['created_f']?></td>
		<td onclick="editMsgSchedule('<?php echo $m['MessagesSummary']['id']; ?>'); return false;"><?php echo implode('<br>', $destinations); ?></td>
		<td onclick="editMsgSchedule('<?php echo $m['MessagesSummary']['id']; ?>'); return false;"><?php 
		  $time_range = '';
		  if ($m['MessagesSummary']['tx_interval']) $time_range = ", every {$m['MessagesSummary']['tx_interval']} minutes";
		  if ($m['MessagesSummary']['all_day']) $time_range .= ", all day";
		  else {
  		  if ($m['MessagesSummary']['start_time_f'] && $m['MessagesSummary']['end_time_f']) $time_range .= ", from {$m['MessagesSummary']['start_time_f']} to {$m['MessagesSummary']['end_time_f']}";
  		  if ($m['MessagesSummary']['send_time_f']) $time_range = ", at " . $m['MessagesSummary']['send_time_f'];
		  }
		  echo implode(', ', $m['MessagesSummary']['day_range']) . $time_range;
		?>
		
		</td>
		<td align="center" onclick="editMsgSchedule('<?php echo $m['MessagesSummary']['id']; ?>'); return false;"><?php 
		  if ($m['MessagesSummary']['msg_type'] == '1') echo 'Undelivered'; 
		  else if ($m['MessagesSummary']['msg_type'] == '2') echo 'All';  
		?></td>
		<td align="center" onclick="editMsgSchedule('<?php echo $m['MessagesSummary']['id']; ?>'); return false;"><?php echo $m['MessagesSummary']['no_message']? 'Yes': 'No'; ?></td>
		<td class="actions">
			<?php echo '<a href="#" onclick="user_confirm(\'Are you sure you want to delete this entry?\', function(){ deleteSchedule('.$m['MessagesSummary']['id'].'); });"><img title="delete" alt="delete" src="/img/delete.png" width="16" height="16"></a>'; ?>
		</td>
	</tr>
<?php endforeach; 
  }
  else {
    echo '<tr><td colspan="8" align="center">None found, click <a href="#" onclick="addMsgSchedule(); return false;"><i>here</i></a> to create one</td></tr>';
  }
?>
	</table>



</div>

<script type="text/javascript">
  function checkNoMessage(t) {
    if (t.value == '2') $('#no_message_send_time').show();
    else $('#no_message_send_time').hide();
  }
  
  function checkSummaryForm() {
      $('#msgsummary input[type=text]:hidden').val(''); // zero out all unused input
      if ($('input.everyday:checked').length < 1) {
        alert('You must select the days that this schedule is active for');
        return false;
      }
      if ($('#no_message input:checked').length > 0 && $('#no_message_when').val() == '2' && $('#no_message_send_time').val() == '') {
        alert('You must specify the time that the no-message notification is to be sent');
        document.getElementsByName("data[MessagesSummary][no_message_send_time]")[0].focus();
        return false;
      }
      
      if ((document.getElementsByName("data[MessagesSummary][destination_email]")[0].value == '') && (document.getElementsByName("data[MessagesSummary][destination_fax]")[0].value == '') && $('#contact_ids').select2('val').length == 0) {
        alert('You must enter either a destination email or fax number');
        document.getElementsByName("data[MessagesSummary][destination_email]")[0].focus();
        return false;
      } 
      if ((document.getElementsByName("data[MessagesSummary][destination_email]")[0].value != '' ||  document.getElementsByName("data[MessagesSummary][destination_fax]")[0].value != '') && $('#contact_ids').select2('val').length > 0) {
        alert('Please remove the TAS imported destination email/fax before saving this entry');
        document.getElementsByName("data[MessagesSummary][destination_email]")[0].focus();
        return false;
      }           
      if (document.getElementsByName("data[MessagesSummary][tx_interval]")[0].value == '0') {
        if (document.getElementsByName("data[MessagesSummary][send_time]")[0].value == '') {
          alert('You must enter a time');
          document.getElementsByName("data[MessagesSummary][send_time]")[0].focus();
          return false;
        }
      }
      else {
        if (document.getElementsByName("data[MessagesSummary][all_day]")[1].checked === false && (document.getElementsByName("data[MessagesSummary][start_time]")[0].value == '' || document.getElementsByName("data[MessagesSummary][end_time]")[0].value == '')) {
          alert('You must enter a time');
          document.getElementsByName("data[MessagesSummary][start_time]")[0].focus();
          return false;          
        }
      }
      return true;  
  }
  function addMsgSchedule() {
    var url = '/MessagesSummary/add/<?php echo $did_id; ?>';
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'html',
  	      	type: 'GET'
  	      }).done(function(data) {
  					$('#did-detail').html(data);
  					didLayout.center.children.layout1.open('east');
  	      });	
  }
  
  function editMsgSchedule(id) {
    var url = '/MessagesSummary/edit/' + id;
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'html',
  	      	type: 'GET'
  	      }).done(function(data) {
  					$('#did-detail').html(data);
  					didLayout.center.children.layout1.open('east');
  	      });	
  }  
  
  function deleteSchedule(id) {

    var url = '/MessagesSummary/delete/' + id;
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'json',
  	      	type: 'GET'
  	      }).done(function(data) {
            if (data.success) {
    					loadPage(this, '/MessagesSummary/index/<?php echo $did_id; ?>', 'did-content');          
              didLayout.center.children.layout1.close('east')					
            }
            alert(data.msg);
  	      });	
  }
  
  $(function () {
    $('#summary_edits .descr').readmore({
      speed: 7,
      maxHeight: 24
    });
  });
   
</script>
