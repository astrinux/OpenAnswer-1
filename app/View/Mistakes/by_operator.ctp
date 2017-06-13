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
<div id="mistakesgdiv" class="Mistakes index" style="height: 100%; width: 100%;">
<div class="ui-layout-center" style="overflow:hidden">
  <div class="panel-content tblheader">
	<h2><?php echo __(' Mistakes'); ?></h2>
	<?php
	//echo $this->Element('paging');
	?>	
	</div>
	<DIV class="tableWrapper tblheader">
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">

			<COL class="col80">
			<COL class="col100">

	
	<tr>
			<th class="col80">Username</th>
			<th class="col100"><?php //echo $this->Paginator->sort('cnt', 'Count'); ?>Count</th>
	</tr>
  </table>
  </div>	
  <div class="data tableWrapper"><div class="innerWrapper">
    	<table cellpadding="0" cellspacing="0" class="gentbl"  width="100%">
    
			<COL class="col80">
			<COL class="col100">
    	<?php

    	foreach ($Mistakes as $m): ?>
    	<tr>
    		<td class="col80" align="center"><?php echo $m['User']['username']? $m['User']['username']: ''; ?></td>
    		<td class="col100" align="center"><?php echo $m['0']['cnt']; ?></td>
    	</tr>
    <?php endforeach; 
    ?>
    	</table>
   	</div>
	</div>

</div>

</div>
<script>
$(document).ready(function() {
    $('.tblheader').css('margin-right', scrollbarWidth);  
  // create the layout - with data-table wrapper as the layout-content element  
  $('#mistakesgdiv').layout({
    center__contentSelector: '#mistakesgdiv div.data',
    resizeWhileDragging: true
  });
  
});
</script>