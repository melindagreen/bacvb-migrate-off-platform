/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { PanelColorSettings, withColors } from "@wordpress/block-editor";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/

const ColorControls = (props) => {
  const {
    attributes,
    backgroundColor,
    textColor,
    arrowColor,
    arrowBackgroundColor,
    arrowBackgroundHoverColor,
    paginationColor,
    paginationActiveColor,
    scrollbarColor,
    setBackgroundColor,
    setTextColor,
    setArrowColor,
    setArrowBackgroundColor,
    setArrowBackgroundHoverColor,
    setPaginationColor,
    setPaginationActiveColor,
    setScrollbarColor,
  } = props;

  const setColorPanel = () => {
    let colorPanelSettings = [];

    colorPanelSettings.push({
      value: backgroundColor.color,
      onChange: setBackgroundColor,
      label: __("Card Background Color"),
    });

    colorPanelSettings.push({
      value: textColor.color,
      onChange: setTextColor,
      label: __("Card Text Color"),
    });

    if (attributes.enableArrowNavigation) {
      colorPanelSettings.push({
        value: arrowColor.color,
        onChange: setArrowColor,
        label: __("Arrow Color"),
      });

      colorPanelSettings.push({
        value: arrowBackgroundColor.color,
        onChange: setArrowBackgroundColor,
        label: __("Arrow Background Color"),
      });

      colorPanelSettings.push({
        value: arrowBackgroundHoverColor.color,
        onChange: setArrowBackgroundHoverColor,
        label: __("Arrow Background Hover Color"),
      });
    }
    if (attributes.enablePagination) {
      colorPanelSettings.push({
        value: paginationColor.color,
        onChange: setPaginationColor,
        label: __("Pagination Color"),
      });

      colorPanelSettings.push({
        value: paginationActiveColor.color,
        onChange: setPaginationActiveColor,
        label: __("Active Pagination Color"),
      });
    }
    if (attributes.enableScrollbar) {
      colorPanelSettings.push({
        value: scrollbarColor.color,
        onChange: setScrollbarColor,
        label: __("Scrollbar Color"),
      });
    }
    return colorPanelSettings;
  };

  return (
    <PanelColorSettings
      __experimentalIsRenderedInSidebar
      title={"Colors"}
      colorSettings={setColorPanel()}
      className={`kraken-core-color-panel`}
    />
  );
};

/*** EXPORTS ****************************************************************/
export default withColors({
  backgroundColor: "background-color",
  textColor: "text-color",
  arrowColor: "arrow-color",
  arrowBackgroundColor: "arrow-background-color",
  arrowBackgroundHoverColor: "arrow-background-hover-color",
  paginationColor: "pagination-color",
  paginationActiveColor: "pagination-active-color",
  scrollbarColor: "scrollbar-color",
})(ColorControls);
