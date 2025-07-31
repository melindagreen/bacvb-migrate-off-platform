/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls, MediaUpload, MediaUploadCheck, MediaPlaceholder, RichText } from '@wordpress/block-editor'
import { FocalPointPicker, PanelBody, PanelRow, ToggleControl, Button, ResponsiveWrapper, Spinner, TextControl, IconButton, Disabled } from '@wordpress/components'

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
		logoUrl,
        title,
        subtitle,
        video,
        videoForMobile,
        videoPoster
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
		setAttributes({
			logoId: logo.id,
			logoUrl: [logoLG, logoMD, logoSM],
			logoAlt: logo.alt
		});
	}

    // --- WIZARD CONTROLS MOVED HERE ---
    const ALLOWED_MEDIA_TYPES = ["image", "video"];

    return (
        <InspectorControls>
            <PanelBody title="Hero Content" initialOpen={true}>
                <PanelRow>
                    <RichText
                        value={title}
                        onChange={(title) => setAttributes({ title })}
                        placeholder="Hero Header"
                        formattingControls={[]}
                        tagName="h2"
                    />
                </PanelRow>
                <PanelRow>
                    <RichText
                        value={subtitle}
                        onChange={(subtitle) => setAttributes({ subtitle })}
                        placeholder="Hero Subheader"
                        formattingControls={[]}
                        tagName="h3"
                    />
                </PanelRow>
                <PanelRow>
                {videoHero ? (
                    video ? (
                        <div className="block-video">
                            <IconButton
                                className="remove-media"
                                label={__("Remove Video")}
                                onClick={() => setAttributes({ video: "" })}
                                icon="no-alt"
                            />
                            <figure>
                                <Disabled isDisabled={true}>
                                    <video controls>
                                        <source src={video.url} />
                                    </video>
                                </Disabled>
                            </figure>
                        </div>
                    ) : (
                        <MediaPlaceholder
                            icon="format-video"
                            onSelect={(video) => setAttributes({ video })}
                            allowedTypes={ALLOWED_MEDIA_TYPES}
                            multiple={false}
                            labels={{
                                title: "Hero Video",
                                instructions:
                                    "Upload a video file, or pick one from your media library.",
                            }}
                            value={video}
                        />
                    )
                ) : image ? (
                    <div style={{width: '100%'}}>
                        <IconButton
                            className="remove-media"
                            label={__("Remove Image")}
                            onClick={() => setAttributes({ image: "" })}
                            icon="no-alt"
                        />
                        <div
                            className="block-image"
                            style={{
                                backgroundImage: `url(${image.url})`,
                                minHeight: 120,
                                backgroundSize: 'cover',
                                backgroundPosition: 'center',
                            }}
                        />
                    </div>
                ) : (
                    <MediaPlaceholder
                        icon="images-alt2"
                        onSelect={(image) => setAttributes({ image })}
                        allowedTypes={ALLOWED_MEDIA_TYPES}
                        multiple={false}
                        labels={{
                            title: "Hero Image",
                            instructions:
                                "Upload an image, or pick one from your media library",
                        }}
                    />
                )}
                </PanelRow>
                {/* Mobile Video/Image */}
                <PanelRow>
                {videoHero ? (
                    videoForMobile ? (
                        <div className="block-video block-video--portrait">
                            <IconButton
                                className="remove-media"
                                label={__("Remove Mobile Video")}
                                onClick={() => setAttributes({ videoForMobile: "" })}
                                icon="no-alt"
                            />
                            <figure className="portrait-video">
                                <Disabled isDisabled={true}>
                                    <video controls>
                                        <source src={videoForMobile.url} />
                                    </video>
                                </Disabled>
                            </figure>
                        </div>
                    ) : (
                        <MediaPlaceholder
                            icon="format-video"
                            onSelect={videoForMobile => setAttributes({ videoForMobile })}
                            allowedTypes={ALLOWED_MEDIA_TYPES}
                            multiple={false}
                            labels={{
                                title: "Mobile Hero Video",
                                instructions: "Upload a video file for mobile, or pick one from your media library.",
                            }}
                            value={videoForMobile}
                        />
                    )
                ) : mobileImage ? (
                    <div style={{width: '100%'}}>
                        <IconButton
                            className="remove-media"
                            label={__("Remove Image")}
                            onClick={() => setAttributes({ mobileImage: "" })}
                            icon="no-alt"
                        />
                        <div
                            className="block-image"
                            style={{
                                backgroundImage: `url(${mobileImage.url})`,
                                minHeight: 120,
                                backgroundSize: 'cover',
                                backgroundPosition: 'center',
                            }}
                        />
                    </div>
                ) : (
                    <MediaPlaceholder
                        icon="images-alt2"
                        onSelect={mobileImage => setAttributes({ mobileImage })}
                        allowedTypes={ALLOWED_MEDIA_TYPES}
                        multiple={false}
                        labels={{
                            title: "Mobile Hero Image",
                            instructions: "Upload an image for mobile, or pick one from your media library",
                        }}
                    />
                )}
                </PanelRow>
                {/* Video Poster */}
                {videoHero && (
                    <PanelRow>
                        {videoPoster ? (
                            <div className="block-image-poster" style={{backgroundImage: `url(${videoPoster.url})`, minHeight: 80, backgroundSize: 'cover', backgroundPosition: 'center'}}>
                                <IconButton
                                    className="remove-media"
                                    label={__("Remove Image")}
                                    onClick={() => setAttributes({ videoPoster: "" })}
                                    icon="no-alt"
                                />
                            </div>
                        ) : (
                            <MediaPlaceholder
                                icon="images-alt2"
                                onSelect={(videoPoster) => setAttributes({ videoPoster })}
                                allowedTypes={ALLOWED_MEDIA_TYPES}
                                multiple={false}
                                labels={{
                                    title: "Hero Video Poster",
                                    instructions:
                                        "Upload an image for a video placeholder",
                                }}
                            />
                        )}
                    </PanelRow>
                )}
            </PanelBody>
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
