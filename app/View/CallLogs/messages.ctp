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
    'update' => '#did-content',
    'evalScripts' => true
));
?>

<div class="CallLogs index" style="height: 100%; width: 100%;" id="calllog_msg">
<div class="ui-layout-center" style="overflow:hidden">

<div class="panel-content tblheader">
  <?php 
  if (!empty($global_options['timezone'][$CallLogs[0]['DidNumber']['timezone']])) $tz = ' - ' . $global_options['timezone'][$CallLogs[0]['DidNumber']['timezone']];
  else $tz = '';
  ?>
	<h2><?php echo __(' Call Log'); ?> <?php echo $tz; ?></h2>
<?php
if ($this->Permissions->isAuthorized('CalllogsMessagesClear',$permissions)) {
    echo '<a href="#" onclick="loadPage(null, \'/CallLogs/dumpTestData\', \'dump_result\'); return false;">Clear test data</a> <span id="dump_result"></span>';
    
  }
	echo $this->Element('paging');
	?>
</div>
	<div class="tableWrapper tblheader">
	<table cellpadding="0" cellspacing="0" class="gentbl" border="0" width="100%">
			<COL class="col180">
			<COL class="col100">
			<COL class="col90">	 
			<COL class="col60">	
			<COL class="col180">	
			<COL class="col60">	
			<COL class="col100">		
			<COL class="col80">			
	<tr>
			<th class="col180"><?php echo $this->Paginator->sort('start_time'); ?></th>
			<th class="col100"><?php echo $this->Paginator->sort('firstname', 'Operator'); ?></th>
			<th class="col90"><?php echo $this->Paginator->sort('extension', 'Ext'); ?></th>
			<th class="col60"><?php echo $this->Paginator->sort('queue', 'Queue'); ?></th>
			<th class="col180"><?php echo $this->Paginator->sort('end_time'); ?></th>
			<th class="col60" title="Total call time/ Wrap-up time">Duration</th>
			<th class="col100"><?php echo $this->Paginator->sort('cid_number'); ?></th>
			<th class="col80 actions" align="left"><?php echo __('Actions'); ?></th>
	</tr>
	</table>
	</div>
  <div class="data tableWrapper"><div class="innerWrapper">	
	
	<table cellpadding="0" cellspacing="0" class="gentbl" border="0" width="100%">
			<COL class="col180">
			<COL class="col100">
			<COL class="col90">	
			<COL class="col60">	
			<COL class="col180">	
			<COL class="col60">	
			<COL class="col100">		
			<COL class="col80">			
	<?php
	foreach ($CallLogs as $k => $CallLog): ?>
	<tr>
		<td class="col180" align="center" onclick="loadPage(this, '/CallLogs/view/'  + '<?php echo $CallLog['CallLog']['id']; ?>', 'did-detail');didLayout.center.children.layout1.open('east');"><?php echo h($CallLog[0]['starttimef']); ?></td>
		<td class="col100" align="center" onclick="loadPage(this, '/CallLogs/view/'  + '<?php echo $CallLog['CallLog']['id']; ?>', 'did-detail');didLayout.center.children.layout1.open('east');"><?php echo h($CallLog['User']['firstname'] . " " . $CallLog['User']['lastname']); ?></td>
		<td class="col90" align="center" onclick="loadPage(this, '/CallLogs/view/'  + '<?php echo $CallLog['CallLog']['id']; ?>', 'did-detail');didLayout.center.children.layout1.open('east');"><?php echo h($CallLog['CallLog']['extension']); ?></td>
		<td class="col60" align="center" onclick="loadPage(this, '/CallLogs/view/'  + '<?php echo $CallLog['CallLog']['id']; ?>', 'did-detail');didLayout.center.children.layout1.open('east');"><?php echo h($CallLog['CallLog']['queue']); ?></td>
		<td class="col180" align="center" onclick="loadPage(this, '/CallLogs/view/'  + '<?php echo $CallLog['CallLog']['id']; ?>', 'did-detail');didLayout.center.children.layout1.open('east');"><?php echo h($CallLog[0]['endtimef']); ?></td>
		<td class="col60" align="center" onclick="loadPage(this, '/CallLogs/view/'  + '<?php echo $CallLog['CallLog']['id']; ?>', 'did-detail');didLayout.center.children.layout1.open('east');"><?php if ($CallLog['0']['duration']) echo $this->element('formatDuration', array('t' => $CallLog['0']['duration'])) . ' / ' . $this->element('formatDuration', array('t' => $CallLog['0']['wrapup'])); else echo '';?></td>
		<td class="col100" align="center" onclick="loadPage(this, '/CallLogs/view/'  + '<?php echo $CallLog['CallLog']['id']; ?>', 'did-detail');didLayout.center.children.layout1.open('east');"><?php 
		  if ($CallLog['CallLog']['unique_id'] == 'CAMPAIGN') {
        echo 'Outbound Campaign';
      }
		  else echo $this->element('formatPhone', array('num' => $CallLog['CallLog']['cid_number'])); 
		?></td>
		<td class="col80 actions" align="left"><a href="#" onclick="loadPage(this, '/CallLogs/view/'  + '<?php echo $CallLog['CallLog']['id']; ?>', 'did-detail');didLayout.center.children.layout1.open('east');" title="View call events"><span class="fa fa-lg fa-search"></span></a><?php 
		if ($CallLog['Message']['id']) {
		  ?><a href="#" onclick="loadMessage('<?php echo $CallLog['Message']['id']; ?>', '<?php echo $CallLog['Message']['did_id']; ?>', null, true, null); return false;" title="View message">&nbsp;&nbsp;<span class="fa fa-lg fa-envelope-o"></span></a><?php
    }
    else {
		  ?><a href="#" onclick="return false;" title="View message">&nbsp;&nbsp;<span class="fa fa-lg fa-envelope-o disabled"></span></a><?php
    }
    if (!empty($CallLog['CallLog']['unique_id'])) {

      if ( $CallLog['CallLog']['unique_id'] != 'TESTCALL'  && $CallLog['CallLog']['unique_id'] != 'CAMPAIGN' && !$CallLog['Message']['delivered'] && $CallLog['CallLog']['end_time'] != '0000-00-00 00:00:00') {
        echo ' <a href="#"  onclick="recreateScreenPop('.$CallLog['CallLog']['did_number'].','.$CallLog['CallLog']['did_id'].', \''.$CallLog['CallLog']['unique_id'].'\'); return false;" title="Re-pop operator screen">&nbsp;&nbsp;<span class="fa fa-lg fa-share-square-o"></span></a>';    
      }
      else  {
        echo ' <a href="#"  onclick="return false;" title="Re-pop operator screen">&nbsp;&nbsp;<span class="fa fa-lg fa-share-square-o disabled"></span></a>';    
      }
    }
    
    echo $this->element('calllog_actions', array('c' => $CallLog)); 
    
    ?></td>
	</tr>
<?php endforeach; ?>
	</table>
	</div></div>
</div></div>
	<script type="text/javascript">
	
  function reloadPage() {
        if ($('#calllog_msg').is(':visible')) {
          if (!$("#operatorScreen").dialog('isOpen')) {
            loadPagePost(null, '/CallLogs/messages/' + $('#find_did').val(), 'did-content', 'target=did-content&detail=did-detail', null);
          }
          else {
            if (callCheckTimer) clearInterval(callCheckTimer);
            callCheckTimer = setTimeout(reloadPage, settings['call_update_seconds'] * 1000);
          }
        }
        else {
          if (callCheckTimer) clearInterval(callCheckTimer);
        }
  
  }

	$(document).ready(function() {
    $( '#did-content' ).tooltip();  	  
    $('.tblheader').css('margin-right', scrollbarWidth);
    
	  $('#calllog_msg').layout({
    center__contentSelector: '#calllog_msg div.data',
    resizeWhileDragging: true
    });	
//    if (callCheckTimer) clearInterval(callCheckTimer);
//    callCheckTimer = setTimeout(reloadPage,settings['call_update_seconds'] * 1000);	
  });	

	</script>

<?php
echo $this->Js->writeBuffer();

?>