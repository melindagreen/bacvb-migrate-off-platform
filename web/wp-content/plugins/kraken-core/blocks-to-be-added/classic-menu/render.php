<?php
namespace MaddenTheme\Blocks\ClassicMenu;
include_once __DIR__ . '/functions.php';

$menu_id = isset( $attributes['menu'] ) ? intval( $attributes['menu'] ) : 0;
$text_align = isset( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
$link_padding = isset( $attributes['linkPadding'] ) ? $attributes['linkPadding'] : [];
$link_equal_height = isset( $attributes['linkEqualHeight'] ) ? $attributes['linkEqualHeight'] : false;

$padding_top = isset( $link_padding['top'] ) ? get_spacing_value( $link_padding['top'] ) : false;
$padding_right = isset( $link_padding['right'] ) ? get_spacing_value( $link_padding['right'] ) : false;
$padding_bottom = isset( $link_padding['bottom'] ) ? get_spacing_value( $link_padding['bottom'] ) : false;
$padding_left = isset( $link_padding['left'] ) ? get_spacing_value( $link_padding['left'] ) : false;

$style = isset( $attributes['style'] ) ? $attributes['style'] : [];
$spacing = isset( $style['spacing'] ) ? $style['spacing'] : [];
$blockGap = isset( $spacing['blockGap'] ) ? $spacing['blockGap'] : '';

$layout = isset( $attributes['layout'] ) ? $attributes['layout'] : [];
$justify_content = isset( $layout['justifyContent'] ) ? $layout['justifyContent'] : 'left';
$orientation = isset( $layout['orientation'] ) ? $layout['orientation'] : 'horizontal';
$vertical_alignment = isset( $layout['verticalAlignment'] ) ? $layout['verticalAlignment'] : 'center';

// Sub nav settings
$subnav_display = isset( $attributes['subNavDisplay'] ) ? $attributes['subNavDisplay'] : 'visible';
$subnav_text_align = isset( $attributes['subNavTextAlign'] ) ? $attributes['subNavTextAlign'] : 'left';
$subnav_text_color = isset( $attributes['subNavTextColor'] ) ? $attributes['subNavTextColor'] : false;
$subnav_background_color = isset( $attributes['subNavBackgroundColor'] ) ? $attributes['subNavBackgroundColor'] : false;
$subnav_padding = isset( $attributes['subNavPadding'] ) ? $attributes['subNavPadding'] : [];
$subnav_link_padding = isset( $attributes['subNavLinkPadding'] ) ? $attributes['subNavLinkPadding'] : [];

$subnav_padding_top = isset( $subnav_padding['top'] ) ? get_spacing_value( $subnav_padding['top'] ) : false;
$subnav_padding_right = isset( $subnav_padding['right'] ) ? get_spacing_value( $subnav_padding['right'] ) : false;
$subnav_padding_bottom = isset( $subnav_padding['bottom'] ) ? get_spacing_value( $subnav_padding['bottom'] ) : false;
$subnav_padding_left = isset( $subnav_padding['left'] ) ? get_spacing_value( $subnav_padding['left'] ) : false;

$subnav_link_padding_top = isset( $subnav_link_padding['top'] ) ? get_spacing_value( $subnav_link_padding['top'] ) : false;
$subnav_link_padding_right = isset( $subnav_link_padding['right'] ) ? get_spacing_value( $subnav_link_padding['right'] ) : false;
$subnav_link_padding_bottom = isset( $subnav_link_padding['bottom'] ) ? get_spacing_value( $subnav_link_padding['bottom'] ) : false;
$subnav_link_padding_left = isset( $subnav_link_padding['left'] ) ? get_spacing_value( $subnav_link_padding['left'] ) : false;


if ( is_array( $blockGap ) ) {
  $spacing = get_spacing_value( $blockGap['left'] );
} else {
  $spacing = get_spacing_value( $blockGap );
}

if ( $menu_id ) {

  //$menu_items = wp_get_nav_menu_items( $menu_id );
  $menu_obj = wp_get_nav_menu_object( $menu_id );

  if ( $menu_obj ) {

    $slug = sanitize_html_class( $menu_obj->slug ); // Safe to use in class names

    $custom_classes = "menu-container menu-{$slug}-container";
    $custom_classes .= " vertical-align-{$vertical_alignment}";
    $custom_classes .= " subnav-display-{$subnav_display}";
    if ( $link_equal_height ) {
      $custom_classes .= " link-equal-height";
    }

    $custom_classes .= ' mobile-breakpoint-'.$attributes['mobileBreakpoint'];

    // Main
    $custom_styles = "text-align:{$text_align};";
    $custom_styles .= "--classic-menu--gap:{$spacing};";
    $custom_styles .= "--classic-menu--justify:{$justify_content};";
    $custom_styles .= "--classic-menu--link_padding_top:{$padding_top};";
    $custom_styles .= "--classic-menu--link_padding_right:{$padding_right};";
    $custom_styles .= "--classic-menu--link_padding_bottom:{$padding_bottom};";
    $custom_styles .= "--classic-menu--link_padding_left:{$padding_left};";

    // Subnav
    $custom_styles .= "--classic-menu--subnav_text_align:{$subnav_text_align};";
    $custom_styles .= "--classic-menu--subnav_text_color:{$subnav_text_color};";
    $custom_styles .= "--classic-menu--subnav_background_color:{$subnav_background_color};";
    $custom_styles .= "--classic-menu--subnav_padding_top:{$subnav_padding_top};";
    $custom_styles .= "--classic-menu--subnav_padding_right:{$subnav_padding_right};";
    $custom_styles .= "--classic-menu--subnav_padding_bottom:{$subnav_padding_bottom};";
    $custom_styles .= "--classic-menu--subnav_padding_left:{$subnav_padding_left};";
    $custom_styles .= "--classic-menu--subnav_link_padding_top:{$subnav_link_padding_top};";
    $custom_styles .= "--classic-menu--subnav_link_padding_right:{$subnav_link_padding_right};";
    $custom_styles .= "--classic-menu--subnav_link_padding_bottom:{$subnav_link_padding_bottom};";
    $custom_styles .= "--classic-menu--subnav_link_padding_left:{$subnav_link_padding_left};";

		// Replace the <ul ...> with <ul [block attributes]>
    $block_attributes = get_block_wrapper_attributes([
      'id'    => "{$slug}-container",
      'class' => $custom_classes,
      'style' => $custom_styles,
      'tabindex' => '-1'
    ]);

    echo '<nav ' . $block_attributes . '>';

      // Generate menu HTML
      $menu_html = wp_nav_menu( [
        'menu'            => $menu_id,
        'container'       => false,
        'menu_class'      => "menu menu-{$slug}",
        'echo'            => false,
        'walker'          => new Madden_Classic_Menu_Walker( $attributes ),
      ] );
		  echo $menu_html;

    echo '</nav>';

  } else {

    echo '<p>' . esc_html__( 'Invalid menu selected.', 'madden-theme' ) . '</p>';

  }
} else {
  
	echo '<p>' . esc_html__( 'No menu selected.', 'madden-theme' ) . '</p>';
}
