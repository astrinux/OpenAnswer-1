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
#settings-tabs {border:0px; width: 96%;margin-top:15px; border:0px;}
#settings-tabs label {display: inline-block; width: 150px; text-align:right; margin-right: 10px;}
#settings-tabs .ui-tabs-nav {overflow:hidden}
#settings-tabs .input {
    border-bottom: 1px dashed #dedede;
    clear: both;
    padding: 2px 0px 2px 0px !important;
}


</style>
<div id="settings-tabs"  style="width: 750px; overflow: hidden;">
<ul>
<li><a href="#tabs-colors">Color Highlights</a></li>
<li><a href="#tabs-misc">Misc Settings</a></li>
</ul>
  <div id="tabs-colors" style="overflow: auto;"><h1>Call Highlights</h1>
    <h2>Minders</h2>
    <form id="highlights" autocomplete="off">
    <input type="hidden" name="section" value="highlights">  

    
    <?php
    if (!isset($this->request->data['AppSetting']['minder_warn_time_1'])) $this->request->data['AppSetting']['minder_warn_time_1'] = '60';
    if (!isset($this->request->data['AppSetting']['minder_warn_time_2'])) $this->request->data['AppSetting']['minder_warn_time_2'] = '120';
    if (!isset($this->request->data['AppSetting']['minder_warn_time_3'])) $this->request->data['AppSetting']['minder_warn_time_3'] = '180';

    if (!isset($this->request->data['AppSetting']['minder_warn_color_1'])) $this->request->data['AppSetting']['minder_warn_color_1'] = '#c3f3b9';
    if (!isset($this->request->data['AppSetting']['minder_warn_color_2'])) $this->request->data['AppSetting']['minder_warn_color_2'] = '#b6dcf3';
    if (!isset($this->request->data['AppSetting']['minder_warn_color_3'])) $this->request->data['AppSetting']['minder_warn_color_3'] = '#ffcccc';
    

    echo $this->Form->input('AppSetting.minder_warn_time_1', array('type' => 'text'));
    echo $this->Form->input('AppSetting.minder_warn_color_1', array('type' => 'text', 'class' => 'color', 'data-control' => "hue"));
    echo $this->Form->input('AppSetting.minder_warn_time_2', array('type' => 'text'));
    echo $this->Form->input('AppSetting.minder_warn_color_2', array('type' => 'text', 'class' => 'color', 'data-control' => "hue"));
    echo $this->Form->input('AppSetting.minder_warn_time_3', array('type' => 'text'));
    echo $this->Form->input('AppSetting.minder_warn_color_3', array('type' => 'text', 'class' => 'color', 'data-control' => "hue"));
    //echo $this->Form->input('AppSetting.minder_warn_time_4', array('type' => 'text'));
    //echo $this->Form->input('AppSetting.minder_warn_color_4', array('type' => 'text', 'class' => 'color', 'data-control' => "hue"));
    ?>
    <br><br><br><input type="submit" value="Submit" onclick="submitSettings(this);return false;">
<br><br><br><br><br>&nbsp;

  <br><br>
  <div id="tabs-misc"><h1>Misc Settings</h1>
  <?php
  /*
  foreach ($queues as $k => $q) {
    echo $this->Form->input('AppSetting.queuename.'.$k, array('type' => 'hidden', 'value' => $k));
    echo $this->Form->input('AppSetting.queuecc.'.$k, array('type' => 'checkbox', 'label' => '&nbsp;&nbsp;&nbsp;' . $q));
  }*/
  ?>
    <br><input type="submit" value="Submit" onclick="submitSettings(this);return false;">
  </div>
</div>
<br><br><br><br><br>&nbsp;


<script>
function resetColors() {
  $('#tabs-6').find('.color').each(function(index, value) {
    if (index == 0 || index == 3) $(this).minicolors('value','#c3f3b9');
    else if (index == 1 || index == 4) $(this).minicolors('value','#b6dcf3');
    else if (index == 2 || index == 5) $(this).minicolors('value','#ffcccc');
    //else if (index == 3 || index == 7) $(this).minicolors('value','#f3ebbc');
    else if (index == 6) $(this).minicolors('value','#f3ebbc');
    else if (index == 7) $(this).minicolors('value','#ffcccc');
  });
}

$(document).ready( function() {
	
	tabs = $('#settings-tabs').tabs({
	  'create': function(e, ui) {
        $('#tabs-colors .color').each( function() {

      		$(this).minicolors({
      			control: $(this).attr('data-control') || 'hue',
      			defaultValue: $(this).attr('data-defaultValue') || '',
      			inline: $(this).attr('data-inline') === 'true',
      			letterCase: $(this).attr('data-letterCase') || 'lowercase',
      			opacity: $(this).attr('data-opacity'),
      			position: $(this).attr('data-position') || 'middle left',
      			change: function(hex, opacity) {
      				var log;
      				try {
      					log = hex ? hex : 'transparent';
      					if( opacity ) log += ', ' + opacity;
      					console.log(log);
      				} catch(e) {}
      			},
      			theme: 'default'
      		});
                  
        });
	  },
	  'activate': function(e, ui) {
	    if (ui.newPanel.attr('id') == 'tabs-colors') {
        $('#tabs-colors .color').each( function() {

      		$(this).minicolors({
      			control: $(this).attr('data-control') || 'hue',
      			defaultValue: $(this).attr('data-defaultValue') || '',
      			inline: $(this).attr('data-inline') === 'true',
      			letterCase: $(this).attr('data-letterCase') || 'lowercase',
      			opacity: $(this).attr('data-opacity'),
      			position: $(this).attr('data-position') || 'middle left',
      			change: function(hex, opacity) {
      				var log;
      				try {
      					log = hex ? hex : 'transparent';
      					if( opacity ) log += ', ' + opacity;
      					console.log(log);
      				} catch(e) {}
      			},
      			theme: 'default'
      		});
                  
        });
	    }
	  }
	});
		
  $( "#settings-tabs" ).tabs();		
	
});

  function submitSettings(t) {
    var f = $(t).parent('form');
	  jQuery.ajax({
	      url: '/AppSettings/edit',
	      method: 'POST',
	      dataType: 'json',
	      data: $(f).serialize()
	  }).done(function (response) {
				if (response.success) {
				}
				alert(response.msg);		      
	  }).fail(function () {
		    alert('Cannot save changes, please try again later');     
	      
	  });       
    
  }    
  
</script>
