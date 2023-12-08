/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import { Button, PanelBody, TextControl, SelectControl, } from '@wordpress/components'
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor'

// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';
import { TaxonomyControl } from '../../../components';

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONENTS **************************************************************/

const MediaControls = props => {
    const { attributes, setAttributes } = props;
    const { defaultThumb } = attributes;

    return (
        <>
            <MediaUploadCheck>
                <MediaUpload
                    title={__('Choose Images', THEME_PREFIX)}
                    allowedTypes={ALLOWED_MEDIA_TYPES}
                    onSelect={selected => setAttributes({
                        defaultThumb: selected?.sizes?.thumbnail?.url || selected.url,
                    })}
                    value={defaultThumb}
                    render={({ open }) => (
                        <>
                            {defaultThumb && <img
                                className='listings-grid__default-thumb'
                                src={defaultThumb}
                            />}
                            <Button onClick={open} isLarge icon="format-image" isSecondary>
                                {__('Choose Thumbnail', THEME_PREFIX)}
                            </Button>
                        </>
                    )}
                />
            </MediaUploadCheck>
        </>
    );
}

const Inspector = props => {
    const { attributes: { listingsTitle, postType, listingsPerPage, preFilterCat, filterType }, setAttributes } = props;

    const onFilterTypeChange = newFilterType => {
        setAttributes({ filterType: newFilterType });
    };

    return (
        <InspectorControls>
            <PanelBody
                title="Default Thumbnail"
            >
                <MediaControls {...props} />
            </PanelBody>
            <PanelBody title='Grid Settings'>
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

                {(postType === 'listing' || postType === 'page' || postType === 'event') && <TaxonomyControl
                    controlType='select'
                    taxonomySlug={postType === 'listing' ? 'listing_categories' : 'eventastic_categories'}
                    label={__('Pre-filter category')}
                    value={[preFilterCat]}
                    onChange={categories => setAttributes({ preFilterCat: categories[0] })}
                />}

            </PanelBody>

            <PanelBody title={__('Filter Settings')}>
                <SelectControl
                    label={__('Filter Type')}
                    value={filterType}
                    options={[
                        { label: __('Categories'), value: 'categories' },
                        { label: __('Accommodations'), value: 'accommodations' },
                        { label: __('Rooms'), value: 'room-count' },
                    ]}
                    onChange={onFilterTypeChange}
                />
            </PanelBody>
        </InspectorControls>
    )
}

export default Inspector;
