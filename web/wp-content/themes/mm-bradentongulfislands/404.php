<?php
/**
 * The template for responding to 404 - Not Found errors
 */

get_header(); ?>
<div id="content-404" class="page-content">
	<div class="content-inner">
		<h1>Oops!<span>Looks like this page<br>went to the beach.</span></h1>
		<a class="button" href="<?php echo home_url(); ?>">Bring me back home <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/blue-arrow.png" alt="Go Home" /></a>
	</div>
</div>
<div>
<?php echo do_blocks('<!-- wp:block {"ref":3991} /-->'); ?>
</div>
<?php get_footer(); ?>