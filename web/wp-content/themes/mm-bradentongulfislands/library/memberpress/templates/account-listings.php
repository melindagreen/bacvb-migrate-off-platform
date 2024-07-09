<?php 

include_once get_stylesheet_directory() .'/library/memberpress/form-handler.php';

use MaddenNino\Library\Memberpress\MemberPressFormHandler as FormHandler;

// Retrieve the current user's group
$current_user_group = get_field('partner_group', 'user_' . get_current_user_id());

// Retrieve Group Listing
$group_listings = get_field('group_listing', $current_user_group[0]->ID);
$group_listings_ID = array();

if(!empty($current_user_group)) {

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

$listings = !empty($group_listings) ? $listings : 'add_listing';

// Display frontend form for editing listings
if ($listings === 'add_listing') {

    wp_redirect(add_query_arg('action', 'add_listing'));
}

// Display frontend form for editing listings
else if ($listings->have_posts() && $listings->found_posts === 1) {
    while ($listings->have_posts()) : $listings->the_post();
                    
        $post_id = get_the_ID();
        $meta_data = get_post_meta($post_id);

        $form_handler = new FormHandler();
        $form_handler->updateListing($post_id);

        if (isset($_GET['update']) && $_GET['update'] === 'true') {
            echo '<div style="background-color: #c96a39db; border: 0.5px solid #c96a39; padding: 0.2rem; width:33%;margin-bottom:1rem" class="notice notice-success is-dismissible"><p style="color:white;margin:0;margin-left:1rem;">Listing has been updated successfully.</p></div>';
        }

        include get_stylesheet_directory() . '/library/memberpress/templates/forms/account-listing-form.php';
    
    endwhile;
}

// Displays listings grid 
else if ($listings->have_posts() && $listings->found_posts > 1) { 
    ?>
    <div class="mepr-listing-cards">
    <?php
    while ($listings->have_posts()) : $listings->the_post(); 
    $post_id = get_the_ID();
    $meta_data = get_post_meta($post_id);
    $status = [
        'publish' => 'published',
        'draft' => 'rejected',
        'pending' => 'pending'
    ];
    ?>
    <div class="mepr-listing-cards__card">
            <h3><?php the_title(); ?></h3>
            <h4 class="listing-card-status--<?php echo $status[get_post_status()]; ?>">Status: <span><?php echo $status[get_post_status()]; ?></span></h4>
            <div class="listing-content">
                <?php
                // Display trimmed content or excerpt
                if (!empty(get_the_excerpt())) {
                    echo wp_trim_words(get_the_excerpt(), 20); // Display excerpt with maximum of 20 words
                } else {
                    echo wp_trim_words(get_the_content(), 20); // Display content with maximum of 20 words
                }
                ?>
            </div>
            <a href="<?php echo esc_url(add_query_arg(array('action' => 'edit_listing', 'listing_id' => get_the_ID()), $_SERVER['REQUEST_URI'])); ?>" class="mepr-button btn-outline btn btn-outline">Edit Listing</a>
            </div>
             <?php
            endwhile; ?>
    </div> 
 </div>
<?php }
wp_reset_postdata(); ?>
<br>
    <a style="margin-top:2rem;" href="<?php echo esc_url(add_query_arg(array('action' => 'add_listing'))); ?>" class="mepr-button btn-outline btn btn-outline">Add New Listing</a>

<?php } ?>