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
    .ceditable {min-height: 16px; min-width: 100px;padding:2px;}
    .ceditable:hover {background:#FFD455;}
     div.editable {display:inline;}
    .ceditable .empty {color: #aaa;}
    .handle {text-decoration: none;}
</style>











<?php


?>

<style>
    table.contacts tr td {padding: 10px;}
    .ceditable {min-height: 16px; min-width: 100px;padding:2px;}
    .ceditable:hover {background:#FFD455;}
    div.editable {display:inline;}
    .ceditable .empty {color: #aaa;}
    tr.tbd * {color: #bbb; text-decoration:line-through}
    .employees textarea {vertical-align: top}
    .employees label {display: block; float: left; width: 100px;}
    .trigger {margin: 10px;}
    .trigger a {text-decoration:none;}
    .trigger a:hover {text-decoration:underline;}
</style>

<div class="panel-content form" style="position:relative;">
<!-- search form -->
<?php echo $this->Form->create($thismodel, array('id'=> 'crm_edit')); ?>
    <h1><?php echo $activity ?></h1>
    <div>
    <?php
        foreach($mappings as $mapping) {
            if (isset($data[$mapping['caption']])) {
                echo $this->Form->input($mapping['caption'], array('type' => 'text', 'value' => $data[$mapping['caption']], 'crmprompt'=> $mapping['caption']));
            }
            else {
                echo $this->Form->input($mapping['caption'], array('type' => 'text', 'value' => '', 'crmprompt'=> $mapping['caption']));
            }
        }
        
    ?>
        <div id="crmresults" class="trigger">
        <?php
        if (!$results) {
        	echo "No results found, you may modify the search terms above and try again.";
        }
        ?>
        <button type="button" id='crmsearch' onclick="searchAgain();">Search Again</button>
        </div>
        <table cellpadding="2" cellspacing="0" class="mappings" id="crmmappings">
            <tbody>
                <tr>
                    <?php
                    foreach ($mappings as $mapping) {
                        echo "<th>".$mapping['caption']."</th>";
                    }
                    ?>
                </tr>
                
<?php	
        $rownumber = 1;
        if ($results) {
        	foreach ($results as $result) {
                	echo "<tr >";
	                    foreach ($mappings as $mapping) {
                        	if (isset($mapping['caption'])) {
	                            $caption = $mapping['caption'];
                        	}
                        	else {
	                            $caption = '';
                        	}
                        	if (isset($result[$mapping['value']])) {
	                            $value = $result[$mapping['value']];
                        	}
                        	else {
	                            $value = '';
                        	}
	                            echo "<td crow='".$rownumber."' cprompt='".$caption."'>".$value."</td>";
	                        
                    	}
                    	?>
                    	<td><button type="button" onclick="applyContact('<?php echo $rownumber ?>')">Select this contact</button></td>
                    	<?php
                	echo "</tr>";
	                
                	$rownumber++;
         	}	
        }
?>
                
            </tbody>
        </table>
    </div>
    <br><br>
    </form>
</div>
<script>
    
    function applyContact(rownumber) {
        $("#crmmappings td").each(function (){
            if ($(this).attr('crow') == rownumber) {
                $("[prompt='"+$(this).attr('cprompt')+"']").val($(this).html());
            }
            //$(this).val($("[prompt='"+$(this).attr('crmprompt')+"']").val());
            //$("[prompt='"+$(this).attr('prompt')+"']").val()
        });
        $('#dialogIntegration').dialog('close');
        $('#dialogIntegration').html('');
    }
    
    function searchAgain(did_id = '1') {
        var prompts = new Object;
        $("[crmprompt]").each(function () {
            prompts[$(this).attr('crmprompt')] = $(this).val();
        });
        prompts["xx_retrigger"] = "true";
        //var request = new Object;
        console.log(prompts);
        JSON.stringify(prompts);
        $.ajax({
            url: '/Crms/crmaction/'+ <?php echo $crm_id?>,
            dataType: 'html',
            type: 'post',
            data: JSON.stringify(prompts),
        }).done(function(data) {
            console.log("We are done");
            $('#dialogIntegration').html(data);
            //$('#dialogIntegration').dialog('open');
        })
    }
    
    
  $('.ceditable').blur( function(){
    var thelabel = $(this).html()
    if (thelabel != '') $(this).parent().next().find('.clabel').val(thelabel);
    });  

  $(function() {
    $('#crmmappings tbody').sortable();
    
    <?php 
    //if ($pull_from_prompts) {
    if (0) {
    ?>
    //prepopulate crm search form with values provided by call script prompts.
    //$("#crm_edit input [crmprompt]").each(function (){
    $("[crmprompt]").each(function (){
        $(this).val($("[prompt='"+$(this).attr('crmprompt')+"']").val());
        $("[prompt='"+$(this).attr('prompt')+"']").val()
    });
    
	<?php 
	}
	?>
    $('#CrmType').on('change', function() {
        console.log("test");
        }
    );
    
    $('.numeric').mask("?9999",{placeholder:" "});
    
  });
</script>

<div id = "crmbox">
</div>
</div>
</div>


