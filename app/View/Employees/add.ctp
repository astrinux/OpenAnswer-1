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
.ceditable {min-height: 16px; min-width: 100px;padding:2px;}
.ceditable:hover {background:#FFD455;}
div.editable {display:inline;}
.ceditable .empty {color: #aaa;}
#empcontacts .handle {text-decoration: none;}
}
</style>

<?php 
if ($fax_enabled) {
  $labels = array(CONTACT_PHONE => 'Phone', CONTACT_CELL => 'Cell', CONTACT_EMAIL => 'Email', CONTACT_VMAIL => 'Voicemail', CONTACT_TEXT => 'Text', CONTACT_WEB => 'URL', CONTACT_PAGER => 'Pager', CONTACT_FAX => 'Fax');
}
else {
  $labels = array(CONTACT_PHONE => 'Phone', CONTACT_CELL => 'Cell', CONTACT_EMAIL => 'Email', CONTACT_VMAIL => 'Voicemail', CONTACT_TEXT => 'Text', CONTACT_WEB => 'URL', CONTACT_PAGER => 'Pager');
}

$icons = array( CONTACT_PHONE => '/img/icons/phone.png',  CONTACT_CELL => '/img/icons/text.png',  CONTACT_EMAIL => '/img/icons/email.png',  CONTACT_VMAIL => '/img/icons/voicemail.png',  CONTACT_TEXT => '/img/icons/text.png',  CONTACT_WEB => '/img/icons/web.png', CONTACT_PAGER => '/img/icons/pager.jpg', CONTACT_FAX => '/img/icons/fax.jpg');
?>

<script>

</script>


<style>
  table.contacts tr td {padding: 10px;}
.ceditable {min-height: 16px; min-width: 100px;padding:2px;}
.ceditable:hover {background:#FFD455;}
div.editable {display:inline;}
.ceditable .empty {color: #aaa;}
tr.tbd * {color: #bbb; text-decoration:line-through}
.employees textarea {vertical-align: top}
.employees label {display: block; float: left; width: 100px;}
.trigger {margin: 10px;}
.trigger a {text-decoration:none;}
.trigger a:hover {text-decoration:underline;}
#add-contact, #add-contact ul {text-align:left; width: 50px;}
</style>
<div class="panel-content  employees form" style="position:relative;">
 
<?php echo $this->Form->create('Employee', array('id'=> 'emp_edit')); ?>
		<h1>New Employee</h1>
	<div>
	<?php
		echo $this->Form->input('id', array('type' => 'hidden'));
		echo $this->Form->input('did_id', array('type' => 'hidden', 'value' => $did_id));
		
		echo $this->Form->input('name', array('class' => 'required'));
		echo '<div class="input">';
		echo $this->Form->input('sort', array('type' => 'text', 'size' => '2', 'class'=> 'numeric', 'div' => false));
		echo '&nbsp;&nbsp;&nbsp;<i>(A low number puts the employee higher on the list)</i>';
		echo '</div>';
		echo $this->Form->input('gender', array('options' => $global_options['gender']));
		echo $this->Form->input('special_instructions');

    ?>
    <div class="trigger"><a href="#" data-dropdown="#add-contact" data-horizontal-offset="30" data-vertical-offset="25">+ Add contact</a>	  
		</div>    
    <table cellpadding="2" cellspacing="0" class="contacts" id="empcontacts">
    <tbody>
      <tr><th width="15"><th width="80"></th><th width="110">&nbsp;</th><th width="570">&nbsp;</th></tr>    
    <?php
    $old_type = '';
    $this->request->data['EmployeesContact'] = array();
    /*foreach ($this->request->data['EmployeesContact'] as $k=>$c) {
        echo '<tr onmouseover="showDel(this);" onmouseout="hideDel(this);"><td align="right" width="100"><span class="ceditable" contenteditable=true>';
        echo $c['label'] . '</span>&nbsp;&nbsp;<img src="'.$icons[$c['contact_type']].'" align="absmiddle">';
        echo '</td><td width="550"><input type="hidden" class="clabel" name="data[Contact][contact_type]['.$k.']" value="'.$c['contact_type'].'"><input type="hidden" class="clabel" name="data[Contact][label]['.$k.']" value="'.$c['label'].'"><input type="hidden" name="data[Contact][id]['.$k.']" value="' . $c['id'] . '"><input type="text" name="data[Contact][contact]['.$k.']" size="30" value="' . $c['contact'] . '" class="required type_'.$c['contact_type'].'">';
        if ($c['contact_type'] == CONTACT_PHONE) {
        	echo '&nbsp;&nbsp;&nbsp;Ext: <input type="text" name="data[Contact][ext]['.$k.']" size="10" value="'  .$c['ext'] . '" >';
        }
        else if ($c['contact_type'] == CONTACT_TEXT) {
        	echo '&nbsp;&nbsp;&nbsp;Carrier: <select name="data[Contact][carrier]['.$k.']" class="required" >';
        	foreach($carriers as $car) {
        		echo '<option value="'.$car['addr'].'"';
        		if ($c['carrier'] == $car['addr']) echo ' selected';
        		echo '>'.$car['name'].'</option>';
        	}
        	echo '</select>';
        }    
        else {
	        echo '<input type="hidden" name="data[Contact][ext]['.$k.']"value="" ><input type="hidden" name="data[Contact][carrier]['.$k.']"value="" >';
        }    
        echo '&nbsp;<span class="trash is_hidden"><a href="#" onclick="deleteContact(this, ';

        echo 'true)" title="Remove this contact">';
        echo '<img src="/img/icons/delete.png" width="12" height="12" align="absmiddle"></a></span>';
        echo '</td></tr>';
    }*/
	?>
    </tbody>
	  </table>

	</div>

	<br><br>
	</form>
</div>
<script>
  $('.ceditable').blur( function(){
    var thelabel = $(this).html()
    if (thelabel != '') $(this).parent().next().find('.clabel').val(thelabel);
    });  

  $(function() {
    $('#empcontacts tbody').sortable();
    
    $('#did_save_btn').off('click');
    $('#did_save_btn').on('click', function() {
      addEmployeeSubmit('<?php echo $did_id; ?>');
    })
   	$('.numeric').mask("?9999",{placeholder:" "});
    
  });
</script>	
		
	  <div id="add-contact" class="dropdown dropdown-tip dropdown-relative">
    	<ul class="dropdown-menu">
        <li><img src="/img/icons/email.png" align="left"> <a href="#1" onclick="addContact(<?php echo CONTACT_EMAIL; ?>, 'empcontacts'); return false;">Email</a></li>
        <li><img src="/img/icons/phone.png" align="left"> <a href="#1" onclick="addContact(<?php echo CONTACT_PHONE; ?>, 'empcontacts'); return false;">Phone</a></li>
        <li><img src="/img/icons/text.png" align="left"> <a href="#2" onclick="addContact(<?php echo CONTACT_TEXT; ?>, 'empcontacts'); return false;">Text</a></li>
        <!--<li><img src="/img/icons/voicemail.png" align="left"> <a href="#3" onclick="addContact(<?php echo CONTACT_VMAIL; ?>, 'empcontacts'); return false;">Voicemail</a></li>-->
        <li><img src="/img/icons/web.png" align="left"> <a href="#4" onclick="addContact(<?php echo CONTACT_WEB; ?>, 'empcontacts'); return false;">Web URL</a></li>
<?php 
if ($fax_enabled) {
  ?>
        <li><img src="/img/icons/fax.jpg" align="left"> <a href="#4" onclick="addContact(<?php echo CONTACT_FAX; ?>, 'empcontacts'); return false;">Fax</a></li>
<?php
}
?>

    	</ul>
		</div> 	


