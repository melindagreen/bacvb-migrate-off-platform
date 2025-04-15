<?php /**
 * Outputs a list of social icons
 */

foreach( $args['links'] as $link_slug => $link ) {
    $icon_path = isset( $link['path'] ) 
        ? $link['path'] 
        : get_stylesheet_directory() . "/assets/images/icons/social/{$link_slug}.svg";

    ?><a class="social-icon <?= $link_slug; ?>" href="<?php echo $link['url'] ?>">
        <?php if( file_exists( $icon_path ) ) echo file_get_contents( $icon_path ); ?>
        <span class="sr-only"><?php echo $link['name']; ?></span>
    </a><?php
}