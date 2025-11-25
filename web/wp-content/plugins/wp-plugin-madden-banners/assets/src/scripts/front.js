import '../styles/front.scss';
import { PLUGIN_PREFIX } from './inc/constants';
import { getIsSmall, getIsMedium } from './inc/utils';
import { renderTickers, renderFlyins } from './inc/render-banners';

(function ($) {
	/**
	 * Detect the device size/type
	 * @return {String}                             The device type
	 */
	const getDevice = () => {
		let device = {
			type: 'desktop',
			os: false
		};
		if (getIsMedium()) device.type = 'tablet';
		else if (getIsSmall()) {
			device.type = 'mobile';
		}

		// From https://stackoverflow.com/questions/21741841/detecting-ios-android-operating-system
		const userAgent = navigator.userAgent || navigator.vendor || window.opera;

		if (/android/i.test(userAgent)) {
			device.os = 'android';
		} else if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
			// iOS detection from: http://stackoverflow.com/a/9039885/177710
			device.os = 'ios';
		}

		return device;
	};

	/**
	 * Are all ticker conditions true?
	 * @param {Object[]} conditions             All conditions that must be true
	 * @return {Boolean}                        Do all conditions match?
	 */
	const checkConditions = (id, conditions) => {
		if (!conditions) return true;

		// Get our relevant variables
		const device = getDevice();
		const path = window.location.pathname;
		const params = window.location.search;

		// First check each AND group; all must be true
		let isAndMatch = true;
		conditions.forEach(conditionAndGroup => {
			if (isAndMatch) {
				let isOrMatch = false;

				// Now check each OR group; only one must be true
				Object.values(conditionAndGroup).forEach(condition => {
					if (!isOrMatch) {
						let isConditionMatch = false;

						// Parse out condition pieces
						const { condition_field: field, matchtype } = condition;

						// Check path values against conditions
						if (field === 'page_url' || field === 'url_parameters') {
							const lookupKey = field === 'url_parameters' ? 'parameter' : 'condition';
							const matchAgainst = field === 'url_parameters' ? params : path;

							const {
								[`${lookupKey}_match`]: match,
								[`${lookupKey}_value`]: value,
							} = condition;

							switch (match) {
								case 'match_exactly':
									isConditionMatch = matchAgainst === value;
									break;
								case 'contains':
									isConditionMatch = matchAgainst.includes(value);
									break;
								case 'regex':
									const regExp = new RegExp(value);
									isConditionMatch = regExp.test(matchAgainst);
									break;
							}
						} else if (field === 'device_type') {
							const { device_type: deviceMatch } = condition;
							if (deviceMatch.includes('any')) {
								isConditionMatch = deviceMatch.replace('_any', '') === device.type;
							} else {
								const [deviceMatchType, deviceMatchOS] = deviceMatch.split('_');
								isConditionMatch = deviceMatchType === device.type && deviceMatchOS === device.os;
							}

						// Check for repeat users
						} else if (field === 'is_repeat_user') {
							if (window.localStorage && window.sessionStorage) {
								const repeatKey = `is_repeat_${id}`;
								const sessionKey = `in_session_${id}`;
								const isRepeatUser = window.localStorage.getItem(repeatKey);
								if (!isRepeatUser) {
									window.localStorage.setItem(repeatKey, true);
									window.sessionStorage.setItem(sessionKey, true);
								} else {
									const inSession = window.sessionStorage.getItem(sessionKey);
									isConditionMatch = isRepeatUser && !inSession;
									if (isConditionMatch) window.sessionStorage.setItem(sessionKey, true);
								}

							} else {
								console.error('localStorage is not available');
								isConditionMatch = true;
							}
						} else if(field === 'survey_value') {
							if (window.localStorage) {
								const surveyVal = window.localStorage.getItem(`${PLUGIN_PREFIX}-survey-value`);
								isConditionMatch = surveyVal === condition.survey_value;
							} else {
								console.error('localStorage is not available');
								isConditionMatch = true;
							}
						}

						// If we have a hit, we can return true right away
						if (
							(matchtype === 'is_true' && isConditionMatch) ||
							(matchtype === 'is_false' && !isConditionMatch)
						)
							isOrMatch = true;
					}
				});

				if (!isOrMatch) isAndMatch = false;
			}
		});

		// Should we show the ticker?
		return isAndMatch;
	};

	/**
	 * Check to see if it's time to show the ticker
	 * @param {String} id               The ticker's unique ID
	 * @param {String} frequency        The frequency type
	 * @returns {Boolean}               Is it time?
	 */
	const checkFrequency = (id, frequency) => {
		if (frequency === 'no_limit') return true;

		const key = `banner_time_${id}`;

		if (window.localStorage) {
			// Get expry and current time in unix seconds
			const expr = window.localStorage.getItem(key);
			const now = parseInt(new Date().getTime() / 1000);

			// Determine lifespan in seconds
			const interval = ((frequency) => {
				switch (frequency) {
					case 'daily':
						return 60 * 60 * 24;
					case 'weekly':
						return 60 * 60 * 24 * 7;
					case 'only_once':
					default:
						return 60 * 60 * 24 * 365;
				}
			})(frequency);

			// If expr is in the future, no ticker
			if (expr && expr >= now) return false;
			else {
				// Otherwise, store new expr and show ticker
				window.localStorage.setItem(key, now + interval);
				return true;
			}
		} else {
			console.error("LocalStorage not available.");
			return true;
		}
	};

	/**
	 * Check all banners against priorities and conditions to select a banner to add.
	 * @param {Object[]} allBanners				All banner config objects 
	 * @returns {Object}						The selected banner
	 */
	const findBannerToAdd = (allBanners) => {
		let bannerToAdd = false;

		allBanners.forEach((banner) => {
			if (
				(
					!bannerToAdd ||
					parseInt(banner.priority) < parseInt(bannerToAdd.priority)
				) &&
				(
					(!banner.conditions || !banner.frequency) ||
					(checkConditions(banner.id, Object.values(banner.conditions)))


				)
			) {
				if (checkFrequency(banner.id, banner.frequency)) bannerToAdd = banner;
			}
		});

		return bannerToAdd;
	}

	$(document).ready(() => {
		if (typeof madden_banners_options !== 'undefined') {
			const allTickers = madden_banners_options?.tickers?.all_tickers || [];
			if (allTickers && Array.isArray(allTickers)) {
				const ticker = findBannerToAdd(allTickers);
				if(ticker) renderTickers(ticker);
			}

			const allFlyins = madden_banners_options?.flyins?.all_flyins || [];
			if (allFlyins && Array.isArray(allFlyins)) {
				const flyin = findBannerToAdd(allFlyins);
				if(flyin) renderFlyins(flyin);
			}
		}
	});
})(jQuery);
