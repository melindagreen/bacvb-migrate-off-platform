export const getIsSmall = function () {
	return (
		jQuery( '#isSmall' ).length &&
		jQuery( '#isSmall' ).css( 'float' ) !== 'none'
	);
};
export const getIsMedium = function () {
	return (
		jQuery( '#isMedium' ).length &&
		jQuery( '#isMedium' ).css( 'float' ) !== 'none'
	);
};
export const getIsLarge = function () {
	return (
		jQuery( '#isLarge' ).length &&
		jQuery( '#isLarge' ).css( 'float' ) !== 'none'
	);
};
