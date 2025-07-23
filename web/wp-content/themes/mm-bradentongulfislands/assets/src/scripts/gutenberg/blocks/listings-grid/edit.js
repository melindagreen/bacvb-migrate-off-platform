/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { SelectControl, TextControl, QueryControls } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from '@wordpress/block-editor';
import { withSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

// Local Dependencies
// Controls - add block/inspector controls here 
import Controls from './controls'
import { TaxonomyControl } from '../../components';

/*** CONSTANTS **************************************************************/

/*** COMPONTANTS ************************************************************/

/**
 * Fields that modify the attributes of the current block
 * @param {*} props 
 * @returns {WPElement}
 */
const Wizard = props => {
  const { attributes: { listingsTitle, postType, listingsPerPage, preFilterCat, catFilterSelections }, setAttributes } = props;

  return <>
    <TextControl
      label={__('Listings grid title')}
      value={listingsTitle}
      onChange={listingsTitle => setAttributes({ listingsTitle })}
    />

    <SelectControl
      label={__('Post type')}
      options={[
        {
          value: 'listing',
          label: 'Listings',
        }, {
          value: 'event',
          label: 'Events',
        }, {
          value: 'posts',
          label: 'Posts',
        },
      ]}
      selected={postType}
      onChange={postType => setAttributes({ postType })}
    />

    <TextControl
      label={__('Listings per page')}
      type='number'
      min='1'
      max='24'
      step='1'
      value={listingsPerPage}
      onChange={listingsPerPage => setAttributes({ listingsPerPage })}
    />

    {(postType === 'listing' || postType === 'page') && <TaxonomyControl
      controlType='select'
      taxonomySlug={postType === 'listing' ? 'idss_listing_categories' : 'category'}
      label={__('Pre-filter category')}
      value={[preFilterCat]}
      onChange={categories => setAttributes({ preFilterCat: categories[0] })}
    />}

{(postType === 'listing' || postType === 'page') && 
<TextControl
      label={__('Category Filter Selections')}
      value={catFilterSelections}
      onChange={catFilterSelections => setAttributes({ catFilterSelections })}
    />
}
  </>
}

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */
const Editor = props => {

  const blockProps = useBlockProps();
  const { attributes: { mode }, className } = props;

  return (
    <section {...blockProps} >
      {mode === 'edit'
        ? <Wizard {...props} />
        : <ServerSideRender block={props.name} {...props} />}
    </section>
  )
}

const edit = (props) => {
  return (
    <>
      <Controls {...props} />
      <Editor {...props} />
    </>
  );
};

/*** EXPORTS ****************************************************************/

export default edit;
