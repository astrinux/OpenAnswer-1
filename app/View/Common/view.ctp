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
    $this->start('script');
    echo $this->Html->script('lib/jquery-ui-1.11.2/jquery-ui.min');
    echo $this->Html->script('lib/dropdown/jquery.dropdown');
    echo $this->Html->script('socket.io.client.js');    
    echo $this->Html->script('lib/jquery.layout.1.4.4');    
    echo $this->Html->script('lib/jquery.layout.resizeTabLayout-latest.js');    
    echo $this->Html->script('lib/select2-3.4.2/select2');    
    echo $this->Html->script('lib/multiselect/jquery.multiselect');    
    echo $this->Html->script('lib/jquery-ui-timepicker-addon');    
    echo $this->Html->script('lib/jquery-ui-sliderAccess');    
    echo $this->Html->script('lib/jquery.timepicker');    
    echo $this->Html->script('lib/jquery-ui-timepicker-addon');    
    echo $this->Html->script('lib/jquery-stickytableheaders.min');    
    echo $this->Html->script('lib/autosize.min');    
    echo $this->Html->script('lib/jquery.cookie');    
    echo $this->Html->script('lib/readmore.min');    
    echo $this->Html->script('lib/expanding');    
    echo $this->Html->script('lib/tablesorter2.22/js/jquery.tablesorter');    
    echo $this->Html->script('lib/tablesorter2.22/js/jquery.tablesorter.widgets');    
    echo $this->Html->script('lib/jquery.maskedinput.min');    
    echo $this->Html->script('lib/toast/jquery.toast.min');    
    echo $this->Html->script('lib/jquery-minicolors/jquery.minicolors');    
    echo $this->Html->script('lib/moment/moment.min');    
    echo $this->Html->script('lib/moment/moment-timezone-with-data-2010-2020.min');    
    echo $this->Html->script('general.vn-ver5');    
    echo $this->Html->script('lib/dropzone/dropzone');    
    echo $this->Html->script('lib/jqueryte/jquery-te-1.4.0.min');    
    echo $this->Html->script('lib/flot/jquery.flot');    
    echo $this->Html->script('lib/flot/jquery.flot.categories');    
    echo $this->Html->script('lib/flot/jquery.flot.axislabel');    
    echo $this->Html->script('lib/flot/jquery.flot.time');    
    echo $this->Html->script('lib/flot/jquery.flot.pie');    
    echo $this->Html->script('lib/canvasjs1.6/canvasjs.min.js');    


    if ($google_api_key) {
    ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key; ?>&libraries=places"></script>  
    <?php
    }
    $this->end(); 


    $this->start('css');
    echo $this->Html->css('/js/lib/jquery-ui-1.11.2/themes/smoothness/jquery-ui.min');
    echo $this->Html->css('layout-default-1.4.4');
    echo $this->Html->css('style.vn-ver1');
    echo $this->Html->css('/skin/skin1/css/style');
    echo $this->Html->css('/js/lib/select2-3.4.2/select2');
    echo $this->Html->css('/js/lib/multiselect/jquery.multiselect');
    echo $this->Html->css('jquery.timepicker');
    echo $this->Html->css('/js/lib/dropdown/jquery.dropdown');
    echo $this->Html->css('/js/lib/dropzone/css/dropzone');
    echo $this->Html->css('/js/lib/jquery-minicolors/jquery.minicolors');
    echo $this->Html->css('/js/lib/jqueryte/jquery-te-1.4.0');
    echo $this->Html->css('/js/lib/toast/jquery.toast.min');
    echo $this->Html->css('lib/animate');
    echo $this->Html->css('/font-awesome-4.4.0/css/font-awesome.min');

    $this->end(); 
?>

<?php echo $this->fetch('content'); ?>
