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
