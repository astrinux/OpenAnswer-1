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
	function getDayRanges($schedule, $php_daysofweek) {
	  //FireCake::log($schedule);
	  $min = '';
	  $prev = '';
    if ($schedule['mon']) $thedays[1] = 1;
    else $thedays[1] = 0;
    if ($schedule['tue']) $thedays[2] = 1;
    else $thedays[2] = 0;
    if ($schedule['wed']) $thedays[3] = 1;
    else $thedays[3] = 0;
    if ($schedule['thu']) $thedays[4] = 1;
    else $thedays[4] = 0;
    if ($schedule['fri']) $thedays[5] = 1;
    else $thedays[5] = 0;
    if ($schedule['sat']) $thedays[6] = 1;
    else $thedays[6] = 0;
    if ($schedule['sun']) $thedays[7] = 1;
    else $thedays[7] = 0;
	  
	  $min = '';
	  $prev = '';
	  $first = true;
	  $days = array();
	  foreach ($thedays as $k => $v) {
	    if ($first) {
	      if ($v) {
	        $min = $k;
	        $prev = $k;
	        $first = false;
	      }
	    }
	    else {
	      if ($v == 0) {
	        if ($prev) {
	          if ($min == $prev)
	            $days[] = $php_daysofweek[$min];
	          else $days[] = substr($php_daysofweek[$min],0,3) . '-' . substr($php_daysofweek[$prev],0,3);
	        }
//	          FireCake::log("Min $min, Prev: $prev, k: $k");
//	          FireCake::log($days);
	        $min = '';
	        $prev = '';
	      }
	      else {
	        if (!$min) $min = $k;
	        else if ($k == 7) {
	          if ($min == $k) {
	            $days[] = $php_daysofweek[$min];
	          }
	          else {
	            $days[] = substr($php_daysofweek[$min], 0, 3) . '-' . substr($php_daysofweek[7], 0, 3);
	          }
	        }
	        else {
	          $prev = $k;
	        }
	      }
      }
      if ($v==1) $prev = $k;
	  }
	  return $days;
	}

$this->Paginator->options(array(
    'update' => '#did-content',
    'evalScripts' => true
));

	function phoneFormat($num) {
    if ($num) {
      $num = preg_replace('/[^0-9]/', '', $num);
      if (strlen($num) == 11 && substr($num, 0, 1) == '1') $num = substr($num, -10);
      if (strlen($num) == 10)
        $num2 =  '(' . substr($num, 0, 3) . ') ' . substr($num, 3, 3) . '-' . substr($num, 6); 
      else $num2 = $num . ' <span class="mistake">?</span>';
    }
    else $num2 = '';	  
    return $num2;
	}
?>

<div class="CallList index">
	<h2><?php echo __('On Call Lists'); ?></h2><br>
<a href="#" onclick="addList(); return false;">Add new list</a><br><br>
	<table cellpadding="0" cellspacing="0" class="gentbl">
	<tr>
			<th width="200" align="left"><?php echo $this->Paginator->sort('title'); ?></th>
			<th width="200" align="left">Effective</th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	if (sizeof($CallLists)) {
	foreach ($CallLists as $m): 
	?>
	<tr>
		<td><?php echo $m['CallList']['title']; ?></td>
		<td>
		</td>
		<td class="actions">
			<?php echo '<a href="#" onclick="editList('.$m['CallList']['id'].'); return false; ">edit</a>'; ?>
			<?php echo '<a href="#" onclick="if confirm(\'Are you sure you want to delete this entry?\') deleteList('.$m['CallList']['id'].'); return false; ">delete</a>'; ?>
		</td>
	</tr>
<?php endforeach; 
  }
  else {
    echo '<tr><td colspan="6" align="center">None found, click <a href="#" onclick="addList(); return false;"><i>here</i></a> to create one</td></tr>';
  }
?>
	</table>
</div>

<script type="text/javascript">
  function addList() {
    var url = '/CallLists/add/<?php echo $did_id; ?>';
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'html',
  	      	type: 'GET'
  	      }).done(function(data) {
  					$('#did-detail').html(data);
  					didLayout.center.children.layout1.open('east');
  	      });	
  }
  
  function editList(id) {
    var url = '/CallLists/edit/' + id;
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'html',
  	      	type: 'GET'
  	      }).done(function(data) {
  					$('#did-detail').html(data);
  					didLayout.center.children.layout1.open('east');
  	      });	
  }  
  
  function deleteList(id) {

    var url = '/CallLists/delete/' + id;
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'json',
  	      	type: 'GET'
  	      }).done(function(data) {
            if (data.success) {
    					loadPage(this, 'Notes/index/<?php echo $did_id; ?>', 'did-content');          
              didLayout.center.children.layout1.close('east')					
            }
            alert(data.msg);
  	      });	
  }
  
  function removeEmployee(t) {
    var li = $(t).parents('li');
    $('#sortable2').append(li);
  }   
  
  function addEmployee(t) {
    var li = $(t).parents('li');
    $('#sortable1').append(li);
  }     
</script>