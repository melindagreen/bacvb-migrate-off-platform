/**
 * Control components for the slider-pagination block
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl, RadioControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

const Controls = ({ attributes, setAttributes, sliderBlocks }) => {
    const { 
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

    // Create slider options for the dropdown with meaningful names and simple numeric index
    const sliderOptions = [
        { label: __('Select a slider', 'madden-theme'), value: 0 },
        ...sliderBlocks.map((slider, index) => ({
            label: slider.name || `Slider ${index + 1}`,
            value: index + 1 // Simple numeric ID
        }))
    ];

    // Verify that a selected slider still exists
    useEffect(() => {
        if (selectedSlider > 0 && sliderBlocks.length < selectedSlider) {
            // The selected slider no longer exists (or there are fewer sliders now)
            setAttributes({ selectedSlider: 0 });
        }
    }, [selectedSlider, sliderBlocks, setAttributes]);

    return (
        <InspectorControls>
            <PanelBody title={__('Slider Controls Settings', 'madden-theme')}>
                <SelectControl
                    label={__('Target Slider', 'madden-theme')}
                    value={selectedSlider}
                    options={sliderOptions}
                    onChange={(value) => {
                        setAttributes({ selectedSlider: parseInt(value, 10) });
                    }}
                    help={__('Select the slider to control with these controls', 'madden-theme')}
                />
                
                <ToggleControl
                    label={__('Show Pagination Dots', 'madden-theme')}
                    checked={enablePagination}
                    onChange={(value) => setAttributes({ enablePagination: value })}
                    help={__('Display pagination dots for the slider', 'madden-theme')}
                />
                
                <ToggleControl
                    label={__('Show Arrow Navigation', 'madden-theme')}
                    checked={enableArrows}
                    onChange={(value) => setAttributes({ enableArrows: value })}
                    help={__('Display previous/next arrows for the slider', 'madden-theme')}
                />
            </PanelBody>
            
            {enablePagination && (
                <PanelColorSettings
                    title={__('Pagination Colors', 'madden-theme')}
                    initialOpen={false}
                    colorSettings={[
                        {
                            value: dotColor,
                            onChange: (color) => setAttributes({ dotColor: color }),
                            label: __('Dot Color', 'madden-theme'),
                        },
                        {
                            value: dotColorActive,
                            onChange: (color) => setAttributes({ dotColorActive: color }),
                            label: __('Active Dot Color', 'madden-theme'),
                        },
                        {
                            value: dotBorderColor,
                            onChange: (color) => setAttributes({ dotBorderColor: color }),
                            label: __('Dot Border Color', 'madden-theme'),
                        },
                    ]}
                />
            )}
            
            {enableArrows && (
                <>
                    <PanelBody title={__('Arrow Style', 'madden-theme')} initialOpen={false}>
                        <RadioControl
                            label={__('Arrow Style', 'madden-theme')}
                            selected={arrowStyle}
                            options={[
                                { label: __('Default', 'madden-theme'), value: 'default' },
                                { label: __('Modern Arrow', 'madden-theme'), value: 'modern' },
                            ]}
                            onChange={(value) => setAttributes({ arrowStyle: value })}
                        />
                    </PanelBody>
                    
                    <PanelColorSettings
                        title={__('Arrow Colors', 'madden-theme')}
                        initialOpen={false}
                        colorSettings={[
                            {
                                value: arrowColor,
                                onChange: (color) => setAttributes({ arrowColor: color }),
                                label: __('Arrow Color', 'madden-theme'),
                            },
                            {
                                value: arrowBackgroundColor,
                                onChange: (color) => setAttributes({ arrowBackgroundColor: color }),
                                label: __('Arrow Background', 'madden-theme'),
                            },
                            {
                                value: arrowBorderColor,
                                onChange: (color) => setAttributes({ arrowBorderColor: color }),
                                label: __('Arrow Border Color', 'madden-theme'),
                            },
                        ]}
                    />
                </>
            )}
        </InspectorControls>
    );
};

export default Controls;