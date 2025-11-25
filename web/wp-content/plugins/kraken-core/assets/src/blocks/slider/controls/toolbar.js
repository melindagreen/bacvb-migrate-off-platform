/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { __ } from "@wordpress/i18n";
import { createBlock } from "@wordpress/blocks";
import { store as blockEditorStore } from "@wordpress/block-editor";
import { Toolbar, ToolbarButton } from "@wordpress/components";
import { useSelect, useDispatch } from "@wordpress/data";

/*** COMPONENTS **************************************************************/

const Tools = (props) => {
  const { insertBlock, selectBlock } = useDispatch(blockEditorStore);
  const innerBlocks = useSelect(
    (select) => select(blockEditorStore).getBlock(props.clientId).innerBlocks,
  );

  const addSlide = () => {
    const block = createBlock("kraken-core/single-slide");
    insertBlock(block, innerBlocks.length, props.clientId, false);
    selectBlock(block.clientId);
  };

  return (
    <Toolbar label="Options">
      <ToolbarButton Button icon="plus" onClick={addSlide}>
        {__("Add Slide", "kraken-core")}
      </ToolbarButton>
    </Toolbar>
  );
};

/*** EXPORTS ****************************************************************/

export default Tools;
