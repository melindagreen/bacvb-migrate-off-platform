<?php
/*
 * Template Name: Archive
 */
use Madden\Library\Utilities as Utilities;
get_header();
$cat = get_queried_object();
// $fields = get_fields( $cat );
?>

<main id="<?php echo \MaddenMadre\Library\Constants::THEME_PREFIX; ?>-content" class="page-content blogPage archivePage" role="main">
    <h1><?php echo $cat->name; ?> Blogs</h1>


    <div class="blogNav">
        <div class="navSelect">
            <label for="blogTopics">Browse Blog Topics</label>
            <select name="blogTopics" id="blogTopics">
                <option value="0">Select</option>
                <?php $tags = get_terms( array(
                    'taxonomy' => 'post_tag',
                ) );

                $cats = get_terms( array(
                    'taxonomy' => 'category',
                    'parent'        => 0,
                ) );

                $taxItems = array_map( function( $t ) {
                    return array( 'name' => $t->name, 'url' => get_term_link( $t->term_id ) );
                }, array_merge( $tags, $cats ) );

                usort( $taxItems, function( $a, $b ) {
                    return strcmp( $a['name'], $b['name'] );
                } );

                foreach( $taxItems as $tax ): ?>
                    <option value="<?php echo $tax['url']; ?>"><?php echo $tax['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>

    <?php
    global $wp_query;
    $pageNumber = ( get_query_var( 'page' ) ) ?: 1;
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 8,
        'paged' => $pageNumber,
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => $cat->taxonomy,
                'field'    => 'term_id',
                'terms'    => $cat->term_id,
            )
        )
    );
    $posts = new WP_Query( $args );

    if ( $posts->have_posts() ):
    ?>
    <div class="blogPostModule">
        <div class="blogPosts">
            <?php while( $posts->have_posts() ): $posts->the_post();
            $image = get_the_post_thumbnail_url( $post->ID, 'madden_inline_small' );
            $date = get_the_date( 'M d, Y' );
            $author = get_the_author_meta( 'display_name' );
            $authorURL = get_author_posts_url( get_the_author_meta( 'ID' ) );
            $excerpt = get_the_excerpt();
            $title = get_the_title();
            $url = get_the_permalink();
            ?>
            <div class="blogPost">
                <a href="<?php echo $url; ?>" class="image">
                    <img data-load-type='img'
                         data-load-offset='lg'
                         data-load-all='<?php echo $image; ?>'
                         data-load-alt='<?php echo $title; ?>'/>
                </a>
                <div class="text">
                    <h2>
                        <a href="<?php echo $url; ?>"><?php echo $title; ?></a>
                    </h2>
                    <p class="info"><?php echo $date; ?> / <a href="<?php echo $authorURL; ?>" class="author"><?php echo $author; ?></a></p>
                    <p class="excerpt">
                        <?php echo $excerpt; ?>
                        <a href="<?php echo $url; ?>" class="more-link">MORE</a>
                    </p>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php  $totalPosts = $posts->found_posts;
        $pages = ceil( $totalPosts / 8 );
        $end = ( $pageNumber <= 5 ) ? 9 : $pageNumber+4;
        if ( $end > $pages ) $end = $pages;
        $start = ( ( $end - 8 ) > 0 ) ? $end-8 : 1;
        if ( $pages > 1 ) {
            $html = "<div class='pagination'>";
            if ( $pageNumber > 1 ) {
                $html .= "<div class='prevPage'>‹ PREVIOUS</div>";
            }
            for( $i = $start; $i <= $end; $i++ ) {
                $html .= "<a class='pageLink";
                if ( $i == $pageNumber ) $html .= " current";
                $html .= "' href='?page={$i}'>{$i}</a>";
            }
            if ( $pageNumber < $pages ) {
                $html .= "<div class='nextPage'>NEXT ›</div>";
            }
            $html .= "</div>";
            echo $html;
        }
        ?>
    </div>
    <?php endif; ?>

</main><!-- #site-content -->

<?php get_footer(); ?>
