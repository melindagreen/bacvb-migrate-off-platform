import '../styles/admin.scss';
import { PLUGIN_PREFIX, PLUGIN_SETTNGS_SLUG } from './inc/constants';

( function ( $ ) {
    /////// FUNCTIONS //////
    
    /**
	 * Reset the settings form
	 * @param {Event} e             The triggering event
     * @return {null}
	 */
	function resetForm( e ) {
		e.preventDefault();
		if (
			window.confirm(
				'Are you sure? This will overwrite your current settings.'
			)
		) {
			madden_plugin_options_defaults.forEach( ( optionGroup ) => {
				optionGroup.fields.forEach( ( field ) => {
                    const fieldName = `${ PLUGIN_SETTNGS_SLUG }[${ optionGroup.id }][${ field.id }]`;

                    $( `[name="${ fieldName }"]` ).val(
                        field.args.default
                    );
				} );
			} );

			$( `#submit` ).click();
		}
	}

	$( document ).ready( function () {
        // Reset defaults
		$( `#${ PLUGIN_PREFIX }-reset` ).click( resetForm );
	} );
} )( jQuery );
