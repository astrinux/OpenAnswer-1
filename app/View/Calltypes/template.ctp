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
  .choices {margin: 10px 30px;}
  h2 {margin-top:30px; margin-bottom: 10px; font-size: 16px; font-weight: normal}
  div.input {padding: 10px 20px !important;}
  #CalltypeTemplateEditForm label {display: inline;  width: 300px;}  
  
 </style>
 <div class="panel-content">
 <div class="calltypes form">
  <form id="CalltypeTemplateEditForm" method="post" accept-charset="utf-8">
  <?php
    echo $this->Form->input('Calltype.account_id', array('type' => 'hidden', 'value' => $account_id));
    echo $this->Form->input('Calltype.did_id', array('type' => 'hidden', 'value' => $did_id));
    echo '<h2>Select the Call Types to import to this account</h2>';
    foreach ($Calltypes as $Calltype):
  ?> 
  
    <?php echo '<input type="checkbox" name="data[Calltypes]['.$Calltype['Calltype']['id'].']" id="ctp'.$Calltype['Calltype']['id'].'" value="1">'; ?>
    <?php echo '<label for="ctp'.$Calltype['Calltype']['id'].'">'.$Calltype['Account']['account_num'] . ' ' . $Calltype['DidNumber']['company'] . ': ' . $Calltype['Calltype']['title'].'</label>'; ?> <br>
<?php endforeach; ?>
  </form>
</div>

</div>

<script>

  
  
  
  $(document).ready(function() {  
    $('#did_save_btn').off('click');
    $('#did_save_btn').on('click', function() {
      saveTemplate(this, <?php echo $did_id; ?>);
    //console.log($('#calltype_form').serialize());
    });    
  });
</script>
