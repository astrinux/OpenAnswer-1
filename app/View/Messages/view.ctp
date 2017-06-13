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
    'update' => '#msg-content' ,
    'evalScripts' => true
));
?>
<div class="msgdiv" id="msgtab" style="height: 100%; width: 100%;">


<div class="messages index ui-layout-center">
  <div class="panel-content tblheader">
  <h1><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['messages']; ?>"></i>  Messages</h1>
	<?php
	echo $this->Element('paging');
	?>
  </div>
	<DIV class="tableWrapper tblheader">
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">

			<COL class="col80">
			<COL class="col110">
			<COL class="col160">
			<COL class="col80">	
			<COL class="col60">	
			<COL class="col60">	
			<COL class="col60">	
			<COL class="col60">	
	<tr>
			<th class="col80"><?php echo $this->Paginator->sort('id'); ?></th>
			<th class="col110">Account #</th>
			<th class="col160" align="left"><?php echo $this->Paginator->sort('DidNumber.company', 'Company'); ?></th>
			<th class="col80">Total</th>
			<th class="col60" >Delivered</th>
			<th class="col60" >Minder</th>
			<th class="col60">Hold</th>
			<th class="col60">Audited</th>
	</tr>
  </table>
  </div>
  <div class="data tableWrapper"><div class="innerWrapper">
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
			<COL class="col80">   
			<COL class="col110">  
			<COL class="col160">  
			<COL class="col80">	
			<COL class="col60">	  
			<COL class="col60">	  
			<COL class="col60">	
			<COL class="col60">	

	<?php
	foreach ($Messages as $Message): ?>
	<tr  onclick="loadMessages('<?php echo $Message['DidNumber2']['id']; ?>', null); return false;">
		<td class="col80" align="center"><?php echo h($Message['DidNumber2']['id']); ?></td>
		<td class="col110" align="center"><?php echo $Message['Account']['account_num']; ?></td>
		<td class="col160" ><?php echo h($Message['DidNumber2']['company']); ?></td>
		<td class="col80" align="center"><?php echo $Message['0']['total']; ?></td>
		<td class="col60" align="center"><?php echo $Message['0']['delivered']; ?></td>
		<td class="col60" align="center"><?php echo $Message['0']['minder']; ?></td>
		<td class="col60"  align="center"><?php echo $Message['0']['hold']; ?>
		</td>
		<td class="col60"  align="center"><?php echo $Message['0']['audited']; ?>
		</td>
		</td>

	</tr>
<?php endforeach; ?>
	</table>
	</div>
	</div>
</div>

</div>
<?php
echo $this->Js->writeBuffer();
?>
<script>
function loadMessages(did_id) {
  msgLayout.center.children.layout1.open('east');
  loadPagePost(null, '/Messages/view_msgs/'+did_id , 'msg-detail', $('#msg-filter').serialize(), null);       
}

$(document).ready(function() {
    $('.tblheader').css('margin-right', scrollbarWidth);  
  $('#msgtab').layout({
    center__contentSelector: '#msgtab div.data',
    resizeWhileDragging: true
  });
  
});
</script>
