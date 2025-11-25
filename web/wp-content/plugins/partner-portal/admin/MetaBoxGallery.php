<?php

namespace PartnerPortal\Admin;

/**
 * Meta box for PartnerPortal gallery information
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 *
 * Copyright (c) 2020 Madden Media
 */

require_once(__DIR__.'/AbstractMetaBox.php');
require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/FormControlLayout.php');

use PartnerPortal\Admin\AbstractMetaBox as AbstractMetaBox;
use PartnerPortal\Library\Constants as Constants;
use PartnerPortal\Library\FormControlLayout as FormControlLayout;

class MetaBoxGallery extends AbstractMetaBox {

	//public const ID = "partnerportal_gallery_box";
	public const TITLE = "Partner Gallery";

//	public const META_KEY_GALLERY_IMAGES =	array("key" => Constants::WP_POST_META_KEY_PREPEND."gallery_images", "label" => "Gallery Images");

	/**
	 * Constructor
	 */
    public function __construct ( $metaBoxInput ) {
    	$this->ID = $metaBoxInput['key'] ? $metaBoxInput['key'] : $this->ID;
    	$title = $metaBoxInput['label'] ? $metaBoxInput['label'] : self::TITLE;
    	$this->max_images =  array_key_exists('max_images', $metaBoxInput) ? $metaBoxInput['max_images'] : null;
    	$this->instructions = $metaBoxInput['instructions'] ? $metaBoxInput['instructions'] : null;

    	$this->META_KEY_GALLERY_IMAGES = array("key" => $this->ID, "label" => "Gallery Images");
//error_log( print_r($this->META_KEY_GALLERY_IMAGES, true));
		parent::__construct( $this->ID , $title);
		// add our save action
		add_action('save_post', array($this, 'saveMetaBoxData'));
    }

    /**
     * Renders the content of the meta box
	 *
	 * @param object $post The parent post data
	 * @return void
     */
    public function displayMetaBox ($post, $options = []) {

		// get the post meta
		$postMeta = get_post_meta($post->ID);

		// current gallery images
		$meta_image_ids =  array_key_exists($this->META_KEY_GALLERY_IMAGES['key'], $postMeta ) ? $postMeta[$this->META_KEY_GALLERY_IMAGES['key']][0] : '';

		$curGalleryImages = [];
		if( isset( $meta_image_ids ) ){
			$curGalleryImages = json_decode($meta_image_ids);
		}

		// security
		wp_nonce_field(self::$NONCE_BASE, Constants::NONCE_ROOT.$this->ID);

		// instructions
		if( $this->instructions){
			echo "<p>".__($this->instructions)."</p>";
		}

		// image manager
		echo '<div class="gallery-wrapper" id="partnerportalGalleryThumbs' . $this->ID . '">';

		// add existing thumbs
		if( is_array( $curGalleryImages) && count( $curGalleryImages ) > 0 ){
			foreach ($curGalleryImages as $img) {
				$sizes = get_intermediate_image_sizes($img);
				$smallestSize = -1;
				$thumbURL = "";
				foreach ($sizes as $size) {
					$sizeData = wp_get_attachment_image_src($img, $size);
					if ( ($smallestSize == -1) || ($sizeData[2] < $smallestSize) ) {
						$thumbURL = $sizeData[0];
						$smallestSize = $sizeData[2];
					}
				}
				if ($thumbURL != "") {
					$imageUploadId = 'partnerportalUploadImage' . $this->ID;
					echo '<div class="image-wrapper ' . $imageUploadId . '" data-id="'.$img.'"><img src="'.$thumbURL.'" /><button class="un-button delete-img' . $this->ID .'" data-id="'.$img.'">Delete</button></div>';
				}
			}
		}
		else{
			$curGalleryImages = [];
		}
		echo '</div>';
		echo FormControlLayout::renderHiddenInput($postMeta, $this->META_KEY_GALLERY_IMAGES);
		$uploadStyle = "";
		if( $this->max_images && $this->max_images <= count($curGalleryImages) ){
			$uploadStyle = "disable";
		}
		$buttonText = $options['buttonText'] ? $options['buttonText'] : "Add Images to Gallery"; 
		echo '<input id="partnerportalUploadImage' . $this->ID . '" type="button" class="galleryUpload ' . $uploadStyle . ' button-primary" value="' . $buttonText . '" />';
		
		?>
		<style>
			.galleryUpload.disable{ display:none; }
			.gallery-wrapper{
				display: flex;
			}
		</style>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			var mediaUploader,
				max_images<?php echo $this->ID ?> = <?php echo ( $this->max_images ? $this->max_images : 0); ?>;
			function deleteImage( event ){
				// if the click is not an image, we know it was the psuedo element
				if (event.target.tagName != "IMG") {
					var id = $(event.target).data("id");

					if (id !== 'undefined') {
						// remove it
						var attachIds = JSON.parse($('#<?php echo $this->ID ?>').val());
						$.each(attachIds, function(i){
							if (attachIds[i] === id) {
								attachIds.splice(i, 1);
								return false;
							}
						});
						console.log('maximages:',max_images<?php echo $this->ID ?>);
						console.log('atachlength:',attachIds.length );
						if( max_images<?php echo $this->ID ?> && max_images<?php echo $this->ID ?> >= attachIds.length ){
							$('#partnerportalUploadImage<?php echo $this->ID; ?>').removeClass('disable');
						}
						else{
							if( max_images<?php echo $this->ID ?> ){
								$('#partnerportalUploadImage<?php echo $this->ID; ?>').addClass('disable');							
							}
						}
						$('#<?php echo $this->ID ?>').val(JSON.stringify(attachIds));
						console.log($(event.target));
						$(event.target).fadeOut(300, function() { $(this).remove(); });
						$(event.target).parent().fadeOut(300, function() { $(this).remove(); });
					}
				}
			}
			// any current value for images? if not, make it an empty array
			$('#<?php echo $this->ID ?>').val('<?php echo json_encode($curGalleryImages) ?>');

			// listen to existing images in gallery for delete
			$('.delete-img<?php echo $this->ID; ?>' ).on('click', function(event) {
				deleteImage(event);
			});

			// listen for upload
			$('#partnerportalUploadImage<?php echo $this->ID; ?>').click(function(e) {
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
					var attachIds = JSON.parse( $('#<?php echo $this->ID; ?>' ).val());
					attachIds.push(attachment.id);
					console.log(attachIds);
					console.log('after add: ', max_images<?php echo $this->ID ?>);
					console.log('#partnerportalUploadImage<?php echo $this->ID; ?>');
					if( max_images<?php echo $this->ID ?> && max_images<?php echo $this->ID ?> >= attachIds.length ){
						$('#partnerportalUploadImage<?php echo $this->ID; ?>').addClass('disable');
					}
					else{
						if( max_images<?php echo $this->ID ?> ){
							$('#partnerportalUploadImage<?php echo $this->ID; ?>').removeClass('disable');							
						}
					}
					$('#<?php echo $this->ID; ?>').val(JSON.stringify(attachIds));
					if (thumbURL != "") {
						var newImg = '<div class="partnerportalImageWrap<?php echo $this->ID; ?>" data-id="' + attachment.id + '"><img src="' + thumbURL + '" /><button class="un-button delete-img<?php echo $this->ID; ?>" data-id="' + attachment.id + '">Delete</button></div>';
						$(newImg).appendTo($('#partnerportalGalleryThumbs<?php echo $this->ID; ?>')).fadeIn();
						$('.delete-img<?php echo $this->ID; ?>' ).on('click', function(event) {
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
	 * Listings meta box content save upon submission
	 *
	 * @param int $postId The related post id
	 * @return void
	 */
	public static function saveMetaBoxData ($postId) {

		// need to decode our data first
		$localPost = $_POST;
		$localPost[$this->META_KEY_GALLERY_IMAGES["key"]] = (isset($localPost[$this->META_KEY_GALLERY_IMAGES["key"]])) ? json_decode($localPost[$this->META_KEY_GALLERY_IMAGES["key"]]) : '';

		parent::_savePassedData(
			$this->ID,
			array(
				$this->META_KEY_GALLERY_IMAGES["key"]
			),
			$postId,
			$localPost
		);
	}
}
