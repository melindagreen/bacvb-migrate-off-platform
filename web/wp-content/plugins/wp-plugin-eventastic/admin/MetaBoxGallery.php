<?php

namespace Eventastic\Admin;

/**
 * Meta box for event gallery information
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 *
 * Copyright (c) 2020 Madden Media
 */

require_once(__DIR__.'/AbstractMetaBox.php');
require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/FormControlLayout.php');

use Eventastic\Admin\AbstractMetaBox as AbstractMetaBox;
use Eventastic\Library\Constants as Constants;
use Eventastic\Library\FormControlLayout as FormControlLayout;

class MetaBoxGallery extends AbstractMetaBox {

	public const ID = "event_gallery_box";
	public const TITLE = "Event Gallery";

	public const META_KEY_GALLERY_IMAGES =	array("key" => Constants::WP_POST_META_KEY_PREPEND."gallery_images", "label" => "Gallery Images");

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
		$curGalleryImages = (isset($postMeta[self::META_KEY_GALLERY_IMAGES["key"]][0]) && $postMeta[self::META_KEY_GALLERY_IMAGES["key"]][0])
			? unserialize($postMeta[self::META_KEY_GALLERY_IMAGES["key"]][0])
			: array();

		// security
		wp_nonce_field(self::$NONCE_BASE, Constants::NONCE_ROOT.self::ID);

		// instructions
		echo "<p>" . 
			__("Add any optional gallery images for this event. The featured image should be added separately in the sidebar to the right and will be used as the event thumbnail.") .
		"</p>";

		// image manager
		echo '<div id="eventasticGalleryThumbs">';

		// add existing thumbs
	    $curGalleryImagesValid = [];
		if( $curGalleryImages && is_array( $curGalleryImages ) ) {
			foreach ( $curGalleryImages as $img ) {
				$sizes = get_intermediate_image_sizes( $img );
				$smallestSize = -1;
				$thumbURL = "";
				foreach ( $sizes as $size ) {
					$sizeData = wp_get_attachment_image_src( $img, $size );
          if ( $sizeData ) {
            if ( ( $smallestSize == -1 ) || ($sizeData[2] < $smallestSize ) ) {
              $thumbURL = $sizeData[0];
              $smallestSize = $sizeData[2];
            }
          }
				}
				if ( $thumbURL != "" ) {
					$curGalleryImagesValid[] = $img;					
					echo '<div class="eventasticImageWrap" data-id="'.$img.'"><img src="'.$thumbURL.'" /></div>';
				}
			}
		}

		echo '</div>';
		echo FormControlLayout::renderHiddenInput($postMeta, self::META_KEY_GALLERY_IMAGES);
		echo '<input id="eventasticUploadImage" type="button" class="button-primary" value="Add Image to Gallery..." />';

		?>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			var mediaUploader;

			// any current value for images? if not, make it an empty array
			$('#<?php echo self::META_KEY_GALLERY_IMAGES["key"] ?>').val('<?php echo json_encode($curGalleryImagesValid) ?>');

			function deleteImage( event ){
				// if the click is not an image, we know it was the psuedo element
				if (event.target.tagName != "IMG") {
					var id = $(event.target).data("id");
					if (id !== 'undefined') {
						// remove it
						var attachIds = JSON.parse($('#<?php echo self::META_KEY_GALLERY_IMAGES["key"] ?>').val());
						$.each(attachIds, function(i){
							if (attachIds[i] === id) {
								attachIds.splice(i, 1);
								return false;
							}
						});
						$('#<?php echo self::META_KEY_GALLERY_IMAGES["key"] ?>').val(JSON.stringify(attachIds));
						$(event.target).fadeOut(300, function() { $(event.target).remove(); });
					}
				}
			}

			// listen to existing images in gallery for delete
			$('.eventasticImageWrap').on('click', function(event) {
				deleteImage(event);
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
					var attachIds = JSON.parse($('#<?php echo self::META_KEY_GALLERY_IMAGES["key"] ?>').val());
					attachIds.push(attachment.id);
					$('#<?php echo self::META_KEY_GALLERY_IMAGES["key"] ?>').val(JSON.stringify(attachIds));
					if (thumbURL != "") {
						var newImg = '<div class="eventasticImageWrap" data-id="' + attachment.id + '"><img src="' + thumbURL + '" /></div>';
						$(newImg).appendTo($('#eventasticGalleryThumbs')).fadeIn();

						// listen to existing images in gallery for delete
						$('.eventasticImageWrap[data-id="' + attachment.id + '"]').on('click', function(event) {
							deleteImage(event);
						});
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
		$localPost[self::META_KEY_GALLERY_IMAGES["key"]] = (isset($localPost[self::META_KEY_GALLERY_IMAGES["key"]])) ? json_decode($localPost[self::META_KEY_GALLERY_IMAGES["key"]]) : '';

		parent::_savePassedData(
			self::ID,
			array(
				self::META_KEY_GALLERY_IMAGES["key"]
			),
			$postId,
			$localPost
		);
	}
}
