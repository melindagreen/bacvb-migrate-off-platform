<form class="mepr-account-form" method="post" enctype="multipart/form-data" action="">
    <?php wp_nonce_field('update_post_meta', 'update_post_nonce'); ?>

    <!-- Upload Image -->
    <div class="mepr-account-form__featured-image">
        <label for="eventastic_gallery_square_featured_image">Featured Image:</label>
        <?php
        $post_thumbnail_url = isset($post_id) ? get_the_post_thumbnail_url($post_id, 'thumbnail') : false;
        if ($post_thumbnail_url) : ?>
            <img src="<?php echo esc_url($post_thumbnail_url); ?>" alt="Featured Image" style="max-width: 100px;">
        <?php endif; ?>
        <input type="file" name="eventastic_gallery_square_featured_image" id="eventastic_gallery_square_featured_image">       
        <hr class="mepr-account-form__separator">
    </div>
    <!-- ==== GENERAL INFO ==== -->
    <h2 class="mepr-account-form__section-title">General Info</h2>

    <!-- Post Title -->
    <label for="post_title">Event Name: <span class="mepr-required-asterisk">*</span></label>
    <input type="text" name="post_title" id="post_title" value="<?php echo isset($post_id) ? esc_attr(get_the_title($post_id)) : ''; ?>" required>

    <!-- Description -->
    <label for="eventastic_description">Description: <span class="mepr-required-asterisk">*</span></label>
    <textarea name="eventastic_description" id="eventastic_description" required><?php echo isset($post_id) ? esc_html(get_post_field('post_content', $post_id) ?? '') : ''; ?></textarea>

    <div class="mepr-account-form__col-2">
        <!-- Business Name -->
        <label for="eventastic_business_name">Business Name: <span class="mepr-required-asterisk">*</span></label>
        <input type="text" name="eventastic_business_name" id="eventastic_business_name" value="<?php echo esc_attr($meta_data['eventastic_business_name'][0] ?? ''); ?>" required>

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
        <label for="eventastic_address_1">Address Line 1: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="text" name="eventastic_address_1" id="eventastic_address_1" value="<?php echo esc_attr($meta_data['eventastic_address_1'][0] ?? ''); ?>" required>
    </div>
    <div class="mepr-account-form__col-2">
        <!-- Address Line 2 -->
        <label for="eventastic_address_2">Address Line 2:</label><br>
        <input type="text" name="eventastic_address_2" id="eventastic_address_2" value="<?php echo esc_attr($meta_data['eventastic_address_2'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-3">
        <!-- City -->
        <label for="eventastic_city">City: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="text" name="eventastic_city" id="eventastic_city" value="<?php echo esc_attr($meta_data['eventastic_city'][0] ?? ''); ?>" required>
    </div>
    <div class="mepr-account-form__col-3">
        <!-- Zip Code -->
        <label for="eventastic_zip">Zip Code: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="text" name="eventastic_zip" id="eventastic_zip" value="<?php echo esc_attr($meta_data['eventastic_zip'][0] ?? ''); ?>" required>
    </div>
    <div class="mepr-account-form__col-3">
        <!-- State -->
        <label for="partnerportal_state">State: <span class="mepr-required-asterisk">*</span></label><br>
        <select name="partnerportal_state" id="partnerportal_state" required>
            <option value="">Select a state</option>
            <option value="AL" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'AL'); ?>>Alabama</option>
            <option value="AK" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'AK'); ?>>Alaska</option>
            <option value="AZ" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'AZ'); ?>>Arizona</option>
            <option value="AR" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'AR'); ?>>Arkansas</option>
            <option value="CA" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'CA'); ?>>California</option>
            <option value="CO" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'CO'); ?>>Colorado</option>
            <option value="CT" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'CT'); ?>>Connecticut</option>
            <option value="DE" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'DE'); ?>>Delaware</option>
            <option value="FL" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'FL'); ?>>Florida</option>
            <option value="GA" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'GA'); ?>>Georgia</option>
            <option value="HI" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'HI'); ?>>Hawaii</option>
            <option value="ID" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'ID'); ?>>Idaho</option>
            <option value="IL" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'IL'); ?>>Illinois</option>
            <option value="IN" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'IN'); ?>>Indiana</option>
            <option value="IA" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'IA'); ?>>Iowa</option>
            <option value="KS" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'KS'); ?>>Kansas</option>
            <option value="KY" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'KY'); ?>>Kentucky</option>
            <option value="LA" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'LA'); ?>>Louisiana</option>
            <option value="ME" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'ME'); ?>>Maine</option>
            <option value="MD" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'MD'); ?>>Maryland</option>
            <option value="MA" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'MA'); ?>>Massachusetts</option>
            <option value="MI" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'MI'); ?>>Michigan</option>
            <option value="MN" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'MN'); ?>>Minnesota</option>
            <option value="MS" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'MS'); ?>>Mississippi</option>
            <option value="MO" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'MO'); ?>>Missouri</option>
            <option value="MT" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'MT'); ?>>Montana</option>
            <option value="NE" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'NE'); ?>>Nebraska</option>
            <option value="NV" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'NV'); ?>>Nevada</option>
            <option value="NH" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'NH'); ?>>New Hampshire</option>
            <option value="NJ" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'NJ'); ?>>New Jersey</option>
            <option value="NM" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'NM'); ?>>New Mexico</option>
            <option value="NY" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'NY'); ?>>New York</option>
            <option value="NC" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'NC'); ?>>North Carolina</option>
            <option value="ND" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'ND'); ?>>North Dakota</option>
            <option value="OH" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'OH'); ?>>Ohio</option>
            <option value="OK" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'OK'); ?>>Oklahoma</option>
            <option value="OR" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'OR'); ?>>Oregon</option>
            <option value="PA" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'PA'); ?>>Pennsylvania</option>
            <option value="RI" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'RI'); ?>>Rhode Island</option>
            <option value="SC" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'SC'); ?>>South Carolina</option>
            <option value="SD" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'SD'); ?>>South Dakota</option>
            <option value="TN" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'TN'); ?>>Tennessee</option>
            <option value="TX" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'TX'); ?>>Texas</option>
            <option value="UT" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'UT'); ?>>Utah</option>
            <option value="VT" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'VT'); ?>>Vermont</option>
            <option value="VA" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'VA'); ?>>Virginia</option>
            <option value="WA" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'WA'); ?>>Washington</option>
            <option value="WV" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'WV'); ?>>West Virginia</option>
            <option value="WI" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'WI'); ?>>Wisconsin</option>
            <option value="WY" <?php selected($meta_data['partnerportal_state'][0] ?? '', 'WY'); ?>>Wyoming</option>
        </select>
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
        <input type="checkbox" name="eventastic_price_varies" id="eventastic_price_varies" value="varies" <?php echo isset($meta_data['eventastic_price_varies'][0]) ? 'checked' : ''; ?>>
    </div>

     <!-- Tickets -->
    <label for="eventastic_city">Tickets Url:</label><br>
    <input type="url" name="eventastic_ticket_link" id="eventastic_ticket_link" value="<?php echo esc_attr($meta_data['eventastic_ticket_link'][0] ?? ''); ?>">
    
     <!-- ==== EVENT DATE INFORMATION ==== -->
     <h2 class="mepr-account-form__section-title">Event Date Information</h2>
            
    <div class="mepr-account-form__col-2">
        <!-- Start Date -->
        <label for="eventastic_start_date">Start Date: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="date" name="eventastic_start_date" id="eventastic_start_date" value="<?php echo esc_attr($meta_data['eventastic_start_date'][0] ?? ''); ?>" required>
    </div>
    <div class="mepr-account-form__col-2">
        <!-- End Date -->
        <label for="eventastic_end_date">End Date: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="date" name="eventastic_end_date" id="eventastic_end_date" value="<?php echo esc_attr($meta_data['eventastic_end_date'][0] ?? ''); ?>" required>
    </div>
    
    <!-- eventastic_event_end -->
    <!-- <label for="eventastic_event_end">My event does not repeat:</label><br>
    <input type="radio" name="eventastic_event_end" id="eventastic_event_end" value="finite">

    <label for="eventastic_event_end">My event is ongoing:</label><br>
    <input type="radio" name="eventastic_event_end" id="eventastic_event_end" value="infinite"> -->


    <!-- Event Runs All Day -->
    <label for="eventastic_event_all_day">Event runs all day:</label><br>
    <input type="checkbox" name="eventastic_event_all_day" id="eventastic_event_all_day" value="true" <?php echo isset($meta_data['eventastic_event_all_day'][0]) ? 'checked' : ''; ?>>
    <div id="eventastic_start_col" class="mepr-account-form__col-2">
        <!-- Start Time -->
        <label for="eventastic_start_time">Start Time: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="time" name="eventastic_start_time" id="eventastic_start_time" value="<?php echo esc_attr($meta_data['eventastic_start_time'][0] ?? 'true'); ?>">
    </div>
    <div id="eventastic_end_col" class="mepr-account-form__col-2">
        <!-- End Time -->
        <label for="eventastic_end_date">End Time: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="time" name="eventastic_end_time" id="eventastic_end_time" value="<?php echo esc_attr($meta_data['eventastic_end_time'][0] ?? 'true'); ?>">
    </div>

    <!-- ==== SUBMIT FORM ==== -->
    <br style="clear:both;">
    <input class="mepr-button btn-outline btn btn-outline" type="submit" value="<?php echo isset($post_id) ? 'Update Event' : 'Add Event'; ?>">
</form>