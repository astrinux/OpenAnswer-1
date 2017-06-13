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
 		<div class="paging">
 		<?php
    echo '<i>(' . $this->Paginator->param('count'). ' found)</i>&nbsp;&nbsp;';
	  /*echo $this->Paginator->counter(array(
	  'format' => __('page {:page} of {:pages}')
	  ));*/
	  echo '&nbsp;&nbsp;&nbsp;';
	  /*  echo $this->Paginator->counter(array(
	      'format' => '<i>{:start}-{:end} of {:count}</i>'
	    ));*/
 		if ($this->Paginator->hasNext() || $this->Paginator->hasPrev()) {
   		echo $this->Paginator->first('&laquo;', array('escape' => false)). '&nbsp;';
    	echo $this->Paginator->prev('&lsaquo;', array('escape' => false), null, array('class' => 'prev disabled', 'escape' => false)) . '&nbsp;';
     	echo $this->Paginator->numbers(array('separator' => '&middot;'));
      echo '&nbsp;';
      echo $this->Paginator->next('&rsaquo;', array('escape' => false), null, array('class' => 'next disabled', 'escape' => false));
      echo '&nbsp;' . $this->Paginator->last('&raquo;', array('escape' => false));
    }
    ?>
    </div>
