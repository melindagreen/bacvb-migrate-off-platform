import { PLUGIN_PREFIX } from './constants';

const MAILCHIMP_FORM = `<div id="mc_embed_signup">
	<form action="//atlanta.us13.list-manage.com/subscribe/post?u=5e36fc1570940759e07bedf0f&amp;id=039e1217a3" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate="" _lpchecked="1">
		<div id="mc_embed_signup_scroll">
			<div class="mc-field-group">
				<input type="email" placeholder="Email" value="" name="EMAIL" class="required email" id="mce-EMAIL" autocomplete="off">
			</div>
			<div class="mc-field-group">
				<input type="text" placeholder="Zip Code" value="" name="ZIPCODE" class="required" id="mce-ZIPCODE">
			</div>
			<div class="mc-field-group submit">
				<button type="submit" name="subscribe" id="mc-embedded-subscribe" class="learnMore gsc-track gsc-tracking-code-MTk5MS4xODYzNC4xMTEzMjAuMTgzNzI4LjUzNzU4 " data-gsc="MTk5MS4xODYzNC4xMTEzMjAuMTgzNzI4LjUzNzU4">
					SIGN UP
					<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="24.799999237060547 14.399999618530273 18.200000762939453 35.30000305175781" style="enable-background:new 0 0 60 60;" xml:space="preserve" fill="white">
						<g id="Layer_2"></g>
						<g id="Layer_1">
							<g id="Path_3">
								<polygon class="st0" points="26.3,49.7 24.8,48.4 40.2,32.1 24.8,15.8 26.3,14.4 43,32.1"></polygon>
							</g>
						</g>
					</svg>
				</button>
			</div>
		</div>
	</form>
</div>
<div class="thank-you-for-subscribing" style="display: none;">
	Thank you for subscribing.
</div>`;

const SURVEY_OPTIONS = [
    {
        name: 'arts-culture',
        label: 'Arts & Culture',
    }, {
        name: 'dining',
        label: 'Dining',
    }, {
        name: 'family-fun',
        label: 'Family Fun',
    }, {
        name: 'local-events',
        label: 'Local Events',
    }, {
        name: 'music',
        label: 'Music',
    }, {
        name: 'neighborhoods',
        label: 'Neighborhoods',
    }, {
        name: 'nightlife',
        label: 'Nightlife',
    }, {
        name: 'outdoor-activities',
        label: 'Outdoor Activities',
    }, {
        name: 'sports',
        label: 'Sports',
    }
];

/**
 * Render top tickers
 * @param {Object[]} tickers
 * @returns {null}
 */
export const renderTickers = (ticker) => {
    // Reusable exit button HTML
    const exitButton = `<button type='button' class='close-ticker ${PLUGIN_PREFIX}-dismiss' data-bannerid='${ticker.id_slug}' data-bannertype='ticker'>
        <span class='sr-only'>Close Ticker</span>
        <img src='/wp-content/plugins/wp-plugin-madden-banners/assets/images/close.png' alt='' />
    </button>`;

    // Render ticker HTML based on config
    const tickerHtml = `<div 
        class='${PLUGIN_PREFIX}-ticker'
        style='background-color:${ticker.bg_color};color:${ticker.text_color}'
    >
        ${ticker.exit_pos === 'left' ? exitButton : ''}

        ${ticker.image ? `<img width='50' height='50' class='ticker-image' src='${ticker.image}' />` : ''}

        <div class='ticker-content'>
            ${ticker.link && !ticker.cta ? `<a href='${ticker.link}' class='${PLUGIN_PREFIX}-link' data-bannertype='ticker' data-bannerid='${ticker.id_slug}'>` : ''}
                ${ticker.content}
            ${ticker.link && !ticker.cta ? '</a>' : ''}
        </div>

        ${ticker.cta && ticker.link ? `<a 
            class='ticker-cta ${PLUGIN_PREFIX}-link' 
            href='${ticker.link}' 
            data-bannertype='ticker'
            data-bannerid='${ticker.id_slug}'
            style='color:${ticker.bg_color};background-color:${ticker.text_color}'
        >
            ${ticker.cta}
        </a>` : ''}

        ${ticker.exit_pos === 'right' ? exitButton : ''}
    </div>`;

    jQuery('body').prepend(tickerHtml).addClass('has-top-ticker');

    jQuery(`.${PLUGIN_PREFIX}-ticker .close-ticker`).click(closeTicker);
};

/**
 * Template HTML for fly-in banners and render to the screen with appropriate delays
 * @param {Object[]} flyins					All fly-ins 
 * @returns {null}
 */
export const renderFlyins = (flyin, isAdmin = false) => {
    const template = (() => {
        switch (flyin.template) {
            case 'mail_signup':
                return `<div class='mail-signup'>${MAILCHIMP_FORM}</div>`;
            case 'survey':
                const FORM_ID = flyin.form;
                let form;
                (function ($) {
                    const path = window.location.pathname;
                    $.post(ajaxdata.url, {
                        action: 'gf_get_form',
                        form_id: FORM_ID,
                    })
                        .then(function (response) {
                            console.log(response)
                            const formHtml = response.replace(`action='/wp-admin/admin-ajax.php#gf_${FORM_ID}'`, `action='${path}#gf_${FORM_ID}'`)
                            $('.survey.gravityForm').html(formHtml).fadeIn();
                        })
                })(jQuery);
                return `<div class='survey gravityForm'></div>`;

            case 'cta':
            default:
                return `<a class='cta-button ${PLUGIN_PREFIX}-link' href='${flyin.cta_link}' data-bannerid='${flyin.id_slug}' data-bannertype='flyin'>${flyin.cta_text}<svg x="0px" y="0px" viewBox="24.799999237060547 14.399999618530273 18.200000762939453 35.30000305175781" style="enable-background:new 0 0 60 60;" xml:space="preserve" fill="white">
                    <g id="Layer_2"></g>
                    <g id="Layer_1">
                        <g id="Path_3">
                            <polygon class="st0" points="26.3,49.7 24.8,48.4 40.2,32.1 24.8,15.8 26.3,14.4 43,32.1"></polygon>
                        </g>
                    </g>
                </svg></a>`
        }
    })();

    const scrollDepth = parseInt(flyin.scroll_depth);
    const delay = parseInt(flyin.time_passed);

    const flyinContent = `<div class='flyin-content'>
        <h3>${flyin.title}</h3>
        <p>${flyin.content}</p>
        ${template}
    </div>`;
    const closeButton = `<button type='button' class='close-flyin ${PLUGIN_PREFIX}-dismiss' data-bannerid='${flyin.id_slug}' data-bannertype='flyin'>
        <span class='sr-only'>Close Fly-in</span>
        <img src='/wp-content/plugins/wp-plugin-madden-banners/assets/images/close.png' alt='' />
    </button>`;

    const flyinHtml = flyin.template === 'cta'
        ? `<div class='${PLUGIN_PREFIX}-flyin cta ${flyin.corner} ${((scrollDepth || delay) && !isAdmin) ? '' : 'slide-in'} ${(flyin.bg_image !== '') ? '' : 'no_image'}'>
            ${closeButton}
            ${flyinContent}
            ${(flyin.bg_image === '') ? '' : `<div class='flyin-image' style='${flyin.bg_image ? `background-image:url("${flyin.bg_image}")` : ''}'></div>`}
        </div>`
        : `<div
            class='${PLUGIN_PREFIX}-flyin ${flyin.template} ${flyin.corner} ${(scrollDepth || delay) ? '' : 'slide-in'}'
            style='${flyin.bg_image ? `background-image:url("${flyin.bg_image}")` : ''}'
        >
            ${closeButton}
            ${flyinContent}
        </div>`;

    jQuery('body').prepend(flyinHtml);

    jQuery(`.${PLUGIN_PREFIX}-flyin .close-flyin`).click(closeFlyin);

    // Show on scroll depth if set
    if (scrollDepth && !isAdmin) jQuery(window).scroll(() => {
        if (jQuery(window).scrollTop() > scrollDepth) jQuery(`.${PLUGIN_PREFIX}-flyin`).addClass('slide-in');
    });
    // Show on delay if set
    if (delay && !isAdmin) setTimeout(() => {
        jQuery(`.${PLUGIN_PREFIX}-flyin`).addClass('slide-in');
    }, delay * 1000);

    // Store survey results in localStorage
    if (flyin.template === 'survey') {
        const surveyKey = `${PLUGIN_PREFIX}-survey-value`;
        jQuery('.madden-banners-survey-option').click(function () {
            const option = jQuery(this).data('option');
            if (window.localStorage) {
                window.localStorage.setItem(surveyKey, option);
            } else console.error('localStorage is not available');

            jQuery(this).closest('.survey').html('<p>Thank you. Your selection will help us tailor our content to your best interests!</p>');
        });
    }
}

/**
 * Remove the current ticker
 * @returns {null}
 */
function closeTicker() {
    jQuery(this).closest(`.${PLUGIN_PREFIX}-ticker`).remove();
    jQuery('body').removeClass('has-top-ticker');
}

/**
 * Remove the current flyin
 * @returns {null}
 */
function closeFlyin() {
    jQuery(this).closest(`.${PLUGIN_PREFIX}-flyin`).remove();
}