<?php
$svgID = $attributes['svgID'] ?? null;
if (empty($svgID)) {
	echo '';
	return;
}

$svg_path = get_attached_file($svgID);

if (
	! $svg_path ||
	! file_exists($svg_path) ||
	strtolower(pathinfo($svg_path, PATHINFO_EXTENSION)) !== 'svg'
) {
	echo '';
	return;
}

$svg_content = file_get_contents($svg_path);
if (empty($svg_content) || strpos($svg_content, '<svg') === false) {
	echo '';
	return;
}

$width = esc_attr($attributes['width'] ?? '100px');
$height = esc_attr($attributes['height'] ?? '100px');
$lock = !empty($attributes['lockAspectRatio']);

$container_width = esc_attr($attributes['containerWidth'] ?? false);
$container_height = esc_attr($attributes['containerHeight'] ?? false);

$svg_link = $attributes['svgLink'] ?? [];
$url = $svg_link['url'] ?? false;
$target = ( isset( $svg_link['opensInNewTab'] ) && $svg_link['opensInNewTab'] ) ? '_blank' : '_self';

// Use WP_HTML_Tag_Processor to modify the <svg> element
$processor = new WP_HTML_Tag_Processor($svg_content);

if ($processor->next_tag('svg')) {
	$processor->set_attribute('width', $width);
  if ( ! $lock && $height ) {
    $processor->set_attribute('height', $height);
  }
	if (! $lock) {
		$processor->set_attribute('preserveAspectRatio', 'none');
	} else {
		$processor->remove_attribute('preserveAspectRatio');
	}

	$svg_content = $processor->get_updated_html();
}

$style = '';
if ( $container_width && '' != $container_width ) {
  $style .= "width:{$container_width}";
}
if ( $container_height && '' != $container_height ) {
  $style .= "height:{$container_height}";
}
$wrapper_attributes = get_block_wrapper_attributes( [
  //'style' => $style,
] );

echo '<div ' . $wrapper_attributes . '>';
  if ( $url ) {
    echo '<a href="' . esc_url( $url ) . '" target="' . esc_attr( $target ) . '">';
  }
  echo $svg_content;
  if ( $url ) {
    echo '</a>';
  }
echo '</div>  ';