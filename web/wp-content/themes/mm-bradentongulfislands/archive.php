<?php
/*
 * Template Name: Archive
 */

use MaddenNino\Library\Utilities as U;
use \MaddenNino\Library\Constants as C;

get_header();

// Get queried term
$current_term = get_queried_object();

// Get all active terms
$all_tags = get_terms(array(
    'taxonomy' => 'post_tag',
));
$all_categories = get_terms( array(
    'taxonomy' => 'category',
    'parent'        => 0,
) );
$all_terms = array_map( function ($t) {
    return array(
        'name' => $t->name,
        'url' => get_term_link( $t->term_id )
    );
}, array_merge( $all_tags, $all_categories ) );

// Sort alphabetically
usort( $all_terms, function ( $a, $b ) {
    return strcmp( $a['name'], $b['name'] );
} );

// Query for posts matching current criteria
$page_number = ( get_query_var( 'page' ) ) ?: 1;
$args = array(
    'post_type' => 'post',
    'posts_per_page' => 8,
    'paged' => $page_number,
    'post_status' => 'publish',
    'tax_query' => array(
        array(
            'taxonomy' => $current_term->taxonomy,
            'field'    => 'term_id',
            'terms'    => $current_term->term_id,
        ),
    ),
);
$posts = new WP_Query( $args ); ?>

<h1><?php echo $current_term->name; ?></h1>

<!-- TERM SELECT -->
<form class="archive-topic">
    <label class="archive-topic__label" for="<?php echo C::THEME_PREFIX ?>-archive-topic__select"><?php _e('Browse Topics', 'mmnino'); ?></label>

    <select name="archive_topic" id="<?php echo C::THEME_PREFIX ?>-archive-topic__select" class="archive-topic__select">
        <option value="0"><?php _e( 'Select a topic', 'mmnino' ); ?></option>

        <?php foreach ( $all_terms as $term ) : ?>
            <option value="<?php echo $term['url']; ?>"><?php echo $term['name']; ?></option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($posts->have_posts()) : ?>
    <!-- TERM RESULTS -->
    <section class="archive-results">
        <?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
            <article class="archive-result">
                <a href="<?php echo $url; ?>" class="archive-result__image">
                    <img 
                        data-load-type='img' 
                        data-load-offset='lg' 
                        data-load-all='<?php echo get_the_post_thumbnail_url( $post->ID, 'madden_inline_small' ); ?>' 
                        data-load-alt='<?php echo get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ); ?>' 
                    />
                </a>
                <div class="archive-result__text">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                    <p class="archive-result__info">
                        <?php the_date( 'M d, Y' ); ?> /
                        <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="author">
                            <?php the_author_meta( 'display_name' ); ?>
                        </a>
                    </p>

                    <p class="archive-result__excerpt">
                        <?php echo U::excerpt_by_sentences( get_the_ID(), 2, 200 ); ?>
                        <a href="<?php the_permalink(); ?>" class="more-link"><?php _e( 'More', 'mmnino' ); ?></a>
                    </p>
                </div>
            </article>
        <?php endwhile;
        wp_reset_postdata(); ?>
    </section>

    <?php the_posts_pagination( array(
        'mid_size'  => 2,
    ) ); ?>
<?php endif; ?>

<?php get_footer(); ?>