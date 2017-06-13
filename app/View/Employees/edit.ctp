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
if ($fax_enabled) {
  $labels = array(CONTACT_PHONE => 'Phone', CONTACT_CELL => 'Cell', CONTACT_EMAIL => 'Email', CONTACT_VMAIL => 'Voicemail', CONTACT_TEXT => 'Text', CONTACT_WEB => 'URL', CONTACT_PAGER => 'Pager', CONTACT_FAX => 'Fax');
}
else {
  $labels = array(CONTACT_PHONE => 'Phone', CONTACT_CELL => 'Cell', CONTACT_EMAIL => 'Email', CONTACT_VMAIL => 'Voicemail', CONTACT_TEXT => 'Text', CONTACT_WEB => 'URL', CONTACT_PAGER => 'Pager');
}
$icons = array( CONTACT_PHONE => '/img/icons/phone.png',  CONTACT_CELL => '/img/icons/text.png',  CONTACT_EMAIL => '/img/icons/email.png',  CONTACT_VMAIL => '/img/icons/voicemail.png',  CONTACT_TEXT => '/img/icons/text.png',  CONTACT_WEB => '/img/icons/web.png', CONTACT_PAGER => '/img/icons/pager.jpg', CONTACT_FAX => '/img/icons/fax.jpg');
?>
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
#add-contact, #add-contact ul {text-align:left; width: 50px;padding: 4px 15px;}
}
</style>

</script>


<style>
  table.contacts tr td {padding: 10px;}
  #empcontacts .handle {text-decoration: none;}
  
</style>
<div class="panel-content employees form" style="position:relative;">
<?php echo $this->Form->create('Employee', array('id'=> 'emp_edit')); ?>
		<h1><?php echo h($this->request->data['Employee']['name']); ?></h1>
	<div>
	<?php
		echo $this->Form->input('id', array('type' => 'hidden'));
		echo $this->Form->input('account_id', array('type' => 'hidden'));
		echo $this->Form->input('did_id', array('type' => 'hidden'));
		echo $this->Form->input('name');
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
      <tr><th width="15"></th><th width="80"></th><th width="110">&nbsp;</th><th width="570">&nbsp;</th></tr>    
    <?php
    $old_type = '';
    if ($contacts) {
      foreach ($contacts['label'] as $k => $d) {
          if (in_array($contacts['contact_type'][$k], array(CONTACT_PHONE, CONTACT_TEXT, CONTACT_FAX))) {
            $type = 'phone';
            $size = '15';
          }
          else {
            $type="";
            $size = '30';
          }
          
          echo '<tr onmouseover="showDel(this);" onmouseout="hideDel(this);"><td width="15"><a href="#" class="handle" onclick="return false;" title="Click and drag to reorder">&equiv;</a></td><td><input type="hidden" name="data[Contact][sort]['.$k.']" value="'.$k.'"><input type="hidden" name="data[Contact][visible]['.$k.']" value="0"><input type="checkbox" name="data[Contact][visible]['.$k.']" value="1" '.($contacts['visible'][$k]? 'checked': '').'> visible</td><td align="right"><span class="ceditable" contenteditable=true>';
          echo $contacts['label'][$k] . '</span>&nbsp;&nbsp;<img src="'.$icons[$contacts['contact_type'][$k]].'" align="absmiddle">';
          echo '</td><td><input type="hidden" class="clabel" name="data[Contact][label]['.$k.']" value="'.$contacts['label'][$k].'"><input type="hidden" name="data[Contact][id]['.$k.']" value="' . $contacts['id'][$k] . '"><input type="hidden" name="data[Contact][contact_type]['.$k.']" value="'.$contacts['contact_type'][$k].'"><input type="text" name="data[Contact][contact]['.$k.']"  size="'.$size.'" value="' . $contacts['contact'][$k] . '" class="mycontact required '.$type.'">';
          if ($contacts['contact_type'][$k] == CONTACT_PHONE) {
          	echo '&nbsp;&nbsp;&nbsp;Ext: <input type="hidden" name="data[Contact][carrier]['.$k.']" value="" ><input type="text" name="data[Contact][ext]['.$k.']"  class="numeric" size="10" value="'  .$contacts['ext'][$k] . '" >';
          }
          else if ($contacts['contact_type'][$k] == CONTACT_TEXT) {
          	echo '&nbsp;&nbsp;&nbsp;Carrier: <input type="hidden" name="data[Contact][ext]['.$k.']" value="" ><select name="data[Contact][carrier]['.$k.']" class="required"><option value="">Select';
          	foreach($sms_carriers as $car) {
          		echo '<option value="'.$car['c']['id'].'"';
          		if ($contacts['carrier_id'][$k] == $car['c']['id']) echo ' selected';
          		echo '>'.$car['c']['name'].' ('.$car['c']['addr'].')';
          		if (!empty($car['c']['prefix'])) echo ' -- Prefix: ' . $car['c']['prefix'];
          		echo '</option>';
          	}
          	echo '</select>  <a href="#" onclick="testText(this); return false;" title="Not sure if you picked the right carrier?  Click \'test\' to send a quick test message to your text device">unsure?</a>';
          }        
          else {
            echo '<input type="hidden" name="data[Contact][ext]['.$k.']" value="" ><input type="hidden" name="data[Contact][carrier]['.$k.']" value="" >';
          }
  //        echo '&nbsp;<span class="trash is_hidden"><a href="#" onclick="deleteContact('.$contacts['id'][$k].', ';
          echo '&nbsp;<span class="trash is_hidden"><a href="#" onclick="deleteContact(this, '.$contacts['id'][$k] .')" title="Remove this contact">';
          echo '<img src="/img/icons/delete.png" width="12" height="12" align="absmiddle"></a></span>';
          echo '</td></tr>';
      }
    }
	?>
	</tbody>
	  </table>
		<br><br>
    <h2>Edits</h2>
    <table cellpadding="2" cellspacing="0" border="0" class="gentbl">
      <tr><th width="160">Date</th>
        <th width="100">User</th>
        <th width="350">Description</th>
      </tr>
      <?php 
      if ($this->request->data['EmployeesEdit']) {
        foreach ($this->request->data['EmployeesEdit'] as $e) {
          echo '<tr>';
          echo '<td>'.$e['created'].'</td>';
          echo '<td>'.$e['user_username'].'</td>';
          echo '<td>'.str_replace("\r\n", "<br>", $e['description']);
          if ($e['change_type'] == 'delete' && $e['section'] == 'employee') {
          ?>
            <br><a href="#" onclick="user_confirm('Are you sure you want to recover this data?', function() { getJson('/Employees/recover/<?php echo $e['id']; ?>', null, null); return false;});">recover</a>         <?php   
          }

          echo '</td>';
          echo '</tr>';
        }
      }
      ?>
    </table>
	</div>

	<br><br>
	</form>
<?php //echo $this->Form->end(__('Submit')); ?>
</div>
<script>

  $(function() {
    $('#empcontacts tbody').sortable({ handle: ".handle" });
    $('.phone').mask("(999) ?999-99999");      
   	$('.numeric').mask("?9999999",{placeholder:" "});     
    $('#did_save_btn').off('click');
    $('#did_save_btn').on('click', function() {
      saveEmployee('<?php echo $employee_id; ?>');
    })

    $('#emp_edit .ceditable').blur( function() {
  	changeLabel(this)});      
    
    $('#emp_edit .ceditable').on( 'keypress', function(e){
      if(e.keyCode == 13)
      {
        return false;
      }
    });       
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


