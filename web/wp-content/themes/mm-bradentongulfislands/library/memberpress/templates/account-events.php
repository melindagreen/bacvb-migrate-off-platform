<?php
// Retrieve the current user's group
$current_user_group = get_field('partner_group', 'user_' . get_current_user_id());

// Retrieve Group Event
$group_events = get_field('group_events', $current_user_group[0]->ID);
$group_events_ID = array();

if (!empty($group_events)) {
    foreach ($group_events as $event) {
        $group_events_ID[] = $event->ID;
        }
}

$events = new WP_Query(array(
    'post_type'      => 'event',
    'post__in'       => $group_events_ID, 
    'posts_per_page' => -1,
    'post_status'    => array('publish', 'pending', 'draft'), // Include both published and pending review posts
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
        
            
// Display frontend form for editing events
if ($events->have_posts() && !empty($group_events)) : ?>
    <div class="mepr-event-cards">
    <?php
    while ($events->have_posts()) : $events->the_post(); ?>
        <div class="mepr-event-cards__card">
            <h3><?php the_title(); ?></h3>
            <h4 class="event-card-status<?php echo $status[get_post_status()]; ?>">Status: <span><?php echo $status[get_post_status()]; ?></span></h4>
            <div class="event-content">
                <?php
                // Display trimmed content or excerpt
                if (!empty(get_the_excerpt())) {
                    echo wp_trim_words(get_the_excerpt(), 20); // Display excerpt with maximum of 20 words
                } else {
                    echo wp_trim_words(get_the_content(), 20); // Display content with maximum of 20 words
                }
                ?>
            </div>
            <a href="<?php echo esc_url(add_query_arg(array('action' => 'edit_event', 'event_id' => get_the_ID()), $_SERVER['REQUEST_URI'])); ?>" class="mepr-button btn-outline btn btn-outline">Edit Event</a>
            </div>
             <?php
            endwhile; ?>
        </div>
        <?php
        else :
            echo '<p>No events found.</p>';
        endif;
        wp_reset_postdata();  
        ?>  
        <br>        
        <a href="<?php echo esc_url(add_query_arg('action', 'add_event', $_SERVER['REQUEST_URI'])); ?>" class="mepr-button btn">Create New Event</a>