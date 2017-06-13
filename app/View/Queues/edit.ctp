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
<div class="queues form" id="qedit">
<?php echo $this->Form->create('Queue', array('id' => 'editQueue')); ?>
  <h1>Queue: <?php echo $this->request->data['Queue']['extension'] ?> - <?php echo $this->request->data['Queue']['descr'] ?></h1>	

  <table cellpadding="2" cellspacing="0" class="gentbl">
    <?php
    $col = 1;
    foreach ($users as $u) {
      if ($col == 1) echo '<tr>';
      echo '<td><input type="checkbox" class="cb" value="'.$u['User']['id'].'" name="data[Misc][]"';
      $key = array_search($u['User']['id'], $members);
      if ($key !== false) {
        echo ' checked';
        $p = ' - ' . $penalty[$key];
      }
      else $p = '';
      echo '> </td><td width = "200">'.$u['User']['firstname'] . " " . $u['User']['lastname'].' <i>(' . $u['User']['role'] . ') </i>';
      if ($key !== false) echo '&nbsp;&nbsp;<input type="text" name="data[Penalty][]" value="'.(($penalty[$key]=='')? '0': $penalty[$key]).'" size="2">';
      else echo '&nbsp;&nbsp;<input type="text" name="data[Penalty][]" value="'.(($penalty[$key]=='')? '0': $penalty[$key]).'" size="2" disabled class="is_hidden">';
      echo '</td>';
      $col++;
      if ($col == 4) {
        echo '</tr>';
        $col = 1;
      }
    }
	  ?>
    
  </table>
</div>
<script>
  $(document).ready(function() {  

    $('#user_save_btn').off('click');
    $('#qedit .cb').on('click', function() {
      $(this).parents('tr').find('input');
      if (this.checked) {
        $(this).parent('td').next('td').find('input[type=text]').removeClass('is_hidden');
        $(this).parent('td').next('td').find('input[type=text]').prop('disabled', false);
      }
      else {
        $(this).parent('td').next('td').find('input[type=text]').addClass('is_hidden');
        $(this).parent('td').next('td').find('input[type=text]').prop('disabled', true);
      }
    });
    $('#user_save_btn').on('click', function() {
      saveQueue(<?php echo $this->request->data['Queue']['extension']; ?>);
    //console.log($('#calltype_form').serialize());
    });
	});   
</script> 
