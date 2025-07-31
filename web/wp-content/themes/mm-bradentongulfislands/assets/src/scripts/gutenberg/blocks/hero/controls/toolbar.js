/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { __ } from '@wordpress/i18n';
import { Toolbar, Button } from '@wordpress/components';

// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';

/*** COMPONENTS **************************************************************/

const PreviewButton = () => (
  <Button
    label={__('Preview', THEME_PREFIX)}
    icon={'visibility'}
    isPressed={true}
    // No onClick, just visually selected
  />
);

const Tools = () => (
  <Toolbar>
    <PreviewButton />
  </Toolbar>
);

/*** EXPORTS ****************************************************************/

export default Tools;
