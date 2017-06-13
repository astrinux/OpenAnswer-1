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
<div class="reviews index">
  <div class="tblheader panel-content fg_grey">
  <form method="post" name="incident_form" id="incident_form">
<h2><?php echo __('Review Requests'); ?></h2>  
  <input type="hidden" name="format" value="">
  <?php
  echo $this->Form->input('Search.status', array('div' => false, 'options' => array('0' => 'Pending', '1' => 'Resolved'), 'empty' => 'Select')); ?>
  <input type="submit" class="submitbtn" value="Go">

  </form>
    <?php
    echo $this->Element('paging');
    ?>
  </div>     

    
    <table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
    <tr>
            <th><?php echo $this->Paginator->sort('created'); ?></th>
            <th><?php echo h('User'); ?></th>
            <th><?php echo h('Company'); ?></th>
            <th><?php echo $this->Paginator->sort('status'); ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
    </tr>
    <?php
    foreach ($reviews as $review): ?>
    <tr>
        <td><?php echo date('m/d/y h:ia', strtotime($review['ReviewRequest']['created'])); ?>&nbsp;</td>
        <td><?php echo h($review['User']['username']); ?>&nbsp;</td>
        <td><?php echo h($review['Account']['account_num']) . ' - ' . h($review['DidNumber']['company']); ?>&nbsp;</td>
        <td><?php echo $review['ReviewRequest']['status']? 'Resolved': 'Pending'; ?>&nbsp;</td>
        <td class="actions">
        <?php 
        $url = $this->Html->url(array('controller' => 'ReviewRequests', 'action' => 'edit',$review['ReviewRequest']['id'] ));
        ?>
          <a href="#"  onclick="openDialogWindow('<?php echo $url; ?>', 'Edit Request', null);return false;">edit</a>
        
        </td>
    </tr>
<?php endforeach; ?>
    </table>
</div>
