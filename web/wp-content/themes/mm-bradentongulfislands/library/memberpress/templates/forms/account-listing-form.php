<form class="mepr-account-form" method="post" enctype="multipart/form-data" action="">
    <?php wp_nonce_field('update_post_meta', 'update_post_nonce'); ?>
    
    <!-- Upload Image -->
    <div class="mepr-account-form__featured-image">
        <label for="partnerportal_gallery_square_featured_image">Featured Image:</label>
        <?php
        $post_thumbnail_url =  isset($post_id) ? get_the_post_thumbnail_url($post_id, 'thumbnail') : false;
        if ($post_thumbnail_url) : ?>
            <img src="<?php echo esc_url($post_thumbnail_url); ?>" alt="Featured Image" style="max-width: 100px;">
        <?php endif; ?>
        <input type="file" name="partnerportal_gallery_square_featured_image" id="partnerportal_gallery_square_featured_image">       
        <hr class="mepr-account-form__separator">
    </div>
    <!-- ==== GENERAL INFO ==== --> 
    <h2 class="mepr-account-form__section-title">General Info</h2>

    <!-- Post Title -->
    <label for="post_title">Business Title: <span class="mepr-required-asterisk">*</span></label>
    <input type="text" name="post_title" id="post_title" value="<?php echo isset($post_id) ? esc_attr(get_the_title($post_id)) : ''; ?>" required>

    <!-- Description -->
    <label for="partnerportal_description">Description:</label>
    <textarea name="partnerportal_description" id="partnerportal_description"><?php echo isset($post_id) ? esc_html($meta_data['partnerportal_description'][0] ?? ''): ''; ?></textarea>

    <div class="mepr-account-form__col-2">
        <!-- Business Name -->
        <label for="partnerportal_business_name">Business Name: <span class="mepr-required-asterisk">*</span></label>
        <input type="text" name="partnerportal_business_name" id="partnerportal_business_name" value="<?php echo isset($post_id) ? esc_attr($meta_data['partnerportal_business_name'][0] ?? '') : ''; ?>" required>

        <!-- Website Link -->
        <label for="partnerportal_website_link">Website Link:</label>
        <input type="url" name="partnerportal_website_link" id="partnerportal_website_link" value="<?php echo isset($post_id) ? esc_attr($meta_data['partnerportal_website_link'][0] ?? ''): ''; ?>">
    </div>

    <div class="mepr-account-form__col-2">
        <!-- Phone Number -->
        <label for="partnerportal_phone_number">Phone Number: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="tel" name="partnerportal_phone_number" id="partnerportal_phone_number" value="<?php echo isset($post_id) ? esc_attr($meta_data['partnerportal_phone_number'][0] ?? '') : ''; ?>" required><br>

        <!-- Contact Email for Visitors -->
        <label for="partnerportal_contact_email_for_visitors">Contact Email for Visitors: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="email" name="partnerportal_contact_email_for_visitors" id="partnerportal_contact_email_for_visitors" value="<?php echo isset($post_id) ? esc_attr($meta_data['partnerportal_contact_email_for_visitors'][0] ?? '') : ''; ?>" required><br>
    </div>

    <!-- ==== HOURS ==== -->
    <h2 class="mepr-account-form__section-title">Hours</h2>
    
    <!-- Hours Description -->
    <label for="partnerportal_hours_description">Hours Description:</label>
    <textarea name="partnerportal_hours_description" id="partnerportal_hours_description"><?php echo isset($post_id) ? esc_html($meta_data['partnerportal_hours_description'][0] ?? '') : ''; ?></textarea>
    
    <!-- ==== ADDRESS INFORMATION ==== -->
    <h2 class="mepr-account-form__section-title">Address Information</h2>
    
    <div class="mepr-account-form__col-2">
        <!-- Address Line 1 -->
        <label for="partnerportal_address_1">Address Line 1: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="text" name="partnerportal_address_1" id="partnerportal_address_1" value="<?php echo isset($post_id) ? esc_attr($meta_data['partnerportal_address_1'][0] ?? '') : ''; ?>" required>
    </div>
    <div class="mepr-account-form__col-2">
         <!-- Address Line 2 -->
         <label for="partnerportal_address_2">Address Line 2:</label><br>
        <input type="text" name="partnerportal_address_2" id="partnerportal_address_2" value="<?php echo isset($post_id) ? esc_attr($meta_data['partnerportal_address_2'][0] ?? '') : ''; ?>">
    </div>

    <div class="mepr-account-form__col-3">
        <!-- City -->
        <label for="partnerportal_city">City: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="text" name="partnerportal_city" id="partnerportal_city" value="<?php echo isset($post_id) ? esc_attr($meta_data['partnerportal_city'][0] ?? '') : ''; ?>" required>
    </div>
    <div class="mepr-account-form__col-3">
        <!-- Zip Code -->
        <label for="partnerportal_zip">Zip Code: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="text" name="partnerportal_zip" id="partnerportal_zip" value="<?php echo isset($post_id) ? esc_attr($meta_data['partnerportal_zip'][0] ?? '') : ''; ?>" required>
    </div>
    <div class="mepr-account-form__col-3">
        <!-- State -->
        <label for="partnerportal_state">State: <span class="mepr-required-asterisk">*</span></label><br>
        <input type="text" name="partnerportal_state" id="partnerportal_state" value="<?php echo isset($post_id) ? esc_attr($meta_data['partnerportal_state'][0] ?? '') : ''; ?>" required>
    </div> 

    <!-- ==== SOCIAL ==== -->
    <h2 class="mepr-account-form__section-title">SOCIAL</h2>
    
    <div class="mepr-account-form__col-2">
        <!-- Facebook -->
        <label for="partnerportal_facebook">Facebook</label><br>
        <input type="url" name="partnerportal_facebook" id="partnerportal_facebook" value="<?php echo isset($post_id) ? esc_attr($meta_data['partnerportal_facebook'][0] ?? '') : ''; ?>">

        <!-- Instagram -->
        <label for="partnerportal_instagram">Instagram</label><br>
        <input type="url" name="partnerportal_instagram" id="partnerportal_instagram" value="<?php echo isset($post_id) ? esc_attr($meta_data['partnerportal_instagram'][0] ?? '') : ''; ?>">
    </div>

    <div class="mepr-account-form__col-2">
        <!-- Twitter -->
        <label for="partnerportal_twitter">Twitter</label><br>
        <input type="url" name="partnerportal_twitter" id="partnerportal_twitter" value="<?php echo isset($post_id) ? esc_attr($meta_data['partnerportal_twitter'][0] ?? '') : ''; ?>">
    </div>

    <!-- ==== SUBMIT FORM ==== -->
    <br style="clear:both;">
    <input class="mepr-button btn-outline btn btn-outline" type="submit" value="<?php echo isset($post_id) ? 'Update Listing' : 'Add Listing'; ?>">
</form>