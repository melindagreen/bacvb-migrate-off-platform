<form class="mepr-account-form" method="post" enctype="multipart/form-data" action="">
    <?php wp_nonce_field('update_post_meta', 'update_post_nonce'); ?>

    <!-- Upload Image -->
    <div class="mepr-account-form__featured-image">
        <label for="eventastic_gallery_square_featured_image">Featured Image:</label>
        <?php
        $post_thumbnail_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
        if ($post_thumbnail_url) : ?>
            <img src="<?php echo esc_url($post_thumbnail_url); ?>" alt="Featured Image" style="max-width: 100px;">
        <?php endif; ?>
        <input type="file" name="eventastic_gallery_square_featured_image" id="eventastic_gallery_square_featured_image">       
        <hr class="mepr-account-form__separator">
    </div>
    <!-- ==== GENERAL INFO ==== -->
    <h2 class="mepr-account-form__section-title">General Info</h2>

    <!-- Post Title -->
    <label for="post_title">Title:</label>
    <input type="text" name="post_title" id="post_title" value="<?php echo esc_attr(get_the_title($post_id)); ?>">

    <!-- Description -->
    <label for="eventastic_description">Description:</label>
    <textarea name="eventastic_description" id="eventastic_description"><?php echo esc_html($meta_data['eventastic_description'][0] ?? ''); ?></textarea>

    <div class="mepr-account-form__col-2">
        <!-- Business Name -->
        <label for="eventastic_business_name">Business Name:</label>
        <input type="text" name="eventastic_business_name" id="eventastic_business_name" value="<?php echo esc_attr($meta_data['eventastic_business_name'][0] ?? ''); ?>">

        <!-- Website Link -->
        <label for="eventastic_website_link">Website Link:</label>
        <input type="url" name="eventastic_website_link" id="eventastic_website_link" value="<?php echo esc_attr($meta_data['eventastic_website_link'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-2">
        <!-- Phone Number -->
        <label for="eventastic_phone_number">Phone Number:</label><br>
        <input type="tel" name="eventastic_phone_number" id="eventastic_phone_number" value="<?php echo esc_attr($meta_data['eventastic_phone_number'][0] ?? ''); ?>"><br>

        <!-- Contact Email for Visitors -->
        <label for="eventastic_contact_email_for_visitors">Contact Email for Visitors:</label><br>
        <input type="email" name="eventastic_contact_email_for_visitors" id="eventastic_contact_email_for_visitors" value="<?php echo esc_attr($meta_data['eventastic_contact_email_for_visitors'][0] ?? ''); ?>"><br>
    </div>
    
    <!-- ==== ADDRESS INFORMATION ==== -->
    <h2 class="mepr-account-form__section-title">Address Information</h2>

    <div class="mepr-account-form__col-2">
        <!-- Address Line 1 -->
        <label for="eventastic_address_1">Address Line 1:</label><br>
        <input type="text" name="eventastic_address_1" id="eventastic_address_1" value="<?php echo esc_attr($meta_data['eventastic_address_1'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-2">
        <!-- Address Line 2 -->
        <label for="eventastic_address_2">Address Line 2:</label><br>
        <input type="text" name="eventastic_address_2" id="eventastic_address_2" value="<?php echo esc_attr($meta_data['eventastic_address_2'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-3">
        <!-- City -->
        <label for="eventastic_city">City:</label><br>
        <input type="text" name="eventastic_city" id="eventastic_city" value="<?php echo esc_attr($meta_data['eventastic_city'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-3">
        <!-- Zip Code -->
        <label for="eventastic_zip">Zip Code:</label><br>
        <input type="text" name="eventastic_zip" id="eventastic_zip" value="<?php echo esc_attr($meta_data['eventastic_zip'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-3">
        <!-- State -->
        <label for="eventastic_state">State:</label><br>
        <input type="text" name="eventastic_state" id="eventastic_state" value="<?php echo esc_attr($meta_data['eventastic_state'][0] ?? ''); ?>">
    </div>

    <!-- ==== SOCIAL ==== -->
    <h2 class="mepr-account-form__section-title">SOCIAL</h2>

    <div class="mepr-account-form__col-2">
        <!-- Facebook -->
        <label for="eventastic_facebook">Facebook</label><br>
        <input type="url" name="eventastic_facebook" id="eventastic_facebook" value="<?php echo esc_attr($meta_data['eventastic_facebook'][0] ?? ''); ?>">

        <!-- Instagram -->
        <label for="eventastic_instagram">Instagram</label><br>
        <input type="url" name="eventastic_instagram" id="eventastic_instagram" value="<?php echo esc_attr($meta_data['eventastic_instagram'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-2">
        <!-- Twitter -->
        <label for="eventastic_twitter">Twitter</label><br>
        <input type="url" name="eventastic_twitter" id="eventastic_twitter" value="<?php echo esc_attr($meta_data['eventastic_twitter'][0] ?? ''); ?>">
    </div>

    <!-- ==== EVENT PRICE ==== -->
    <h2 class="mepr-account-form__section-title">Event Price</h2>

    <div class="mepr-account-form__col-2">
        <!-- Price -->
        <label for="eventastic_address_1">Price:</label><br>
        <input type="number" name="eventastic_price" id="eventastic_price" value="<?php echo esc_attr($meta_data['eventastic_price'][0] ?? ''); ?>" step=".01">
    </div>
    <div class="mepr-account-form__col-2">
        <!-- Price Varies -->
        <label for="eventastic_price_varies">Price Varies:</label><br>
        <input type="checkbox" name="eventastic_price_varies" id="eventastic_price_varies" value="<?php echo esc_attr($meta_data['eventastic_price_varies'][0] ?? 'varies'); ?>">
    </div>

     <!-- Tickets -->
    <label for="eventastic_city">Tickets Url:</label><br>
    <input type="url" name="eventastic_ticket_link" id="eventastic_ticket_link" value="<?php echo esc_attr($meta_data['eventastic_ticket_link'][0] ?? ''); ?>">
    
     <!-- ==== EVENT DATE INFORMATION ==== -->
     <h2 class="mepr-account-form__section-title">Event Date Information</h2>
            
    <div class="mepr-account-form__col-2">
        <!-- Start Date -->
        <label for="eventastic_start_date">Start Date:</label><br>
        <input type="date" name="eventastic_start_date" id="eventastic_start_date" value="<?php echo esc_attr($meta_data['eventastic_start_date'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-2">
        <!-- End Date -->
        <label for="eventastic_end_date">End Date:</label><br>
        <input type="date" name="eventastic_end_date" id="eventastic_end_date" value="<?php echo esc_attr($meta_data['eventastic_end_date'][0] ?? ''); ?>">
    </div>
    
    <!-- eventastic_event_end -->
    <!-- <label for="eventastic_event_end">My event does not repeat:</label><br>
    <input type="radio" name="eventastic_event_end" id="eventastic_event_end" value="finite">

    <label for="eventastic_event_end">My event is ongoing:</label><br>
    <input type="radio" name="eventastic_event_end" id="eventastic_event_end" value="infinite"> -->


    <!-- Event Runs All Day -->
    <label for="eventastic_price_varies">Event runs all day:</label><br>
    <input type="checkbox" name="eventastic_event_all_day" id="eventastic_event_all_day" value="<?php echo esc_attr($meta_data['eventastic_event_all_day'][0] ?? 'true'); ?>">

    <div class="mepr-account-form__col-2">
        <!-- Start Time -->
        <label for="eventastic_start_time">Start Time:</label><br>
        <input type="time" name="eventastic_start_time" id="eventastic_start_time" value="<?php echo esc_attr($meta_data['eventastic_event_start_time'][0] ?? 'true'); ?>">
    </div>
    <div class="mepr-account-form__col-2">
        <!-- End Time -->
        <label for="eventastic_end_date">End Time:</label><br>
        <input type="time" name="eventastic_end_time" id="eventastic_end_time" value="<?php echo esc_attr($meta_data['eventastic_event_end_time'][0] ?? 'true'); ?>">
    </div>

    <!-- ==== SUBMIT FORM ==== -->
    <br style="clear:both;">
    <input class="mepr-button btn-outline btn btn-outline" type="submit" value="Add Event">
</form>