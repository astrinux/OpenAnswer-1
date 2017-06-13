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
    'update' => $target,
    'evalScripts' => true
));
?>
<div id="<?php echo $div_id; ?>" style="height: 100%; width: 100%;">

<div class="Complaints index">
  <div class="panel-content tblheader searchbox">
	<h2><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['complaints']; ?>"></i> <?php echo __(' Complaints'); ?></h2>
  <div class="wrapper">
	<a class="addcomplaint" href="#"><i class="fa fa-plus fa-lg"> </i> Add Complaint</a>
	<?php
	if (!empty($did_id)) { ?>	
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="openComplaintDialog('null', '<?php echo $did_id; ?>', 'add', '', null);  return false;"><i class="fa fa-plus"></i> Add Complaint</a>
	<?php
  }
	echo $this->Element('paging');
	?>
	</div>
	</div>
	<DIV class="tableWrapper tblheader">
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
			<COL class="col110">
			<COL class="col120">
			<COL class="col80">
			<COL class="col110">
			<COL class="col150">
			<COL class="col130">
			<COL class="col120">
			<COL class="col120">
	  <tr>
			<th class="col110"><?php echo $this->Paginator->sort('created'); ?></th>
			<th class="col120"><?php echo $this->Paginator->sort('company'); ?></th>
			<th class="col80"><?php echo $this->Paginator->sort('status'); ?></th>
			<th class="col110"><?php echo $this->Paginator->sort('incident_date'); ?></th>
			<th class="col150"><?php echo $this->Paginator->sort('category'); ?></th>
			<th class="col130"><?php echo $this->Paginator->sort('callers_name', 'Caller\'s name'); ?></th>
			<th class="col120"><?php echo $this->Paginator->sort('user_username', 'Operator'); ?></th>
			<th class="col120 actions"><?php echo __('Actions'); ?></th>
	  </tr>
  </table>
  </div>
  <div class="data tableWrapper"><div class="innerWrapper">
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
			<COL class="col110">
			<COL class="col120">
			<COL class="col80">
			<COL class="col110">
			<COL class="col150">
			<COL class="col130">
			<COL class="col120">
			<COL class="col120">	
	<?php
	foreach ($Complaints as $Complaint): ?>
	<tr>
		<td class="col110 editcomplaint" align="center" data-id="<?php echo $Complaint['Complaint']['id'];?>"><?php echo h($Complaint['Complaint']['created_f']); ?></td>	
		<td class="col120 editcomplaint" align="center" data-id="<?php echo $Complaint['Complaint']['id'];?>"><?php echo $Complaint['Account']['account_num'] . ' - ' . $Complaint['DidNumber']['company']; ?></td>
		<td class="col110 editcomplaint" align="center" data-id="<?php echo $Complaint['Complaint']['id'];?>"><?php echo $complaint_options[$Complaint['Complaint']['status']]; ?></td>
		<td class="col110 editcomplaint" align="center" data-id="<?php echo $Complaint['Complaint']['id'];?>"><?php echo h($Complaint['Complaint']['incident_date_f']); ?></td>
		<td class="col150 editcomplaint" align="center" data-id="<?php echo $Complaint['Complaint']['id'];?>"><?php echo h($Complaint['Complaint']['category']); ?></td>
		<td class="col130 editcomplaint" align="center" data-id="<?php echo $Complaint['Complaint']['id'];?>"><?php echo h($Complaint['Complaint']['callers_name']); ?></td>
		<td class="col120 editcomplaint" align="center" data-id="<?php echo $Complaint['Complaint']['id'];?>"><?php if ($Complaint['0']['operators']) echo $Complaint['0']['operators']; else echo '&nbsp;'?></td>
		<td class="col120 editcomplaint actions" align="center">
			<a href="#" class="delcomplaint" data-id="<?php echo $Complaint['Complaint']['id'];?>"><i class="fa fa-trash fa-lg"></i></a>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	</div></div>
</div>

</div>
<?php
echo $this->Js->writeBuffer();
?>
<script>
$(function() {
    $('.tblheader').css('margin-right', scrollbarWidth);
    $('#<?php echo $div_id; ?> .editcomplaint').on('click', function() {
			var parent_div = $('#<?php echo $div_id; ?>').parent('div').attr('id');
			openDialogWindow('/Complaints/edit/' + $(this).attr('data-id'), 'Complaint edit', null, function() {
					loadPage(this, '/Complaints/index/<?php echo $did_id; ?>', parent_div);
				}, 900, 600);			
    });
    
    $('#<?php echo $div_id; ?> .delcomplaint').on('click', function() {
    	var data_id = $(this).attr('data-id');
			var parent_div = $('#<?php echo $div_id; ?>').parent('div').attr('id');

   		user_confirm('Are you sure you want to delete this entry?', function () {
					var url = '/Complaints/delete/' + data_id;
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'json',
  	      	type: 'GET'
  	      }).done(function(data) {
            if (data.success) {
    					loadPage(this, '/Complaints/index/<?php echo $did_id; ?>', parent_div);          
            }
            alert(data.msg);
  	      });	   			
   		}); 
   		return false; 
    });
    <?php 
    $url = "/Complaints/add";
    if (!empty($msg_id)) {
    	$url .= "/"	. $msg_id;
    }
    else {
    	$url .= "/null";
    }
    if (!empty($did_id)) {
    	$url .= "/"	. $did_id;
    }
    ?>
    
    $('#<?php echo $div_id; ?> .addcomplaint').on('click', function() {
			var parent_div = $('#<?php echo $div_id; ?>').parent('div').attr('id');
			openDialogWindow('<?php echo $url; ?>', 'Add Complaint', null, function() {
					loadPage(this, '/Complaints/index/<?php echo $did_id; ?>', parent_div);
				}, 900, 600);			    
		});

});

</script>
