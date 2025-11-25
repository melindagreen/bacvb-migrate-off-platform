/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
  Button,
  __experimentalNumberControl as NumberControl,
  ResponsiveWrapper,
  SelectControl,
  TextControl,
  TextareaControl,
  ToggleControl,
} from "@wordpress/components";
import { MediaUpload, MediaUploadCheck } from "@wordpress/block-editor";
const { useSelect } = wp.data;

// Local dependencies
import { renderDynamicControls } from "../../../filters/helpers";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/

const CardContent = (props) => {
  const { attributes, setAttributes } = props;

  const blockName = useSelect((select) => select("core/block-editor").getBlockName(props.clientId));

  return (
    <>
      {attributes.postType !== "queried_post" && blockName === "kraken-core/content-card" && (
        <>
          <ToggleControl
            label={__("Custom Title")}
            checked={attributes.displayCustomTitle}
            onChange={() => {
              setAttributes({
                displayCustomTitle: !attributes.displayCustomTitle,
              });
            }}
          />
          {attributes.displayCustomTitle && (
            <TextareaControl
              label={__("Customize Title Text")}
              value={attributes.customTitle}
              onChange={(val) => {
                setAttributes({ customTitle: val });
              }}
            />
          )}
          <ToggleControl
            label={__("Custom Image")}
            checked={attributes.displayCustomImage}
            onChange={() => {
              setAttributes({
                displayCustomImage: !attributes.displayCustomImage,
              });
            }}
          />
          {attributes.displayCustomImage && (
            <MediaUploadCheck>
              <MediaUpload
                title={__("Upload Image")}
                allowedTypes={["image"]}
                onSelect={(images) => {
                  setAttributes({
                    customImage: {
                      id: images.id,
                      url: images.url,
                    },
                  });
                }}
                value={attributes.customImage.id}
                render={({ open }) => (
                  <div className="image-select">
                    <Button onClick={open} isLarge icon="format-gallery">
                      {__("Select Image")}
                    </Button>
                    {attributes.customImage.url != "" && (
                      <ResponsiveWrapper>
                        <img src={attributes.customImage.url} />
                      </ResponsiveWrapper>
                    )}
                  </div>
                )}
              />
            </MediaUploadCheck>
          )}
        </>
      )}

      <ToggleControl
        label={__("Display Additional Content?")}
        checked={attributes.displayAdditionalContent}
        onChange={() => {
          setAttributes({
            displayAdditionalContent: !attributes.displayAdditionalContent,
          });
        }}
      />

      {attributes.displayAdditionalContent && (
        <>
          {attributes.postType.includes("event") && (
            <>
              <ToggleControl
                label={__("Event Date")}
                checked={attributes.displayEventDate}
                onChange={() => {
                  setAttributes({
                    displayEventDate: !attributes.displayEventDate,
                  });
                }}
              />
              <ToggleControl
                label={__("Event Time")}
                checked={attributes.displayEventTime}
                onChange={() => {
                  setAttributes({
                    displayEventTime: !attributes.displayEventTime,
                  });
                }}
              />
            </>
          )}
          <ToggleControl
            label={__("Address")}
            checked={attributes.displayAddress}
            onChange={() => {
              setAttributes({
                displayAddress: !attributes.displayAddress,
              });
            }}
          />
          <ToggleControl
            label={__("Website Link")}
            checked={attributes.displayWebsiteLink}
            onChange={() => {
              setAttributes({
                displayWebsiteLink: !attributes.displayWebsiteLink,
              });
            }}
          />
          <ToggleControl
            label={__("Post Excerpt")}
            checked={attributes.displayExcerpt}
            onChange={() => {
              setAttributes({
                displayExcerpt: !attributes.displayExcerpt,
                displayCustomExcerpt: false,
              });
            }}
          />
          {attributes.displayExcerpt && (
            <NumberControl
              label={__("Excerpt Word Count")}
              value={attributes.excerptLength}
              min={0}
              onChange={(val) => {
                setAttributes({ excerptLength: Number(val) });
              }}
            />
          )}
          {attributes.postType !== "queried_post" && blockName === "kraken-core/content-card" && (
            <>
              <ToggleControl
                label={__("Custom Excerpt")}
                checked={attributes.displayCustomExcerpt}
                onChange={() => {
                  setAttributes({
                    displayExcerpt: false,
                    displayCustomExcerpt: !attributes.displayCustomExcerpt,
                  });
                }}
              />
              {attributes.displayCustomExcerpt && (
                <TextareaControl
                  label={__("Customize Excerpt Text")}
                  value={attributes.customExcerpt}
                  onChange={(val) => {
                    setAttributes({
                      customExcerpt: val,
                    });
                  }}
                />
              )}
            </>
          )}
          <hr />
          <ToggleControl
            label={__("Read More Text")}
            checked={attributes.displayReadMore}
            onChange={() => {
              setAttributes({
                displayReadMore: !attributes.displayReadMore,
              });
            }}
          />
          {attributes.displayReadMore && (
            <TextControl
              label={__("Customize Read More Text")}
              value={attributes.readMoreText}
              onChange={(val) => {
                setAttributes({ readMoreText: val });
              }}
            />
          )}

          <hr />
          <ToggleControl
            label={__("Display Mindtrip CTA")}
            help={__("Requires Mindtrip integration")}
            checked={attributes.displayMindtripCta}
            onChange={() => {
              setAttributes({
                displayMindtripCta: !attributes.displayMindtripCta,
              });
            }}
          />
          {attributes.displayMindtripCta && (
            <>
              <SelectControl
                label={__("Mindtrip CTA Type")}
                value={attributes.mindtripCtaType}
                options={[
                  { label: "Button", value: "button" },
                  { label: "Icon Only", value: "icon-only" },
                ]}
                onChange={(val) => {
                  setAttributes({ mindtripCtaType: val });
                }}
              />
              <TextControl
                label={__("Mindtrip CTA Text")}
                help={__("Sparkle added automatically")}
                value={attributes.mindtripCtaText}
                onChange={(val) => {
                  setAttributes({ mindtripCtaText: val });
                }}
              />
              <TextControl
                label={__("Mindtrip Prompt")}
                help={__(
                  "Use %postname% to output the post/listing name in your prompt. Defaults to `Explore things to do near %postname%`",
                )}
                value={attributes.mindtripPrompt}
                onChange={(val) => {
                  setAttributes({ mindtripPrompt: val });
                }}
              />
            </>
          )}

          {/* Dynamic Custom Additional Content Controls */}
          {(() => {
            const customSettings = KrakenThemeSettings.blockData.cardAttributes;
            return (
              Object.keys(customSettings).length > 0 && (
                <>
                  <hr />
                  {renderDynamicControls(
                    customSettings,
                    attributes,
                    setAttributes,
                    "customAdditionalContent",
                  )}
                </>
              )
            );
          })()}
        </>
      )}
    </>
  );
};

/*** EXPORTS ****************************************************************/

export default CardContent;
