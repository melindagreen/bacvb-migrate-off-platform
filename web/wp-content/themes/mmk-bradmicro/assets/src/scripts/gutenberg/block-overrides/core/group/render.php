<?php

$wraparound_link = isset( $attributes['wraparoundLink'] ) ? $attributes['wraparoundLink'] : [];
$wraparound_url = isset( $wraparound_link['url'] ) ? $wraparound_link['url'] : false;
$wraparound_target = isset( $wraparound_link['opensInNewTab'] ) ? $wraparound_link['opensInNewTab'] : '_self';

$block_content = $render_content ?? $content;

// Maybe add wraparound
if ( $wraparound_url ) {
  $class = 'wp-block-group__wraparound-link wraparound-link';
  if ( isset( $attributes['align'] ) ) {
    $class .= " align{$attributes['align']}";
  }


  // Convert an inner a tags to spans
  $block_content = preg_replace_callback(
    '/<a\s+(.*?)>(.*?)<\/a>/i',
    function ($matches) {
        $attributes = $matches[1];
        $inner_html = $matches[2];

        // Remove href and target attributes
        $attributes = preg_replace('/\s*(href|target|data-type|data-id)=".*?"/i', '', $attributes);

        // Return the updated span tag
        return '<span ' . $attributes . '>' . $inner_html . '</span>';
    },
    $block_content
  );


  // Modify the styles
  $link_style_parts = ['display:block;'];
  $cover_style_parts = [];

  $processor = new WP_HTML_Tag_Processor( $block_content );
  $processor->next_tag();

  $styles = $processor->get_attribute( 'style' );
  $style = ! empty( $styles ) ? $styles . ';' : '';
  $style_parts = explode( ';', $style );

  if ( is_array( $style_parts ) && count( $style_parts ) > 0 ) {
    foreach ( $style_parts as $part ) {
      if ( '' == $part ) {
        continue;
      }
      if ( strpos( $part, 'margin-' ) !== false ) {
        $link_style_parts[] = $part;
      } else {
        $cover_style_parts[] = $part;
      }
    }
  }

  $processor->set_attribute( 'style', implode( ';', $cover_style_parts ) );
  $block_content = $processor->get_updated_html();

  echo '<a style="' . esc_attr( implode( ';', $link_style_parts ) ) . '" class="' . $class . '" href="' . esc_url( $wraparound_url ) . '" target="' . esc_attr( $wraparound_target ) . '">';
    echo $block_content;
  echo '</a>';
} else {
  echo $block_content;
}
