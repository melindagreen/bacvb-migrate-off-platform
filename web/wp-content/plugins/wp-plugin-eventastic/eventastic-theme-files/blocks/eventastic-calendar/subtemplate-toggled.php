<?php 
    use Eventastic\Library\Utilities as Utilities;
	$useFilters = isset( $attributes['useFilters'] ) ? $attributes['useFilters'] : true; 
?> 
<div class="toggle-wrapper">
	<div class="toggle-target active" data-target="calendar">
		<div class="eventastic-sidebar">
			<div id="calendar"></div>
		</div>
	</div>
	<div class="toggle-target grid-list " data-target="list">
		<?php Utilities::include_template( "list", $attributes); ?>
	</div>
</div>
