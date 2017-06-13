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

span.disabled {color: #ccc !important;}
</style>

<?php
$this->Paginator->options(array(
    'update' => '#call-content',
    'evalScripts' => true
));
?>


<div class="CallLogs index"  style="height: 100%; width: 100%;" id="calllog_index">
<div class="ui-layout-center" style="overflow:hidden">
  <div class="panel-content tblheader">
  <?php 

    if ($CallLogs) {
        if (!empty($global_options['timezone'][$CallLogs[0]['DidNumber']['timezone']])) $tz = ' - ' . $global_options['timezone'][$CallLogs[0]['DidNumber']['timezone']];
        else $tz = '';
    }
    else $tz = '';
  ?>  
	<h2><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['calls']; ?>"></i> <?php echo __(' Call Log'); ?></h2>
		
  <input type="checkbox" value="1" onclick="$('#calllog_tbl .local').toggle();"> display local time
  <?php
	echo $this->Element('paging');
	
	if (!empty($total_duration)) {
	  echo '&nbsp;&nbsp;&nbsp;<b title="(Total call time, including wrap-up)/ Wrap-up time">Totals: </b>' . $this->element('formatDuration', array('t' => $total_duration)) . ' / ' . $this->element('formatDuration', array('t' => $total_wrapup)); 
	}
	else {
	  echo '&nbsp;&nbsp;&nbsp;<b>Totals: </b> <i>(select a date range less than '.$max_delta.' days to display totals)</i>'; 
	}
	?>
	
	</div>
	<div class="tableWrapper tblheader">
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%" >
			<COL class="col120">
			<COL class="col160">
			<COL class="col100">
			<COL class="col60">	
			<COL class="col120">	
			<COL class="col60">	
			<COL class="col80">		
			<COL class="col80">	
	<tr>
			<th><?php echo $this->Paginator->sort('start_time'); ?></th>
			<th align="left"><?php echo $this->Paginator->sort('DidNumber.company', 'Company'); ?></th>
			<th align="center"><?php echo $this->Paginator->sort('firstname', 'Operator'); ?></th>
			<th><?php echo $this->Paginator->sort('queue', 'Queue'); ?></th>
			<th><?php echo $this->Paginator->sort('end_time'); ?></th>
			<th title="Total call time/ Wrap-up time">Duration</th>
			<th><?php echo $this->Paginator->sort('cid_number'); ?></th>
			<th class="col80 actions" align="left"><?php echo __('Actions'); ?></th>
			
	</tr>
	</table>
	</div>
  <div class="data tableWrapper"><div class="innerWrapper">	
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%" id="calllog_tbl">
			<COL class="col120">
			<COL class="col160">
			<COL class="col100">
			<COL class="col60">	
			<COL class="col120">	
			<COL class="col60">	
			<COL class="col80">		
			<COL class="col80">	
	
	<?php
	foreach ($CallLogs as $k => $CallLog): ?>
	<tr >
		<td align="center"><?php echo $CallLog[0]['starttime']; ?><span class="local is_hidden"><br><?php echo $CallLog[0]['starttimef'] . ' ' . $global_options['timezone'][$CallLog['DidNumber']['timezone']]; ?></span></td>
		<td align="left"><?php echo $CallLog['Account']['account_num'] . ' - ' . h($CallLog['DidNumber']['company']); ?></td>
		<td align="center"><?php echo h($CallLog['User']['firstname'] . " " . $CallLog['User']['lastname']); ?></td>
		<td align="center"><?php echo h($CallLog['CallLog']['queue']); ?></td>
		<td align="center"><?php 
		  if (!empty($CallLog[0]['endtimef'])) echo h($CallLog['0']['endtime']); ?><span class="local is_hidden"><br><?php if (!empty($CallLog[0]['endtimef'])) echo $CallLog[0]['endtimef'] . ' ' . $global_options['timezone'][$CallLog['DidNumber']['timezone']]; ?></td>
		<td align="center"><?php 
		
		if ($CallLog['0']['duration'] && ($CallLog['0']['wrapup'] || $CallLog['0']['wrapup'] === '0')) {
		  echo $this->element('formatDuration', array('t' => $CallLog['0']['duration'])) . ' / ' . $this->element('formatDuration', array('t' => $CallLog['0']['wrapup'])); 
		}  
		else if ($CallLog['0']['duration']) {
		  echo $this->element('formatDuration', array('t' => $CallLog['0']['duration'])); 
		}  
		else echo ''
		;?>
		</td>
		<td align="center"><?php 
		if ($CallLog['CallLog']['unique_id'] == 'CAMPAIGN') echo 'Outbound Campaign';
		else echo $this->element('formatPhone', array('num' => $CallLog['CallLog']['cid_number']));  ?>
		
		</td>
		<td class="col100 actions" align="left"><a href="#" onclick="loadPage(this, '/CallLogs/view/'  + '<?php echo $CallLog['CallLog']['id']; ?>', 'call-detail');callsLayout.center.children.layout1.open('east');" title="View call events"><span class="fa fa-lg fa-search"></span></a><?php 
		if ($CallLog['Message']['id']) {
		  ?><a href="#" onclick="loadMessage('<?php echo $CallLog['Message']['id']; ?>', '<?php echo $CallLog['Message']['did_id']; ?>', null, true, null); return false;" title="View message">&nbsp;&nbsp;<span class="fa fa-lg fa-envelope-o"></span></a>
    <?php
    }
    else {
      ?><a href="#" onclick="return false;" title="View message">&nbsp;&nbsp;<span class="fa fa-lg fa-envelope-o disabled"></span></a>      
      <?php
    }
    if (!empty($CallLog['CallLog']['unique_id']) && $CallLog['CallLog']['unique_id'] != 'TESTCALL' && !$CallLog['Message']['delivered'] && $CallLog['CallLog']['end_time'] != '0000-00-00 00:00:00') echo ' <a href="#"  onclick="recreateScreenPop('.$CallLog['CallLog']['did_number'].','.$CallLog['CallLog']['did_id'].', \''.$CallLog['CallLog']['unique_id'].'\'); return false;" title="Re-pop operator screen">&nbsp;&nbsp;<span class="fa fa-lg fa-share-square-o"></span></a>';    
    else echo ' <a href="#"  onclick="return false;" title="Re-pop operator screen">&nbsp;&nbsp;<span class="fa fa-lg fa-share-square-o disabled"></span></a>';    
    // load any content from extension
    echo $this->element('calllog_actions', array('c' => $CallLog)); 
    
    echo '</td>';
    ?>
	</tr>
<?php endforeach; ?>
	</table>
	</div>
	</div>
</div>
</div>
	<script type="text/javascript">
	

	$(document).ready(function() {
    $( '#call-content' ).tooltip();  	 
    $('.tblheader').css('margin-right', scrollbarWidth);
	/*$('.jp-progress-container').hover(function(){
		var current_time = $(this).find('.jp-current-time');
		current_time.stop().show().animate({opacity: 1}, 300);
	}, function(){
		var current_time = $(this).find('.jp-current-time');
		current_time.stop().animate({opacity: 0}, 150, function(){ jQuery(this).hide(); });
	}); */
	  $('#calllog_index').layout({
    center__contentSelector: '#calllog_index div.data',
    resizeWhileDragging: true
  });

	
	
});	

	</script>

<?php
echo $this->Js->writeBuffer();
?>