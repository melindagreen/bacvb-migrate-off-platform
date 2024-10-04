/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor'
import { FocalPointPicker, PanelBody, PanelRow, ToggleControl, Button, ResponsiveWrapper, Spinner, TextControl} from '@wordpress/components'

import { } from '@wordpress/block-editor'
// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/

const Inspector = props => {
    const { attributes: { 
		videoHero, 
		showBottomWave, 
		doParallax,	
		image,
		focalPoint,
		mobileImage,
		focalPointMobile,
		bannerTitle,
		bannerDescription,
		ctaBannerUrl,
		ctaBannerText,
		ctaBannerTitle,
		logoId, 
		logoUrl
	}, setAttributes } = props;

	const removeLogo = () => {
		setAttributes({
			logoId: 0,
			logoUrl: [],
			logoAlt: ''
		});
	}
 
	 const onSelectLogo= (logo) => {
		let logoLG = typeof logo?.sizes?.full?.url !== 'undefined' ? logo.sizes.full.url : logo.url;
		let logoMD = logoLG;
		let logoSM = typeof logo?.sizes?.madden_hero_md?.url !== 'undefined' ? logo.sizes.madden_hero_md.url : logoMD;
		console.log(logo);
		setAttributes({
			logoId: logo.id,
			logoUrl: [logoLG, logoMD, logoSM],
			logoAlt: logo.alt
		});
	 }

    return (
        <InspectorControls>
			<PanelBody title="Hero Options">
				<PanelRow>
					<ToggleControl
						checked={ showBottomWave }
						label={ __( 'Show Bottom Wave' ) }
						onChange={ ( showBottomWave ) => setAttributes({ showBottomWave })}
					/>
				</PanelRow>
				<PanelRow>
					<ToggleControl
						label={__("Video Hero")}
						checked={videoHero}
						onChange={(videoHero) => setAttributes({ videoHero })}
					/>
				</PanelRow>
				{!videoHero && (
					<PanelRow>
						<ToggleControl
							checked={ doParallax }
							label={ __( 'Parallax Hero Image' ) }
							onChange={ ( doParallax ) => setAttributes({ doParallax })}
						/>
					</PanelRow>
				)}
			</PanelBody>
			{/* if doing parallax or video, no focal point picking */} 
			{!doParallax && !videoHero && (
				<PanelBody title="Hero Focal Points">
		            <PanelRow>Note: setting a custom focal point will require you to use a suitably large background image.</PanelRow>
					{image && (
						<PanelRow>
							<FocalPointPicker
								url={ image.url }
								dimensions={ {
									width: image.width,
									height: image.height,
								} }
								value={
									focalPoint || {
										x: 0.5,
										y: 0.5,
									}
								}
								onChange={ ( focalPoint ) =>
									setAttributes( {
										focalPoint,
									} )
								}
							/>
						</PanelRow>
					)}
					{mobileImage && (
						<PanelRow>
							<FocalPointPicker
								url={ mobileImage.url }
								dimensions={ {
									width: mobileImage.width,
									height: mobileImage.height,
								} }
								value={
									focalPointMobile || {
										x: 0.5,
										y: 0.5,
									}
								}
								onChange={ ( focalPointMobile ) =>
									setAttributes( {
										focalPointMobile,
									} )
								}
							/>
						</PanelRow>
					)}
				</PanelBody>
			)}
			<PanelBody title="Banner Settings">
				<PanelRow>
                    <TextControl
                    	label="Banner Title"
                        onChange={ ( bannerTitle ) => setAttributes( { bannerTitle } ) }
                        value={ bannerTitle }
                    />
                </PanelRow>
				<PanelRow>
                    <TextControl
                    	label="Banner Description"
                        onChange={ ( bannerDescription ) => setAttributes( { bannerDescription } ) }
                        value={ bannerDescription }
                    />
                </PanelRow>
			</PanelBody>
			<PanelBody title="CTA Banner Settings">
				<PanelRow>
                    <TextControl
                    	label="Banner Title"
                        onChange={ ( ctaBannerTitle ) => setAttributes( { ctaBannerTitle } ) }
                        value={ ctaBannerTitle }
                    />
                </PanelRow>
				<PanelRow>
                    <TextControl
                    	label="Banner Button Text"
                        onChange={ ( ctaBannerText) => setAttributes( { ctaBannerText } ) }
                        value={ ctaBannerText }
                    />
                </PanelRow>
				<PanelRow>
                    <TextControl
                    	label="Banner Url"
                        onChange={ ( ctaBannerUrl ) => setAttributes( { ctaBannerUrl } ) }
                        value={ ctaBannerUrl }
                    />
                </PanelRow>
			</PanelBody>
			<PanelBody title="Badge Settings" initialOpen={ false }>
                		<div className="editor-post-featured-image">
                        <MediaUploadCheck>
                            <MediaUpload
                                onSelect={onSelectLogo}
                                value={logoId}
                                allowedTypes={ ['image'] }
                                render={({open}) => (
                                    <Button
                                                className={ ! logoId ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview' }
                                                onClick={ open }>
                                                { ! logoId && ( __( 'Set badge image', 'image-selector-example' ) ) }
                                                { !! logoId && ! logoUrl[0] && <Spinner /> }
                                                { !! logoId && logoUrl[0] &&
                                                    <ResponsiveWrapper>
                                                        <img className="components-responsive-wrapper__content--imgsize" src={ logoUrl[0] } alt={ __( 'Background image', 'image-selector-example' ) } />
                                                    </ResponsiveWrapper>
                                                }
                                            </Button>
                                )}
                            />
                        </MediaUploadCheck>
                        <div className="component-buttons">
                            {logoId != 0 && 
                                <MediaUploadCheck>
                                    <MediaUpload
                                        title={__('Replace image')}
                                        value={logoId}
                                        onSelect={onSelectLogo}
                                        allowedTypes={['image']}
                                        render={({open}) => (
                                            <Button onClick={open}>{__('Replace image')}</Button>
                                        )}
                                    />
                                </MediaUploadCheck>
                            }
                            {logoId != 0 && 
                                <MediaUploadCheck>
                                    <Button onClick={removeLogo} isDestructive>{__('Remove image')}</Button>
                                </MediaUploadCheck>
                            }
                        </div>
                    </div>
            </PanelBody>
        </InspectorControls>
    )
}

export default Inspector;
