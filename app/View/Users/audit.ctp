
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
<div style="height: 100%; width: 100%;overflow:hidden;">
  <div class="panel-content tblheader"  style="overflow:auto;>
	<form id="audit_form" method="post">
	<h2 style="display:inline;"><?php echo __('Call Auditing'); ?></h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

	<b>Start:</b>&nbsp;&nbsp; </label><input type="hidden" id="audit_sort" name="Search[sort]" value="<?php echo isset($this->request->data['Search']['sort'])? $this->request->data['Search']['sort']: ''; ?>"><input id="audit_dir"  type="hidden" name="Search[dir]" value="<?php echo isset($this->request->data['Search']['dir'])? $this->request->data['Search']['dir']: ''; ?>"><input type="text" name="Search[start_date]" class="datepicker" value="<?php echo isset($this->request->data['Search']['start_date'])? $this->request->data['Search']['start_date']:$start_of_week; ?>" dtype="start">&nbsp;&nbsp;&nbsp;<b>End:</b> <input type="text" name="Search[end_date]" class="datepicker" dtype="end" value="<?php echo isset($this->request->data['Search']['end_date'])? $this->request->data['Search']['end_date']: date('Y-m-d', strtotime('today')); ?>">
	&nbsp;&nbsp;&nbsp;
	<?php
	$options = array(
		'15' => '15%',
		'20' => '20%',
		'25' => '25%',
		'30' => '30%',
		'35' => '35%',
		'40' => '40%',
		'45' => '45%',
		'50' => '50%',
		'55' => '55%',
		'60' => '60%',
		'65' => '65%',
		'70' => '70%',
		'75' => '75%',
		
	);
	echo $this->Form->input('Search.audit_goal', array('div' => false, 'options' => $options, 'default' => '20'));
	?>&nbsp;<input type="button" id="audit_go" value="Go" onclick="loadPagePost('#audit_form', '/Users/audit', 'report-detail', null, null); return false;">
	</form>
	<?php
	//echo $this->Element('paging');
	?>	
	</div>
	<DIV id="audittbl_cont"">
	<table cellpadding="0" cellspacing="0" class="tablesorter gentbl" width="100%" id="audittbl">

  <thead>
	
	<tr>
			<th class="col80">Username</th>
			<th class="col80">Calls</th>
			<th class="col80">Messages</th>
			<th class="col80">Audited</th>
			<th class="col80">% Audited</th>
			<th class="col80"># to audit</th>
			<th class="col80">Mistakes</th>
	</tr>
  </thead>
  <tbody>
<?php
    	foreach ($data as $m): 
    	  $label = str_replace("'", '&#39', $m['firstname'] . ' ' .$m['lastname'] . ' ('.$m['username'].')');
    	?>
    	<tr>
    		<td class="col80" align="center"><a href="#" onclick="fetchMessages(<?php echo $m['id'];?>, 'audit_form', '<?php echo $label; ?>'); return false;"><?php echo !empty($m['username'])? $m['username']: '<i>(unknown)</i>'; ?></a></td>
    		<td class="col80" align="center"><?php echo $m['num_calls']; ?></td>
    		<td class="col80" align="center"><?php echo $m['num_messages']; ?></td>
    		<td class="col80" align="center"><?php 
    			
    			echo $m['num_audited'] . '</td>';
    	  echo '<td class="col80" align="center">' .$m['percent'].'%' . '</td>'; 
    	  echo '<td class="col80" align="center">';
    	  $to_audit = $m['to_audit'];
    	  if ($to_audit < 0) echo '0';
    	  else echo $to_audit.'</td>'; ?>
    		<td class="col80" align="center"><a href="#" onclick="fetchMistakes(<?php echo $m['id'];?>, '<?php echo $label; ?>'); return false;"><?php echo $m['num_mistakes']; ?></a></td>
    	</tr>
    <?php endforeach; 
    ?>
    </tbody>
    	</table>


</div>
<script>

$(document).ready(function() {
		$('.datepicker').datepicker({
    	dateFormat: 'yy-mm-dd',
      changeMonth: true
		});		
    $('.tblheader').css('margin-right', scrollbarWidth);  
  $("#audittbl").tablesorter({debug: true, scrollableArea: $("#audittbl_cont")});  
  
  $("#audittbl").stickyTableHeaders();	
      

});
</script>
