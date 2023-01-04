<?php /**
 * Reusable utility functions used across the site
 */

namespace MaddenNino\Library;

class Utilities {
	/**
	* Check if all keys exist within an associative array
	* @param array $keys               The keys to check for
	* @param array $arr                The array to check
	* @return boolean                  Do all keys exist?
	*/
	public static function array_keys_exist($keys, $arr) {
		foreach($keys as $key) {
			if(!array_key_exists($key, $arr)) return false;
		}
		return true;
	}

	/**
	 * Implode an array of strings into a human-readable list
	 * e.g. [1,2] -> "1 and 2", [1,2,3] -> "1, 2, and 3"
	 * @param string[] $arr				The elements to glue
	 * @return string					The glueued string
	 */
	public static function fancy_implode( $arr ) {
		if( !is_array( $arr ) ) return false;

		if( count( $arr ) === 0 ) return "";
		else if( count( $arr ) === 1 ) return $arr[0];
		else if( count( $arr ) === 2 ) return "{$arr[0]} and {$arr[1]}";
		else return implode( ", ", array_slice( $arr, 0, count( $arr ) - 1 ) ) . ", and " . $arr[count( $arr ) - 1];
	}

	/**
	 * Generate an HTML string representing an address from an array
	 * of meta-data
	 * @param string[] $address_fields			The address pieces
	 * @param boolean $breaks					Add line breaks?
	 * @param string[] $keys					Map address pieces to different keys	
	 * @return string					The resulting address string
	 */
	public static function generate_address( $address_fields, $breaks = true, $keys = array(
		'address_1' => 'address_1',
		'address_2' => 'address_2',
		'city' => 'city',
		'state' => 'state',
		'zip' => 'zip',
	) ) {
		$addr_str = '';

		if( $address_fields[ $keys['address_1'] ] ) $addr_str .= $address_fields[ $keys['address_1'] ];
		if( $address_fields[ $keys['address_2'] ] ) $addr_str .= ( $breaks ? '<br>' : ', ' ) . $address_fields[ $keys['address_2'] ];
		if( $address_fields[ $keys['address_1'] ] || $address_fields[ $keys['address_2'] ] ) $addr_str .= ( $breaks ? '<br>' : ', ' );
		if( $address_fields[ $keys['city'] ] ) $addr_str .= $address_fields[ $keys['city'] ];
		if( $address_fields[ $keys['city'] ] && ($address_fields[ $keys['state'] ] || $address_fields[ $keys['zip'] ] ) ) 
			$addr_str .= ', ';
		if ($address_fields[ $keys['state'] ] ) {
			$addr_str .= ' ';
			$addr_str .= ( is_array( $address_fields[ $keys['state' ] ] ) ) 
				? $address_fields[ $keys['state'] ][0] 
				: $address_fields[ $keys['state'] ];
		}
		if ( $address_fields[ $keys['zip'] ] ) $addr_str .= ' '.$address_fields[ $keys['zip'] ];
		return $addr_str;
	}

	/**
	 * Get all authors in the WordPress database
	 * @return array					An array of authors
	 */
	public static function get_all_authors() {
		global $wpdb;

		foreach ( $wpdb->get_results(
			"SELECT DISTINCT post_author, COUNT(ID) AS count 
			FROM $wpdb->posts 
			WHERE 
				post_type = 'post' 
				AND " . get_private_posts_cap_sql( 'post' ) . " 
			GROUP BY post_author"
		) as $row ) :
			$author = get_userdata( $row->post_author );
			if ($author->ID == 1) continue;
			$authors[$row->post_author]['name'] = $author->display_name;
			$authors[$row->post_author]['post_count'] = $row->count;
			$authors[$row->post_author]['posts_url'] = get_author_posts_url( $author->ID);
		endforeach;

		return $authors;
	}

	/**
	 * Generate an exceprt of the given length of the given post
	 * @param int $post_id					The ID of the post to excerpt
	 * @param int $sentences				How many sentences to return
	 * @return string						The excerpt
	 */
	public static function excerpt_by_sentences( $post_id, $sentences = 1, $min_length = 50 ) {
		$raw_excerpt = has_excerpt( $post_id ) 
			? get_the_excerpt( $post_id )
			: wp_strip_all_tags( get_the_content( null, false, $post_id ) );

		$pieces = preg_split( '/(?<!Mr.|Mrs.|Dr.|Ms.|Jr.|St.)(?<=[\.?!])\s+/', $raw_excerpt, -1, PREG_SPLIT_NO_EMPTY );

		$first = str_replace( '[…]', '…', implode( ' ', array_slice( $pieces, 0, $sentences ) ) );

		$next = $sentences;
		while( strlen( $first ) < $min_length && isset( $pieces[$next] ) ) {
			$first .= " " . $pieces[$next];
			$next += 1;
		}

		return $first;
	}

	/**
	* Search for the selected block within inner blocks.
	*
	* The helper function for enhanced_has_block() function.
	*
	* @param array $blocks Blocks to loop through.
	* @param string $block_name Full Block type to look for.
	* @return bool
	*/
	public static function search_reusable_blocks_within_innerblocks( $blocks, $block_name ) {
		foreach ( $blocks as $block ) {
			if ( isset( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) ) {
				self::search_reusable_blocks_within_innerblocks( $block['innerBlocks'], $block_name );
			} elseif ( 
				'core/block' === $block['blockName'] 
				&& ! empty( $block['attrs']['ref'] ) 
				&& has_block( $block_name, $block['attrs']['ref'] ) 
			) {
				return true;
			}
		}
 
		return false;
	}

	/**
	* Has block function which searches as well in reusable blocks.
	*
	* @param mixed $block_name Full Block type to look for.
	* @return bool
	*/
	public static function enhanced_has_block( $block_name ) {
		if ( has_block( $block_name ) ) {
			return true;
		}
 
		if ( has_block( 'core/block' ) ) {
			$content = get_post_field( 'post_content' );
			$blocks = parse_blocks( $content );
			return self::search_reusable_blocks_within_innerblocks( $blocks, $block_name );
		}
 
		return false;
	}

	/**
	 * Output HTML representing social icons with links from an array
	 * @param array $links					An array of social links to output
	 * @param string $links[]['path']		The path to the link's icon. Defaults to 
	 * 										/assets/images/icons/social/{$link_slug}.svg
	 * @param string $links[]['url']		The link's HREF
	 * @param string $links[]['name']		The social site's name
	 * @return null
	 */
	public static function the_social_links( $links ) {
		foreach( $links as $link_slug => $link ) {
			$icon_path = isset( $link['path'] ) 
				? $link['path'] 
				: get_stylesheet_directory() . "/assets/images/icons/social/{$link_slug}.svg";

			?><a class="social-icon" href="<?php echo $link['url'] ?>">
				<?php if( file_exists( $icon_path ) ) echo file_get_contents( $icon_path ); ?>
				<span class="sr-only"><?php echo $link['name']; ?></span>
			</a><?php
		}
	}
}
