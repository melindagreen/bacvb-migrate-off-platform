<?php
/*
 Template Name: Eventastic Single
*/
use Eventastic\Library\Utilities as Utilities;

get_header();

global $post;

$meta = eventastic_get_event_meta($post->ID);
//calculate dates
/*
$startDate = strtotime($meta['start_date']); //convert to time so we can use the date() function for rendering
$endDate = strtotime($meta['end_date']);
$dateFormat = 'M jS';
if (date('Y') != date('Y', $startDate) || date('Y') != date('Y', $endDate)) $dateFormat .= ', Y'; //only show year if it's not the current year

//calculate recurrance
$recurring = (array_key_exists('event_end', $meta) && ($meta['event_end'] == 'finite')) ? false : true;
if ($recurring) {
    $recurringDays = (array_key_exists('recurring_days', $meta) && $meta['recurring_days'] ) ? $meta['recurring_days'] : [];
    $recurringRepeat = (array_key_exists('recurring_repeat', $meta) && $meta['recurring_repeat'] ) ? $meta['recurring_repeat'] : [];
   
}
*/
$utilities = new Utilities();
$upcomingObj = $utilities->getUpcomingOcccurences( $post );
$upcoming = $upcomingObj['html'];
if( is_array($upcoming) && count($upcoming) > 0 ){
    $start_date_str = $upcoming['days'][0];
    $start_date = DateTime::createFromFormat('Y-m-d', $start_date_str);
    $end_date = DateTime::createFromFormat('Y-m-d', $start_date_str);
}
elseif( 'one_day' == $meta['recurrence_options'] ){
    $start_date = DateTime::createFromFormat('Y-m-d', $meta['start_date']);
    $end_date = DateTime::createFromFormat('Y-m-d', $meta['end_date']);
}
else{
    $start_date = DateTime::createFromFormat('Y-m-d', $meta['start_date']);
    if( isset( $meta['end_date'] ) && $meta['end_date'] ){
        $end_date = DateTime::createFromFormat('Y-m-d', $meta['end_date']);
    }
    else{
        $end_date = DateTime::createFromFormat('Y-m-d', $meta['start_date']);
    }
}
$dateFormat = 'M jS';

//calculate recurrance
//print_r($meta);
$recurring = null;
$allDay = null;
    $date_text = "";
        $args = [
            'post' => $post
        ];
        $date_text .= Utilities::datesToString( $args );

$startTime = $meta['start_time'];
$endTime = $meta['end_time'];

//prices
$price = (isset($meta['price'])) ? $meta['price'] : "";
$priceVarries = (isset($meta['price_varies'])) ? $meta['price_varies'] : "";
$ticketUrl = (isset($meta['ticket_link'])) ? $meta['ticket_link'] : "";

//address
$location = (isset($meta['addr_multi'])) ? $meta['addr_multi'] : "";
$street =(isset($meta['addr1'])) ? $meta['addr1'] : "";
$street2 = (isset($meta['addr2'])) ? $meta['addr2'] : "";
$city = (isset($meta['city'])) ? $meta['city'] : "";
$state = (isset($meta['state'])) ? $meta['state'] : "";
$zip = (isset($meta['zip'])) ? $meta['zip'] : "";
$lat = (isset($meta['lat'])) ? $meta['lat'] : "";
$lng = (isset($meta['lng'])) ? $meta['lng'] : "";

//social
$website = (isset($meta['url'])) ? $meta['url'] : "";
$email = (isset($meta['email'])) ? $meta['email'] : "";
$phone = (isset($meta['phone'])) ? $meta['phone'] : "";
$facebook = (isset($meta['facebook'])) ? $meta['facebook'] : "";
$twitter = (isset($meta['twitter'])) ? $meta['twitter'] : "";

//images
$images = [];
if (get_post_thumbnail_id()) {
    array_push($images, get_post_thumbnail_id());
}
if (isset($meta['gallery_images'])) {
    $images = array_merge($images, $meta['gallery_images']);
}
?>
<main id="main-content">
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
                <?php if ($start_date): //just checking for safety ?>
                    <p class="dates detail">
                        <span class="label">Date:</span>                                
                        <?php echo $date_text; ?>
                    </p>
                    <?php if (isset($upcoming) ) : ?>
                            <div class="allDates">Upcoming Dates: 
                                <?php echo $upcoming; ?>
                            </div>
                    <?php endif; ?>

                <?php endif; ?>

				<?php if (!$allDay && ($startTime || $endTime)):  ?>
				<p class="times detail">
					<span class="fas fa-clock"></span>
					<?php
                    if ($startTime) { //this logic is accounting for if there is only an end time
                        echo (!$endTime) ? $startTime : $startTime.' - '.$endTime;
                    } else echo 'Ends at '.$endTime; //see!
                    ?>
				</p>
				<?php endif; ?>

				<?php if ($location || $street): //make sure we got somethin at least?>
				<p class="address detail">
					<span class="fas fa-map-marked-alt"></span>
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

				<?php if ($price || $priceVarries || $ticketUrl): ?>
				<div class="prices">
					<?php if ($price || $priceVarries): //these will look the same so check either?>
					<p class="price detail">
						<span class="fas fa-dollar-sign"></span>
						<?php echo ($price) ?: $priceVarries; ?>
					</p>
					<?php endif; ?>

					<?php if ($ticketUrl): ?>
					<a href="<?php echo $ticketUrl; ?>" class="ticketLink">Register Here</a>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php if ($website): ?>
				<p class="website detail">
					<span class="fas fa-globe"></span>
					<a href="<?php echo $website; ?>">Event Website</a>
				</p>
				<?php endif; ?>

				<?php if ($phone): ?>
				<p class="phone detail">
					<span class="fas fa-phone-alt"></span>
					<?php echo $phone; ?>
				</p>
				<?php endif; ?>

				<?php if ($email): ?>
				<p class="email detail">
					<span class="fas fa-envelope"></span>
					<a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
				</p>
				<?php endif; ?>

				<?php if ($facebook || $twitter): ?>
				<p class="social">
					<?php if ($facebook): ?>
					<a href="<?php echo $facebook; ?>"><span class="fab fa-facebook-f"></span></a>
					<?php endif; ?>

					<?php if ($twitter): ?>
					<a href="<?php echo $twitter; ?>"><span class="fab fa-twitter"></span></a>
					<?php endif; ?>
				</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="eventDescription">
			<?php the_content(); ?>
		</div>

		<?php if ($lat && $lng) eventastic_render_event_map($post->ID, 'eventMap'); ?>
	</div>
</main>

<?php
get_footer();
?>
