<?php
/*
 Template Name: Eventastic Events Page
*/
get_header();
global $post;
$meta = get_post_meta($post->ID);
$args = array(
    'posts_per_page' => -1
);
$events = eventastic_get_events($args);
$categories = eventastic_get_categories(true);
?>

<div class="main eventasticEventsPage">

    <h1 class="eventasticHeadline"><?php echo !empty($meta['eventastic_headline'][0]) ? $meta['eventastic_headline'][0] : get_the_title($post->ID); ?></h1>

    <div class="eventasticEvents">
        <div class="filters">
            <p class="title">Search</p>
            <div class="dateAndKeyworkFilters">
                <div class="dateFilter">
                    <label for="start_date">Start Date:</label>
                    <div class="dateInput">
                        <input id="StartDate" maxlength="10" name="start_date" readonly="true" type="date" value="<?php echo date('Y-m-d'); ?>" class="eventasticDatePicker hasDatepicker">
                    </div>
                </div>
                <div class="dateFilter">
                    <label for="end_date">End Date:</label>
                    <div class="dateInput">
                        <input id="EndDate" maxlength="10" name="end_date" readonly="true" type="date" value="<?php echo date("Y-m-d", strtotime("+1 month", strtotime(date("Ymd")))); ?>" class="eventasticDatePicker hasDatepicker">
                    </div>
                </div>
                <div class="keywordFilter">
                    <label for="keyword">Search By Name:</label>
                    <input type="text" id="Keyword" name="keyword" value="" maxlength="50" class="keywords">
                </div>
            </div>
            <?php if ($categories): ?>
            <div class="categoryFilters">
                <div class="filterToggle">Categories:</div>
                <div class="categories">
                    <?php foreach ($categories as $cat): ?>
                    <div class="checkbox">
                        <input type="checkbox" name="category" value="<?php echo $cat->term_id; ?>">
                        <label for="category"><?php echo $cat->name; ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            <button class="filterSubmit">Filter</button>
        </div>

        <div class="events events-wrapper">
            <?php 
            $eventCount = 0; 
            
            foreach($events as $event):
                $eventCount++;
                $eventID = $event->ID;
                $eventMeta = eventastic_get_event_meta($eventID);
                $image = get_the_post_thumbnail_url($eventID, 'full');
                $startDate = strtotime($eventMeta['start_date']);
                $endDate = strtotime($eventMeta['end_date']);
                $recurring = ( (isset($eventMeta['event_end'])) && ($eventMeta['event_end'] == 'infinite') ) ? true : false;
                if ($recurring) {
                    $recurringDays = $eventMeta['recurring_days'];
                    $recurringRepeat = $eventMeta['recurring_repeat'];
                }
                $allDay = (isset($eventMeta['event_all_day'])) ? $eventMeta['event_all_day'] : false;
                if (!$allDay) {
                    $startTime = (array_key_exists('start_time', $eventMeta) && $eventMeta['start_time'] ) ? $eventMeta['start_time'] : null;
                    $endTime = (array_key_exists('end_time', $eventMeta) && $eventMeta['end_time'] ) ? $eventMeta['end_time'] : null;
                }
                ?>
            <a href="<?php echo get_the_permalink($eventID); ?>" class="event<?php if ($eventCount > 20) echo ' hidden'; ?>">
                <?php if ($image): ?>
                <div class="image">
                    <img data-load-alt="<?php get_the_title($eventID); ?>"
                         data-load-type="img"
                         data-load-offset="lg"
                         data-load-all="<?php echo $image; ?>"
                         src="<?php echo get_template_directory_uri(); ?>/library/images/pixel.png" />
                </div>
                <?php endif; ?>
                <div class="text">
                    <h3><?php echo get_the_title($eventID); ?></h3>
                    <div class="date">
                        <?php
                        if ($recurring) {
                            if ($endDate && $startDate != $endDate) {
                                echo date('M j, Y', $startDate).' to '.date('M j, Y', $endDate).'<br>';
                            }
                            echo 'every ';
                            switch ($recurringRepeat) {
                                case '1':
                                    echo '1st ';
                                    break;
                                case '2':
                                    echo '2nd ';
                                    break;
                                case '3':
                                    echo '3rd ';
                                    break;
                                case '4':
                                    echo '4th ';
                                    break;
                            }
                            $days = 0;
                            foreach($recurringDays as $day) {
                                $days++;
                                if ($days > 1) echo ', ';
                                echo $day;
                            }
                        } else {
                            echo date('M j, Y', $startDate);
                            if ($endDate && $startDate != $endDate) echo ' to '.date('M j, Y', $endDate);
                        }
                        ?>
                    </div>
                    <p><?php echo (get_post_field('post_excerpt', $eventID)) ?: wp_trim_words(strip_tags(get_the_content(null, null, $eventID)), 50); ?></p>
                    <button>View Details</button>
                </div>
            </a>
            <?php endforeach; if ($eventCount > 20) echo '<div class="showMoreButton">Show More</div>'; ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>