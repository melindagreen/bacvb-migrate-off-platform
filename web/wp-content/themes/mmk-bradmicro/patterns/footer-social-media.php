<?php
/**
 * Title: Footer Social Media
 * Slug: madden-theme/footer-social-media
 * Inserter: no
 */

use MaddenTheme\Library\Constants as C;
?>

<!-- wp:social-links {"iconColor":"white","iconColorValue":"#ffffff","openInNewTab":true,"size":"has-normal-icon-size","className":"site-footer__icons is-style-logos-only","style":{"layout":{"selfStretch":"fit","flexSize":null}},"layout":{"type":"flex","justifyContent":"center"}} -->
<ul class="wp-block-social-links has-normal-icon-size has-icon-color site-footer__icons is-style-logos-only">
<?php 
foreach(C::SOCIAL_LINKS as $link_slug => $link) {
    ?>
    <!-- wp:social-link {"url":"<?php echo $link['url']; ?>","service":"<?php echo $link_slug; ?>", "label":"<?php echo $link['name']; ?>"} /-->
<?php
}
?>
</ul><!-- /wp:social-links -->