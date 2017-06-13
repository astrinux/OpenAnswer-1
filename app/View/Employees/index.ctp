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
$labels = array(CONTACT_PHONE => 'Phone', CONTACT_CELL => 'Cell', CONTACT_EMAIL => 'Email', CONTACT_VMAIL => 'Voicemail', CONTACT_TEXT => 'Text', CONTACT_WEB => 'URL', CONTACT_PAGER => 'Pager', CONTACT_FAX => 'Fax');
$icons = array( CONTACT_PHONE => '/img/icons/phone.png',  CONTACT_CELL => '/img/icons/text.png',  CONTACT_EMAIL => '/img/icons/email.png',  CONTACT_VMAIL => '/img/icons/voicemail.png',  CONTACT_TEXT => '/img/icons/text.png',  CONTACT_WEB => '/img/icons/web.png', CONTACT_PAGER => '/img/icons/pager.jpg', CONTACT_FAX => '/img/icons/fax.jpg');
?>

<script>
var carrierArray = <?php echo json_encode($carriers); ?>;
var labels = {'<?php echo CONTACT_PHONE?>': 'Phone', '<?php echo CONTACT_CELL; ?>':  'Cell', '<?php echo CONTACT_EMAIL; ?>' : 'Email', '<?php echo CONTACT_VMAIL; ?>': 'Voicemail', '<?php echo CONTACT_TEXT; ?>':  'Text', '<?php echo CONTACT_WEB; ?>': 'URL', '<?php echo CONTACT_FAX; ?>': 'Fax'};

var icons = <?php echo json_encode($icons); ?>;

function populateSelect() {
  var carrier;
  $.each(carrierArray, function(key, value) {   
    if (value.c.prefix) {
      carrier = value.c.name + ' ('+value.c.addr+') -- Prefix:' + value.c.prefix;
    }
    else {
      carrier = value.c.name + ' ('+value.c.addr+')';
    }
     $('select.carrier')
         .append($("<option></option>")
         .attr("value",value.c.id)
         .text(carrier)); 
  });
}


function showDel(t) {
	$(t).find('.trash').show();
}

function hideDel(t) {
	$(t).find('.trash').hide();
}
function showDel(t) {
	$(t).find('.trash').show();
}

function hideDel(t) {
	$(t).find('.trash').hide();
}

function deleteContact(t, id) {
  if (id !== null) {
  	var url = '/EmployeesContact/checkDelete/' + id;
    $.ajax({
    	url: url,
    	dataType: 'json',
    	type: 'GET',
    }).done(function(data) {
  		if (!data.success) {
  		  alert(data.msg);
  		  return false;
  		}
  		else {
      	$(t).parents('tr').addClass('tbd');
      	$(t).parents('tr').find('input').prop('disabled', true);
      	alert('You must still click on \'Save\' to save your changes');
      	return true;
  		}
  
    });	
  }
  else {
      	$(t).parents('tr').addClass('tbd');
      	$(t).parents('tr').find('input').prop('disabled', true);    
    	alert('You must still click on \'Save\' to save your changes');
    	return true;
  }
}

/*function deleteContact(id, can_be_deleted) {
	if (!can_be_deleted) {
		alert('This contact cannot be deleted since it is used in a call type instruction');
		return false;
	}
				var url = '/EmployeesContact/delete/' + id;
	      $.ajax({
	      	url: url,
	      	dataType: 'json',
	      	type: 'GET',
	      }).done(function(data) {
          

	      });		
}*/



function saveEmployee(employee_id) {
	if ($('#empcontacts').find('tr').not('.tbd').length < 2) {
	  alert('You must specify at least one contact for this employee');
	}
	else {
    var missing_info = checkMissingInfo('emp_edit');
		if (!missing_info) {

				var url = '/Employees/edit/' + employee_id;
	      $.ajax({
	      	url: url,
	      	dataType: 'json',
          data: $('#emp_edit').serialize(),
	      	type: 'POST',
	      }).done(function(data) {
          if (data.success) {
           loadPagePost(null, '/Employees/index/<?php echo $did_id; ?>', 'did-content', 'target=did-content&detail=did-detail', null);               
            didLayout.center.children.layout1.close('east');	          
          }
          alert(data.msg);
	      });	
	  }
	  else alert('You must fill in the required fields');
	}
}


function loadEmployee(did_id, employee_id) {
				var url = '/Employees/edit/' + employee_id;
	      $.ajax({
	      	url: url,
	      	dataType: 'html',
	      	type: 'GET',
	      }).done(function(data) {
					$('#did-detail').html(data);
					didLayout.center.children.layout1.open('east');
	      });	
}

function addEmployee(did_id, target) {
				var url = '/Employees/add/' + did_id;
	      $.ajax({
	      	url: url,
	      	dataType: 'html',
	      	type: 'GET',
	      }).done(function(data) {
					$('#did-detail').html(data);
					didLayout.center.children.layout1.open('east');
	      });	
}

function deleteEmployee(id) {
				var url = '/Employees/delete/' + id;
	      $.ajax({
	      	url: url,
	      	dataType: 'json',
	      	type: 'GET',
	      }).done(function(data) {
					if (data.success) {
            loadPagePost(null, '/Employees/index/' + $('#find_did').val(), 'did-content', 'target=did-content&detail=did-detail', null);               
						       
					}
					else alert(data.msg);

	      });	
}


</script>

<?php
$this->Paginator->options(array(
    'update' => '#did-content',
    'evalScripts' => true
));
?>
<div class="empdiv">

<div class="employees index">
  <div class=" panel-content tblheader fg_grey">
	<h2><i class="fa <?php echo $global_options['icons']['setup']; ?>"></i> <?php echo __(' Employees'); ?></h2>
	<b>Search:</b> <input type="text" size="20" value="" onkeyup="searchFilter('employeetbl', this.value)">
<?php if ($this->Permissions->isAuthorized('EmployeesAdd',$permissions)) { ?> 
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="addEmployee('<?php echo $did_id; ?>'); return false;"><i class="fa fa-plus"></i> Add Employee</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="#" onclick="openDialogWindow('/CellCarriers', 'Application Settings', null); return false;"><i class="fa fa-mobile"></i> Cell carriers</a>
<?php } ?>
<br>
<?php
	echo $this->Element('paging');
	?>
  </div>			
	<table cellpadding="0" cellspacing="0" class="gentbl" id="employeetbl" width="100%">
  <thead>
	  <tr>
			<th align="left" width="80"><?php echo $this->Paginator->sort('id'); ?></th>
			<th align="left" width="150"><?php echo $this->Paginator->sort('name'); ?></th>
			<th width="60"><?php echo $this->Paginator->sort('sort'); ?></th>
			<th align="left" width="300">Special Instructions</th>
			<th class="actions" filter="false"><?php echo __('Actions'); ?></th>
	  </tr>
	</thead>
  <tbody>
	<?php
	foreach ($Employees as $Employee): ?>
	<tr>
	  <td  onclick="loadEmployee('<?php echo $Employee['Employee']['did_id']; ?>','<?php echo $Employee['Employee']['id']; ?>', 'did-detail'); return false;"><?php echo $Employee['Employee']['id']; ?></td>
		<td  onclick="loadEmployee('<?php echo $Employee['Employee']['did_id']; ?>','<?php echo $Employee['Employee']['id']; ?>', 'did-detail'); return false;"><span class="<?php echo strtolower($global_options['gender'][$Employee['Employee']['gender']]); ?>">&nbsp;</span>&nbsp;<?php echo $Employee['Employee']['name'];?></td>
	  <td align="center"  onclick="loadEmployee('<?php echo $Employee['Employee']['did_id']; ?>','<?php echo $Employee['Employee']['id']; ?>', 'did-detail'); return false;"><?php echo $Employee['Employee']['sort']; ?></td>
		<td  onclick="loadEmployee('<?php echo $Employee['Employee']['did_id']; ?>','<?php echo $Employee['Employee']['id']; ?>', 'did-detail'); return false;"><?php if ($Employee['Employee']['special_instructions']) echo '<span class="notes">'.$Employee['Employee']['special_instructions'].'</span>';?></td>
		<td class="actions">
<!--			<a href="#" onclick="loadEmployee('<?php echo $Employee['Employee']['did_id']; ?>','<?php echo $Employee['Employee']['id']; ?>', 'did-detail'); return false;"><img title="edit" alt="edit" src="/img/edit.png" width="16" height="16"></a>-->

<?php if ($this->Permissions->isAuthorized('EmployeesDelete',$permissions)) { ?> 
			
			<a href="#" onclick="user_confirm('Are you sure you want to delete this employee?', function() { deleteEmployee('<?php echo $Employee['Employee']['id'];?>'); return false;});"><img title="delete" alt="delete" src="/img/delete.png" width="16" height="16"></a>		
<?php } ?>

		</td>
	</tr>
<?php endforeach; ?>
</tbody>
	</table>

</div>

</div>
<?php
echo $this->Js->writeBuffer();
?>
<script>
$(document).ready(function() {
//	$('#employeetbl').tableFilter({enableCookies: false});
});
</script>