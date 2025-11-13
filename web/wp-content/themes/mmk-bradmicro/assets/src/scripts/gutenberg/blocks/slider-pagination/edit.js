/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { useEffect } from "@wordpress/element";

// Local dependencies
import Controls from "./controls";
import { THEME_PREFIX } from "scripts/inc/constants";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/

const Editor = (props) => {
    const { attributes, setAttributes, clientId } = props;
    const { 
        blockId,
        selectedSlider,
        enablePagination, 
        enableArrows, 
        dotColor, 
        dotColorActive,
        dotBorderColor,
        arrowColor,
        arrowBackgroundColor,
        arrowBorderColor,
        arrowStyle
    } = attributes;
    const blockProps = useBlockProps();

    // Generate unique ID if not already set - this ensures our block has a stable ID
    useEffect(() => {
        if (!blockId) {
            const randomId = Math.random().toString(36).substring(2, 12);
            setAttributes({ blockId: randomId });
        }
    }, [blockId, setAttributes]);

    // Get all slider blocks on the page
    const sliderBlocks = useSelect(select => {
        const { getBlocks } = select('core/block-editor');
        const rootBlocks = getBlocks();
        
        // Function to recursively find slider blocks
        const findSliderBlocks = (blocks) => {
            let sliders = [];
            blocks.forEach(block => {
                if (block.name === 'madden-theme/slider') {
                    // Get block metadata for custom name
                    const blockMetadata = block.attributes?.metadata || {};
                    const customName = blockMetadata.name || '';
                    
                    // Generate a meaningful name
                    let sliderName;
                    if (customName) {
                        sliderName = customName; // Use WordPress custom name
                    } else if (block.attributes.anchor) {
                        sliderName = `Slider: ${block.attributes.anchor}`;
                    } else {
                        sliderName = `Slider ${sliders.length + 1}`;
                    }
                    
                    // Store each slider with relevant info
                    sliders.push({
                        id: block.attributes.sliderId || '',
                        name: sliderName
                    });
                }
                if (block.innerBlocks && block.innerBlocks.length) {
                    sliders = [...sliders, ...findSliderBlocks(block.innerBlocks)];
                }
            });
            return sliders;
        };
        
        return findSliderBlocks(rootBlocks);
    }, []);

    // Get the currently selected slider based on index
    const getSelectedSliderName = () => {
        if (selectedSlider > 0 && sliderBlocks.length >= selectedSlider) {
            return sliderBlocks[selectedSlider - 1]?.name || __('Unknown Slider', 'madden-theme');
        }
        return '';
    };
    
    // Custom styling based on selected colors
    const dotStyles = {
        backgroundColor: dotColor || undefined,
        border: dotBorderColor ? `1px solid ${dotBorderColor}` : undefined
    };
    
    const activeDotStyles = {
        backgroundColor: dotColorActive || '#000',
        border: dotBorderColor ? `1px solid ${dotBorderColor}` : undefined
    };
    
    const arrowStyles = {
        color: arrowColor || undefined,
        backgroundColor: arrowBackgroundColor || undefined,
        border: arrowBorderColor ? `1px solid ${arrowBorderColor}` : undefined
    };

    // SVG for modern arrow style
    const renderModernArrow = (direction) => {
        if (arrowStyle !== 'modern') return direction === 'prev' ? '←' : '→';
        
        return direction === 'prev' ? (
            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none" className="arrow-svg arrow-prev">
                <path d="M10.553 3.05273L3.50226 10.1035L10.553 17.1542" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                <path d="M3.50226 10.1035L18.5022 10.1035" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
        ) : (
            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none" className="arrow-svg arrow-next">
                <path d="M10.553 3.05273L17.6037 10.1035L10.553 17.1542" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                <path d="M17.6037 10.1035L2.60376 10.1035" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
        );
    };

    return (
        <>
            <Controls
                attributes={attributes}
                setAttributes={setAttributes}
                sliderBlocks={sliderBlocks}
            />
            
            <div {...blockProps}>
                <div className="slider-controls-placeholder">
                    <p>{__('Slider Controls', 'madden-theme')}</p>
                    {selectedSlider > 0 ? (
                        <p className="slider-controls-target">
                            {__('Connected to:', 'madden-theme')} {getSelectedSliderName()}
                        </p>
                    ) : (
                        <p className="slider-controls-warning">{__('Please select a target slider in the block settings', 'madden-theme')}</p>
                    )}
                    
                    <div className="slider-controls-preview">
                        {enableArrows && (
                            <>
                                <div className="slider-arrow prev" style={arrowStyles}>{renderModernArrow('prev')}</div>
                                {enablePagination && (
                                    <div className="slider-pagination-dots">
                                        <span className="active" style={activeDotStyles}></span>
                                        <span style={dotStyles}></span>
                                        <span style={dotStyles}></span>
                                    </div>
                                )}
                                <div className="slider-arrow next" style={arrowStyles}>{renderModernArrow('next')}</div>
                            </>
                        )}
                        
                        {!enableArrows && enablePagination && (
                            <div className="slider-pagination-dots">
                                <span className="active" style={activeDotStyles}></span>
                                <span style={dotStyles}></span>
                                <span style={dotStyles}></span>
                            </div>
                        )}
                        
                        {!enableArrows && !enablePagination && (
                            <p className="slider-controls-warning">{__('Enable pagination or arrows in the block settings', 'madden-theme')}</p>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
};

export default Editor;