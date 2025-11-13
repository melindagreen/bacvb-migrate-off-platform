import {
  useBlockProps,
  BlockControls,
  MediaReplaceFlow,
  InspectorControls,
  useSettings,
  __experimentalLinkControl as LinkControl,
} from "@wordpress/block-editor";
import {
  PanelBody,
  Popover,
  ToggleControl,
  ToolbarButton,
  __experimentalUnitControl as UnitControl,
  __experimentalUseCustomUnits as useCustomUnits,
} from "@wordpress/components";
import { useState, useEffect, RawHTML } from "@wordpress/element";
import { useSelect } from "@wordpress/data";
import { link, linkOff } from "@wordpress/icons";

function processSVGString(raw, { width, height, lockAspectRatio }) {
  const parser = new DOMParser();
  const doc = parser.parseFromString(raw, "image/svg+xml");
  const svgEl = doc.querySelector("svg");
  if (!svgEl) {
    return "";
  }

  svgEl.setAttribute("width", width);
  if (!lockAspectRatio && height) {
    svgEl.setAttribute("height", height);
  }

  if (lockAspectRatio) {
    svgEl.removeAttribute("preserveAspectRatio");
  } else {
    svgEl.setAttribute("preserveAspectRatio", "none");
  }

  // serialize back to string
  return new XMLSerializer().serializeToString(svgEl);
}

export default function Edit({ name, attributes, setAttributes, isSelected }) {
  const {
    svgID,
    width,
    height,
    containerWidth,
    containerHeight,
    svgLink,
    lockAspectRatio,
  } = attributes;
  const [previewSVG, setPreviewSVG] = useState(null);
  const [popoverOpen, setPopoverOpen] = useState(false);
  const [units] = useSettings("spacing.units");
  const customUnits = useCustomUnits({
    availableUnits: units,
  });

  const allBlockProps = useBlockProps();
  console.log("attributes", attributes);
  console.log("allBlockProps", allBlockProps);
  const { style: supportStyle, ...outerProps } = allBlockProps;

  // Fetch media info
  const svg = useSelect(
    (select) => (svgID ? select("core").getMedia(svgID) : null),
    [svgID]
  );

  // Fetch inline SVG content
  useEffect(() => {
    if (svg?.source_url?.endsWith(".svg")) {
      fetch(svg.source_url)
        .then((res) => res.text())
        .then((raw) => {
          if (raw.includes("<svg")) {
            const processed = processSVGString(raw, {
              width,
              height,
              lockAspectRatio,
            });
            setPreviewSVG(processed);
            // store in attributes for save step
            setAttributes({ svgContent: processed });
          }
        });
    } else {
      setPreviewSVG(null);
      setAttributes({ svgContent: "" });
    }
  }, [svg, width, height, lockAspectRatio]);

  const onSelectSVG = (media) => {
    if (media && media.id && media.mime === "image/svg+xml") {
      setAttributes({ svgID: media.id });
    }
  };

  return (
    <>
      {isSelected && (
        <>
          <BlockControls>
            <MediaReplaceFlow
              mediaId={svgID}
              mediaURL={svg?.source_url || ""}
              allowedTypes={["image/svg+xml"]}
              accept="image/svg+xml"
              onSelect={onSelectSVG}
              name={svgID ? "Replace SVG" : "Add SVG"}
              onError={(err) => console.error(err)}
            />
            <>
              <ToolbarButton
                icon={
                  svgLink && Object.keys(svgLink).length > 0 ? linkOff : link
                }
                label="Link"
                onClick={() => setPopoverOpen(!popoverOpen)}
              />
              {popoverOpen && (
                <Popover>
                  <LinkControl
                    value={svgLink}
                    onChange={(updatedLink) => {
                      setAttributes({
                        svgLink: updatedLink,
                      });
                    }}
                    onRemove={() => {
                      setAttributes({
                        svgLink: false,
                      });
                    }}
                  />
                </Popover>
              )}
            </>
          </BlockControls>
        </>
      )}

      <InspectorControls>
        <PanelBody title="SVG Settings" initialOpen>
          <UnitControl
            label="Width"
            value={width}
            onChange={(value) => setAttributes({ width: value })}
            units={customUnits}
          />
          <UnitControl
            label="Height"
            value={height}
            onChange={(value) => setAttributes({ height: value })}
            units={customUnits}
            disabled={lockAspectRatio}
          />
          <ToggleControl
            label="Lock Aspect Ratio"
            checked={lockAspectRatio}
            onChange={(value) => setAttributes({ lockAspectRatio: value })}
          />
        </PanelBody>
        <PanelBody title="Container Settings" initialOpen>
          <UnitControl
            label="Container Width"
            value={containerWidth}
            onChange={(value) => setAttributes({ containerWidth: value })}
            units={customUnits}
          />
          <UnitControl
            label="Container Height"
            value={containerHeight}
            onChange={(value) => setAttributes({ containerHeight: value })}
            units={customUnits}
            disabled={lockAspectRatio}
          />
        </PanelBody>
      </InspectorControls>

      <div {...allBlockProps}>
        {previewSVG ? (
          <RawHTML
            className="svgElement-wrapper"
            style={{ ...supportStyle, display: "inline-block" }}
          >
            {svgLink && svgLink.url
              ? `<a href="${svgLink.url}" target="${
                  svgLink.opensInNewTab ? "_blank" : "_self"
                }">${previewSVG}</a>`
              : previewSVG}
          </RawHTML>
        ) : (
          <p>Select an SVG from the toolbar</p>
        )}
      </div>
    </>
  );
}
