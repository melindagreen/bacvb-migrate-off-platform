<?php
/*
 Template Name: Listing Single
*/
use MaddenNino\Library\Utilities as U;

get_header();
global $post;

$rhett = array();
$postMeta = get_post_meta($post->ID);
//print_r($postMeta);
$terms = get_the_terms( $post->ID , 'listing_categories' );
$is_lodging = 0;
if ( is_array( $terms ) && ! is_wp_error( $terms ) ) {
    $terms_html = "<div class='terms-wrapper'>";
    foreach ($terms as $term) {
        if( 'lodging' == $term->slug ){
            $is_lodging = 1;
        }
        $term_link = get_term_link($term, 'listing_categories');
        if (is_wp_error($term_link))
            continue;
        $terms_html .= '<a href="" class="term">' . $term->name . '</a>';
    }
    $terms_html .= "</div>";
}
$keyRoot = str_replace("[KEY]", "", "partnerportal_[KEY]");

// do some cleanup and only return ours
foreach ($postMeta as $key => $val) {
    $cleanKey = str_replace($keyRoot, "", $key);
    if (strstr($key, $keyRoot) !== false) {
        if (is_array($val)) {
            $rhett[$cleanKey] = maybe_unserialize($val[0]);
        } else {
            $rhett[$cleanKey] = $val;
        }
    }
}
$meta = $rhett;
//print_r($meta);
//calculate dates
//address
$hours = "";
$hours_description = $meta['hours_description'];
if( $hours_description ){
    $hours .= "<div class='hours-description'>" . $hours_description . "</div>";
}
$days_array = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
foreach( $days_array as $day ){
    $open = $meta['open_' . $day];
    $closed = $meta['closed_' . $day];
    if( $open || $closed){
        $open = date("g:i a", strtotime($open . " UTC"));
        $closed = date("g:i a", strtotime($closed . " UTC"));
        if( !$open || $open == '12:00 am'){
            $open = "CLOSED";
        }
        if( !$closed || $closed == '12:00 am' ){
            $closed = "";
        }
        
        if(!empty($closed)) {
            $hours .= "<div class='day'>" . ucfirst($day) . ": " . $open . " - " . $closed . "</div>";
        } else {
            $hours .= "<div class='day'>" . ucfirst($day) . ": " . $open . "</div>";
        }
    }
}
$published_name =(isset($meta['business_name'])) ? $meta['business_name'] : "";
if( preg_match('#,The#', $published_name ) ){
    $published_name = "The " . preg_replace('#,The#','',$published_name); 
}
if( preg_match('#, The#', $published_name ) ){
    $published_name = "The " . preg_replace('#, The#','',$published_name); 
}
$street =(isset($meta['address_1'])) ? $meta['address_1'] : "";
$street2 = (isset($meta['address_2'])) ? $meta['address_2'] : "";
// $phone = (isset($meta['phone_number'])) ? U::clean_phone( $meta['phone_number']) : "";
$phone = (isset($meta['phone_number'])) ? $meta['phone_number'] : "";
$website = (isset($meta['website_link'])) ? $meta['website_link'] : "";
$website_text = (isset($meta['website_text'])) ? $meta['website_text'] : "View Website";

$city = (isset($meta['city'])) ? $meta['city'] : "";
$state = (isset($meta['state'])) ? $meta['state'] : "";
$zip = (isset($meta['zip'])) ? $meta['zip'] : "";
$lat = (isset($meta['latitude'])) ? $meta['latitude'] : "";
$lng = (isset($meta['longitude'])) ? $meta['longitude'] : "";
$description = (isset($meta['description'])) ? $meta['description'] : "";

$amenities = (isset($meta['lodging_amenities'])) ? $meta['lodging_amenities'] : "";
$beds = (isset($meta['lodging_beds'])) ? $meta['lodging_beds'] : "";
$baths = (isset($meta['lodging_baths'])) ? $meta['lodging_baths'] : "";
$guests = (isset($meta['lodging_guests'])) ? $meta['lodging_guests'] : "";

//images
$images = [];
$image_ids = (isset($meta['gallery_images']) ) ? $meta['gallery_images'] : "";
if( $image_ids ){
    $image_ids = preg_replace("#\[#", '', $image_ids);
    $image_ids = preg_replace("#\]#", '', $image_ids);
    if( $image_ids ){
        $image_array = explode(',',$image_ids);
        foreach( $image_array as $image_id ){
            $images[] = wp_get_attachment_image_url($image_id, 'full');
        }
    }
}
if (!$images ) {
    $fallback_image = get_the_post_thumbnail_url() ?  get_the_post_thumbnail_url($post->ID,'full') : '/wp-content/uploads/coming-soon.jpg';
    $images = array($fallback_image);
}
// print_r($images);
?>

<link rel='stylesheet' href='https://unpkg.com/swiper@8/swiper-bundle.min.css' />
<script src='https://unpkg.com/swiper@8/swiper-bundle.min.js'></script>

<div class="listingMain">

    <div class="listingInfo">

        <div class="content-wrapper">
            <div class="left-col <?php if (!empty($images)) {echo "multiCol";}?>">

                <div class="details">

                    <h1 class="listingTitle"><?php echo $published_name; ?></h1>
                    <?php if ( $street): //make sure we got somethin at least?>
                    <p class="address detail">
                        <?php
                        if ($street) echo ($stree2) ? $street.', '.$street2 : $street;
                        
                        if ($city || $state || $zip) {
                            if ($city) echo ' ' .$city;
                            if ($state) echo ' '.$state;
                            if ($zip) echo ', '.$zip;
                        }
                        ?>
                    </p>
                    <?php endif; ?>
                    <?php if ($phone): ?>
                    <p class="phone detail">
                        <?php echo $phone; ?>
                    </p>
                    <?php endif; ?>
                    <?php
                    if (!empty($website)):
                        if(!str_contains($website,'n/a')): ?>
                        <p class="website detail">
                            <a href="<?php echo $website; ?>" target="_blank"><?php echo $website_text; ?></a>
                        </p>
                    <?php endif;
                    endif; ?>
                    <?php if (1 != 1 && $terms_html): ?>
                        <?php echo $terms_html; ?> 
                    <?php endif; ?>

                    <div class="listingDescription">
                        <?php the_content(); ?>
                    </div>
                    <?php if($description): ?>
                        <div class="description">
                            <p><?php echo $description; ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if( $amenities || $beds || $guests || $baths ): ?>
                        <div class="lodging-meta">
                            <h3>Lodging Amenities</h3>
                            <?php if( $amenities ): ?>
                                <p><?php echo $amenities; ?></p>
                            <?php endif; ?>
                            <?php if( $beds) : ?>
                                <div class="lodging-item"><b>Beds: </b><?php echo $beds; ?></div>
                            <?php endif; ?>
                            <?php if( $baths ) : ?>
                                <div class="lodging-item"><b>Baths: </b><?php echo $baths; ?></div>
                            <?php endif; ?>                                    
                            <?php if( $guests ) : ?>
                                <div class="lodging-item"><b>Guests: </b><?php echo $guests; ?></div>
                            <?php endif; ?>                                    
                        </div>
                    <?php endif; ?>
                    <?php if( $hours ): ?>
                        <h3>Hours</h3>
                        <div class="hours">
                            <?php echo $hours; ?>
                        </div> 
                    <?php endif; ?>

                </div>
            </div>

            <div class="right-col eventsMapImg">
                <?php if (is_array($images) && count($images)>1): ?>
                    <div class="images swiper listingImgSwiper">
                        <div class="swiper-wrapper">
                        <?php $i = 0; foreach($images as $image): $i++; ?>
                            <div class="swiper-slide image-wrapper">
                                <img src="<?php echo $image; ?>" alt="<?php echo get_post_meta($image, '_wp_attachment_image_alt', TRUE); ?>" class="slideImg">
                            </div>
                        <?php endforeach; ?>
                        </div>
                        <?php if (count($images) > 1): ?>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        <?php endif; ?>
                    </div><!--/images-->
                <?php else : ?>
                    <?php if (is_array($images) && count($images)>0): ?>
                    <div class="images">
                        <?php $i = 0; foreach($images as $image): $i++; ?>
                        <img src="<?php echo $image; ?>" alt="<?php echo get_post_meta($image, '_wp_attachment_image_alt', TRUE); ?>" class="singleImg">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($lat && $lng) : ?>
                    <?php  eventastic_render_event_map($post->ID, 'listingMap'); ?><!--/end rendermap-->
                    <div id="listingmap"></div>
                    <script>
                    jQuery(document).ready(function(){
                        var center = [<?php echo $meta['latitude'] . ", " . $meta['longitude']; ?>];
                        var map = L.map('listingmap').setView(center, 15);
                        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 18}).addTo(map);
                        L.marker(center).addTo(map);
                    });
                    </script>
                <?php endif; ?>
            </div><!--/right-col-->

        </div><!-- .content-wrapper -->
    </div>

</div>

<!--This block is xyz-->
<?php
get_footer();
?>
