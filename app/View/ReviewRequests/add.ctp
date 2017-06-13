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
<div class="panel-content">
    <?php 
        echo $this->Form->create('DidNumber', array('id' => 'review_form')); 
        echo $this->Form->input('id', array('type' => 'hidden'));    
        echo $this->Form->input('Account.account_num', array('type' => 'hidden'));    
        echo $this->Form->input('DidNumber.company', array('type' => 'hidden'));    
        echo $this->Form->input('Account.account_num', array('type' => 'text', 'disabled' => true));    
        echo $this->Form->input('DidNumber.company', array('type' => 'text', 'disabled' => true));    
        echo '<p>Please enter the details of why you are requesting a review</p>';
        echo $this->Form->input('Misc.reason', array('type' => 'text', 'rows' => '20', 'cols' => 60, 'label', 'Request Details'));    
    ?>
    <div class="input">
    <input type="submit" value="Submit" id="acct_rev" />
    </div>
    </form>
</div>
<script>
$(function() {
    $('#acct_rev').on('click', function() {
          postJson('/ReviewRequests/add', $('#review_form').serialize(), function(data) {
            if (data.success) {
              alert('Request submitted');
              $('#dialogWin').dialog('close');              
            }
            else alert('Unable to submit request, please try again later');
          }); 			
        return false;
    });
});
</script>