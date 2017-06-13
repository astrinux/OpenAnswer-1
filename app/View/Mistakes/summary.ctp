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
    'update' => '#mistakes-content',
    'evalScripts' => true
	));
?>
<div id="mistakesdiv" class="Mistakes index" style="height: 100%; width: 100%;">
<div class="ui-layout-center" style="overflow:hidden">
  <div class="panel-content tblheader">
	<h2><?php echo __(' Mistake Summary'); ?></h2>
	<?php
	echo $this->Element('paging');
	?>	
	</div>
	<DIV class="tableWrapper tblheader">
	
	
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">

			<COL class="col80">
			<COL class="col120">
			<COL class="col170">
			<COL class="col320">

	
	<tr>
			<th class="col80" align="center"><?php echo $this->Paginator->sort('created', 'Message Date'); ?></th>
			<th class="col120"><?php echo $this->Paginator->sort('recipient_username', 'Recipient'); ?></th>
			<th class="col170"><?php echo $this->Paginator->sort('category'); ?></th>
			<th class="col320"><?php echo $this->Paginator->sort('description'); ?></th>
	</tr>
  </table>
  </div>	
  <div class="data tableWrapper">
    <div class="innerWrapper">
    	<table cellpadding="0" cellspacing="0" class="gentbl"  width="100%">
    
			<COL class="col80">
			<COL class="col120">
			<COL class="col170">
			<COL class="col320">
    	<?php
    	foreach ($Mistakes as $Mistake): ?>
    	<tr>
    		<td onclick="openMistakeDialog('','', 'edit', '<?php echo $Mistake['Mistake']['id'];?>', function() {$('#mistakes-go').trigger('click');}); return false; "class="col80" align="center"><?php echo h($Mistake[0]['created_f']); ?></td>
    		<td onclick="openMistakeDialog('','', 'edit', '<?php echo $Mistake['Mistake']['id'];?>', function() {$('#mistakes-go').trigger('click');}); return false; "class="col120" align="center"><?php echo $Mistake['Mistake']['recipient_username']? $Mistake['Mistake']['recipient_username']: ''; ?></td>
    		<td onclick="openMistakeDialog('','', 'edit', '<?php echo $Mistake['Mistake']['id'];?>', function() {$('#mistakes-go').trigger('click');}); return false; "class="col170" align="center"><?php echo h($Mistake['Mistake']['category']); ?></td>
    		<td onclick="openMistakeDialog('','', 'edit', '<?php echo $Mistake['Mistake']['id'];?>', function() {$('#mistakes-go').trigger('click');}); return false; "class="col320" align="left"><?php echo h($Mistake['Mistake']['description']); ?></td>
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
function deleteMistake(id) {
  var url = '/Mistakes/delete/' + id;
  getJson(url, null, function() {
    $('#mistakes-go').trigger('click');
  });
}

$(document).ready(function() {
    $('.tblheader').css('margin-right', scrollbarWidth);  
  // create the layout - with data-table wrapper as the layout-content element  
  $('#mistakesdiv').layout({
    center__contentSelector: '#mistakesdiv div.data',
    resizeWhileDragging: true
  });
  
});
</script>