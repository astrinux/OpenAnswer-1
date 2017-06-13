<?php
$this->extend('/Common/view');
?>
<style>
input[type=text] {
    width: 90%;
}

.l_option {
    border-bottom: 1px dashed #ccc;
    padding: 10px;
}

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
<div class="settings form  content">
        <?php echo  $this->Form->create('Setting', array('id' => 'editSetting')) ?>
        <h3><?php echo  __('Add Setting') ?></h3>
        <ul class="list-inline">
            <li><?php echo  $this->Html->link(__('List Settings'), array('action' => 'index')) ?></li>
        </ul>
        <div id="formbody">
        <?php
            echo $this->Form->input('name');
            echo $this->Form->input('notes', array('cols' => 50, 'rows' => 5));            
            echo '<br><br><label>Value(s)</label><br>';
            echo '<div id="option_list">';            
                echo '<div class="l_option input">';
                echo $this->Form->input('value_array', array('name' => 'data[Setting][value_array][]', 'div' => false, 'class' => 'optionval', 'label' => false,  'type' => 'text', 'maxlength' => 255, 'value' => ''));
                echo '&nbsp;<a href="#" class="del_value"><i class="fa fa-trash"></i></a>&nbsp;<a href="#" class="reorder"><i class="fa fa-reorder"></i></a>';
                echo '</div>';            
            //echo $this->Form->input('value_array', array('id' => false, 'class' => 'optionval', 'div' => false, 'name' => 'data[Setting][value_array][]', 'label' => false,  'type' => 'text', 'maxlength' => 255));
            
            echo '</div>';
        ?>
        </div>
        &nbsp;&nbsp;<a href="#" onclick="$('#option_list div.input:last').clone().appendTo('#option_list').find('input').val(''); return false;">+</a>
    
    <?php echo  $this->Form->button(__('Submit')) ?>
    <?php echo  $this->Form->end() ?>
</div>

<script>
$(function() {
    $(window).keydown(function(event){
      if(event.keyCode == 13) {
        if ($(event.target).hasClass('optionval')) $('#formbody div.input:last input:last').clone().val('').appendTo('#formbody div.input:last');        
        event.preventDefault();
        $('#formbody div.input:last input:last').focus();
        return false;
      }
    });    
    
    $('.del_value').on('click', function() {
        var t = this;
        if (confirm('Are you sure you want to delete this value?')) {
            $(t).parents('div.input').remove();
        }
        return false;
    });    
});
</script>