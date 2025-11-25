<?php 
use Eventastic\Library\Utilities as Utilities;
$listConfig_showTitle = isset( $attributes['listConfig_showTitle'] ) ? $attributes['listConfig_showTitle'] : true;
?> 
<div class="calendarListWrapper">
    <?php if($listConfig_showTitle) :  ?>
        <div id="events-list-title"></div>
    <?php endif; ?>
    <div id="calendarList"></div>
</div>