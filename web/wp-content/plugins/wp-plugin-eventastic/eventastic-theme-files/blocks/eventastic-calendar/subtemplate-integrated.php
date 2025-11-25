<?php 
    use Eventastic\Library\Utilities as Utilities;

	$useFilters = isset( $attributes['useFilters'] ) ? $attributes['useFilters'] : true; 
    $integratedCalendarLocation = isset( $attributes['integratedCalendarLocation'] ) ? $attributes['integratedCalendarLocation'] : 'sidebar';	
    $filterLocation = isset( $attributes['filterLocation'] ) ? $attributes['filterLocation'] : 'sidebar';

?> 
<?php if( "top" == $integratedCalendarLocation ) : ?>
	<div id="calendar"></div>
<?php endif; ?>
<div class="eventastic-sidebar">
	<?php if( "sidebar" == $integratedCalendarLocation ) : ?>
		<div id="calendar"></div>
	<?php endif; ?>        
	<?php if( $useFilters && 'sidebar' == $filterLocation) : ?>
	    <?php Utilities::include_template('filters' , $attributes ); ?> 
	<?php endif; ?>
</div>
<?php Utilities::include_template( "list", $attributes); ?>