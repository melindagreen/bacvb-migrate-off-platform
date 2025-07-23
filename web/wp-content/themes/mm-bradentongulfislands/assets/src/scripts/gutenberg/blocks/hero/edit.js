/*** IMPORTS ****************************************************************/
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from '@wordpress/block-editor';

// Local dependencies
import { initHero } from "./assets/hero.js";

// Controls - add block/inspector controls here
import Controls from './controls'

/*** CONSTANTS **************************************************************/

// Removed ALLOWED_MEDIA_TYPES and Wizard

/*** COMPONENTS **************************************************************/

// Removed Wizard component

/**
 * The editor for the block
 * @param {*} props
 * @returns {WPElement}
 */
const Editor = (props) => {

	const blockProps = useBlockProps();

  useEffect(() => {
    initHero();
  }, [props.attributes]);

  return (    
		<div {...blockProps} className={`${blockProps.className} is-preview`} >
      <ServerSideRender httpMethod={'POST'} block={props.name} {...props} />
    </div>
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
