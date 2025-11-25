import '../styles/front.scss';
import { PLUGIN_PREFIX } from './inc/constants';

( function ( $ ) {
    /////// FUNCTIONS //////
    
    /**
     * An exmaple function
     * @param {String} param        A paremeter
     * @return {null}
     */
    const exampleFunc = ( param ) => {
        //console.log(`Hello, ${param}!`);
    }

	$( document ).ready( function () {
        exampleFunc('On Load');
	} );
} )( jQuery );
