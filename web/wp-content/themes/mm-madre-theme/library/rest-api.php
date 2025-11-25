<?php /**
 * This file contains hooks that modify or extend the WordPress REST API.
 */

namespace MaddenMadre\Library;

use MaddenMadre\Library\Utilities as U;

class RestApi {
	private $json_settings;

	function __construct () {
		$this->json_settings = U::get_json_settings( 'restApi' );

        add_filter( 'rest_endpoints', array( $this, 'disable_rest_endpoints' ) );
    }

    /**
    * Disable REST API endpoints for non-logged in users. Danke https://stackoverflow.com/a/62430375
    *
    * @param array $endpoints      The original endpoints
    * @return array $endpoints     The updated endpoints
    */
    public function disable_rest_endpoints ( $endpoints ) {
        $endpointsToSave = (isset($this->json_settings['endpointsToSave']) && is_array($this->json_settings['endpointsToSave'])) ? $this->json_settings['endpointsToSave'] : array();
        if ( ! is_user_logged_in() ) {
            foreach ( $this->json_settings['endpointsToRemove'] as $rem_endpoint ) {
                foreach ( $endpoints as $maybe_endpoint => $object ) {
                    if ( stripos( $maybe_endpoint, $rem_endpoint ) !== false ) {
                        if( !in_array( $rem_endpoint, $endpointsToSave ) ){
                            unset( $endpoints[ $maybe_endpoint ] );
                        }
                    }
                }
            }
        }
        return $endpoints;
    }
}
