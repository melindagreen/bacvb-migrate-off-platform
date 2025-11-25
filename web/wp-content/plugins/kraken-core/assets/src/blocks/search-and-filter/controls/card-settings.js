/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { PanelBody, SelectControl } from "@wordpress/components";
import { getBlockType } from "@wordpress/blocks";

// Local Dependencies
import CardContent from "../../content-card/controls/card-content";
import { getCardStyles } from "../../../filters/helpers";

/*** COMPONENTS **************************************************************/
const CardSettings = (props) => {
  const { attributes, setAttributes } = props;

  return (
    <>
      {
        /*only output card settings if content card exists */
        getBlockType("kraken-core/content-card") && (
          <>
            <PanelBody title="Card Settings" initialOpen={false}>
              <SelectControl
                label={__("Card Style")}
                value={attributes.cardStyle}
                options={getCardStyles()}
                onChange={(val) => {
                  setAttributes({ cardStyle: val });
                }}
              />
              <CardContent {...props} />
            </PanelBody>
          </>
        )
      }
    </>
  );
};

export default CardSettings;
