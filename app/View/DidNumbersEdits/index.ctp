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
    'update' => '#did-content',
    'evalScripts' => true
));
?>
<div style="height: 100%; width: 100%;" id="editlog">
<div class="ui-layout-center" style="overflow:hidden">
<div class="tblheader panel-content">

    <h2>Edit History</h2>
    <form>
    <?php
    $options = array("" => 'All', "summary" => "Message Summary", "did" => "Account Info", "employee,employee_contact" => "Employees", "calltype" => "Calltypes", "schedule" => "Calltype Instructions", "oncall" => "On-calls");

		echo '<b>Type:</b> ' . $this->Form->input('Search.edit_type', array('div' => false, 'options' => $options, 'label' => false));    
		?>
		
    <input type="submit" onclick="loadPagePost(null, '/DidNumbersEdits/index/<?php echo $did_id; ?>', 'did-content', $(this).parent('form').serialize(), null);return false;" value="Go"></form>
	<?php
	echo $this->Element('paging');
	?>    
</div>
	<div class="tableWrapper tblheader">
    <table cellpadding="2" cellspacing="0" border="0" class="gentbl" width="100%">
			<COL class="col170">
			<COL class="col100">
			<COL class="col170">	     
      <tr><th width="170">Date</th>
        <th width="100">User</th>
        <th width="170" align="left">Description</th>
      </tr>
    </table>
   </div>
  <div class="data tableWrapper"><div class="innerWrapper">	   
    <table cellpadding="2" cellspacing="0" border="0" class="gentbl" width="100%">
			<COL class="col170">
			<COL class="col100">
			<COL class="col170">	      
      <?php 
      if ($edits) {
        foreach ($edits as $e) {
          echo '<tr>';
          echo '<td width="170" align="center">'.$e['0']['created_f'].'</td>';
          echo '<td width="100" align="center">'.$e['DidNumbersEdit']['user_username'].'</td>';
          echo '<td width="170">'.str_replace("\r\n", "<br>", $e['DidNumbersEdit']['description']);
          if ($e['DidNumbersEdit']['change_type'] == 'delete' && $e['DidNumbersEdit']['section'] == 'employee') {
          ?>
            <br><a href="#" onclick="user_confirm('Are you sure you want to recover this data?', function() { getJson('/Employees/recover/<?php echo $e['DidNumbersEdit']['id']; ?>', null, null); return false;});">recover</a>         <?php   
          }
          else if ($e['DidNumbersEdit']['change_type'] == 'delete' && $e['DidNumbersEdit']['section'] == 'oncall') {
          ?>
            <br><a href="#" onclick="user_confirm('Are you sure you want to recover this data?', function() { getJson('/CallLists/recover/<?php echo $e['DidNumbersEdit']['id']; ?>', null, null); return false;});">recover</a>         <?php   
          }           
          else if ($e['DidNumbersEdit']['change_type'] == 'delete' && $e['DidNumbersEdit']['section'] == 'summary') {
          ?>
            <br><a href="#" onclick="user_confirm('Are you sure you want to recover this data?', function() { getJson('/MessagesSummary/recover/<?php echo $e['DidNumbersEdit']['id']; ?>', null, null); return false;});">recover</a>         <?php   
          }          
          echo '</td>';
          echo '</tr>';
        }
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
$(function() {
    $('.tblheader').css('margin-right', scrollbarWidth);

	  $('#editlog').layout({
    center__contentSelector: '#editlog div.data',
    resizeWhileDragging: true
  });	  
});
</script>		
