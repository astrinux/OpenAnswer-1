<?php
$this->extend('/Common/view');
?>

<div class="panel-content">
  <div id="msg_hourly" style="display: inline-block; width:45%; height: 250px; position: relative;">
  </div>
  <div id="msg_daily" style="display: inline-block; width:45%; height: 250px; position: relative">
  </div>
  <div style="clear:both; height: 50px;">&nbsp;</div>
  <div id="calls_hourly" style="display: inline-block; width:45%; height: 250px;">
  </div>
  <div id="calls_daily" style="display: inline-block; width:45%; height: 250px;">
  </div>
  
  <div style="clear:both; height: 50px;">&nbsp;</div>
  <div id="breaks_both" style="display: inline-block; width:100%; height: 250px; position: relative"><center><span style="font-size: 4.0em"><i class="fa fa-spinner fa-spin"></i></span></center>
  </div>

  <div id="mistakes_daily" style="display: inline-block; width:45%; height: 250px; position: relative">
  </div>
  
</div>
<script>
$(function() {
  
  $('#msg_hourly').load('/Messages/my_hourly/320/<?php echo date('Y-m-d'); ?>');
  $('#msg_daily').load('/Messages/my_daily/320/');
  $('#calls_hourly').load('/CallLogs/my_hourly/320/<?php echo date('Y-m-d'); ?>');
  $('#calls_daily').load('/CallLogs/my_daily/320/');
  $('#breaks_both').load('/UserLogs/my_daily/320/');
  $('#mistakes_daily').load('/Mistakes/my_daily/320/');
});
</script>