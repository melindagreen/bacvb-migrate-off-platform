<?php
namespace MaddenNino\Library\Memberpress;

class Utilities {

    /**
     * Unlinks the cloned post and 
     * @param int $original_post_id	The ID of the original post
	 * @param int $cloned_post_id		The ID of the cloned post
	 * @return bool					
     */
    public static function unlink_cloned_post($original_post_id, $cloned_post_id) {

        // Remove cloned_post_id meta data
        $original_res = delete_post_meta($original_post_id, 'cloned_post_id');
        // Remove original_post_id post meta from the original post
        $cloned_res = delete_post_meta($cloned_post_id, 'original_post_id');

        return $original_res || $cloned_res ? true : false;
    }

    /**
     * Checks if post is cloned
     * @param int $post_id	ID of post
	 * @return bool					
     */
    public static function is_cloned_post($post_id) {

        // Check if the original_post_id metadata exists for the post
        if (metadata_exists('post', $post_id, 'original_post_id')) {

            $original_post_id = get_post_meta($post_id, 'original_post_id', true);
            
            // Verify if the retrieved original_post_id is a valid post ID
            if ($original_post_id && get_post($original_post_id)) {
                // Verify post is not a clone of itself (deleting the same post)
                return $original_post_id != $post_id;
            }
        }

        return false;
    }

    /**
     * Gets the original post id from cloned post
     * @param int $post_id	ID of cloned
	 * @return mixed					
     */
    public static function get_original_post_id($post_id) {

        return get_post_meta($post_id, 'original_post_id', true);
    }

     /**
      * Gets the cloned post id from original post
     * @param int $post_id	ID of post
	 * @return mixed					
     */
    public static function get_cloned_post_id($post_id) {

        return get_post_meta($post_id, 'cloned_post_id', true);
    }
}