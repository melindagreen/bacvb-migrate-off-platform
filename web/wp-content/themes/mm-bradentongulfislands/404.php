<?php
/**
 * The template for responding to 404 - Not Found errors
 */

get_header(); ?>
<div id="content-404" class="page-content">
	<div class="content-inner">
		<h1>Oops! <span>Looks like this page<br>went to the beach.</span></h1>
		<a class="button" href="<?php echo home_url(); ?>">Bring me back home</a>
		
	</div>

</div>

<?php get_footer(); ?>