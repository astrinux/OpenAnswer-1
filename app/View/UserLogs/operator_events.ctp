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
<style>
tr.agent_inactive td {border-top: 3px solid yellow};
</style>

<div class="users view panel-content">
<h2>Operator Events</h2>
<form>
<b>Operator:</b> <input name="data[Search][user_id]" style="width: 180px;"  class="required report_user_sel2" type="hidden" value="<?php echo $this->request->data['Search']['user_id']; ?>"> &nbsp;&nbsp;&nbsp;<b>Ext:</b> <input type="text" name="data[Search][extension]" value="<?php echo $this->request->data['Search']['extension']; ?>" size="5"> &nbsp;&nbsp;&nbsp;<b>Date:</b> <input name="data[Search][report_date]" value="<?php echo $this->request->data['Search']['report_date']; ?>" type="text" class="required datepicker"> &nbsp;&nbsp;&nbsp;Inactivity: <input type="text" name="data[Search][inactive]" value="<?php echo $this->request->data['Search']['inactive']; ?>" size="3"> seconds&nbsp;&nbsp;<input type="submit" value="Go" onclick="submitOperatorEvents(this); return false;">
&nbsp;&nbsp;&nbsp;
  <!--<input type="button" value="Verbose" onclick="$('.verbose').toggle();return false;" />-->
<input type="checkbox" value="1" onclick="if (this.checked) {$('.call_event').hide();} else {$('.call_event').show();}; "> logins/breaks only
</form>
<br>
</div>
<div id="userevents">

<?php
$this->Paginator->options(array('update' => '#report-detail',
    'evalScripts' => true
    ));
?>
  <div class="panel-content tblheader">
  <div class="paging">
    <i>(<?php echo count($log); ?> found)</i>
  </div>
  </div>
<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
	<tr>
		<th width="160" align="left">Created</th>
		<th width="50">Username</th>
		<th width="50">Ext</th>
		<th width="260" align="left">Account</th>
		<th align="300">Event</th>
	</tr>
<?php
$prev = '';
foreach ($log as $l) {
  $class = '';
  if (!empty($prev)) {
    if (($l['0']['created_ts']-$prev['0']['created_ts']) > $inactive_period) {
      $class .= "agent_inactive";
    }
    $prev = $l;
  }
  else $prev = $l;
	if ($l['all_events']['type'] == 'login' || $l['all_events']['log_type'] == USEREVT_LOGIN) $event = 'Logged in';
	else if ($l['all_events']['type'] == 'logout' || $l['all_events']['log_type'] == USEREVT_LOGOUT) $event =  'Logged out';
	else if ($l['all_events']['type'] == 'not_taking_calls'  || $l['all_events']['log_type'] == USEREVT_NOT_TAKING_CALLS) { 
	  $event =  'Not taking calls';
	  $class = 'verbose ';
	}
	else if ($l['all_events']['type'] == 'taking_calls'  || $l['all_events']['log_type'] == USEREVT_TAKING_CALLS) {
	  $event =  'Taking calls';
	  $class = 'verbose ';
	}
	else if ($l['all_events']['type'] == 'not_taking_calls_btn' || $l['all_events']['log_type'] == USEREVT_NOT_TAKING_CALLS_BTN) $event =  '[BTN] Not taking calls';
	else if ($l['all_events']['type'] == 'taking_calls_btn' || $l['all_events']['log_type'] == USEREVT_TAKING_CALLS_BTN) $event =  '[BTN] Taking calls';
	else if ($l['all_events']['type'] == 'leave_break' || $l['all_events']['log_type'] == USEREVT_LEAVE_BREAK) $event =  'End of Break';
	else if ($l['all_events']['type'] == 'break' || $l['all_events']['log_type'] == USEREVT_BREAK) $event =  'Break - ' . $l['all_events']['break_reason'];
	else if ($l['all_events']['type'] == 'refresh_browser' || $l['all_events']['log_type'] == USEREVT_REFRESH_BROWSER) $event =  'Refresh Browser';
	else {
		$event =  $l['all_events']['type'];
		$class="call_event";
	}
  
  if ($l['all_events']['event_type'] == EVENT_FILL_PROMPT || $l['all_events']['event_type'] == EVENT_BTNCLICK ||  $l['all_events']['event_type'] == EVENT_CALLTYPE ||$l['all_events']['event_type'] == EVENT_MINDERCLICK) $class .= ' call_event';
  else $class .= '';
	echo '<tr class="'.$class.'"><td>'. $l['0']['created_f'] . '</td>';
	echo '<td align="center">' . $l['u']['username'] . '</td>';
	echo '<td align="center">' . $l['all_events']['extension'] . '</td>';
	echo '<td>';
	if (!empty($l['all_events']['account_num'])) echo $l['all_events']['account_num'] . ' - ' . $l['all_events']['account_name'];
	else echo '';
	echo '</td>';
	if ($l['all_events']['event_type'] == EVENT_BTNCLICK) {
    $button_data = unserialize($l['all_events']['button_data']);
    if (!empty($button_data['emp_name'])) $data = $button_data['emp_name'] . ' ';
    else $data = '';
    if (!empty($button_data['bfulldata'])) $data .= $button_data['bfulldata'];
    else $data .= $button_data['bdata'];
    if (!empty($button_data['blabel']))
      echo '<td><div style="max-width:500px;">[BTN CLICK] '. $button_data['blabel'] . ' - '.$data . '</div></td>';
    else 
      echo '<td><div style="max-width:500px;">[BTN CLICK] '. $data . '</div></td>';
  }
	else {
	$l['all_events']['description'] = preg_replace('/[^\s];[^\s]/', '; ', $l['all_events']['description']);
	$l['all_events']['description'] = preg_replace('/[^\s],[^\s]/', '; ', $l['all_events']['description']);
  echo '<td><div style="width:300px;">'. $event . $l['all_events']['description'] . '</div></td>';
  }
	
	echo '</tr>';
//exit;
}

?>
</table>

<a href=""></a>
</div>
<script>
function submitOperatorEvents(t) {
  var myform = $(t).parent('form')
  var missingEntry = false;

  if (missingEntry) return false;
   $.ajax({
        url: '/UserLogs/events/',
        type: 'post',
        dataType: 'html',
        data: myform.serialize()
		}).done(function(data) {    
			$('#report-detail').html(data);
		}).fail(function() {
		      alert('Cannot communicate to the OpenAnswer Server, contact Technical Support');
		});     
}

  $(function () {
		$('.datepicker').datepicker({
    	dateFormat: 'yy-mm-dd',
    	changeMonth: true,
      changeYear: true,
      showButtonPanel: true 
		});	
		    
		$(".report_user_sel2").select2({
      initSelection : function (element, callback) {
        var id=$(element).val();
        if (id!=="") {        
          $.ajax("/Users/find/"+id, {
            dataType: "json"
          }).done(function(data) { 
            if (data.length > 0) {
              callback(data[0]); 
            }
            else {
              $(element).val('');
            }
          });
        }
      },		  
		  placeholder: 'Search operator or extension',
		  minimumInputLength: 2,
      allowClear: true,
      blurOnChange: true,
      openOnEnter: false,		  
		  ajax: {
			  url: "/Users/find/",
		    data: function(term, page) {
		      return {term: term, page: page};
		    },
			  dataType: 'json',
		    results: function (data, page) {
		      return {results: data};
		    }
		  }		  

	  });
	});
</script>
<?php
echo $this->Js->writeBuffer();
?>
