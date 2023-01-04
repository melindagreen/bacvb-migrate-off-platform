<?php

namespace MaddenNino\Blocks\ExampleDynamic;
use MaddenNino\Library\Constants as Constants;
  
/**
 * Render function for the dynamic example block
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {
  $html = "<section class='" . Constants::BLOCK_CLASS . "-example-dynamic'>";

  $html .= "<pre>" . print_r( $attrs, true ) . "</pre>";

  $html .= "</section>";

  return $html;
}