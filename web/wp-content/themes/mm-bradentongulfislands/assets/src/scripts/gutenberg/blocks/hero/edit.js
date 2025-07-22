/*** IMPORTS ****************************************************************/

// WordPress dependencies
import {
  MediaPlaceholder,
  RichText,
} from "@wordpress/block-editor";
import {
	Disabled,
	IconButton,
	PanelRow,
} from "@wordpress/components";
import { __ } from '@wordpress/i18n';

import { useEffect } from '@wordpress/element';

import ServerSideRender from '@wordpress/server-side-render';


// Local dependencies
import { initHero } from "./assets/hero.js";
// Removed usePreview import
// import { usePreview } from '../../inc/hooks.js';

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
const Editor = props => {
  const { className } = props;
  useEffect(() => {
    initHero();
  }, [props.attributes]);

  return (
    <section className={`${className} is-preview`}>
      <ServerSideRender block={props.name} httpMethod={'POST'} {...props} />
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
