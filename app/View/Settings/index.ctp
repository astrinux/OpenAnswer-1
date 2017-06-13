<?php
$this->extend('/Common/view');
?>
<style>
label {
    display: block;
    font-weight: bold;
}

.list-inline {
    list-style: none;
    margin: 20px 0px;
    padding: 0px;
}

</style>
<div class="settings index large-9 medium-8 columns content">
    <h3><?= __('Settings') ?></h3>
    <ul class="list-inline">
        <li><?php echo $this->Html->link(__('Add New Setting'), array('action' => 'add')) ?></li>
    </ul>
    <table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
        <thead>
            <tr>
                <th scope="col"><?php echo  $this->Paginator->sort('name') ?></th>
                <th scope="col" class="actions"><?php echo  __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($settings as $setting): ?>
            <tr>
                <td><?php echo  h($setting['Setting']['name']) ?></td>
                <td class="actions">
                    <?php echo  $this->Html->link(__('Edit'), array('action' => 'edit', $setting['Setting']['id'])) ?>
                    <?php //echo  $this->Html->link(__('Delete'), array('action' => 'delete', $setting['Setting']['id']), array('confirm' => __('Are you sure you want to delete # {0}?', $setting['Setting']['id']))) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?php echo  $this->Paginator->prev('< ' . __('previous')) ?>
            <?php echo  $this->Paginator->numbers() ?>
            <?php echo  $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?php echo  $this->Paginator->counter() ?></p>
    </div>
</div>
