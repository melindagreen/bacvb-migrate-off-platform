/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import { FocalPointPicker, PanelBody, PanelRow, ToggleControl, TextControl } from '@wordpress/components'

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
		bannerDescription
	}, setAttributes } = props;

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
        </InspectorControls>
    )
}

export default Inspector;
