const save = ({ attributes }) => {
	const { heightDesktop, heightMobile, unit, spacerId } = attributes;

	const desktopHeight = `${heightDesktop}${unit}`;
	const mobileHeight = `${heightMobile}${unit}`;

	return (
		<div
			className={`wp-block-mm-bradentongulfislands-responsive-spacer  custom-spacer-block ${spacerId}`}
			style={{
				height: desktopHeight,
			}}
		>
			<style>
				{`@media (max-width: 768px) {
          .${spacerId} {
            height: ${mobileHeight} !important;
          }
        }`}
			</style>
		</div>
	);
};

export default save;
