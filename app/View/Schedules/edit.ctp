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

  .invalid {color:  red;}

	#scheduleEdit {height: 100%}
	#scheduleEdit ul {
		list-style-type: none;
	}
	
	#leftColS {
		display: inline-block; 
		min-width:250px; 
		width: 30%; 
		vertical-align:top;
	}
	
	#rightColS {
		display: inline-block; 
		vertical-align:top;
		width: 69%;
		border-left: 1px solid #ccc;
		padding: 10px 0px; 
	}

	#newactions { 
		margin: 0; 
		padding: 0px; 
		min-width:250px; 
		width: 30%; 
		position: fixed;
		z-index: 2;
	}
	
	.sortable-el {
		padding: 5px; 
		background: #f5f5f5; 
		margin-top: 5px;
	}
	
	#newactions li {
		padding: 10px 10px; 
		margin-bottom: 2px;
	}
	
	.userprompts {
		margin: 0px;
		padding: 10px 10px;
	}
	.userprompts li {
		margin: 2px 2px !important;; 
		padding: 0px;
		border-bottom: 1px solid #ccc;
	}
		
	input.input_h {
		border: 1px solid transparent; 
		background-color: transparent; 
		font-weight:bold; 
		font-size:13px;
	}
	
	input.input_h:hover {
		border: 1px solid #ccc; 
		background-color: #FFD455;
	}
	
	input.input_h:focus {
		border: 1px solid #ccc; 
		background-color: white;
	}
	
	.modified { border-color: #A51412 !important; }
	.noprompts .add_prompt, .noprompts .userprompts {
		display: none;
	}
	
	#agent_script .script_section {
		position: relative; 
		padding: 25px 20px; 
		background: #EBEAD3; 
		border-radius: 2px; 
		min-height: 80px; 
		margin-bottom: 10px;
	}
	
	#agent_script .script_section li {
		list-style: none;
	}
	
	.actionlabel {
		float: right;
		top: 0px; 
		right:30px; 
		min-width: 80px; 
		height: 18px; 
		background: #eee; 
		border-bottom-left-radius: 2px; 
		border-bottom-right-radius: 2px; 
		padding: 2px 2px; 
		text-align:center;
	}
	
	input.action_label {
		border: 0px;
		background: transparent;
		min-width: 150px;
		text-align:right;
		padding:2px 5px;
	}
		
	.section_title {
		position: absolute; 
		top: 0px; 
		left:30px; 
		min-width: 80px; 
		height: 15px; 
		background: #ADD8E6; 
		border-bottom-left-radius: 10px; 
		border-bottom-right-radius: 10px; 
		padding: 2px 10px; 
		text-align:center;
	}
	
	.section_action {
		position: absolute; 
		bottom: 0px; 
		right:0px; 
		min-width: 80px; 
		height: 15px; 
		background: #bbb;  
		padding: 2px 10px; 
		text-align:center;
	}
	
	#helperedit-dialog {
		padding: 0px !important;
	}
	
	#helperform .jqte {
		border: 0px solid #ccc !important; 
		margin: 0px !important; 
		box-shadow: 0px 0px 0px #ccc;
	}
	
	.actbutton {
		width: 30%; 
		border:1px solid #aaa; 
		padding: 3px 8px; 
		text-align: center; 
		margin: 3px 2px; 
		color: #555;
	}
	 
	#options {
		margin-top: 10px; 
	}
	
	#sectionEditor, #promptEditor, #actionEditor {
		border-top: 1px solid #ccc; 
		border-bottom: 1px solid #ccc;
	}
	/*#promptEditor {max-height: 380px; overflow: auto;}*/
	
	.empty_action {
		border: 1px solid #1E48B4; 
		height: 50px; 
		background-color: #fdfdfd;
	}
	
	#leftColS .input {
		margin-bottom: 10px; 
	}
	
	.prompt_ddown {
		margin-bottom: 4px;
	}
	
	.cond_action {
		margin: 4px 0px;
	}
	
	.cond_row {
		padding: 4px 0px;
		border-bottom: 1px dotted #ddd;
	}
	
	.prompt .fa {
		color: #406FB8;
		margin-left: 10px;
		display: inline-block;
		min-width: 20px;
	}
	
	#arrow-right {
		z-index:100;
		width: 0; 
		height: 0; 
		position:absolute;
		top: 80px;
		left: 200px;
		display: none;
		border-top: 20px solid transparent;
		border-bottom: 20px solid transparent;
		border-left: 20px solid #aaa;
	}    
	
	#alertdiv {
	  color: red; 
	  font-style: italic;
	}
</style>


<div id="scheduleEdit">
	<div id="leftColS">
		<ul id="newactions" >
			<li class="ui-state-default dg new_step" data-atype="action_template" title="Click and hold, then drag and drop on the script"><i class="fa fa-arrows"></i> &nbsp;Action Step</li>
			<li class="ui-state-default dg new_step" data-atype="prompt_template" title="Click and hold, then drag and drop on the script"><i class="fa fa-arrows"></i> &nbsp;Call Prompt</li>
			<li class="ui-state-default dg new_step" data-atype="text_template" title="Click and hold, then drag and drop on the script"><i class="fa fa-arrows"></i> &nbsp;Text/Info box</li>
			<li class="ui-state-default" data-atype="section" title="Click to add a new section that will appear at the end of the script"><a href="#" id="section_add"><i class="fa fa-plus"></i> &nbsp;Section</a></li>
		</ul>
		<div id="options">
			<div id="promptEditor" class="options_editor is_hidden">
				<input type="hidden" id="el2_index" value="">
	
				<h2>PROMPT OPTIONS</h2>     
			  <div id="alertdiv"></div>
				<div class="input">
					<b>Prompt Title</b><br>
					<input type="hidden" id="section_index" value="">                   
					<input type="text" id="pcaption" class="action_caption" maxlength="255" /><br>
				</div>
				<div class="input">
					<b>Required</b><br>
					<select id="prequired">
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>
				</div> 
				<div class="input">
					<b>Type</b><br>
					<select id="ptype">
						<option value="1">Single line</option>
						<option selected="selected" value="2">Multi-line</option>
						<option value="3">Drop down</option>   
						<option value="4">Conditional</option>   
								 
					</select>
				</div>
			
				
				<div class="input text">
					<b>Verification</b>&nbsp
					<select id="pverification">
						<option selected value="0">none</option>
						<option value="1">phone</option>
						<option value="2">email</option>
						<option value="3">street</option>
						<option value="4">city/state/zip</option>
						<option value="5">full addr</option>                    
					</select>
				</div>                      
				<div class="input text">
					<b>Max length</b><br>
					<input type="text" id="pmax" size="4" maxlength="3" value="255"/>
				</div>            
				
				<div class="input is_hidden cond_div">
					<b>Conditional options</b><br>
					<div class="input" id="cond_options">
						<div class="cond_row">
						<input type="text" maxlength="255" size="30" value="" class="cond_title">
						<input class="cond_action_val" type="hidden" /><input class="cond_action" style="width:90%" /> <a href="#" data-type="del_cond">x</a> <a href="#" data-type="add_cond">+</a>
						</div>
					</div>      
				</div>
				<div class="ddown_div is_hidden">
					<div class="dd_row">
					<b>Dropdown options</b><br>
					<div id="ddown_options" class="input"><input type="text" maxlength="255" size="30" class="prompt_ddown"> <a href="#" class="del_dd">x</a>&nbsp;<a href="#" class="add_dd">+</a><br>
					</div>
				</div>      
				</div>
				
				<button class="actbutton" id="prompt_save">Done</button><button class="actbutton cancelbtn" id="prompt_cancel">Cancel</button>
			</div>          
			<div id="sectionEditor" class="options_editor is_hidden ">
				<h2>SECTION OPTIONS</h2>        
				<div class="input">
				<b>Section title</b><br>
				<input type="hidden" id="section_index" value="">
				<input id="section_title" class="" maxlength="32"><br>
				</div>
				<div class="input">
				<b>Action</b><br>
				<select id="sectionaction">
					<option value="0">Stop here</option>
					<option value="1">Go to section</option>
				</select>
				</div>
				<div class="input  is_hidden" id="sectionsel">
				<b>Section</b><br>                
				<select>
				</select>
				</div>
				<div class="input is_hidden" id="calltypesel">
				<b>Calltype</b><br>
				<select>
					<option>calltype 1</option>
					<option>calltype 2</option>
				</select>                
				</div>
				
				<button class="actbutton" id="section_save">Done</button><button class="actbutton cancelbtn" id="section_cancel">Cancel</button><button class="actbutton" id="section_delete">Delete</button>
			</div>     
			
			<div id="labelEditor" class="options_editor is_hidden ">
				<h2>LABEL OPTIONS</h2>        
				<div class="input">
				<b>Label</b><br>
				<input type="hidden" id="label_index" value="">
				<input id="label_title" class="" maxlength="32"><br>
				</div>
				
				<button class="actbutton" id="label_save">Done</button><button class="actbutton cancelbtn" id="label_cancel">Cancel</button>
			</div>  			 
			<div id="actionEditor"  class="is_hidden options_editor">
			<h2>STEP OPTIONS</h2>
			<i>Single click on the text boxes to perform inline editing or double click for the HTML editor</i>
				<div id="actionbox_msg"></div>
				<input type="hidden" id="el_index" value="">
				<textarea id="helper_save" class="is_hidden" rows="2" cols="40"></textarea>
				<span id="caption1" class="editable action_caption" title="Text that will prefix the instruction" contenteditable="true"></span>
				<select id="actsel" onchange="checkAction(); ">
							<option value="" selected>Select action</option>
					<?php
					foreach ($actionbox_actions as $k => $a) {
						if ($a['show']) {
							?>
							<option value="<?php echo $k; ?>"><?php echo $a['label']; ?></option>
							<?php
						}
					}
					?>
	
				</select>
				<div class="is_hidden">
				<select id="unused_select" class="options_editor is_hidden">
				</select>
				</div>
				<span class="editable action_caption" id="caption3" contenteditable="true"></span>
				<div id="empseldiv" style="position:relative;"><select id="empsel" multiple="multiple">
				
				<?php
				foreach ($employees as $cat => $emps) {
					if (!empty($global_options['contact_types'][$cat])) {
						echo '<optgroup label="'.$global_options['contact_types'][$cat].'" class="contact contact'.$cat.'" >';
					}
					else echo '<optgroup label="'.$cat.'" class="contact contact'. $cat.'" >';
					foreach ($emps as $e) {
						echo '<option value="'.$e['id'].'" class="contact contact'.$cat.'">' . $e['label'] . '</option>';
					}
					echo '</optgroup>';
				}
				?>
				</select>
				<span id="caption2" class="editable action_caption" contenteditable="true"></span>
				</div>
				<div id="optseldiv" style="position:relative;">
				<select id="optsel" name= name='action_opt'>
				<?php
				    foreach($crms as $crm_id => $crm_name) {
                        echo "<option value='".$crm_id."'>".$crm_name."</option>";
				    }
				?>
				</select>
				</div>
				<button class="actbutton" id="action_save">Done</button><button class="actbutton" id="action_cancel">Cancel</button>
				
			</div>
		</div>
	</div>
	<div id="arrow-right"></div>    
	<div id="rightColS">
			
			
	 <?php 
	 
	echo $this->Form->create('Schedule', array(
		'inputDefaults' => array(
		'label' => false,
		'div' => false,
		'id' => false
		),
		'id' => 'AgentScripting',
		'method' => 'POST',
		'onsubmit' => 'return false'
	)); 

	echo $this->Form->input('Schedule.active_original', array('id' => 'schedule_active_original', 'type' => 'hidden', 'value' => $this->request->data['Schedule']['active']));
	echo $this->Form->input('Schedule.active', array('id' => 'schedule_active', 'div' => true, 'type' => 'select', 'options' => array('1' => 'Yes', '0' => 'No'), 'label' => ' &nbsp;&nbsp;Make this agent script active ', 'empty' => 'Select', 'default' => '0'));
	
	?>
	<br>
	<input type="hidden" id="sch_did_id" value="<?php echo $this->request->data['Schedule']['did_id'];?>" />
 
	<div id="agent_script">
	 
	<?php
	
	// iterate through each action
	$section = '';
	
	$section_actions = array();
	foreach ($sections as $k => $s) {
		$sections[$k]['actions'] = array();	    
	}

	foreach ($this->request->data['Action'] as $k => $a) {
		$sections[$a['section']]['actions'][] = $a;	    

	}
	$cnt = 0;
	
	foreach ($sections as $s => $arr) {
		$action_array = $arr['actions'];
		echo '<div class="script_section" data-section="'.$s.'">'; 
				?>
			<div class="section_title"><?php 
			if (!empty($sections[$s]['title'])) echo $sections[$s]['title'];
			else echo 'Section ' . ($s? $s: 1); 
			?></div>
			<?php 
			if (isset($sections[$s]['title'])) {
				$section_title = $sections[$s]['title'];
				$section_action = $sections[$s]['section_action'];
				$section_sort = $sections[$s]['sort'];
				$section_num = $sections[$s]['section_num'];
			}
			else {
				$section_title = 'Section ' . ($s+1);
				$section_action = 0;
				$section_sort = '1';
				$section_num = '0';
				
			}
			?>
			<?php echo $this->Form->input('Section.section_title', array('name' => 'data[Section][section_title][]', 'class' => 'section_title', 'type' => 'hidden', 'default' => 'Section ' . $a['section']));  ?>
			<?php echo $this->Form->input('Section.section_action', array('name' => 'data[Section][section_action][]', 'class' => 'section_action', 'type' => 'hidden', 'value' => $section_action));    ?>
			<?php echo $this->Form->input('Section.sort', array('name' => 'data[Section][sort][]', 'class' => 'section_sort', 'type' => 'hidden', 'value' => $section_sort));    ?>
			<?php echo $this->Form->input('Section.section_num', array('name' => 'data[Section][section_num][]', 'class' => 'section_num', 'type' => 'hidden', 'value' => $section_num));    ?>
			<div class="section_action"><?php
			if (!empty($sections[$s]['section_action'])) {
				if ($sections[$s]['section_action'] == '0') echo 'Stop here';
				else if ($sections[$s]['section_action'] == '1') {
					echo 'Go to ' . $sections[$sections[$s]['section_num']]['title'];
				}
			}
			else {
				echo 'Stop here';
			}
			?></div>
				<?php
		foreach ($action_array as $k => $a) {
			$extra_class = '';
		
			
			if ($a['dispatch_only']) $extra_class .="dispatcher ";  // mark dispatcher actions by assigning an extra class
			if ($a['action_type'] != ACTION_PROMPTS) $extra_class .= "noprompts ";

			?>
			
			
			<div class="sortable-el ui-state-default <?php echo $extra_class; ?> " data-atype="<?php echo $a['action_type']; ?>">
				<div class="actionlabel"><span class="is_hidden"><?php echo $a['action_label']; ?></span><?php echo $this->Form->input('Action.'.$cnt.'.action_label', array('class'=> 'action_label', 'type' => 'text', 'onchange' => 'return checkLabel(this);')); ?></div>
				<div class="links">
					<a class="ahandle ui-sortable-handle" title="Click and drag to reorder" onclick="return false;" href="#">&equiv;</a>
					<a class="copy_action" title="Copy this step" href="#"><i class="fa fa-copy"></i></a>
					<a class="remove_action" title="Delete this step" href="#">x</a>
					<a title="Toggle Dispatch" class="toggle_dispatch" href="#">D</a>
					<?php
					if ($a['action_type'] == ACTION_PROMPTS) {
						?>
					<a title="Add prompt" class="add_prompt" href="#">+</a>
					<?php
					} 
					else {
					?>
					<!--<a title="Edit action" onclick="editAction(this); return false;" href="#">edit</a>-->
					<?php 
					}
					?>
				</div>
				
		
				<div style="clear:both;" class="act_content" >
				<?php echo $this->Form->input('Action.'.$cnt.'.sort', array('type' => 'hidden', 'class' => 'sort_order')) . "\r\n";    ?>
				<?php echo $this->Form->input('Action.'.$cnt.'.did_id', array('type' => 'hidden', 'value' =>  $this->request->data['Schedule']['did_id'])) . "\r\n";    ?>
				<?php echo $this->Form->input('Action.'.$cnt.'.dispatch_only', array('type' => 'hidden', 'class' => "dispatch_only")) . "\r\n";   ?>
				<?php echo $this->Form->input('Action.'.$cnt.'.section', array('type' => 'hidden', 'class' => 'section')) . "\r\n"; ?>
				<?php echo $this->Form->input('Action.'.$cnt.'.eid', array('type' => 'hidden', 'class' => 'emp_id')) . "\r\n";    ?>
				<?php echo $this->Form->input('Action.'.$cnt.'.action_type', array('type' => 'hidden', 'class' => 'action_type')) . "\r\n";   ?>
				<?php echo $this->Form->input('Action.'.$cnt.'.action_opt', array('type' => 'hidden', 'class' => 'action_opt')) . "\r\n";   ?>
		
				<?php       
				$emp = array();
				if (!empty($a['eid'])) {
		
					// employee ids are stored comma-delimited in the DB action table
					// On-call list ids are prefixed by 'ONCALL'
					// Calendar ids are prefixed by 'CALENDAR'
					$e_arr = explode(',', $a['eid']);
					foreach ($e_arr as $j => $v) {
						if ($v == 'ALL') $emp[$j] = 'Requested Staff';
						else if ($v == 'CALENDAR_ALL') $emp[$j] = 'Requested Calendar';
						else if (substr($v, 0, 6) == 'ONCALL' ) {
							$emp[$j] = $employees['Oncall'][str_replace('ONCALL_', '', $v)]['label'];
						}
						else if (strpos($v, 'CALENDAR') !== false) {
							$emp[$j] = $employees['Calendar'][str_replace('CALENDAR_', '', $v)]['label'];
						}
						else {
							if (!empty($contacts[$v])) {
								$emp[$j] = ($contacts[$v]['Employee']['name'] . ' (' . $contacts[$v]['EmployeesContact']['label'] . ': ' . $contacts[$v]['EmployeesContact']['contact'] . ')');
							}
						}
					}
				}
	
				// mark the start/ end of editable/customizable text within the action step
				$caption1 = $caption2 = $caption3 = '';
				$action_text = $a['action_text'];
				$action_opt = $a['action_opt'];
				if ($action_opt) {
				    $action_option = $crms[$action_opt];
				}
				else {
				    $action_option = '';
				}
				
				$temp = explode('[a]', $action_text);
				if (sizeof($temp) > 1) {
					$temp1 = explode('[e]', $temp[1]);
				}
				if (sizeof($temp) > 1) {
					$caption1 = trim($temp[0]);
					if (sizeof($temp1) > 1) {
						
						$caption2 = trim($temp1[1]);
						$caption3 = trim($temp1[0]);
					}
				}
				else $caption1 = $a['action_text'];
				$action_text = ' <span class="caption1">' . $caption1 . '</span> ';
				$action_text .= ' <span class="the_action">' . $global_options['actions'][$a['action_type']]['label'] . '</span>';
				$action_text .= ' <span class="action_option">' . $action_option . '</span>';
				$action_text .= ' <span class="caption3">' . $caption3 . '</span> ';
				$action_text .= '<span class="the_recipients">' . implode(',', $emp) . '</span>';
				$action_text .= ' <span class="caption2">'. $caption2 . '</span>';
	
				if ($a['action_type'] == ACTION_INFO) {
					
					echo $this->Form->input('Action.'.$cnt.'.action_text', array('class' => 'action_text is_hidden', 'div' => false, 'type' => 'textarea')) ;
					echo '<div onclick="editHelper(this); return false;" class="actiontext">';
					echo $a['action_text'].'</div>' . "\r\n";
				}
				else if ($a['action_type'] != ACTION_PROMPTS) {
					echo $this->Form->input('Action.'.$cnt.'.action_text', array('class' => 'is_hidden action_text', 'type' => 'textarea')) . "\r\n";
				?>
				<div class="edit_action">
				    <?php echo $action_text; ?>
				</div>
				<?php
				}
				else {
					echo $this->Form->input('Action.'.$cnt.'.action_text', array('class' => 'input_h is_hidden', 'type' => 'textarea', 'size' => 45));         
					echo '<div onclick="editHelper(this); return false;" class="actiontext">';
					echo $a['action_text'].'</div>' . "\r\n";
				}
			
				if (sizeof($a['Prompt']) > 0) {
					echo '<ul class="userprompts">';
					foreach ($a['Prompt'] as $p => $pval) {
						echo '<li class="prompt">';
	//                  echo ' <a href="#" class="del_prompt">x</a>&nbsp;<a href="#" class="handle" onclick="return false;" title="Click and drag to reorder">&equiv;</a>&nbsp;&nbsp;&nbsp;';
	//                  echo '<a class="pcaption">' . $pval['caption'] . '</a>';
						echo ' <a href="#"  class="del_prompt">x</a>&nbsp;<a href="#" class="handle" onclick="return false;" title="Click and drag to reorder">&equiv;</a>&nbsp;&nbsp;&nbsp;';
											
						echo '<span class="pcaption">' . $pval['caption'] . '</span>';
						if ($pval['ptype'] == '4') echo ' <i class="picon fa fa-lg fa-code-fork"></i>';
						else if ($pval['ptype'] == '3') echo ' <i class="picon fa fa-lg fa-caret-down"></i>';
	
						echo $this->Form->input('Action.'.$cnt.'.Prompt.'.$p.'.caption', array('class' => 'pcaption', 'type' => 'hidden', 'label' => false));
						echo $this->Form->input('Action.'.$cnt.'.Prompt.'.$p.'.maxchar', array('class' => 'pmax', 'type' => 'hidden', 'label' => false));
						echo $this->Form->input('Action.'.$cnt.'.Prompt.'.$p.'.sort', array('class' => 'psort', 'type' => 'hidden', 'label' => false));
						echo $this->Form->input('Action.'.$cnt.'.Prompt.'.$p.'.did_id', array('type' => 'hidden', 'value' =>  $this->request->data['Schedule']['did_id']));    
						
						echo $this->Form->input('Action.'.$cnt.'.Prompt.'.$p.'.ptype', array('class' => 'ptype', 'type' => 'hidden', 'label' => false));
						echo $this->Form->input('Action.'.$cnt.'.Prompt.'.$p.'.required', array('class' => 'prequired', 'type' => 'hidden', 'label' => false));
						echo $this->Form->input('Action.'.$cnt.'.Prompt.'.$p.'.verification', array('class' => 'pverification', 'type' => 'hidden', 'label' => false));
						echo $this->Form->input('Action.'.$cnt.'.Prompt.'.$p.'.options', array('class' => 'poptions', 'type' => 'hidden', 'label' => false));
	/*                  echo ' Max char: '. $this->Form->input('Action.'.$k.'.Prompt.'.$p.'.maxchar', array('size' => 3, 'type' => 'text'));
						echo ' ' . $this->Form->input('Action.'.$k.'.Prompt.'.$p.'.ptype', array('options' => array('1' => 'Single line', '2' => 'Multi-line', '3' => 'Drop down')));
						echo ' ' . $this->Form->input('Action.'.$k.'.Prompt.'.$p.'.required', array('type' => 'checkbox', 'title' => 'required field'));
						echo ' ' . $this->Form->input('Action.'.$k.'.Prompt.'.$p.'.verification', array('options' => array('0' => 'none' , '1' => 'phone', '2' => 'email', '3' => 'street', '4' => 'city/state/zip', '5' => 'full addr')));*/
						
						echo '</li>';
					}
					echo '</ul>';
				}
				
				?>
				Helper: (<?php  echo $this->Form->input('Action.'.$cnt.'.helper', array('class' => 'is_hidden', 'type' => 'textarea')); ?>
				<div onclick="editHelper(this); return false;" class="helpertxt"><?php echo trim($a['helper']); ?></div>)
			</div>
				</div>
			<?php
				$cnt++;
		}
				echo '</div>';
	}
	?>
	</div>
	<input type="submit" value="Save" class="is_hidden">
	</form>
		<script type="text/template" id="cond_template"><div class="cond_row">
		<input type="text" maxlength="255" size="30" value="" class="cond_title" /><input class="cond_action_val" type="hidden" /><input class="cond_action unconfigured" style="width:90%;" /> <a href="#" data-type="del_cond">x</a> <a href="#" data-type="add_cond">+</a></div></script>
		<script type="text/template" id="ddown_template">
			<div class="ddown_row"><input type="text" maxlength="255" size="30" class="prompt_ddown" value="" /> <a href="#" class="del_dd">x</a>&nbsp;<a href="#" class="add_dd">+</a><br>
			</div>
		</script>
		<script type="text/template" id="action_template">
			<div class="sortable-el ui-state-default noprompts">
			<div class="actionlabel"><span class="is_hidden"></span><input type="text" name="data[Action][][action_label]" class="action_label" onchange="checkLabel(this);"></div>
			
				<div class="links">
					<a class="ahandle ui-sortable-handle" title="Click and drag to reorder" onclick="return false;" href="#">&equiv;</a>
					<a class="copy_action" title="Copy this step" href="#"><i class="fa fa-copy"></i></a>
					<a class="remove_action" title="Delete this step" href="#">x</a>
					<a title="Toggle Dispatch" class="toggle_dispatch" href="#">D</a>
				</div>
				
		
				<div style="clear:both;" class="act_content" >
					<input type="hidden" name="data[Action][][sort]" class="sort_order" value="" />
					<input type="hidden" name="data[Action][][did_id]" value="<?php echo $this->request->data['Schedule']['did_id']; ?>" />
					<input type="hidden" name="data[Action][][section]" class="section" value="" />
					<input type="hidden" name="data[Action][][dispatch_only]" class="dispatch_only" value="0" />
					<input type="hidden" name="data[Action][][eid]" class="emp_id" value="" />
					<input type="hidden" name="data[Action][][action_type]" class="action_type" value="" />
					<input type="hidden" name="data[Action][][action_opt]" class="action_opt" value="" />
					<textarea name="data[Action][][action_text]" class="is_hidden action_text" cols="30" rows="6">[a] [e]</textarea>
			
					<div class="edit_action unconfigured">
						<span class="caption1"></span>
						<span class="the_action">(select action)</span>
						<span class="action_option"></span>
						<span class="caption3"> </span>
						<span class="the_recipients"><i>(select recipient)</i></span>
						<span class="caption2"> </span>
					</div>
					
					Helper: (<textarea name="data[Action][][helper]" class="is_hidden" cols="30" rows="6"></textarea>       
					<div onclick="editHelper(this); return false;" class="helpertxt"> </div>)
				</div>
			</div>
		</script>
	
	
		<script type="text/template" id="prompt_template">
			<div class="sortable-el clone ui-state-default">
			<div class="actionlabel"><span class="is_hidden"></span><input type="text" class="action_label" name="data[Action][][action_label]" onchange="checkLabel(this);"></div>
			
				<div class="links">
					<a class="ahandle ui-sortable-handle" title="Click and drag to reorder" onclick="return false;" href="#">&equiv;</a>
					<a class="copy_action" title="Copy this step" href="#"><i class="fa fa-copy"></i></a>
					<a class="remove_action" title="Delete this step" href="#">x</a>
					<a title="Toggle Dispatch" class="toggle_dispatch"  href="#">D</a>
					<a title="Add prompt" class="add_prompt" href="#">+</a>
					<a title="Edit action" class="toggle_dispatch" href="#">edit</a>
				</div>
				<div style="clear:both;" class="act_content" >
					<input type="hidden" name="data[Action][][sort]" class="sort_order" value="" />
					<input type="hidden" name="data[Action][][did_id]" value="<?php echo $this->request->data['Schedule']['did_id']; ?>" />
					<input type="hidden" name="data[Action][][section]" class="section" value="" />
					<input type="hidden" name="data[Action][][dispatch_only]" class="dispatch_only" value="0" />
					<input type="hidden" name="data[Action][][eid]" class="emp_id" value="" />
					<input type="hidden" name="data[Action][][action_type]" class="action_type" value="30" />
					<textarea class="input_h is_hidden" name="data[Action][][action_text]">Gather User Information</textarea>					
					<div class="actiontext" onclick="editHelper(this); return false;"></div>
						<ul class="userprompts ui-sortable">
						<li class="prompt">
						<a  class="del_prompt" href="#">x</a>
						<a class="handle" title="Click and drag to reorder" onclick="return false;" href="#">&equiv;</a>
						<span class="pcaption"></span>
						<input class="pcaption" type="hidden" value="" name="data[Action][][Prompt][][caption]"><i class="picon"></i>
						<input type="hidden" value="<?php echo $this->request->data['Schedule']['did_id'];?>" name="data[Action][][Prompt][][did_id]">
						<input class="pmax" type="hidden" value="255" name="data[Action][][Prompt][][maxchar]">
						<input class="ptype" type="hidden" value="2" name="data[Action][][Prompt][][ptype]">
						<input class="psort" type="hidden" value="" name="data[Action][][Prompt][][sort]">
						<input class="prequired" type="hidden" value="1" name="data[Action][][Prompt][][required]">
						<input class="poptions" type="hidden" value="1" name="data[Action][][Prompt][][options]">
						<input class="pverification" type="hidden" value="0" name="data[Action][][Prompt][][verification]">
						</li>
						</ul>       
						Helper: (<textarea name="data[Action][][helper]" class="is_hidden" cols="30" rows="6"></textarea><div onclick="editHelper(this); return false;" class="helpertxt"> </div>)
						
				</div>
			</div>
		</script>
		
		
		<script type="text/template" id="text_template">
			<div class="sortable-el ui-state-default">
			<div class="actionlabel"><span class="is_hidden"></span><input type="text" class="action_label" name="data[Action][][action_label]" onchange="checkLabel(this);"></div>
			
				<div class="links">
					<a class="ahandle ui-sortable-handle" title="Click and drag to reorder" onclick="return false;" href="#">&equiv;</a>
					<a class="copy_action" title="Copy this step" href="#"><i class="fa fa-copy"></i></a>
					<a class="remove_action" title="Delete this step" href="#">x</a>
					<a title="Toggle Dispatch" class="toggle_dispatch" href="#">D</a>
				</div>
				
				<div style="clear:both;" class="act_content" >
						<input type="hidden" name="data[Action][][sort]" class="sort_order" value="" />
						<input type="hidden" name="data[Action][][did_id]" value="<?php echo $this->request->data['Schedule']['did_id']; ?>" />
						<input type="hidden" name="data[Action][][section]" class="section" value="" />
						<input type="hidden" name="data[Action][][dispatch_only]" class="dispatch_only" value="0"/>
						<input type="hidden" name="data[Action][][eid]" class="emp_id" value=""/>
						<input type="hidden" name="data[Action][][action_type]" class="action_type" value="45"/>
						<textarea style="width: 90%"  name="data[Action][][action_text]" class="is_hidden"></textarea>
						<div onclick="editHelper(this); return false;" class="actiontext" ></div>
				
						Helper: (<textarea name="data[Action][][helper]" class="is_hidden" cols="30" rows="6"></textarea><div onclick="editHelper(this); return false;" class="helpertxt"> </div>)
				</div>
			</div>
		</script>
		
		<script type="text/template" id="section_template">
			<div class="script_section unconfigured">
				<div class="section_title" >sectiontitle</div>
				<input class="section_title" type="hidden" value="sectiontitle" name="data[Section][section_title][]">
				<input class="section_action" type="hidden" value="0" name="data[Section][section_action][]">
				<input class="section_sort" type="hidden" value="0" name="data[Section][sort][]">
				<input class="section_num" type="hidden" value="0" name="data[Section][section_num][]">
				<div class="section_action">Stop here</div>
			</div>
		</script>
	<br><br><br><br><br><br><br><br><br><br><br><br>&nbsp;
</div>

<div id="helperedit-dialog" style="display: none;">
<form name="helperform" id="helperform" >
	<textarea id="helpereditor" name="helpereditor" class="htmleditor" style="minHeight:360px; minWidth: 500px;width: 100%; height: 100%"></textarea>
</form>
</div>

<div id="save-confirm" style="display: none;">
<p>This set of calltype instructions is not yet active, do you want to activate it?</p>
</div>

 
<script>
	 
$(function() {
    function allValid() {
        var all_actions_valid = true;
        $('#agent_script .action_type').each(function() {
            var val = $(this).val();
            if (val != ACTION_PROMPTS && val != ACTION_NONE && val != ACTION_INFO && val != ACTION_HOLD && val != ACTION_DELIVER && val != ACTION_DISPATCH  && val != ACTION_CALENDAR) {
                if ($(this).siblings('.emp_id').val() == '') {
                    all_actions_valid = false;
                    console.log('here'); console.log(this);
                    $(this).siblings('.edit_action').find('.the_action').addClass('invalid');
                }
                else {
                    $(this).siblings('.edit_action').find('.the_action').removeClass('invalid');
                }
            }
        });
        $('#agent_script .prompt').each(function() {
            var prompt = this;
            if ($(this).find('.ptype').val() == '4') {
                $(this).find('.poptions').each(function() {
                    
                    var temp1 = $(this).val().split('||');
                    var length1  = 0;
                    var length2 = 0;
                    if (temp1[0]) var length1 = temp1[0].split('|').length;
                    if (temp1[1]) var length2 = temp1[1].split('|').length;
                    if ((length1!= length2) || length1 == 0 || length2==0) {
                    console.log('here2'); console.log(this);
                      all_actions_valid = false;
                      $(prompt).find('.pcaption').addClass('invalid');
                    }
                    else $(prompt).find('.pcaption').removeClass('invalid');
                });
            }
        });        
        return all_actions_valid;
    }
    
    function saveCalltypeNew(sid) {
        var action_text;
        var eid;
        var action_type;
        var save_calltype = true;
        var section_id = 0;
        var action_num = 0;
        var msg = '';
 
        
          $('#agent_script').children('.script_section').each(function(index, val) {
              section_id++;
              var prompt_num = 0;
              $(this).find('.sortable-el').each(function(index, val) {                
                  $(this).find('select, input, textarea').each(function(index, val) {
                      var input_name = $(this).attr("name").replace(/\[Action\]\[\d*\]/, "[Action]["+action_num+"]");
                      $(this).attr("name", input_name);               
                  });
                  $(this).find('input.sort_order').val(action_num);           
                  action_num++;
                  $(this).find('input.section').val(section_id);           
              });
              
              $(this).find('input.section_sort').val(section_id);           
              $(this).find('input.section_title').val($(this).find('div.section_title').html());           
              
          });  
          
           $('#agent_script').find('ul.userprompts').each(function(index, val) {
              var sort_order = 0;
              $(this).find('li.prompt').each(function() {
                  $(this).find('select, input, textarea').each(function(index, val) {
                      var input_name = $(this).attr("name").replace(/\[Prompt\]\[\d*\]/, "[Prompt]["+sort_order+"]");
                      $(this).attr("name", input_name);          
                  });
                  $(this).find('input.psort').val(sort_order);
                  sort_order++;
              });        
              
                          
          }); 
          var data = $('#AgentScripting').serialize();

                        
              $.ajax({
                  type: 'POST',
                  url: '/Schedules/edit/' + sid, 
                  data: data,
                  dataType: 'json'
              }).done(function(data) {
                  if (data.success) {
                      alert('Your changes have been saved');
                      didLayout.center.children.layout1.close('east');      
                      loadCalltypes($('#find_did').val(), 'did-content', 'did-detail', 'didLayout'); return false;
                  }
                  else alert(data.msg);
              }).fail(function (j, textStatus) {
                  alert('Failed to save your changes, try again later - ' + textStatus);        
              });

        return false;
    }
        
	var section_source = [];
	
	$( "#promptEditor #pcaption").autocomplete({
		minLength: 0,             
		source: <?php echo json_encode($prompts); ?>
	});    
	
	$( "#promptEditor #pcaption").on('change', function () {
		var new_value = $(this).val();
		
		$('#promptEditor #alertdiv').html('');

		$('#agent_script input.pcaption').each(function(target) {
		
			
			if ($(this).val() == new_value) {
        $('#promptEditor #alertdiv').html('You already have a prompt titled ' + new_value +'.  Changing the value of this prompt on the operator screen will change the other prompt(s) with the same title');			  
        return false;
			}
		});
	});  
					
	$('#did_save_btn').off('click');

	$('#did_save_btn').on('click', function() {
        var all_actions_valid = allValid();

        if (!all_actions_valid) {
          user_confirm('There are some conditionals or actions that have not been properly configured.  Please review all prompts/ actions highlighted in red. Are you sure you want to save this script?', function() {
	    
	    
        		if ($('#schedule_active_original').val() == '0' && $('#schedule_active').val() == '0'){
        			$( "#save-confirm" ).dialog({
        				resizable: false,
        				height: 200,
        				width: 340,
        				modal: true,
        				autoOpen: true,
        				close: function () {
        						 $('#save-confirm').dialog('destroy');      
        				},
        				buttons: {
        					"Yes, make it active": function() {
        						$('#schedule_active').val('1');      
        						saveCalltypeNew('<?php echo $schedule_id; ?>');
        						$( this ).dialog( "close" );
        					},
        					"No thanks, keep it inactive": function() {
        						saveCalltypeNew('<?php echo $schedule_id; ?>');
        						$( this ).dialog( "close" );
        					}
        				}
        			});
        		}
        		else{ 
        			saveCalltypeNew('<?php echo $schedule_id; ?>');
        		}
    	    });
    	  }
    	  else {
        			saveCalltypeNew('<?php echo $schedule_id; ?>');
    	  
    	  }
    	  
	}); 
	
	
	
	$('#actionEditor .editable').on('dblclick', function() {
		var clickedEl = this;
		editText(this); 
		return false;  
	});  


	$('#actionEditor .editable').on('keydown', function(event) {
		// 
		var esc = event.which == 27,
			nl = event.which == 13,
			el = event.target,
			input = el.nodeName != 'INPUT' && el.nodeName != 'TEXTAREA';

		var data;
		if (input) {
			if (esc) {
				// restore state
				document.execCommand('undo');
				el.blur();
			} else if (nl) {
				// save
				data = el.innerHTML;
	
				el.blur();
				event.preventDefault();
			}

		}
	});

	<?php // flag editable spans as modified if content has changed 
	?>			
	$('.action_caption').on('blur', function() {
		var caption_id = $(this).attr('id');
		
		var idx = $('#el_index').val();
		var children = $('#agent_script div.sortable-el');
		var the_div = children[idx];
		if ($(the_div).find('.'+caption_id).first().html() != $(this).html()) {
			$(this).addClass('modified');
		}
	})
	
	
	
	$('#action_save').on('click', function() {
		var idx = $('#el_index').val();
		var children = $('#agent_script div.sortable-el');
		var the_div = children[idx];
		var action_text = ($('#caption1').html() + ' [a] ' + $('#caption3').html() + ' [e] ' + $('#caption2').html());
		$(the_div).find('.caption1').html($('#caption1').html());
		$(the_div).find('.caption2').html($('#caption2').html());
		$(the_div).find('.caption3').html($('#caption3').html());
		$(the_div).find('.the_action').html($('#actsel option:selected').text());
		$(the_div).find('input.action_type').val($('#actsel option:selected').val());
		$(the_div).find('input.action_opt').val($('#optsel option:selected').val());
		$(the_div).find('.action_option').html($('#optsel option:selected').html());
		$(the_div).find('.action_text').val(action_text);

		var recipients = [];
		var emp_ids = [];
		$('#empsel option:selected').each(function() {
			recipients.push($(this).text());
			emp_ids.push($(this).val());
		});

		$(the_div).find('.the_recipients').html(recipients.join(', '));
		$(the_div).find('.emp_id').val(emp_ids.join(','));
		$('.options_editor input, .options_editor select, .options_editor span').removeClass('modified');		
		close_options_editor();
		$('#newactions').show();
		$('#arrow-right').hide();        
	});
	
	$('#prompt_save').on('click', function() {
		var new_value = $('#promptEditor input.pcaption').val();
		var invalid = false;
		$('#agent_script input.pcaption').each(function() {
			if ($(this).val() == new_value) {
				alert('You already have a prompt titled \'' + new_value +'\'.  You are not permitted to have duplicate titles, please select another title');
				invalid = true;
				return false;
			}
		});
		if (invalid) return false;
		var idx = $('#el2_index').val();
		var children = $('#agent_script li.prompt');
		var the_div = children[idx];


		$(the_div).find('span.pcaption').html($('#pcaption').val());
		$(the_div).find('input.pcaption').val($('#pcaption').val());
		$(the_div).find('.ptype').val($('#ptype').val());
		$(the_div).find('.pverification').val($('#pverification').val());
		$(the_div).find('.pmax').val($('#pmax').val());
		$(the_div).find('.prequired').val($('#prequired').val());
		$(the_div).find('i').removeClass();
		if ($('#ptype').val() == '3') { 
			var options = [];
			$('#promptEditor input.prompt_ddown').each(function() {
				options.push($(this).val());
			});
			$(the_div).find('.poptions').val(options.join('|'));
			$(the_div).find('i').addClass('fa fa-2x fa-caret-down');
			
		}
		else if ($('#ptype').val() == '4') { 
			var cond_titles = [];
			var cond_actions = [];
			$('#promptEditor .cond_title').each(function() {
				if ($(this).val() != '') cond_titles.push($(this).val());
			});
			$('#promptEditor .cond_action').each(function() {
				if ($(this).val() != '') cond_actions.push($(this).val());
			});            
			$(the_div).find('.poptions').val(cond_titles.join('|') + '||' + cond_actions.join('|'));
			$(the_div).find('i').addClass('fa fa-2x fa-code-fork');
		}
		$('.options_editor input, .options_editor select, .options_editor span').removeClass('modified');		


		close_options_editor();
		$('#newactions').show();
		$('#arrow-right').hide();
	});
	
	$('#section_save').on('click', function() {
		var idx = $('#section_index').val();
		var children = $('#agent_script .script_section');
		var t = children[idx];
		// zero out values if hidden
		if ($('#sectionaction').is(':hidden')) $('#sectionaction').val('');
		if ($('#sectionsel').is(':hidden')) $('#sectionsel select').val('');
		if ($('#calltypesel').is(':hidden')) $('#calltypesel select').val('');
		
				// save new values of section parameters
		$(t).find('div.section_title').html($('#section_title').val());
		$(t).find('input.section_title').val($('#section_title').val());
		$(t).find('input.section_action').val($('#sectionaction').val());
		if ($('#sectionaction').val() == '1') $(t).find('input.section_num').val($('#sectionsel select').val());
		else $(t).find('input.section_num').val('0');
			
		// update section action text
		var section_action;
		if ($('#sectionaction').val() == '1') section_action = "Go to ";
		else section_action  = $('#sectionaction option:selected').text();
		if ($('#sectionaction').val() == '1') section_action += (" " + $('#sectionsel select option:selected').text());
		$(t).find('div.section_action').html(section_action);
		$('.options_editor input, .options_editor select, .options_editor span').removeClass('modified');
		close_options_editor();
		$('#newactions').show();
		$('#arrow-right').hide();        
		
	}); 
	
	$('#label_save').on('click', function() {
		var idx = $('#label_index').val();
		var children = $('#agent_script div.sortable-el');
		var the_div = children[idx];
		
		$(the_div).find('span.labeltxt').html($('#label_title').val());
		$(the_div).find('.action_text').val($('#label_title').val());

	});
	
	$('#section_cancel, #action_cancel, #prompt_cancel, #label_cancel').on('click', function() {
		close_options_editor();
		$('#newactions').show();
		$('#arrow-right').hide();
	});
	

	$('#section_delete').on('click', function() {
		var idx = $('#section_index').val();
		user_confirm('Are you sure you want to delete this section?', function() {
			var idx = $('#section_index').val();
			var children = $('#agent_script .script_section');
			var t = children[idx];
			var sort = $(t).attr('data-section');
			
			
			// reindex sections
			$('#agent_script input.section_num').each(function() {
				if ($(this).val() >= sort) $(this).val($(this).val() - 1);
			});

			// adjust conditional inputs to point to correct section
			$('#agent_script input.poptions').each(function() {
				if ($(this).val() != '' && ($(this).val().indexOf('||') > -1)) {

					var poptions_arr = $(this).val().split('||');
					var poptions_sections = poptions_arr[1].split('|');
					for (var i=0; i< poptions_sections.length; i++) {
						var temp = poptions_sections[i].split('_');
						if (temp[0].toString() == '1') {
							// set to 'no action' for those conditionals that point to deleted section
							if (temp[1] == sort) {
								poptions_sections[i] = '1_0';
							} 
							else if (temp[1] > sort) {
								poptions_sections[i] = '1_' + (temp[1] - 1).toString;
							} 
						}
					}
					var section2 = poptions_sections.join('|');
					$(this).val(poptions_arr[0] + '||' +  section2);
				}
				
			});

			$(t).remove();
			close_options_editor();
			$('#newactions').show();
			$('#arrow-right').hide();            
		});
	});

	
	var employees = <?php echo json_encode($employees);?>;
	var crms = <?php echo json_encode($crms);?>;
	
	function step_add(event, ui) {
				var step_type = ui.item.attr('data-atype');
				// grab template for step type
				var the_html = $('#'+step_type).html();
				
				if (step_type == 'prompt_template') {
					var num_of_prompts = $('#agent_script div.sortable-el').find('ul.userprompts li').length;
					var the_html = the_html.replace(/\[Prompt\]\[\]/g, "[Prompt]["+num_of_prompts+"]");
				}
				
				$('#agent_script .new_step').replaceWith(the_html);
				$('#agent_script .unconfigured').trigger('click').removeClass('unconfigured');	  
	}
	
	$('#section_add').on('click', function() {
		var next_section = $('.script_section').length + 1;
		var template_html = $('#section_template').html();
		
		template_html = template_html.replace(/sectiontitle/g, ('Section ' + next_section));
		$('#agent_script').append(template_html);  	
		$('#agent_script .unconfigured').sortable({
			revert: true,
			items: "> div.sortable-el",
			handle: '.ahandle',
			placeholder: 'sortable-el empty_action',
			forcePlaceholderSize: true,
			forceHelperSize: true,      
			connectWith: '.script_section',	
			receive: step_add           
		});
		$('#agent_script .unconfigured').removeClass('unconfigured');
		$('#did-detail').scrollTop($('#rightColS').height());
	});
	
	$('#helpereditor').jqte({
		formats: [
		  ["p", "Normal"],
		  ["h1", "Advisement"],
		  ["h2", "Verbatim"],
		  ["h3", "Alert"],
		]
		
	});         
		
	$('.userprompts').sortable({ handle: ".handle", connectWith: '.userprompts' });
		

		
	$( ".script_section" ).sortable({
		revert: true,
		items: "> div.sortable-el",
		handle: '.ahandle',
		placeholder: 'sortable-el empty_action',
		forcePlaceholderSize: true,
		forceHelperSize: true,      
		connectWith: '.script_section',
		receive: step_add
	});    
	
	$( ".dg" ).draggable({
			connectToSortable: ".script_section",
			helper: "clone",
/*          helper: function () {
				var el_type = $(this).attr('data-atype');
				var return_el = $('.' + el_type + '> div').clone();
				return return_el;
			},*/
			revert: "invalid"
		});
		//$( "ul, li" ).disableSelection();  

	
	$('#cond_options').on('click', function(event) {
		var t = event.target;

		if ($(t).attr('data-type') == 'add_cond') {
			$('#cond_options').append($('#cond_template').html());  	
			$('#cond_options .unconfigured').select2({
				data: section_source,
				placeholderOption: 'first'
				
			});        	
			return false;               
		}
		if ($(t).attr('data-type') == 'del_cond') {
			$(t).parents('.cond_row').remove();
	
			return false;               
		}
	})
	
	// click handler for the agent script pane
	$('#agent_script').on('click', function(event) {
		var t = event.target;

		var editor = '';
		if ($(t).hasClass('add_prompt')) {
			var num_of_prompts = $(t).parents('div.sortable-el').find('ul.userprompts li').length;
			var li_clone = $(t).parents('div.sortable-el').find('ul.userprompts li:first').clone(false);
			
			// blank out the captions
			$(li_clone).find("input:first").val('');
			$(li_clone).find("span.pcaption").html('');
		
			$(li_clone).find("select, input").each(function(){
				var input_name = $(this).attr("name").replace(/\[Prompt\]\[\d+\]/, "[Prompt]["+num_of_prompts+"]");
				 $(this).attr("name", input_name);
			});
			li_clone.insertAfter($(t).parents('div.sortable-el').find('ul.userprompts li:last'));
			event.stopPropagation();
		}
		else if ($(t).hasClass('toggle_dispatch')) {
			var el = $(t).parents('.sortable-el');
			if ($(el).find('.dispatch_only').val() == '0') {
				$(el).find('.dispatch_only').val('1');
				$(el).addClass('dispatcher');
			}      
			else {
				$(el).find('.dispatch_only').val('0');
				$(el).removeClass('dispatcher');
			}
		}
		else if ($(t).hasClass('remove_action')) {
			if (confirm('Are you sure you want to delete this action?')) {
				$(t).parents('.sortable-el').find('input, select, textarea').prop('disabled', true);
				$(t).parents('.sortable-el').hide();
			}            
		}
		else if ($(t).parent().hasClass('copy_action')) {
			if (confirm('Are you sure you want to copy this action?')) {
				var el = $(t).parents('.sortable-el');
				el.after(el.clone());
			}            
		}		
		else if ($(t).hasClass('del_prompt')) {
			removePrompt(t); 
			event.stopPropagation();

		}
		else if ($(t).hasClass('prompt') || $(t).parent('li').hasClass('prompt')) {
			if (!unsaved_changes()) {
				$('#promptEditor .cond_row').remove();
				$('#cond_options').append($('#cond_template').html());  	
				$('#cond_options .unconfigured').select2({
					data: section_source,
					placeholderOption: 'first'
					
				});    
							
				if (!$(t).hasClass('prompt')) t = $(t).parent('li');
				// keep track of which prompt we're editing
				var idx = $('#agent_script li.prompt').index(t);  
				$('#el2_index').val(idx); // keep track of which action we're editing 
	
				section_source.length = 0;
				section_source.push({id: '0', text: 'No action'});
				
				var section_options = [];
				var label_options = [];
				
				$('#AgentScripting').find('div.section_title').each(function(idx, val) {
					section_options.push({id: '1_' + (idx+1).toString(), 'text': 'Go to: ' + $(this).html()});
				});
				
				$(t).parents('.script_section').find('.action_label').each(function(idx, val) {
					if ($.trim($(this).val()) != '') label_options.push({id: '2_' + $(this).val(), 'text': 'Go to: ' + $(this).val()});
					if ($.trim($(this).val()) != '') label_options.push({id: '3_' + $(this).val(), 'text': 'Execute until: ' + $(this).val()});
				});			
				event.stopPropagation();
				
				if (label_options.length > 0) {
					section_source.push({text: 'Labels', children: label_options});
				}
	
				if (section_options.length > 0) {
					section_source.push({text: 'Sections', children: section_options});
				}
				
				$('#pcaption').val($(t).find('span').html());
				$('#ptype').val($(t).find('input.ptype').val());
				$('#ptype').trigger('change');
				$('#pverification').val($(t).find('input.pverification').val());
				if ($('#ptype').val() == '3') { 
					var options = $(t).find('input.poptions').val().split('|');
					var html;
					$('#ddown_options').html('');
					for (var i=0; i < options.length; i++) {
						html = $('#ddown_template').html();
						html = html.replace('value=""', 'value="'+options[i]+'"');
						$('#ddown_options').append(html);          
					}
				}
				else if ($('#ptype').val() == '4') { 
					var options = $(t).find('input.poptions').val().split('||');
					var cond_titles = options[0].split('|');
					var cond_actions = options[1].split('|');
					var el;
					$('#cond_options').html('');
					
					for (var i=0; i < cond_titles.length; i++) {
						$('#cond_options').append($('#cond_template').html());  	
						$('#cond_options .unconfigured').select2({
							data: section_source,
							placeholderOption: 'first'
						});        	
						el = $('#cond_options div.cond_row:last-child');
						el.find('input.cond_title').val(cond_titles[i]);
						$('#cond_options .cond_action.unconfigured').select2('val', cond_actions[i]);
						$('#cond_options .cond_action.unconfigured').removeClass('unconfigured');
					}
	
				}
	
	
				$('#prequired').val($(t).find('input.prequired').val());
				$('#pmax').val($(t).find('input.pmax').val());
	
				$('#promptEditor .cond_action').select2({
					data: section_source,
					placeholderOption: 'first'
					
				});           
				
				$( "#cond_options" ).sortable({
					items: '> div.cond_row'
				});
				$('.options_editor').hide(); 
				$('.options_editor input, .options_editor select, .options_editor span').removeClass('modified');
				$('#promptEditor').show();          
				editor = 'promptEditor';
			}
		}
		else if ($(t).hasClass('edit_action') || $(t).parent().hasClass('edit_action')) {
			if (!unsaved_changes()) {
				t = $(t).parents('div.sortable-el');  
				editAction(t);
				editor = 'actionEditor';
			}
		}
		else if ($(t).hasClass('edit_label') || $(t).parent().hasClass('edit_label')) {  			
			if (!unsaved_changes()) {
				t = $(t).parents('div.sortable-el');  
				editLabel(t);
				editor = 'labelEditor';
			}
		}
		
		else if ($(t).hasClass('script_section') || $(t).parent().hasClass('script_section')) {
			if (!unsaved_changes()) {
				if ($(t).parent().hasClass('script_section')) t = $(t).parent();
				var idx = $('#agent_script .script_section').index(t);  
				$('#section_index').val(idx); // keep track of which action we're editing 
	
				$('#section_title').val($(t).find('.section_title').html());
				$('#sectionaction').val($(t).find('.section_action').val());
				$('#sectionaction').trigger('change');
				$('.options_editor').hide(); 
				$('.options_editor input, .options_editor select, .options_editor span').removeClass('modified');
				$('#sectionEditor').show();
				
				$('#sectionsel').find('.section').remove();
				$('#AgentScripting').find('div.section_title').each(function(idx, val) {
					if ($(t).find('.section_title').html() != $(this).html()) {
						$('#sectionsel select').append('<option class="section" value="'+(idx+1)+'">'+ $(this).html() +'</option>');
					}
				});
				$('#sectionsel select').val($(t).find('.section_num').val());
				editor = 'sectionEditor';
			}
		}       

		<?php // vertically position the options editor next to whichever item is being edited 
		?>
		if (editor != '') {
			x_offset = $('#rightColS').offset().left - $(t).offset().left;
			
			$('#' + editor).position({
				my: "right center",
				at: "left"+x_offset+" center+40",
				of: t,
				collision: 'fit'
			});     

			x_offset = x_offset + 22;
			$('#newactions').hide();
			$('#arrow-right').show();  
			if (editor == 'sectionEditor') {
				$('#arrow-right').position({
					my: "left center",
					at: "left center",
					of: t,
					collision: 'fit'
				});   
				$('#arrow-right').show();  
				
			}
			else {
				$('#arrow-right').position({
					my: "right center",
					at: "left"+(x_offset-2)+" center",
					of: t,
					collision: 'fit'
				});   
				$('#arrow-right').show();  
			}
				 
		}

		
	});

	$('#promptEditor').on('click', function(event){
		var t = event.target;
		if ($(t).hasClass('add_dd')) {
			$('#ddown_options').append($('#ddown_template').html());
		}
		else if ($(t).hasClass('del_dd')) {
			 $(t).parent('.ddown_row').remove();
		}
		event.stopPropagation();
		
	});
	
	$('#sectionaction').on('change', function() {
		$('#sectionsel').hide();
		$('#calltypesel').hide();
		if ($(this).val() == 1) $('#sectionsel').show();
		else if ($(this).val() == 2) $('#calltypesel').show();
	});
	
	$('#ptype').on('change', function() {
		if ($(this).val() == '1' || $(this).val() == '2' ) {
			$('#promptEditor .cond_div').hide();
			$('#promptEditor .ddown_div').hide();
			$('#promptEditor .text').show();
		}
		else if ($(this).val() == '3') {
			$('#promptEditor .cond_div').hide();
			$('#promptEditor .ddown_div').show();
			$('#promptEditor .text').hide();
		}
		else if ($(this).val() == '4') {
			$('#promptEditor .cond_div').show();
			$('#promptEditor .ddown_div').hide();
			$('#promptEditor .text').hide();
		}
		
	});
	
	$('.options_editor input, .options_editor select').on('change', function() {
		$(this).addClass('modified');
	});
	

	
	$('#newactions').width($('#leftColS').width());

});
</script>

