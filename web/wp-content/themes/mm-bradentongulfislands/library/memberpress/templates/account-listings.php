<?php

include_once get_stylesheet_directory() . '/library/memberpress/form-handler.php';

use MaddenNino\Library\Memberpress\MemberPressFormHandler as FormHandler;

// Retrieve the current user's group
$current_user_group = get_field('partner_group', 'user_' . get_current_user_id());

// Check if the current user group exists
if (empty($current_user_group) || !is_array($current_user_group)) {
    wp_redirect('/account/?action=home');
    exit;
}

$group_listings_ID = array();

// Retrieve Group Listing
$group_listings = get_field('group_listing', $current_user_group[0]->ID);

if (!empty($group_listings)) {
    foreach ($group_listings as $listing) {
        $group_listings_ID[] = $listing->ID;
    }
}

$listings = new WP_Query(array(
    'post_type'      => 'listing',
    'post__in'       => $group_listings_ID,
    'posts_per_page' => -1,
    'post_status'    => array('publish', 'pending', 'draft'),
    'meta_query'     => array(
        array(
            'key'     => 'cloned_post_id',
            'compare' => 'NOT EXISTS',
        ),
    )
));

$status = [
    'publish' => 'published',
    'draft' => 'rejected',
    'pending' => 'pending'
];
?>
<div class="mepr-title">
    <h1>Your Listings</h1>
    <a href="<?php echo esc_url(add_query_arg('action', 'add_listing', $_SERVER['REQUEST_URI'])); ?>" class="mepr-button mepr-button--secondary">Create New Listing</a>
    <br/></br/>
</div>
<?php
// Display frontend form for editing listings
if ($listings->have_posts() && !empty($group_listings)) : ?>
    <div class="mepr-listing-cards">
    <?php
    while ($listings->have_posts()) : $listings->the_post(); ?>
        <div class="mepr-listing-cards__card">
            <div class="mepr-listing-status">
                <p class="listing-card-status--<?php echo $status[get_post_status()]; ?>">Status: <span><?php echo $status[get_post_status()]; ?></span></p>
                <a href="<?php echo esc_url(add_query_arg(array('action' => 'edit_listing', 'listing_id' => get_the_ID()), $_SERVER['REQUEST_URI'])); ?>" class="mepr-button mepr-button--primary">Edit</a>
            </div>
            <div class="mepr-image">
                <div class="listing-image">
                    <?php
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('medium');
                    } else {
                        echo '<img src="https://www.bradentongulfislands.com/wp-content/uploads/placeholder.jpg" alt="Listing Fallback Image" />';
                    }
                    ?>
                </div>
            </div>
            <div class="listing-content">
                <div class="content-main">
                    <h2><?php the_title(); ?></h2>
                    <?php
                    // Display trimmed content or excerpt
                    if (!empty(get_the_excerpt())) {
                        echo wp_trim_words(get_the_excerpt(), 20); // Display excerpt with maximum of 20 words
                    } else {
                        echo wp_trim_words(get_the_content(), 20); // Display content with maximum of 20 words
                    }
                    ?>
                </div>
                <a href="<?php the_permalink(); ?>" class="mepr-button mepr-button--tertiary">Preview Listing</a>
            </div>
        </div>
        <?php
    endwhile; ?>
    </div>
    <?php
else :
    echo '<p>No listings found.</p>';
endif;
wp_reset_postdata();
?>
