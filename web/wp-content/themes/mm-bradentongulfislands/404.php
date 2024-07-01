<?php
/**
 * The template for responding to 404 - Not Found errors
 */

get_header(); ?>
<div id="content-404" class="page-content <?php echo isset($_GET['maintenance']) ? 'page-content--maintenance' : ''; ?>">
	<div class="content-inner">
		<h1>Oops!<span>Looks like this page<br>went to the beach.</span></h1>
		<?php if(isset($_GET['maintenance'])) { ?>
			<p class="maintenance-message is-style-collage-square">We're taking some time to make a few updates to the Partner Portal. In the meantime, if you need assistance updating your listing or submitting an event, please reach out to Emily Knight at Emily.Knight@BACVB.com</p>
		<?php } ?>
		<a class="button" href="<?php echo home_url(); ?>">Bring me back home <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/blue-arrow.png" alt="Go Home" /></a>
	</div>
</div>
<div>
<?php echo do_blocks('<!-- wp:block {"ref":3991} /-->'); ?>
</div>
<?php get_footer(); ?>