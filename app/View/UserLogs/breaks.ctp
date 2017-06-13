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
<div class="userbreaks index">
  <div class="panel-content tblheader">
	<h2>Operator Break Report</h2>
	<form id="break_form" name="breakform" method="post" action="/UserLogs/breaks">
	<input type="hidden" name="format" value="">
  <input type="text" id="break_start" size="12" name="Search[start_date]" class="datepicker" value="<?php echo $start_date; ?>" dtype="start">&nbsp;&nbsp;&nbsp;<b>End:</b> <input type="text"  id="break_end" size="12" name="Search[end_date]" class="datepicker" dtype="end" value="<?php echo $end_date; ?>">

	<?php // echo '&nbsp;&nbsp;&nbsp;' . $this->Form->input('Search.period', array('label' => false, 'div' => false, 'options' => array('month' => 'Current month', '0' => 'Current week', '1' => '1 wk ago', '2' => '2 wks ago', '3' => '3 wks ago', '4' => '4 wks ago', '5' => '5 wks ago', '6' => '6 wks ago', '7' => '7 wks ago', '8' => '8 wks ago', '9' => '9 wkds ago'), 'size' => 1, 'empty' => 'Select')); 
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';

	?><input type="button" value="Go" onclick="document.breakform.format.value=''; loadPagePost('#break_form', '/UserLogs/breaks', 'report-detail', null, null); return false;"/>&nbsp;&nbsp;<input type="submit" value="Export" onclick="document.breakform.format.value='csv';document.breakform.setAttribute('target', '_blank'); "/>		
	</form>
  <?php //echo $this->element('paging'); ?>	
  </div>
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%" id="breaktbl">
	<thead>
	<tr>
			<th width="100" align="left"><a href="#">Username</a></th>
			<th width="120" align="left"><a href="#">Total Breaks</a></th>
			<th width="120" align="left"><a href="#">Break Length</a></th>
			<th width="80"><a href="#">Unknown duration</a></th>
			<?php
			foreach($break_reasons as $b) {
			  echo '<th width="80"><a href="#">'.$b.'</a></th>';
			}
			?>
			
	</tr>
	</thead>
	<tbody>
  <?php
  foreach ($breaks as $b) {
    ?>
	<tr onclick="openDialogWindow('/UserLogs/break_log/<?php echo $b['breaks'][0]['User']['id']; ?>/'+ $('#break_start').val() +'/'+ $('#break_end').val() , 'Break details for <?php echo $b['breaks'][0]['User']['username']; ?>', null, null, 900, 700); return false;">
		<td><?php echo $b['breaks'][0]['User']['username']; ?></td>
		<td align="center"><?php echo $b['count']; ?></td>
		<td align="center"><?php echo $b['total']; ?></td>
		<td align="center"><?php echo $b['unknown_duration']; ?></td>
		
			<?php
			foreach($break_reasons as $k2 => $r) {
			  echo '<td align="center">'.$b['reason'][$k2] . '<br>'.$b['duration'][$k2] .'</td>';
			}
			?>
					
	</tr>
  <?php 
  }
  ?>
  </tbody>
	</table>


</div>
<script>
$('#breaktbl').tablesorter({
    widgets: [ 'stickyHeaders'],
    widgetOptions: {
      // jQuery selector or object to attach sticky header to
      stickyHeaders_attachTo : '#report-detail' 
    }    
  }
);
$(document).ready(function() {          
		$('.datepicker').datepicker({
    	dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true      
		});		
    $('.tblheader').css('margin-right', scrollbarWidth);  
});
</script>
<?php
echo $this->Js->writeBuffer();
?>

