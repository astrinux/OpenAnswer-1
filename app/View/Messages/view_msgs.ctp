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
    'update' => '#' . $target,
    'evalScripts' => true
));
?>
<div class="msgdiv" id="msgtab2" style="height: 100%; width: 100%;position:relative;">

<div class="messages index ui-layout-center">
  <div class="panel-content tblheader">
  <h1><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['messages']; ?>"></i> Messages</h1>
	<?php
	echo $this->Element('paging');
	?>
  </div>
	<DIV class="tableWrapper tblheader">
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">

			<COL class="col80">
			<COL class="col80">
			<COL class="col190">
			<COL class="col100">
			<COL class="col180">
			<COL class="col180">	
			<COL class="col80">	
			<COL class="col80">	
			<COL class="col50">	
			<COL class="col50">	
			<COL class="col50">	
	<tr>
			<th class="col80"><?php echo $this->Paginator->sort('id'); ?></th>
			<th class="col80">Account #</th>
			<th class="col190" align="left"><?php echo $this->Paginator->sort('DidNumber.company', 'Company'); ?></th>
			<th class="col100" ><?php echo $this->Paginator->sort('user_name', 'Operator'); ?></th>
			<th class="col180" ><?php echo $this->Paginator->sort('created'); ?></th>
			<th class="col180" ><?php echo $this->Paginator->sort('calltype'); ?></th>
			<th class="col80" ><?php echo $this->Paginator->sort('queue'); ?></th>
			<th class="col80" ><?php echo $this->Paginator->sort('delivered'); ?></th>
			<th class="col50" ><?php echo $this->Paginator->sort('minder'); ?></th>
			<th class="col50" ><?php echo $this->Paginator->sort('hold'); ?></th>
			<th class="col50" ><?php echo $this->Paginator->sort('audited'); ?></th>
	</tr>
  </table>
  </div>
  <div class="data tableWrapper"><div class="innerWrapper">
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
			<COL class="col80">
			<COL class="col80">
			<COL class="col190">
			<COL class="col100">
			<COL class="col180">
			<COL class="col180">	
			<COL class="col80">	
			<COL class="col80">	
			<COL class="col50">	
			<COL class="col50">	
			<COL class="col50">	

	<?php
	$total_cnt = count($Messages);
	$msg_array = array();
	$msg_array_by_id = array();
	
	foreach ($Messages as $k => $m){
	   if ($k==0) $prev = '';
	   else $prev = $Messages[$k-1]['Message']['id'];
	   
	   if ($k == (count($Messages)-1)) $next = '';
	   else $next = $Messages[$k+1]['Message']['id'];
	   $msg_array[$k] = $m['Message']['id'];
	   ?>
	
	<tr  onclick="currentIndex = '<?php echo $k; ?>'; loadMessage('<?php echo $m['Message']['id']; ?>', '<?php echo $m['Message']['did_id']; ?>', null, true, 'current=<?php echo ($k+1); ?>&total=<?php echo $total_cnt;?>'); return false;">
		<td class="col80" align="center"><?php echo h($m['Message']['id']); ?></td>
		<td class="col80"  align="center"><?php echo $m['Account']['account_num']; ?></td>
		<td class="col190" ><?php echo h($m['DidNumber2']['company']); ?></td>
		<td class="col100"  align="center"><?php echo h($m['User']['username']); ?></td>
		<td class="col180" ><?php if (!empty($m['DidNumber2']['timezone'])) echo $m[0]['createdf'] . ' ' .$global_options['timezone'][$m['DidNumber2']['timezone']]; ?></td>
		<td class="col180" align="center"><?php echo $m['Message']['calltype']; ?></td>
		<td class="col80" align="center"><?php echo $m['CallLog']['queue']; ?></td>
		<td class="col80"  align="center"><?php if ($m['Message']['delivered']) echo 'Yes'; else echo ''; ?></td>
		<td class="col50"  align="center"><?php if ($m['Message']['minder']) echo 'Yes'; else echo ''; ?></td>
		<td class="col50"  align="center"><?php 
		if ($m['Message']['hold'] == '1') echo 'Yes'; 
		else if ($m['Message']['hold'] == '2') echo 'Until<br>'. $m[0]['hold_until_f']; 
		else echo ''; ?>
		</td>
		<td class="col50"  align="center"><?php 
		if ($m['Message']['audited'] == '1') echo 'Yes'; 
		else echo ''; ?>
		</td>
	</tr>
  <?php 
  }
  ?>
	</table>
	</div>
	</div>

</div>

</div>
<?php
echo $this->Js->writeBuffer();
?>
<script>
msgListLength = <?php echo count($Messages); ?>;
msgArray = <?php echo json_encode($msg_array); ?>;
msgArrayById = <?php echo json_encode($msg_array_by_id); ?>;



$(document).ready(function() {
    $('.tblheader').css('margin-right', scrollbarWidth);  
    msgLayout.resizeAll();
  var msg2Layout = $('#msgtab2').layout({
    center__contentSelector: '#msgtab2 div.data',
    resizeWhileDragging: true
  });
});
</script>
