/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
  SelectControl,
  ToggleControl,
  PanelBody,
  PanelRow,
  __experimentalNumberControl as NumberControl,
  __experimentalUnitControl as UnitControl,
} from "@wordpress/components";
import { useBlockProps } from "@wordpress/block-editor";
import { useEffect } from "@wordpress/element";

/*** FUNCTIONS **************************************************************/

const SliderOptions = (props) => {
  const { attributes, setAttributes } = props;
  const wrapperProps = useBlockProps({
    className: "wp-block-kraken-core-slider",
  });

  useEffect(() => {
    if (attributes.loop) {
      setAttributes({ enableGridRows: false });
    }
  }, [attributes.loop]);

  useEffect(() => {
    if (attributes.enableGridRows) {
      setAttributes({ loop: false });
      setAttributes({ enableSlidesPerViewAuto: false });
      setAttributes({ enableSlidesPerGroupAuto: false });
    }
  }, [attributes.enableGridRows]);

  useEffect(() => {
    if (attributes.enableSlidesPerViewAuto) {
      setAttributes({ enableGridRows: false });
    } else {
      setAttributes({ enableSlidesPerGroupAuto: false });
    }
  }, [attributes.enableSlidesPerViewAuto]);

  useEffect(() => {
    if (attributes.enableSlidesPerGroupAuto) {
      setAttributes({ enableGridRows: false });
      setAttributes({ enableSlidesPerViewAuto: true });
    }
  }, [attributes.enableSlidesPerGroupAuto]);

  return (
    <div className={wrapperProps.className}>
      <PanelBody title="Slider Navigation">
        <ToggleControl
          label={__("Arrow Navigation")}
          checked={attributes.enableArrowNavigation}
          onChange={() => {
            setAttributes({
              enableArrowNavigation: !attributes.enableArrowNavigation,
            });
          }}
        />
        {attributes.enableArrowNavigation && (
          <ToggleControl
            label={__("Display Arrows Below Slider")}
            checked={attributes.arrowsBelowSlider}
            onChange={() => {
              setAttributes({
                arrowsBelowSlider: !attributes.arrowsBelowSlider,
                arrowsOutsideSlider: false,
              });
            }}
          />
        )}
        {attributes.enableArrowNavigation && (
          <ToggleControl
            label={__("Display Arrows Outside Slider")}
            checked={attributes.arrowsOutsideSlider}
            onChange={() => {
              setAttributes({
                arrowsBelowSlider: false,
                arrowsOutsideSlider: !attributes.arrowsOutsideSlider,
              });
            }}
          />
        )}
        <ToggleControl
          label={__("Dot Pagination")}
          checked={attributes.enablePagination}
          onChange={() => {
            setAttributes({ enablePagination: !attributes.enablePagination });
          }}
        />
        <ToggleControl
          label={__("Scrollbar")}
          checked={attributes.enableScrollbar}
          onChange={() => {
            setAttributes({ enableScrollbar: !attributes.enableScrollbar });
          }}
        />
        <hr />
        <p style={{ fontWeight: 600 }}>Slider Direction</p>
        <PanelRow>
          <SelectControl
            label={__("Desktop")}
            value={attributes.sliderDirectionDesktop}
            options={[
              { label: "Horizontal", value: "horizontal" },
              { label: "Vertical", value: "vertical" },
            ]}
            onChange={(val) => {
              setAttributes({ sliderDirectionDesktop: val });
            }}
          />
          <SelectControl
            label={__("Tablet")}
            value={attributes.sliderDirectionTablet}
            options={[
              { label: "Horizontal", value: "horizontal" },
              { label: "Vertical", value: "vertical" },
            ]}
            onChange={(val) => {
              setAttributes({ sliderDirectionTablet: val });
            }}
          />
          <SelectControl
            label={__("Mobile")}
            value={attributes.sliderDirectionMobile}
            options={[
              { label: "Horizontal", value: "horizontal" },
              { label: "Vertical", value: "vertical" },
            ]}
            onChange={(val) => {
              setAttributes({ sliderDirectionMobile: val });
            }}
          />
        </PanelRow>
      </PanelBody>
      <PanelBody title="Slider Options">
        {attributes.contentType !== "custom" && (
          <ToggleControl
            label={__("Shuffle Slides")}
            help={__("Slides will be shuffled on every page load")}
            checked={attributes.shuffleSlides}
            onChange={() => {
              setAttributes({ shuffleSlides: !attributes.shuffleSlides });
            }}
          />
        )}
        <ToggleControl
          label={__("Autoplay")}
          checked={attributes.enableAutoplay}
          onChange={() => {
            setAttributes({ enableAutoplay: !attributes.enableAutoplay });
          }}
        />
        <ToggleControl
          label={__("Centered Slides")}
          help={__(
            attributes.centeredSlides && "Warning: does not work well when using grouped slides",
          )}
          checked={attributes.centeredSlides}
          onChange={() => {
            setAttributes({ centeredSlides: !attributes.centeredSlides });
          }}
        />
        <ToggleControl
          label={__("Loop Slides")}
          help={__(
            attributes.loop &&
              "Warning: if using slides per view, grouped slides has to be enabled with the same number for loop to work correctly. Not compatible with grid rows.",
          )}
          checked={attributes.loop}
          onChange={() => {
            setAttributes({ loop: !attributes.loop });
          }}
        />
        <ToggleControl
          label={__("Free Scroll")}
          checked={attributes.freeMode}
          onChange={() => {
            setAttributes({ freeMode: !attributes.freeMode });
          }}
        />
        <ToggleControl
          label={__("Mouse Scroll")}
          checked={attributes.enableMouseScroll}
          onChange={() => {
            setAttributes({ enableMouseScroll: !attributes.enableMouseScroll });
          }}
        />
        <ToggleControl
          label={__("Space Between Slides")}
          checked={attributes.enableSpaceBetween}
          onChange={() => {
            setAttributes({
              enableSpaceBetween: !attributes.enableSpaceBetween,
            });
          }}
        />
        {attributes.enableSpaceBetween && (
          <PanelRow>
            <NumberControl
              label={__("Desktop")}
              value={attributes.spaceBetweenDesktop}
              min={0}
              onChange={(val) => {
                setAttributes({ spaceBetweenDesktop: Number(val) });
              }}
            />
            <NumberControl
              label={__("Tablet")}
              value={attributes.spaceBetweenTablet}
              onChange={(val) => {
                setAttributes({ spaceBetweenTablet: Number(val) });
              }}
            />
            <NumberControl
              label={__("Mobile")}
              value={attributes.spaceBetweenMobile}
              min={0}
              onChange={(val) => {
                setAttributes({ spaceBetweenMobile: Number(val) });
              }}
            />
          </PanelRow>
        )}
        <hr />
        <NumberControl
          label={__("Initial Slide")}
          value={attributes.initialSlide}
          min={0}
          onChange={(val) => {
            setAttributes({ initialSlide: Number(val) });
          }}
        />
        <SelectControl
          label={__("Transition Effect")}
          value={attributes.effect}
          options={[
            { label: "Cards", value: "cards" },
            { label: "Coverflow", value: "coverflow" },
            { label: "Fade", value: "fade" },
            { label: "Slide", value: "slide" },
          ]}
          onChange={(val) => {
            setAttributes({ effect: val });
          }}
        />
      </PanelBody>
      <PanelBody title={`Slides Per View - ${attributes.enableSlidesPerView ? "ON" : "OFF"}`}>
        <ToggleControl
          label={__("Enable")}
          checked={attributes.enableSlidesPerView}
          onChange={() => {
            setAttributes({
              enableSlidesPerView: !attributes.enableSlidesPerView,
            });
          }}
        />
        {attributes.enableSlidesPerView && (
          <>
            <ToggleControl
              label={__("Automatic")}
              help={__(
                "Set different breakpoint widths below. Automatic slides per view is not compatible with grid rows.",
              )}
              checked={attributes.enableSlidesPerViewAuto}
              onChange={() => {
                setAttributes({
                  enableSlidesPerViewAuto: !attributes.enableSlidesPerViewAuto,
                });
              }}
            />
            {attributes.enableSlidesPerViewAuto && (
              <>
                <PanelRow>
                  <UnitControl
                    label={__("Desktop")}
                    value={attributes.slidesPerViewWidthDesktop}
                    min={1}
                    onChange={(val) => {
                      setAttributes({ slidesPerViewWidthDesktop: val });
                    }}
                  />
                  <UnitControl
                    label={__("Tablet")}
                    value={attributes.slidesPerViewWidthTablet}
                    min={1}
                    onChange={(val) => {
                      setAttributes({ slidesPerViewWidthTablet: val });
                    }}
                  />
                  <UnitControl
                    label={__("Mobile")}
                    value={attributes.slidesPerViewWidthMobile}
                    min={1}
                    onChange={(val) => {
                      setAttributes({ slidesPerViewWidthMobile: val });
                    }}
                  />
                </PanelRow>
              </>
            )}
            {!attributes.enableSlidesPerViewAuto && (
              <PanelRow>
                <NumberControl
                  label={__("Desktop")}
                  value={attributes.slidesPerViewDesktop}
                  min={1}
                  step={0.1}
                  onChange={(val) => {
                    setAttributes({ slidesPerViewDesktop: Number(val) });
                  }}
                />
                <NumberControl
                  label={__("Tablet")}
                  value={attributes.slidesPerViewTablet}
                  min={1}
                  step={0.1}
                  onChange={(val) => {
                    setAttributes({ slidesPerViewTablet: Number(val) });
                  }}
                />
                <NumberControl
                  label={__("Mobile")}
                  value={attributes.slidesPerViewMobile}
                  min={1}
                  step={0.1}
                  onChange={(val) => {
                    setAttributes({ slidesPerViewMobile: Number(val) });
                  }}
                />
              </PanelRow>
            )}
            <ToggleControl
              label={__("Equal Height Slides")}
              help={__(
                "If using content card, this will add styles to force equal height to the tallest slide",
              )}
              checked={attributes.enableEqualHeightSlides}
              onChange={() => {
                setAttributes({
                  enableEqualHeightSlides: !attributes.enableEqualHeightSlides,
                });
              }}
            />
          </>
        )}
      </PanelBody>
      <PanelBody title={`Grouped Slides - ${attributes.enableSlidesPerGroup ? "ON" : "OFF"}`}>
        <ToggleControl
          label={__("Enable")}
          checked={attributes.enableSlidesPerGroup}
          onChange={() => {
            setAttributes({
              enableSlidesPerGroup: !attributes.enableSlidesPerGroup,
            });
          }}
        />
        {attributes.enableSlidesPerGroup && (
          <>
            <ToggleControl
              label={__("Automatic")}
              help={__("Automatic groups requires slides per view to be set to automatic.")}
              checked={attributes.enableSlidesPerGroupAuto}
              onChange={() => {
                setAttributes({
                  enableSlidesPerGroupAuto: !attributes.enableSlidesPerGroupAuto,
                });
              }}
            />
            {!attributes.enableSlidesPerGroupAuto && (
              <PanelRow>
                <NumberControl
                  label={__("Desktop")}
                  value={attributes.slidesPerGroupDesktop}
                  min={1}
                  step={0.1}
                  onChange={(val) => {
                    setAttributes({ slidesPerGroupDesktop: Number(val) });
                  }}
                />
                <NumberControl
                  label={__("Tablet")}
                  value={attributes.slidesPerGroupTablet}
                  min={1}
                  step={0.1}
                  onChange={(val) => {
                    setAttributes({ slidesPerGroupTablet: Number(val) });
                  }}
                />
                <NumberControl
                  label={__("Mobile")}
                  value={attributes.slidesPerGroupMobile}
                  min={1}
                  step={0.1}
                  onChange={(val) => {
                    setAttributes({ slidesPerGroupMobile: Number(val) });
                  }}
                />
              </PanelRow>
            )}
          </>
        )}
      </PanelBody>
      <PanelBody title={`Slider Grid Rows - ${attributes.enableGridRows ? "ON" : "OFF"}`}>
        <ToggleControl
          label={__("Grid Rows")}
          help={__(
            "Grid rows is not compatible with loop, automatic slides per view, & automatic grouped slides. Use Slides Per View to adjust how many columns appear.",
          )}
          checked={attributes.enableGridRows}
          onChange={() => {
            setAttributes({ enableGridRows: !attributes.enableGridRows });
          }}
        />
        {attributes.enableGridRows && (
          <PanelRow>
            <NumberControl
              label={__("Desktop")}
              value={attributes.gridRowsDesktop}
              min={1}
              onChange={(val) => {
                setAttributes({ gridRowsDesktop: Number(val) });
              }}
            />
            <NumberControl
              label={__("Tablet")}
              value={attributes.gridRowsTablet}
              min={1}
              onChange={(val) => {
                setAttributes({ gridRowsTablet: Number(val) });
              }}
            />
            <NumberControl
              label={__("Mobile")}
              value={attributes.gridRowsMobile}
              min={1}
              onChange={(val) => {
                setAttributes({ gridRowsMobile: Number(val) });
              }}
            />
          </PanelRow>
        )}
      </PanelBody>
    </div>
  );
};

/*** EXPORTS ****************************************************************/
export default SliderOptions;
