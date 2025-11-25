<?php

namespace Eventastic\Admin;

/**
 * Meta box for events page information
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 *
 * Copyright (c) 2020 Madden Media
 */

require_once(__DIR__.'/AbstractMetaBox.php');
require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/FormControlLayout.php');
require_once(__DIR__.'/../library/Utilities.php');


use Eventastic\Admin\AbstractMetaBox as AbstractMetaBox;
use Eventastic\Library\Constants as Constants;
use Eventastic\Library\FormControlLayout as FormControlLayout;
use Eventastic\Library\Utilities as Utilities;


class MetaBoxEventsPage extends AbstractMetaBox {

    public const ID = "event_page_information";
    public const TITLE = "Events Page Information";

    public const META_KEY_HERO_IMAGE =    array("key" => Constants::WP_POST_META_KEY_PREPEND."hero_image", "label" => "Hero Image");
    public const META_KEY_HEADLINE =    array("key" => Constants::WP_POST_META_KEY_PREPEND."headline", "label" => "Headline");

    /**
     * Constructor
     */
    public function __construct () {

        parent::__construct(self::ID, self::TITLE);

        // add our save action
        add_action('save_post', array($this, 'saveMetaBoxData'));
    }

    /**
     * Renders the content of the meta box
     *
     * @param object $post The parent post data
     * @return void
     */
    public function displayMetaBox ($post) {

        // get the post meta
        $postMeta = get_post_meta($post->ID);

        // current gallery images
        $curGalleryImage = $postMeta[self::META_KEY_HERO_IMAGE["key"]][0];

        // security
        wp_nonce_field(self::$NONCE_BASE, Constants::NONCE_ROOT.self::ID);

        // image manager
        echo '<div id="eventasticGalleryThumbs">';

        // add existing thumbs
        $sizes = get_intermediate_image_sizes($curGalleryImage);
        $smallestSize = -1;
        $thumbURL = "";
        foreach ($sizes as $size) {
            $sizeData = wp_get_attachment_image_src($curGalleryImage, $size);
            if (is_array($sizeData)) {
                if ( ($smallestSize == -1) || ($sizeData[2] < $smallestSize) ) {
                    $thumbURL = $sizeData[0];
                    $smallestSize = $sizeData[2];
                }
            }
        }
        if ($thumbURL != "") {
            echo '<div class="eventasticImageWrap" data-id="'.$curGalleryImage.'"><img src="'.$thumbURL.'" /></div>';
        }
        echo '</div>';
        echo FormControlLayout::renderHiddenInput($postMeta, self::META_KEY_HERO_IMAGE);
        echo '<input id="eventasticUploadImage" type="button" class="button-primary" value="Choose Image" /><br><br>';

        echo FormControlLayout::renderTextInput($postMeta, self::META_KEY_HEADLINE, "headline", true, true);

?>
<script type="text/javascript">
    jQuery(document).ready(function($){
        var mediaUploader;

        // any current value for images? if not, make it an empty array
        $('#<?php echo self::META_KEY_HERO_IMAGE["key"] ?>').val('<?php $curGalleryImage; ?>');

        // listen to existing images in gallery for delete
        $('.eventasticImageWrap').on('click', function(event) {
            // if the click is not an image, we know it was the psuedo element
            if (event.target.tagName != "IMG") {
                var id = $(event.target).data("id");
                if (id !== 'undefined') {
                    // remove it
                    var attachId = $('#<?php echo self::META_KEY_HERO_IMAGE["key"] ?>').val();
                    $('#<?php echo self::META_KEY_HERO_IMAGE["key"] ?>').val('');
                    $(this).fadeOut(300, function() { $(this).remove(); });
                }
            }
        });

        // listen for upload
        $('#eventasticUploadImage').click(function(e) {
            e.preventDefault();
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                }, multiple: false });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                var smallestSize = -1;
                var thumbURL = "";

                for (var s in attachment.sizes) {
                    if ( (smallestSize == -1) || (attachment.sizes[s].height < smallestSize) ) {
                        thumbURL = attachment.sizes[s].url;
                        smallestSize = attachment.sizes[s].height;
                    }
                }
                var attachId = attachment.id
                $('#<?php echo self::META_KEY_HERO_IMAGE["key"] ?>').val(attachId);
                if (thumbURL != "") {
                    var newImg = '<div class="eventasticImageWrap" data-id="' + attachment.id + '"><img src="' + thumbURL + '" /></div>';
                    $('#eventasticGalleryThumbs .eventasticImageWrap').remove();
                    $(newImg).appendTo($('#eventasticGalleryThumbs')).fadeIn();
                }
            });
            mediaUploader.open();
        });
    });
</script>
<?php
    }

    /**
     * Event meta box content save upon submission
     *
     * @param int $postId The related post id
     * @return void
     */
    public static function saveMetaBoxData ($postId) {

        // need to decode our data first
        $localPost = $_POST;
        $localPost[self::META_KEY_HERO_IMAGE["key"]] = (isset($localPost[self::META_KEY_HERO_IMAGE["key"]])) ? json_decode($localPost[self::META_KEY_HERO_IMAGE["key"]]) : '';

        parent::_savePassedData(
            self::ID,
            array(
                self::META_KEY_HERO_IMAGE["key"],
                self::META_KEY_HEADLINE["key"]
            ),
            $postId,
            $localPost
        );
    }

    /**
     * Add the box to the view
     */
    public function addMetaBox () {
        if ( ($this->id == "") || ($this->title == "") ) {
            // MAY EXIT THIS BLOCK
            return;
        }
        global $post;
        if(!empty($post)) {
            $pageTemplate = get_post_meta($post->ID, '_wp_page_template', true);
            if($pageTemplate == '../eventastic-theme-files/templates/eventastic-events.php' ) {
                add_meta_box(
                    $this->id,
                    __($this->title, Utilities::getPluginPostType()),
                    array($this, 'displayMetaBox'),
                    'page',
                    'normal',
                    'default'
                );
            }
        }
    }
}
