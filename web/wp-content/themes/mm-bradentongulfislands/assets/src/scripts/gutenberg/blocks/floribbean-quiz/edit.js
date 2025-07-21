/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import { TextControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

// Local dependencies
import { initFloribbeanQuiz } from "./assets/floribbean-quiz";
// Controls - add block/inspector controls here 
import Controls from './controls'

/*** CONSTANTS **************************************************************/

/*** COMPONTANTS ************************************************************/

/**
 * Fields that modify the attributes of the current block
 * @param {*} props 
 * @returns {WPElement}
 */
 const Wizard = props => {
  const { attributes, setAttributes } = props;

  return (<div style={{maxWidth: '50rem', margin: 'auto'}}>
    <FerryStop 
        attributes={props.attributes}
        setAttributes={setAttributes}
    />
    </div>)
}

const FerryMapView = props => {

  return(
    <>
      <img src="/wp-content/themes/mm-bradentongulfislands/assets/images/bradenton-map.svg" />
    </>
  );
}

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */
const Editor = props => {
  const { attributes, className } = props;


  useEffect(() => {
    initFloribbeanQuiz();
  }, [attributes]);

  // Ensure that `attributes` does not contain invalid data
  const safeAttributes = typeof attributes === 'object' ? attributes : {};

  return (
    <section className={className}>
      {/* ServerSideRender not working */}
      {attributes.mode === 'edit'
        ? <Wizard {...props} />
        : <FerryMapView {...props} />}
    </section>
  )
}

const edit = ( props ) => {
  return (
    <>
      <Controls {...props} />
      <Editor {...props} />
    </>
  );
};

/*** EXPORTS ****************************************************************/

export default edit;
