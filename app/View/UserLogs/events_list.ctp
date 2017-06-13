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
$this->Paginator->options(array('update' => '#report-detail',
    'evalScripts' => true
    ));
?>

<div class="users view">
  <div class="panel-content tblheader">
  <h2>Operator Events</h2>
  <form>
  <b>Operator:</b> <input name="data[Search][user_id]" style="width: 180px;"  class="required report_user_sel2" type="hidden" value="<?php echo !empty($this->request->data['Search']['user_id'])? $this->request->data['Search']['user_id']: ''; ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Ext:</b> <input type="text" name="data[Search][extension]" value="<?php echo !empty($this->request->data['Search']['extension'])? $this->request->data['Search']['extension']: ''; ?>" size="5"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Date:</b> <input name="data[Search][report_date]" type="text" class="required datepicker" value="<?php echo !empty($this->request->data['Search']['report_date'])? $this->request->data['Search']['report_date']: ''; ?>"> &nbsp;&nbsp;&nbsp;Inactivity: <input type="text" name="data[Search][inactive]" value="<?php echo $this->request->data['Search']['inactive']; ?>" size="3">&nbsp;&nbsp;&nbsp;<input type="submit" value="Go" onclick="submitOperatorEvents(this); return false;">
  </form>
  <?php echo $this->element('paging'); ?>	
  </div>
  <table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
	<tr>
		<th width="120" align="left">Created</th>
		<th width="150">User</th>
		<th width="100">Extension</th>
		<th width="300" align="left">Event</th>
	</tr>
<?php
foreach ($log as $k=> $l) {
  $class = '';
	if ($l['UserLog']['type'] == 'login' || $l['UserLog']['log_type'] == USEREVT_LOGIN) $event = 'Logged in';
	else if ($l['UserLog']['type'] == 'logout' || $l['UserLog']['log_type'] == USEREVT_LOGOUT) $event =  'Logged out';
	else if ($l['UserLog']['type'] == 'not_taking_calls' || $l['UserLog']['log_type'] == USEREVT_NOT_TAKING_CALLS) { 
	  $event =  'Not taking calls';
	  //$class = 'verbose ';
	}
	else if ($l['UserLog']['type'] == 'taking_calls' || $l['UserLog']['log_type'] == USEREVT_TAKING_CALLS) {
	  $event =  'Taking calls';
	  //$class = 'verbose ';
	}
	else if ($l['UserLog']['type'] == 'not_taking_calls_btn' || $l['UserLog']['log_type'] == USEREVT_NOT_TAKING_CALLS_BTN) $event = '[BTN] Not taking calls';
	else if ($l['UserLog']['type'] == 'taking_calls_btn' || $l['UserLog']['log_type'] == USEREVT_TAKING_CALLS_BTN) $event = '[BTN] Taking calls';
	else if ($l['UserLog']['type'] == 'leave_break'  || $l['UserLog']['log_type'] == USEREVT_LEAVE_BREAK) $event = 'End of Break';
	else if ($l['UserLog']['type'] == 'break'|| $l['UserLog']['log_type'] == USEREVT_BREAK) $event = 'Break - ' . $l['UserLog']['break_reason'];
	else $event = $l['UserLog']['type'];

	echo '<tr class="'.$class.'"><td>'. $l['0']['created_f'] . '</td>';
	echo '<td align="center">' . $l['User']['username'] . '</td>';
	echo '<td align="center">' . $l['UserLog']['extension'] . '</td>';
	echo '<td>';
	echo $event;
	echo '</td>';
	//if ($k > 10) exit;
}
?>
</table>
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