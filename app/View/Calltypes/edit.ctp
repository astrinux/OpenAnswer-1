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
$this->extend('/Common/view');
?>
<style>
.c_header {font-size:14px; border-bottom: 2px solid #ccc; margin-bottom:0px;padding: 10px;}
.c_header a, .c_header a:visited {color: #333; text-decoration:none;}
.c_content {padding:20px;}
.c_div {margin-top:10px;padding:10px; background: #fafafa;border:1px solid #ccc;}
</style>
<?php if (!$this->request->is('ajax')) print_r($this->request->data); 
foreach ($global_options['calltypes'] as $key => $ct) {
	$ctoptions[$ct['caption']] = $ct['description'];
}
?>

<div class="Calltypes form">
<?php echo $this->Form->create('Calltype'); ?>

	<?php
		echo $this->Form->input('id', array('type' => 'hidden'));
		echo $this->Form->input('account_id', array('type' => 'hidden'));
//		echo $this->Form->input('title', array('options' => $ctoptions, 'style' => 'width:450px', 'multiple', 'label' => 'Call Types', 'class' => 'chzn-select'));
		echo $this->Form->input('title', array('options' => $ctoptions, 'style' => 'width:450px', 'label' => 'Call Type'));
		echo $this->Form->input('type');
		
		//foraeach ($this->request->data['
		
		if (count($this->request->data['Schedule']) > 1) $active = ', active: false';
		else $active = '';
	?>
	
<script type="text/javascript">
<?php 
$jsondata['actions'] = $this->request->data['Schedule'];
$jsondata['employees'] = $employees;


?>
var htmldata;
var jsondata = <?php echo json_encode($jsondata);?>;



    (function($) {
        $(function() {
            $(".c_header a").on('click', function() {
            	var thediv = $(this).parent().next();
            	thediv.slideToggle('slow');
            	if (thediv.is(":visible")) {
            		$(this).html($(this).html().replace('&darr;','&uarr;'));
            	}
            	else 
            		$(this).html($(this).html().replace('&uarr;','&darr;'));
            	return false;
            });
        })
    })(jQuery);
</script>

<div style="margin: 40px;; width: 600px;">
	
		<?php
		foreach ($this->request->data['Schedule'] as $schedule) {
		?>
    <div class="c_div">
  			<div class="c_header">
        <a href="#">&darr;&nbsp;&nbsp;<?php echo $this->element('calltype_schedule', array('schedule' => $schedule, 'daysofweek' => $php_daysofweek, 'showlinks' => false)); ?></a>
        </div>
        <div class="c_content" style="display:none;">
          <a href="/Schedules/edit/<?php echo $schedule['id']; ?>">edit</a>
					<?php
					foreach ($schedule['Action'] as $action) {        
						echo '<div class="step">' . $this->element('calltype_schedule_edit', array('action' => $action, 'showlinks' => false)) . '</div>';
					}
					?>
        
        <?php //echo $schedule['instructions']; ?>
        </div>
        

    </div>
			
		<?php
		}?>
</div>
	
<?php echo $this->Form->end(__('Submit')); ?>
</div>


     
<script type="text/javascript">
$(function() {
});
</script>
