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
.schedule_action {float:right;}
.schedule_action a {text-decoration:none;}
.oncall_s {clear:both;border-bottom: 1px dashed #666;padding:2px 0px;}
</style>
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

<div class="CallList index">
  <div class="panel-content tblheader">
	<h2><?php echo __('On Call Lists') . '<i> - ' . $timezone . '</i>'; ?></h2>
<form id="oncall_search2">
<input type="radio" name="data[Search][c_type]" value="current" <?php if ($this->request->data['Search']['c_type'] == 'current' || empty($this->request->data['Search']['c_type'])) echo ' checked'; ?> onclick="updateCallList(document.getElementById('oncall_search2'),'<?php echo $did_id; ?>', 'dialogWin', 'view_all'); return false;"> Current &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="radio" onclick="updateCallList(document.getElementById('oncall_search2'),'<?php echo $did_id; ?>', 'dialogWin', 'view_all'); return false;" name="data[Search][c_type]" value="future"  <?php if ($this->request->data['Search']['c_type'] == 'future') echo ' checked';?>> Future &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="radio" onclick="updateCallList(document.getElementById('oncall_search2'),'<?php echo $did_id; ?>', 'dialogWin', 'view_all'); return false;" name="data[Search][c_type]" value="expired" <?php if ($this->request->data['Search']['c_type'] == 'expired') echo ' checked';?>> Expired&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="radio" onclick="updateCallList(document.getElementById('oncall_search2'),'<?php echo $did_id; ?>', 'dialogWin', 'view_all'); return false;" name="data[Search][c_type]" value="all" <?php if ($this->request->data['Search']['c_type'] == 'all') echo ' checked';?>> all&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								
  
</form>
	</div>
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
	<tr>
			<th width="150" align="left"><?php echo $this->Paginator->sort('title'); ?></th>
			<th width="380" align="left">Effective</th>
	</tr>
	<?php
	if (sizeof($CallLists)) {
	foreach ($CallLists as $m): 
	?>
	<tr>
		<td><?php echo $m['CallList']['title']; ?>
        <?php if ($this->Permissions->isAuthorized('CalllistsAdd',$permissions)) { ?>
		<div class="schedule_action"><a href="#" onclick="addListSchedule(<?php echo $m['CallList']['id']; ?>); return false;">+</a></div>
		<?php
	  }
		?>
		
		</td>
		<td>
    <?php
    $schedules = $m['CallListsSchedule'];
    foreach($schedules as $s) {
	    $day_range = '';
	
			/*$s['starttime'] = $s['start_time_f'];
			$s['endtime'] = $s['end_time_f'];
			$s['startdate'] = $s['startdate_f'];
			$s['enddate'] = $s['enddate_f'];
			$s['days'] = getDayRanges($s, $php_daysofweek);
			$s['day_range'] = $day_range;*/
	
	    echo '<div class="oncall_s" >';
			//echo $this->element('calltype_schedule', array('schedule' => $s, 'daysofweek' => $php_daysofweek, 'showlinks' => false));
			echo $s['schedule'];
			$ids = explode(',', $s['employee_ids']);

			$names = array();
			foreach ($ids as $id) {
			  //print_r($employees[$id]);
			  if (!empty($employees[$id])) $names[] = $employees[$id]['Employee']['name'];
			}
      if (sizeof($names) > 0) {
			  echo '<br>';
			  echo implode(', ', $names);
			}
			else if (trim($s['legacy_list'])) echo '<br>' . str_replace(array('\n', "\r\n"), ', ', $s['legacy_list']);
			echo '<div style="clear:both"></div></div>';
	    
    }
    ?>
		</td>
	</tr>
<?php endforeach; 
  }
  else {
    echo '<tr><td colspan="6" align="center">None found, click <a href="#" onclick="addList(); return false;"><i>here</i></a> to create one</td></tr>';
  }
?>
	</table>
</div>

<script type="text/javascript">
  function addList() {
    var url = '/CallLists/add/<?php echo $did_id; ?>';
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'html',
  	      	type: 'GET'
  	      }).done(function(data) {
  					$('#did-detail').html(data);
  					didLayout.center.children.layout1.open('east');
  	      });	
  }
  
  function addListSchedule(id) {
    var url = '/CallListsSchedule/add/<?php echo $did_id; ?>/' + id;
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'html',
  	      	type: 'GET'
  	      }).done(function(data) {
  					$('#did-detail').html(data);
  					didLayout.center.children.layout1.open('east');
  	      });	
  }  
  
  function editList(id) {
    var url = '/CallListsSchedule/edit/' + id;
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'html',
  	      	type: 'GET'
  	      }).done(function(data) {
  					$('#did-detail').html(data);
  					didLayout.center.children.layout1.open('east');
  	      });	
  }  
  
  function deleteList(id) {

    var url = '/CallLists/delete/' + id;
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'json',
  	      	type: 'GET'
  	      }).done(function(data) {
            if (data.success) {
    					loadPage(this, '/CallLists/index/<?php echo $did_id; ?>', 'did-content');          
              didLayout.center.children.layout1.close('east')					
            }
            alert(data.msg);
  	      });	
  }
  
  function removeEmployee(t) {
    var li = $(t).parents('li');
    $('#sortable2').append(li);
  }   
  
  function addEmployee(t) {
    var li = $(t).parents('li');
    $('#sortable1').append(li);
  }     
  
  function saveCallList(did_id) {
    var employee_ids = [];
    $('#sortable1 li').each(function() {
    	employee_ids.push($(this).attr('eid'));
    });
    $('#employee_ids').val(employee_ids.join(','));
    $('.choices.cdisabled input').val('');    
    $('.choices.cdisabled input').prop('disabled', false);

    var url = "/CallLists/add/" + did_id
	    $.ajax({
	        url: url,
	        type: 'POST',
	        dataType: 'json',
	        data: $('#CallListAddForm').serialize()
			}).done(function(data) {    
        if (data.success) {
					loadPage(this, '/CallLists/index/<?php echo $did_id; ?>', 'did-content');          
          didLayout.center.children.layout1.close('east')					
        }
        alert(data.msg);
			});    

  }   
  

</script>
