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
foreach ($days_array as $day) {
    $open = isset($meta['open_' . $day]) ? $meta['open_' . $day] : null;
    $closed = isset($meta['closed_' . $day]) ? $meta['closed_' . $day] : null;
    if ($open || $closed) {
        $open = $open ? date("g:i a", strtotime($open . " UTC")) : "CLOSED";
        $closed = $closed ? date("g:i a", strtotime($closed . " UTC")) : "";
        if (!empty($closed)) {
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
$website_text = (isset($meta['website_text'])) ? $meta['website_text'] : "Visit Website";

$city = (isset($meta['city'])) ? $meta['city'] : "";
$state = (isset($meta['state'])) ? $meta['state'] : "";
$zip = (isset($meta['zip'])) ? $meta['zip'] : "";
$lat = (isset($meta['latitude'])) ? $meta['latitude'] : "";
$lng = (isset($meta['longitude'])) ? $meta['longitude'] : "";
$description = (isset($meta['description'])) ? $meta['description'] : "";

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
if (!$images) {
    $fallback_image = get_the_post_thumbnail_url($post->ID, 'full') ? get_the_post_thumbnail_url($post->ID, 'full') : get_stylesheet_directory_uri() . '/assets/images/coming-soon.jpg';
    $images = array($fallback_image);
}

// accommodations
$amenities = (isset($meta['accomodations-facility-amenities'])) ? $meta['accomodations-facility-amenities'] : "";
$location = (isset($meta['accomodations-location'])) ? $meta['accomodations-location'] : "";
$artAttractions = (isset($meta['attractions-arts-and-culture'])) ? $meta['attractions-arts-and-culture'] : "";
$attractionsType = (isset($meta['attractions-types'])) ? $meta['attractions-types'] : "";
$attractionAmenities = (isset($meta['attractions-amenities'])) ? $meta['attractions-amenities'] : "";
$recreationServices = (isset($meta['recreation-visitor-services'])) ? $meta['recreation-visitor-services'] : "";
$recreationType = (isset($meta['recreation-recreation-type'])) ? $meta['recreation-recreation-type'] : "";
$shopping = (isset($meta['shopping'])) ? $meta['shopping'] : "";
$diningType = (isset($meta['dining-type'])) ? $meta['dining-type'] : "";
$diningAmenities = (isset($meta['dining-amenities'])) ? $meta['dining-amenities'] : "";


// print_r($images);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="/wp-content/themes/mm-bradentongulfislands/partnerportal-theme-files/scripts/eventastic-single.js"></script>

<div class="listingMain">

    <div class="listingHeader">
        
        <h1 class="listingTitle"><?php echo $published_name; ?></h1>

        <!-- Accommodation Section -->
        <?php
        $accommodations = array();
        if(!empty($amenities)) {
            foreach($amenities as $i) {
                if ($i == 'pet-friendly' || $i == 'eco-friendly' || $i == 'on-site-dining') {
                    $accommodations[] = $i;
                }
            }
        }
        if(!empty($location)) {
            foreach($location as $l) {
                if ($l == 'beachfront' || $l == 'waterfront') {
                    $accommodations[] = $l;
                }
            }
        }
        ?>
        <div class="accommodations">
            <?php
            foreach($accommodations as $acc) { ?>
            <div class="accommodation <?php echo $acc;?>">
                <img src="<?php echo get_theme_file_uri() ?>/assets/images/icons/<?php echo $acc;?>.png" alt="<?php echo $acc;?>" class="icon">
                <span><?php echo ucwords(str_replace('-', ' ', $acc));?></span>
            </div>
            <?php }
            ?>
        </div><!-- .accommodations -->

    </div><!-- .listingHeader -->

    <div class="listingInfo">
        
        <div class="listingWrapper">
            
            <div class="listingImg">        
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

                <div class="contactInfoWrap">
                    <?php
                    if (!empty($website)):
                        if(!str_contains($website,'n/a')): ?>
                        <a class="website" href="<?php echo $website; ?>" target="_blank"><?php echo $website_text; ?></a>
                    <?php endif;
                    endif; ?>

                    <div class="info">
                        <?php if ($phone): ?>
                        <p class="phone">
                            Phone: <a href="tel:<?php echo $phone;?>"><?php echo $phone; ?></a>
                        </p>
                        <?php endif; ?>

                        <?php if ( $street): //make sure we got somethin at least?>
                        <p class="address">
                            <?php
                            if ($street) echo ($street2) ? $street.', '.$street2 : $street;

                            echo "<br>";
                            
                            if ($city || $state || $zip) {
                                if ($city) echo ' ' .$city;
                                if ($state) echo ' '.$state;
                                if ($zip) echo ', '.$zip;
                            }
                            ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="listingDetails">

                <?php if(!empty($description)) { ?>
                    <div class="listingDescription">
                        <p><?php echo $description; ?></p>
                    </div>
                <?php } else { ?>
                    <div class="listingDescription">
                        <?php the_content(); ?>
                    </div>
                <?php } ?>


                <?php if(!empty($amenities) || !empty($location) || !empty($attractionsType) || !empty($recreationServices) || !empty($recreationType) || !empty($shopping) || !empty($diningType) || !empty($diningAmenities)) { ?>
                <h2>Services:</h2>
                <?php } ?>

                <?php if(!empty($amenities)): ?>
                <div class="amenities">
                    <h3>Accommodations</h3>
                    <?php 
                    foreach ($amenities as $key => $amenity) {
                        $formattedAmenity = ucwords(str_replace('-', ' ', $amenity));
                        echo $formattedAmenity;

                        if ($key < count($amenities) - 1) {
                            echo ', ';
                        }
                    } ?>
                    </div>
                <?php endif; ?>

                <?php if(!empty($location)): ?>
                <div class="amenities">
                    <h3>Location</h3>
                    <?php 
                    foreach ($location as $key => $loc) {
                        $formattedLoc = ucwords(str_replace('-', ' ', $loc));
                        echo $formattedLoc;

                        if ($key < count($location) - 1) {
                            echo ', ';
                        }
                    } ?>
                    </div>
                <?php endif; ?>

                <?php if(!empty($artAttractions) || !empty($attractionsType) || !empty($attractionAmenities)): ?>
                <div class="amenities">
                    <h3>Attractions</h3>
                    <?php 
                    if(!empty($artAttractions)) {
                        foreach ($artAttractions as $key => $art) {
                            $formattedart = ucwords(str_replace('-', ' ', $art));
                            echo $formattedart;

                            if ($key < count($artAttractions) - 1) {
                                echo ', ';
                            }
                        }
                    }
                    if(!empty($attractionsType)) {
                        if(!empty($artAttractions)) {
                            echo ", ";
                        }
                        foreach ($attractionsType as $key => $type) {
                            $formattedtype = ucwords(str_replace('-', ' ', $type));
                            echo $formattedtype;

                            if ($key < count($attractionsType) - 1) {
                                echo ', ';
                            }
                        }
                    }
                    if(!empty($attractionAmenities)) {
                        if(!empty($attractionsType)) {
                            echo ", ";
                        }
                        foreach ($attractionAmenities as $key => $amen) {
                            $formattedamen = ucwords(str_replace('-', ' ', $amen));
                            echo $formattedamen;

                            if ($key < count($attractionAmenities) - 1) {
                                echo ', ';
                            }
                        }
                    } ?>
                    </div>
                <?php endif; ?>


                <?php if(!empty($recreationServices) || !empty($recreationType)): ?>
                <div class="amenities">
                    <h3>General Amenities</h3>
                    <?php 
                    if(!empty($recreationServices)) {
                        foreach ($recreationServices as $key => $art) {
                            $formattedart = ucwords(str_replace('-', ' ', $art));
                            echo $formattedart;

                            if ($key < count($recreationServices) - 1) {
                                echo ', ';
                            }
                        }
                    }
                    if(!empty($recreationType)) {
                        if(!empty($recreationServices)) {
                            echo ", ";
                        }
                        foreach ($recreationType as $key => $type) {
                            $formattedtype = ucwords(str_replace('-', ' ', $type));
                            echo $formattedtype;

                            if ($key < count($recreationType) - 1) {
                                echo ', ';
                            }
                        }
                    }
                    ?>
                    </div>
                <?php endif; ?>

                <?php if(!empty($shopping)): ?>
                <div class="amenities">
                    <h3>Shopping</h3>
                    <?php 
                    foreach ($shopping as $key => $shop) {
                        $formattedshop = ucwords(str_replace('-', ' ', $shop));
                        echo $formattedshop;

                        if ($key < count($shopping) - 1) {
                            echo ', ';
                        }
                    } ?>
                    </div>
                <?php endif; ?>

                <?php if(!empty($diningType) || !empty($diningAmenities)): ?>
                <div class="amenities">
                    <h3>Dining Amenities</h3>
                    <?php 
                    if(!empty($diningType)) {
                        foreach ($diningType as $key => $dine) {
                            $formatteddine = ucwords(str_replace('-', ' ', $dine));
                            echo $formatteddine;

                            if ($key < count($diningType) - 1) {
                                echo ', ';
                            }
                        }
                    }
                    if(!empty($diningAmenities)) {
                        if(!empty($diningType)) {
                            echo ", ";
                        }
                        foreach ($diningAmenities as $key => $amen) {
                            $formattedamen = ucwords(str_replace('-', ' ', $amen));
                            echo $formattedamen;

                            if ($key < count($diningAmenities) - 1) {
                                echo ', ';
                            }
                        }
                    }
                    ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    </div><!-- .listingInfo -->

</div>


<?php 
// var_dump($meta); 
?>


<!--This block is xyz-->
<?php
get_footer();
?>
