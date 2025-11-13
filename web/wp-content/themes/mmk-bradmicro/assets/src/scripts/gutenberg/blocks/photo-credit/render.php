<?php
namespace MaddenTheme\Blocks\PhotoCredit;

$caption = $attributes['caption'] ?? '';
$text_align = $attributes['textAlign'] ?? 'left';

$custom_styles = '';
switch ($text_align) {
	case 'left':
		$custom_styles = 'left: 0; right: auto;';
		break;
	case 'right':
		$custom_styles = 'left: auto;right: 0;';
		break;
	case 'center':
		$custom_styles = 'left: 50%; right: auto; transform: translateX(-50%);';
		break;
}

$block_attributes = get_block_wrapper_attributes();
echo '<div ' . $block_attributes . '>';
	echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 302.5 214.5" style="enable-background:new 0 0 302.5 214.5" xml:space="preserve"><path d="M279.8 44.5h-50.4l-7.9-23.7c-3.2-9.8-12.4-16.3-22.6-16.3h-95.6c-10.3 0-19.4 6.6-22.6 16.3l-7.9 23.7H22.2C9.7 44.5-.5 54.7-.5 67.2v124.6c0 12.6 10.2 22.7 22.7 22.7h257.6c12.5 0 22.7-10.2 22.7-22.7V67.2c0-12.5-10.2-22.7-22.7-22.7zM151 191.7c-37.7 0-68.2-30.6-68.2-68.2s30.6-68.2 68.2-68.2 68.2 30.6 68.2 68.3-30.5 68.1-68.2 68.1z" style="fill:#fff"/></svg>';
	echo '<span style="' . esc_attr( $custom_styles ) . '">' . esc_html( $caption ) . '</span>';
echo '</div>';
