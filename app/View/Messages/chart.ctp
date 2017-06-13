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
#performance_legend label {display: inline-block; width: 120px; text-align:right; margin-right: 10px;}
#performance_legend .red_color {border: 1px solid #666; display: inline-block; width: 10px; height: 10px; margin-right: 6px; background: red}
#performance_legend .yellow_color {border: 1px solid #666; display: inline-block; width: 10px; height: 10px; margin-right: 6px; background: #ffff00}
#performance_legend .green_color {border: 1px solid #666; display: inline-block; width: 10px; height: 10px; margin-right: 6px; background: #88dd00}
#performance_legend .clr {display: inline-block; margin-right 8px; width: 90px; }
#performance_legend .clr2 {display: inline-block; margin-right 8px; width: 130px; }
</style>

      <span class="stitle">Productivity</span>
      <div class="snumber"><?php echo $productivity; ?>%</div>
      <div id="message_chart" style="height: 300px; width: 95%; margin: 20px auto;"></div>
      
<script>
$(function() {
    var chart = new CanvasJS.Chart("mistake_chart", {

		  theme: "theme2",//theme1
      axisY:{
        includeZero: false

      },
      axisY:{
        title: "% Mistakes",

      },
      axisX:{
        title: "Operator"
      },  
      title: {
        text: 'Total Mistakes',
        fontFamily: 'Tahoma',
        fontSize: 20
      },      
      data: [
      {
        type: "line", 
        dataPoints: [<?php 
        $rows = array();
        $cnt = 1;
        $marked = false;
        foreach ($messages as $k => $d) {
          if ($view_sensitive) $label = $k;
          else $label = '';
          if ($d == $my_mistakes && !$marked) {
            $rows[] = '{x: ' . $cnt . ',y: ' . $d . ', label: "'.$label.'", markerType: "cross", markerColor: "tomato" , markerSize: 12}';
            $marked = true;
           }
          else {
            $rows[] = '{x: ' . $cnt . ',y: ' . $d . ', label: "'.$label.'"}';            
          }
          $cnt++;
        }
        echo implode(', ', $rows); 
        ?>
        ]
      }
      ]
    });

    chart.render();	  
});
</script>      