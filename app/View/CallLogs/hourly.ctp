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


<div id="calls_chart_hourly" style="height: 250px; width: 95%; "></div>
      
<script>
$(function() {
    var chart = new CanvasJS.Chart("calls_chart_hourly", {

		  theme: "theme2",//theme1
      axisY:{
        includeZero: false

      },
      axisY:{
        title: "Call Count",

      },
      axisX:{
        title: "Hour"
      },  
      title: {
        text: 'Call Count Per Hour',
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
        $first = true;
        foreach ($calls as $k => $d) {
          if ($d > 0) {
            if ($first) $first = false;
            $rows[] = '{y: ' . $d . ', label: "'.$k.'"}';            
          }
          else if (!$first) {
            $rows[] = '{y: ' . $d . ', label: "'.$k.'"}';            
          }
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