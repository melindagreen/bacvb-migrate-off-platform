<?php
/*
 Template Name: Eventastic Single
*/
get_header();
global $post;
$meta = eventastic_get_event_meta($post->ID);

//calculate dates
$startDate = isset($meta['start_date']) ? strtotime($meta['start_date']) : null;
$endDate = isset($meta['end_date']) ? strtotime($meta['end_date']) : null;
$dateFormat = 'M jS';
if ($startDate && $endDate && (date('Y') != date('Y', $startDate) || date('Y') != date('Y', $endDate))) $dateFormat .= ', Y'; //only show year if it's not the current year

//calculate recurrence
$recurring = isset($meta['event_end']) && $meta['event_end'] != 'finite';
$recurringDays = isset($meta['recurring_days']) ? $meta['recurring_days'] : null;
$recurringRepeat = isset($meta['recurring_repeat']) ? $meta['recurring_repeat'] : null;

$allDay = isset($meta['event_all_day']) ? $meta['event_all_day'] : false;
$startTime = isset($meta['start_time']) ? $meta['start_time'] : null;
$endTime = isset($meta['end_time']) ? $meta['end_time'] : null;

//prices
$price = isset($meta['price']) ? $meta['price'] : "";
$priceVarries = isset($meta['price_varies']) ? $meta['price_varies'] : "";
$ticketUrl = isset($meta['ticket_link']) ? $meta['ticket_link'] : "";

//address
$location = isset($meta['addr_multi']) ? $meta['addr_multi'] : "";
$street = isset($meta['addr1']) ? $meta['addr1'] : "";
$street2 = isset($meta['addr2']) ? $meta['addr2'] : "";
$city = isset($meta['city']) ? $meta['city'] : "";
$state = isset($meta['state']) ? $meta['state'] : "";
$zip = isset($meta['zip']) ? $meta['zip'] : "";
$lat = isset($meta['lat']) ? $meta['lat'] : "";
$lng = isset($meta['lng']) ? $meta['lng'] : "";

//social
$website = isset($meta['url']) ? $meta['url'] : "";
$email = isset($meta['email']) ? $meta['email'] : "";
$phone = isset($meta['phone']) ? $meta['phone'] : "";
$facebook = isset($meta['facebook']) ? $meta['facebook'] : "";
$twitter = isset($meta['twitter']) ? $meta['twitter'] : "";
$instagram = isset($meta['instagram']) ? $meta['instagram'] : "";

//images
$images = isset($meta['gallery_images']) ? $meta['gallery_images'] : "";
if (!$images && get_post_thumbnail_id()) {
    $images = array(get_post_thumbnail_id());
}
?>
<div class="eventasticMain">
    <div class="eventasticEventWrapper">
        <h1 class="eventTitle"><?php the_title() ?></h1>

        <div class="eventInfo">
            <?php if ($images): ?>
            <div class="images">
                <?php $i = 0; foreach($images as $image): $i++; ?>
                <div class="image<?php if ($i == 1) echo ' active'; ?>">
                    <img src="<?php echo wp_get_attachment_url($image); ?>" alt="<?php echo get_post_meta($image, '_wp_attachment_image_alt', TRUE); ?>">
                </div>
                <?php endforeach; ?>
                <?php if (count($images) > 1): ?>
                <span class="arrowNext">
                    <i class="fas fa-arrow-right">
                    </i>
                </span>
                <span class="arrowPrev">
                    <i class="fas fa-arrow-left">
                    </i>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="details">

                <div class="column">

                    <?php if ($location || $street): //make sure we got somethin at least?>
                    <p class="address detail">
                        <!-- <span class="fas fa-map-marked-alt"></span> -->
                        <?php
                        if ($location) echo $location.'<br>';
                        if ($street) echo ($street2) ? $street.', '.$street2 : $street;
                        if ($city || $state || $zip) {
                            echo '<br>';
                            if ($city) echo $city;
                            if ($state) echo ' '.$state;
                            if ($zip) echo ', '.$zip;
                        }
                        ?>
                    </p>
                    <?php endif; ?>

                    <?php if ($phone): ?>
                    <p class="phone detail">
                        <span class="fas fa-phone-alt"></span>
                        <?php 
                        // Remove leading +1 or 1
                        $phone = preg_replace('/^\+?1/', '', $phone);

                        // Format the phone number
                        if (strlen($phone) == 10) {
                            $formattedPhone = '('.substr($phone, 0, 3).') '.substr($phone, 3, 3).'-'.substr($phone, 6);
                            echo $formattedPhone;
                        } else {
                            echo $phone;
                        }
                        ?>
                    </p>
                    <?php endif; ?>

                    <?php if ($email): ?>
                    <p class="email detail">
                        <span class="fas fa-envelope"></span>
                        <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
                    </p>
                    <?php endif; ?>
                    
                </div>

                <div class="column">

                    <?php if ($website): ?>
                    <p>
                        <a class="website detail" href="<?php echo $website; ?>">Visit Website</a>
                    </p>
                    <?php endif; ?>

                    <?php if ($facebook || $twitter || $instagram): ?>
                    <p class="social">
                        <?php if ($facebook): ?>
                        <a href="<?php echo $facebook; ?>"><span class="fab fa-facebook-f"></span></a>
                        <?php endif; ?>

                        <?php if ($instagram): ?>
                        <a href="<?php echo $instagram; ?>"><span class="fab fa-instagram"></span></a>
                        <?php endif; ?>

                        <?php if ($twitter): ?>
                        <a href="<?php echo $twitter; ?>"><span class="fab fa-twitter"></span></a>
                        <?php endif; ?>
                    </p>
                    <?php endif; ?>
                    
                </div>

            </div>
        </div>

    </div><!-- .eventasticEventWrapper -->

    <div class="eventDateDetails">

        <div class="eventDateWrapper">
            
            <div class="dateInfo">
                <h2>Event Date</h2>

                <?php if ($startDate): //just checking for safety ?>
                <p class="dates detail">
                    <!-- <span class="fas fa-calendar-alt"></span> -->
                    <?php
                    if ($recurring && $recurringDays) {
                        if ($endDate && $startDate != $endDate) {
                            echo date('M j, Y', $startDate).' to '.date('M j, Y', $endDate).'<br>';
                        }
                        echo 'Every ';
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
                        $i = 0;
                        foreach($recurringDays as $day) {
                            $i++;
                            if ($i > 1) echo ', ';
                            echo $day;
                        }
                    } else {
                        echo date('M j, Y', $startDate);
                        if ($endDate && $startDate != $endDate) echo ' to '.date('M j, Y', $endDate);
                    }
                    ?>
                </p>
                <?php endif; ?>

                <?php if (!$allDay && ($startTime || $endTime)):  ?>
                <p class="times detail">
                    <!-- <span class="fas fa-clock"></span> -->
                    <?php
                    if ($startTime) { //this logic is accounting for if there is only an end time
                        echo (!$endTime) ? $startTime : $startTime.' - '.$endTime;
                    } else echo 'Ends at '.$endTime; //see!
                    ?>
                </p>
                <?php endif; ?>

                <?php if ($price || $priceVarries || $ticketUrl): ?>
                <div class="prices">
                    <?php if ($price || $priceVarries): //these will look the same so check either?>
                    <p class="price detail">
                        <span class="fas fa-dollar-sign"></span>
                        <?php echo ($price) ?: $priceVarries; ?>
                    </p>
                    <?php endif; ?>

                    <?php if ($ticketUrl): ?>
                    <a href="<?php echo $ticketUrl; ?>" class="ticketLink">Purchase Tickets</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div>

            <?php if ($lat && $lng) eventastic_render_event_map($post->ID, 'eventMap'); ?>

        </div>

    </div><!-- .eventDateDetails -->

    <div class="eventDescription">
        <?php the_content(); ?>
    </div>

</div>

<?php
get_footer();
?>
