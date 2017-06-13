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
<div class="users view panel-content">
<h2>Operator Events</h2>

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
		<th width="100" align="left">Length</th>
		<th align="left">Event</th>
	</tr>
<?php
fb($log);
foreach ($log as $l) {
  $class = '';
	if ($l['l']['type'] == 'login' || $l['l']['log_type'] == USEREVT_LOGIN) $event = 'Logged in';
	else if ($l['l']['type'] == 'logout' || $l['l']['log_type'] == USEREVT_LOGOUT) $event =  'Logged out';
	else if ($l['l']['type'] == 'not_taking_calls'  || $l['l']['log_type'] == USEREVT_NOT_TAKING_CALLS) { 
	  $event =  'Not taking calls';
	  $class = 'verbose ';
	}
	else if ($l['l']['type'] == 'taking_calls'  || $l['l']['log_type'] == USEREVT_TAKING_CALLS) {
	  $event =  'Taking calls';
	  $class = 'verbose ';
	}
	else if ($l['l']['type'] == 'not_taking_calls_btn' || $l['l']['log_type'] == USEREVT_NOT_TAKING_CALLS_BTN) $event =  '[BTN] Not taking calls';
	else if ($l['l']['type'] == 'taking_calls_btn' || $l['l']['log_type'] == USEREVT_TAKING_CALLS_BTN) $event =  '[BTN] Taking calls';
	else if ($l['l']['type'] == 'leave_break' || $l['l']['log_type'] == USEREVT_LEAVE_BREAK) $event =  'End of Break';
	else if ($l['l']['type'] == 'break' || $l['l']['log_type'] == USEREVT_BREAK) {
	  $event =  'Break - ' . $l['l']['break_reason'];
	  $class = 'highlight';
	}
	else if ($l['l']['type'] == 'refresh_browser' || $l['l']['log_type'] == USEREVT_REFRESH_BROWSER) $event =  'Refresh Browser';
	else $event =  $l['l']['type'];
  
	echo '<tr class="'.$class.'"><td>'. $l['0']['created_f'] . '</td>';
	echo '<td align="center">' . $l['u']['username'] . '</td>';
	echo '<td align="center">' . $l['l']['extension'] . '</td>';
	$break_len = '';
	if (!empty($l[0]['break_len'])) $break_len = $this->element('formatDuration', array('t' => $l['0']['break_len']));
	echo '<td align="center">' . $break_len  . '</td>';
  echo '<td>'. $event  . '</td>';
	
	echo '</tr>';

}

?>
</table>
<?php
if ($this->Permissions->isAuthorized('UserlogsBreaklogVerbose',$permissions)) {
  ?>

  <a href="#" onclick="$('.verbose').show();return false;">+</a>

  <?php
}
?>
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
