import { THEME_PREFIX } from 'scripts/inc/constants';
import Swiper from 'swiper/bundle';
import "./styles/style.scss";

const debug = false;

// Make Swiper available globally if it's not already
if (typeof window.Swiper === 'undefined') {
    window.Swiper = Swiper;
}

// Create global registry if it doesn't exist
if (!window.maddenTheme) {
    window.maddenTheme = {};
}
if (!window.maddenTheme.sliders) {
    window.maddenTheme.sliders = {};
}

window.addEventListener('DOMContentLoaded', () => {
    if(debug) console.log('DOMContentLoaded event fired');
    // Wait for sliders to initialize first
    setTimeout(() => {
        if(debug) console.log('Initializing slider controls after delay');
        initSliderControls();
    }, 300); // Longer delay to ensure Swiper is ready
});

/**
 * Add any sliders found on the page to the global registry if they're not already there
 */
function addSlidersToRegistry(sliderElements) {
    sliderElements.forEach((sliderSection) => {
        const sliderId = sliderSection.getAttribute('data-uid');
        if (sliderId && !window.maddenTheme.sliders[sliderId]) {
            const swiperElement = sliderSection.querySelector('.swiper');
            if (swiperElement && swiperElement.swiper) {
                // Slider is already initialized, just add to registry
                window.maddenTheme.sliders[sliderId] = swiperElement.swiper;
                if(debug) console.log(`Added existing Swiper to registry: ${sliderId}`);
            } else if (swiperElement) {
                try {
                    // Try to initialize the slider with Swiper
                    const swiperOptions = {
                        observer: true,
                        observeParents: true,
                        pagination: {
                            el: sliderSection.querySelector('.swiper-pagination'),
                            clickable: true
                        },
                        navigation: {
                            nextEl: sliderSection.querySelector('.swiper-button-next'),
                            prevEl: sliderSection.querySelector('.swiper-button-prev')
                        }
                    };
                    
                    const newSwiper = new Swiper(swiperElement, swiperOptions);
                    window.maddenTheme.sliders[sliderId] = newSwiper;
                    if(debug) console.log(`Initialized new Swiper for: ${sliderId}`);
                } catch (err) {
                    console.error('Error initializing Swiper:', err);
                }
            }
        }
    });
}

function initSliderControls() {
    try {
        if(debug) console.log('initSliderControls called');
        
        // Check if we need to add sliders to registry
        const sliderElements = document.querySelectorAll(`.wp-block-${THEME_PREFIX}-slider`);
        if(debug) console.log(`Found ${sliderElements.length} slider elements`);
        
        if (sliderElements.length) {
            // Either add missing sliders to registry or wait for them to be loaded
            const slidersInRegistry = Object.keys(window.maddenTheme.sliders).length;
            if (slidersInRegistry === 0) {
                if(debug) console.log('No sliders in registry, trying to add them');
                addSlidersToRegistry(sliderElements);
            }
            
            // If still no sliders in registry, retry later
            if (Object.keys(window.maddenTheme.sliders).length === 0) {
                if(debug) console.log('Sliders not ready yet, retrying...');
                setTimeout(initSliderControls, 300);
                return;
            }
        }
        
        const controlBlocks = document.querySelectorAll(`.wp-block-${THEME_PREFIX}-slider-pagination`);
        if(debug) console.log(`Found ${controlBlocks.length} control blocks`);
        if (!controlBlocks.length) {
            return;
        }
        
        // Get all slider elements on the page for pagination control
        const pageSliders = document.querySelectorAll(`.wp-block-${THEME_PREFIX}-slider`);
        if(debug) console.log(`Found ${pageSliders.length} slider elements for pagination`);
        if (!pageSliders.length) {
            return;
        }
        
        // Connect control elements to their target sliders
        controlBlocks.forEach(controlBlock => {
            try {
                // Get the selected slider number (1-based index)
                const sliderNumber = parseInt(controlBlock.getAttribute('data-slider-number'), 10);
                if(debug) console.log(`Slider number: ${sliderNumber}`);
                if (isNaN(sliderNumber) || sliderNumber <= 0 || sliderNumber > pageSliders.length) {
                    // Invalid slider number
                    if(debug) console.log('Invalid slider number');
                    return;
                }
                
                // Get the target slider (1-based index)
                const targetSlider = pageSliders[sliderNumber - 1];
                if (!targetSlider) {
                    if(debug) console.log('Target slider not found');
                    return;
                }
                
                // Get the target slider's ID
                const targetSliderId = targetSlider.getAttribute('data-uid');
                if(debug) console.log(`Target slider ID: ${targetSliderId}`);
                if (!targetSliderId || !window.maddenTheme.sliders[targetSliderId]) {
                    if(debug) console.log('Target slider ID not found in maddenTheme.sliders');
                    return;
                }
                
                // Get the Swiper instance
                const swiper = window.maddenTheme.sliders[targetSliderId];
                if(debug) console.log('Swiper instance:', swiper);
                
                // // Hide original navigation in the slider
                // const navWrapper = targetSlider.querySelector('.swiper-navigation-wrapper');
                // if (navWrapper) {
                //     navWrapper.style.display = 'none';
                // }
                
                // Handle pagination dots if enabled
                const paginationEl = controlBlock.querySelector('.external-swiper-pagination');
                if (paginationEl) {
                    if(debug) console.log('Pagination element found');
                    // Add event listeners to pagination bullets manually
                    swiper.on('slideChange', function() {
                        updateExternalPagination(paginationEl, swiper);
                    });
                    
                    // Also update after transition completes for better loop mode support
                    swiper.on('transitionEnd', function() {
                        updateExternalPagination(paginationEl, swiper);
                    });
                    
                    // Initial pagination setup
                    updateExternalPagination(paginationEl, swiper);
                }
                
                // Handle arrows if enabled
                const prevArrowEl = controlBlock.querySelector('.external-swiper-button-prev');
                const nextArrowEl = controlBlock.querySelector('.external-swiper-button-next');
                
                if (prevArrowEl && nextArrowEl) {
                    if(debug) console.log('Arrow elements found');
                    // Add manual click handlers for navigation arrows
                    prevArrowEl.addEventListener('click', function(e) {
                        e.preventDefault();
                        swiper.slidePrev();
                    });
                    
                    nextArrowEl.addEventListener('click', function(e) {
                        e.preventDefault();
                        swiper.slideNext();
                    });
                    
                    // Apply custom styling
                    const arrowColor = prevArrowEl.dataset.color;
                    const arrowBgColor = prevArrowEl.dataset.colorBackground;
                    const arrowBorderColor = prevArrowEl.dataset.borderColor;
                    const arrowStyle = controlBlock.dataset.arrowStyle;
                    
                    // Apply arrow color
                    if (arrowColor) {
                        prevArrowEl.style.color = arrowColor;
                        nextArrowEl.style.color = arrowColor;
                    }
                    
                    // Apply background color
                    if (arrowBgColor) {
                        prevArrowEl.style.backgroundColor = arrowBgColor;
                        nextArrowEl.style.backgroundColor = arrowBgColor;
                    }
                    
                    // Apply border color
                    if (arrowBorderColor) {
                        prevArrowEl.style.border = `1px solid ${arrowBorderColor}`;
                        nextArrowEl.style.border = `1px solid ${arrowBorderColor}`;
                    }
                    
                    // Handle modern arrow style
                    if (arrowStyle === 'modern') {
                        prevArrowEl.classList.add('modern-arrow');
                        nextArrowEl.classList.add('modern-arrow');
                    }
                    
                    // Update arrow states
                    swiper.on('slideChange', function() {
                        updateArrowStates(prevArrowEl, nextArrowEl, swiper);
                    });
                    
                    // Initial arrow states
                    updateArrowStates(prevArrowEl, nextArrowEl, swiper);
                }
            } catch (err) {
                console.error('Error connecting slider controls:', err);
            }
        });
    } catch (err) {
        console.error('Error initializing slider controls:', err);
    }
}

/**
 * Update the external pagination bullets to match the current slide
 */
function updateExternalPagination(paginationEl, swiper) {
    if (!paginationEl || !swiper) return;
    
    if(debug) console.log('Updating external pagination');
    // First ensure the container is cleared
    paginationEl.innerHTML = '';
    
    // Create bullets based on number of slides
    // In loop mode, count only non-duplicate slides 
    let totalSlides = swiper.slides.length;
    if (swiper.params.loop) {
        // Count only non-duplicate slides
        let nonDuplicateCount = 0;
        swiper.slides.forEach(slide => {
            if (!slide.classList.contains('swiper-slide-duplicate')) {
                nonDuplicateCount++;
            }
        });
        
        if (nonDuplicateCount > 0) {
            totalSlides = nonDuplicateCount;
        } else {
            // Fallback if can't detect duplicates
            totalSlides = Math.floor(swiper.slides.length / 3);
        }
    }
    
    const activeIndex = swiper.realIndex || 0;
    if(debug) console.log(`Total slides: ${totalSlides}, Active index: ${activeIndex}`);
    
    // Get custom colors from data attributes
    const dotColor = paginationEl.dataset.color;
    const dotColorActive = paginationEl.dataset.colorActive;
    const dotBorderColor = paginationEl.dataset.borderColor;
    
    for (let i = 0; i < totalSlides; i++) {
        const bullet = document.createElement('span');
        bullet.className = 'swiper-pagination-bullet';
        
        // Apply styles based on active state
        if (i === activeIndex) {
            bullet.classList.add('swiper-pagination-bullet-active');
            if (dotColorActive) {
                bullet.style.backgroundColor = dotColorActive;
            }
        } else if (dotColor) {
            bullet.style.backgroundColor = dotColor;
        }
        
        // Apply border color if set
        if (dotBorderColor) {
            bullet.style.border = `1px solid ${dotBorderColor}`;
        }
        
        // Add click handler
        bullet.addEventListener('click', function(e) {
            e.preventDefault();
            if (swiper.params.loop) {
                swiper.slideToLoop(i);
            } else {
                swiper.slideTo(i);
            }
        });
        
        paginationEl.appendChild(bullet);
    }
}

/**
 * Update the arrow states (enabled/disabled) based on slider position
 */
function updateArrowStates(prevArrowEl, nextArrowEl, swiper) {
    if (!prevArrowEl || !nextArrowEl || !swiper) return;
    
    if(debug) console.log('Updating arrow states');
    // For loop mode, both arrows are always enabled
    if (swiper.params.loop) {
        prevArrowEl.classList.remove('swiper-button-disabled');
        nextArrowEl.classList.remove('swiper-button-disabled');
        return;
    }
    
    // First slide - disable prev button
    if (swiper.isBeginning) {
        prevArrowEl.classList.add('swiper-button-disabled');
    } else {
        prevArrowEl.classList.remove('swiper-button-disabled');
    }
    
    // Last slide - disable next button
    if (swiper.isEnd) {
        nextArrowEl.classList.add('swiper-button-disabled');
    } else {
        nextArrowEl.classList.remove('swiper-button-disabled');
    }
}