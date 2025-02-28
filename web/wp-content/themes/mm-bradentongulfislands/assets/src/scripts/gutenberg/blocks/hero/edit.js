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

// Controls - add block/inspector controls here
import Controls from './controls'
import { THEME_PREFIX } from 'scripts/inc/constants';

/*** CONSTANTS **************************************************************/

const ALLOWED_MEDIA_TYPES = ["image", "video"];

/*** COMPONTANTS ************************************************************/

/**
 * Fields that modify the attributes of the current block
 * @param {*} props
 * @returns {WPElement}
 */
 const Wizard = props => {
  const { attributes: { 
	image, 
	focalPoint,
	mobileImage,
	focalPointMobile,
	title, 
	subtitle, 
	videoHero, 
	smallHero,
	video,
	videoForMobile, 
	videoPoster,
  }, setAttributes } = props;

  const heroStyle = {};
  const heroStyleMobile = {};

  if ( focalPoint )
    heroStyle.backgroundPosition = `${ focalPoint.x * 100 }% ${
	  focalPoint.y * 100
    }%`;

  if ( focalPointMobile )
    heroStyleMobile.backgroundPosition = `${ focalPointMobile.x * 100 }% ${
   		focalPointMobile.y * 100
    }%`;

  return (
	<>
		<RichText
			value={title}
			onChange={(title) => setAttributes({ title })}
			placeholder="Hero Header"
			formattingControls={[]}
			tagName="h2"
		/>
		<RichText
			value={subtitle}
			onChange={(subtitle) => setAttributes({ subtitle })}
			placeholder="Hero Subheader"
			formattingControls={[]}
			tagName="h3"
		/>
		{videoHero ? (
			video ? (
				<div className="block-video">
					<IconButton
						className="remove-media"
						label={__("Remove Video")}
						onClick={(video) => setAttributes({ video: "" })}
						icon="no-alt"
					/>
					<figure>
						<Disabled isDisabled="true">
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
			<PanelRow>
				<IconButton
					className="remove-media"
					label={__("Remove Image")}
					onClick={(image) => setAttributes({ image: "" })}
					icon="no-alt"
				/>
				<div
					className="block-image"
					style={{
						backgroundImage: `url(${image.url})`,
					}}
				>
					<IconButton
						className="remove-media"
						label={__("Remove Image")}
						onClick={(image) => setAttributes({ image: "" })}
						icon="no-alt"
					/>
				</div>
				<div style={ heroStyle } />
			</PanelRow>
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
      {/* Mobile Video */}
      {videoHero ? (
        videoForMobile ? (
          <div className="block-video block-video--portrait">
            <IconButton
              className="remove-media"
              label={__("Remove Mobile Video")}
              onClick={(videoForMobile) => setAttributes({ videoForMobile: "" })}
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
				<PanelRow>
					<IconButton
						className="remove-media"
						label={__("Remove Image")}
						onClick={(mobileImage) => setAttributes({ mobileImage: "" })}
						icon="no-alt"
					/>
					<div
						className="block-image"
						style={{
							backgroundImage: `url(${mobileImage.url})`,
						}}
					>
						<IconButton
							className="remove-media"
							label={__("Remove Image")}
							onClick={(mobileImage) => setAttributes({ mobileImage: "" })}
							icon="no-alt"
						/>
					</div>
					<div style={ heroStyle } />
				</PanelRow>
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
      {/* Mobile Video End */}
		{videoHero && videoPoster && (
			<div
				className="block-image-poster"
				style={{
					backgroundImage: `url(${videoPoster.url})`,
				}}
			>
				<IconButton
					className="remove-media"
					label={__("Remove Image")}
					onClick={(videoPoster) => setAttributes({ videoPoster: "" })}
					icon="no-alt"
				/>
			</div>
		)}
		{videoHero && !videoPoster && (
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
	</>
  )
}

/**
 * The editor for the block
 * @param {*} props
 * @returns {WPElement}
 */
const Editor = props => {
  const { attributes: { mode }, className } = props;

  useEffect(() => {
    initHero();
  }, [props.attributes]);

  return (
    <section className={`${className} ${mode === 'edit' ? 'is-edit' : 'is-preview'}`}>
      {mode === 'edit'
        ? <Wizard {...props} />
        : <ServerSideRender block={props.name} httpMethod={'POST'} {...props} />}
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
