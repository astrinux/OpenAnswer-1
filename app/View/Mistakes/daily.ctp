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

<div id="mistake_chart" style="height: 90%; width: 95%; margin: 20px auto;"></div>
      
<script>
$(function() {
    var chart = new CanvasJS.Chart("mistake_chart", {

		  theme: "theme2",//theme1
      axisY:{
        includeZero: false

      },
      axisY:{
        title: "Mistake Count",

      },
      axisX:{
        title: "Date"
      },  
      title: {
        text: 'Mistake Count Per Day',
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
        foreach ($mistakes as $k => $d) {
          $rows[] = '{y: ' . $d . ', label: "'.$k.'"}';            
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