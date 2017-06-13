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
<div class="panel-content form">
<?php echo $this->Form->create('ReviewRequest', array('id' => 'reviewreq')); ?>
        <h2><?php echo __('Edit Review'); ?></h2>
    <?php
        echo $this->Form->input('id');
        echo $this->Form->input('created', array('type' => 'text', 'disabled' => true));
        echo $this->Form->input('did_id', array('type' => 'hidden'));
        echo $this->Form->input('DidNumber.company');
        echo $this->Form->input('status', array('options' => array('0' => 'Pending', '1' => 'Resolved')));
        echo $this->Form->input('description', array('rows' => 10, 'cols' => 80));
        echo $this->Form->input('notes', array('rows' => 10, 'cols' => 80));
    ?><br>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

<script>
$(function() {
    $('#reviewreq').on('submit', function() {
        var url = "<?php echo $this->Html->url(array('controller' => 'ReviewRequests', 'action' => 'edit', $this->request->data['ReviewRequest']['id'] )); ?>";
        $.post(url, $('#reviewreq').serialize(), function(data) {
            if (data.success) {
                createToast('info', data.msg);
            }
            else {
                alert(data.msg); 
            }
        }, 'json');
        return false;
    });
});
</script>
