<?php

// Retrieve the current user's group
$current_user_group = get_field('partner_group', 'user_' . get_current_user_id());

// Check if the current user group exists
if (empty($current_user_group) || !is_array($current_user_group)) {
    wp_redirect('/account/?action=home');
    exit;
}

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
$start_date = get_field('eventastic_start_date');
$end_date = get_field('eventastic_end_date');
$start_time = get_field('eventastic_start_time');
$end_time = get_field('eventastic_end_time');
?>
<div class="mepr-title">
    <h1>Your Events</h1>
    <a href="<?php echo esc_url(add_query_arg('action', 'add_event', $_SERVER['REQUEST_URI'])); ?>" class="mepr-button mepr-button--secondary">Create New Event</a>
    <br/></br/>
</div>
<?php
// Display frontend form for editing events
if ($events->have_posts() && !empty($group_events)) : ?>
    <div class="mepr-event-cards">
    <?php
    while ($events->have_posts()) : $events->the_post(); ?>
        <div class="mepr-event-cards__card">
            <div class="mepr-event-status">
                <p class="event-card-status--<?php echo $status[get_post_status()]; ?>">Status: <span><?php echo $status[get_post_status()]; ?></span></p>
                <a href="<?php echo esc_url(add_query_arg(array('action' => 'edit_event', 'event_id' => get_the_ID()), $_SERVER['REQUEST_URI'])); ?>" class="mepr-button mepr-button--primary">Edit</a>
            </div>
            <div class="mepr-image-date">
                <div class="event-image">
                    <div class="event-date">
                        <p><?php echo date('M j', strtotime($start_date)); ?> - <?php echo date('M j', strtotime($end_date)); ?></p>
                    </div>
                    <?php
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('medium');
                    } else {
                        echo '<img src="https://www.bradentongulfislands.com/wp-content/uploads/20221101095942-bradenton-cvb-logo-turq-very-small.jpg" alt="Event Fallback Image" />';
                    }
                    ?>
                </div>
            </div>
            <div class="event-content">
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
                <a href="<?php the_permalink(); ?>" class="mepr-button mepr-button--tertiary">Preview Event</a>
            </div>
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