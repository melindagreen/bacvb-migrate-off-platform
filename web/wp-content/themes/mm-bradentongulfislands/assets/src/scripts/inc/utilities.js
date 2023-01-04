export const parseDataCookie = (key) => {
	let rawCookies = document.cookie
		.split('; ')
		.filter((cookieStr) => cookieStr.indexOf(key) > -1);

	if (rawCookies[0]) {
		const rawCookieVal = rawCookies[0].replace(key + '=', '');

		if (atob) return JSON.parse(atob(decodeURIComponent(rawCookieVal)));
		else return false;
	} else return false;
};

export const setDataCookie = (key, rawCookieVal, expr, sameSite = 'Strict') => {
	const encodedCookieVal = encodeURIComponent(
		btoa(JSON.stringify(rawCookieVal))
	);

	document.cookie = `${key}=${encodedCookieVal};path=/;expires=${expr};samesite=${sameSite}`;
};

export const getIsSmall = () =>
	jQuery('#isSmall').length && jQuery('#isSmall').css('float') !== 'none';
export const getIsMedium = () =>
	jQuery('#isMedium').length && jQuery('#isMedium').css('float') !== 'none';
export const getIsLarge = () =>
	jQuery('#isLarge').length && jQuery('#isLarge').css('float') !== 'none';

export const showListingMap = (mapId, latitude, longitude) => {
	if (L && L.esri.basemapLayer && jQuery('#' + mapId).length) {
		const listingMap = L.map(mapId).setView([latitude, longitude], 18);

		L.esri.basemapLayer('Gray').addTo(listingMap);

		const iconScale = 0.5;
		const iconWidth = 64 * iconScale;
		const iconHeight = 86 * iconScale;
		const shadowWidth = 100 * iconScale;
		const shadowHeight = 42 * iconScale;

		const icon = L.icon({
			iconUrl:
				'/wp-content/themes/madden/assets/images/neighborhood_map_icon_red.png',
			shadowUrl:
				'/wp-content/themes/madden/assets/images/neighborhood_map_icon_shadow.png',
			iconSize: [iconWidth, iconHeight],
			shadowSize: [shadowWidth, shadowHeight],
			iconAnchor: [iconWidth / 2, iconHeight],
			shadowAnchor: [5, shadowHeight / 2],
			popupAnchor: [0, -1 * iconHeight],
		});

		L.marker([latitude, longitude], {
			icon,
		}).addTo(listingMap);
	}
}
