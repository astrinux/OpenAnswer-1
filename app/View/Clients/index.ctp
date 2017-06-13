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
<div class="ui-widget">
	Account: <input id="account" />
</div>
<br><br>
<div class="clients index">
	<h2><?php echo __('Accounts'); ?></h2>
	<table cellpadding="2" cellspacing="0" class="gentbl">
	<tr>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('account_name'); ?></th>
			<th><?php echo $this->Paginator->sort('account_num'); ?></th>
			<th><?php echo $this->Paginator->sort('contact_name'); ?></th>
			<th><?php echo $this->Paginator->sort('contact_phone'); ?></th>
			<th><?php echo $this->Paginator->sort('contact_email'); ?></th>
			<th><?php echo $this->Paginator->sort('account_color'); ?></th>
			<th><?php echo $this->Paginator->sort('did'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($Clients as $Client): ?>
	<tr>
		<td><?php echo h($Client['Client']['name']); ?>&nbsp;</td>
		<td><?php echo h($Client['Client']['account_name']); ?>&nbsp;</td>
		<td><?php echo h($Client['Client']['account_num']); ?>&nbsp;</td>
		<td><?php echo h($Client['Client']['contact_name']); ?>&nbsp;</td>
		<td><?php echo h($Client['Client']['contact_phone']); ?>&nbsp;</td>
		<td><?php echo h($Client['Client']['contact_email']); ?>&nbsp;</td>
		<td><?php echo h($Client['Client']['account_color']); ?>&nbsp;</td>
		<td><?php echo h($Client['Client']['did']); ?>&nbsp;</td>
		<td class="actions">
			<?php //echo $this->Html->link(__('View'), array('action' => 'view', $Client['Client']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $Client['Client']['id']), array('actnum' => $Client['Client']['account_num'], 'nav' => '/pages/nav/account/'.$Client['Client']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $Client['Client']['id']), null, __('Are you sure you want to delete # %s?', $Client['Client']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, {:current} out of {:count} total')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('prev'), array(), null, array('class' => 'prev disabled'));
		echo '&nbsp;';
		echo $this->Paginator->numbers(array('separator' => ' | '));
		echo '&nbsp;';
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New  Client'), array('action' => 'add')); ?></li>
	</ul>
</div>

<script>
    $(document).ready(function(){
        $(".actions a").click(function(){
        	var href= this.href;
        	var act = $(this).attr('actnum');
        	var nav = $(this).attr('nav');
        	
					$.ajax(this.href).done(function(data) {
        		addTab(act, data, href, nav);

					});
          //$("#ui-tabs-1").load(this.href);
          return false;
        })
 $( "#account" ).autocomplete({
		source: "/Clients/search",
		minLength: 2,
		select: function( event, ui ) {
		
		}
});        
        
    });
</script>

