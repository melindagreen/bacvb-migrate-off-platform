<?php /**
 * This file contains reusable utility functions available to both parent and child themes.
 */

namespace MaddenMadre\Library;

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
	 * Return the parsed JSON value of the given path, or false
	 * @param string $path				The file's path
	 * @return array|boolean			The file's JSON data as an assoc array, or false
	 */
	public static function maybe_get_file_json( $path ) {
		if( file_exists( $path ) ) {
			$raw_str = \file_get_contents( $path );
            $json = \json_decode( $raw_str, true );
			if( !empty( $json ) ) return $json;
		}
		return false;
	}

	/**
	 * Get the theme-madre/nino.json settings for a particular section, overwriting 
	 * madre with niño if available
	 * @param string $section_key		The section to fetch
	 * @return array					The section's values, as an associative array
	 */
	public static function get_json_settings( $section_key ) {
		$madre_json_raw = self::maybe_get_file_json( get_template_directory() . '/theme-madre.json' );
		$madre_json_settings = $madre_json_raw &&  isset( $madre_json_raw['settings'][$section_key] ) 
			? $madre_json_raw['settings'][$section_key]
			: array();

		$nino_json_raw = self::maybe_get_file_json( get_stylesheet_directory() . '/theme-nino.json' );
		$nino_json_settings = $nino_json_raw &&  isset( $nino_json_raw['settings'][$section_key] ) 
			? $nino_json_raw['settings'][$section_key]
			: array();

		return array_replace_recursive( $madre_json_settings, $nino_json_settings );
	}

	/**
	 * Implode an array of strings into a "pretty" sentence.
	 * [ "a" ] -> "a"
	 * [ "a", "b" ] -> "a and b"
	 * [ "a", "b", "c" ] -> "a, b, and c"
	 * @param string[] $arr			The strings to join
	 * @return string				The joined string
	 */
	public static function fancy_implode( $arr ) {
		if( !is_array( $arr ) ) throw new Error( "fancy_implode expects an array parameter" );

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
	 * Get all authors in the current DB
	 */
	public static function get_all_authors() {
		global $wpdb;

		foreach ( $wpdb->get_results("SELECT DISTINCT post_author, COUNT(ID) AS count FROM $wpdb->posts WHERE post_type = 'post' AND " . get_private_posts_cap_sql( 'post' ) . " GROUP BY post_author") as $row ) :
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
	 * Recursively create a <ul> list of all child posts for the given post type and post ID.
	 * @param string $post_type_slug						The post type's slug
	 * @param string $post_type_slug						The post type's label
	 * @param int $post_id									The parent's ID
	 * @param int $depth									The current tree depth
	 * @return string										A string representing the <ul> element
	 */
	public static function the_child_tree_list( $post_type_slug, $post_type_label, $post_id = 0, $depth = 1 ) {
		$stdout = "";
	
		$header_level = $depth > 5 ? 6 : $depth + 1;
	
		$query_args = array(
			"post_type" => $post_type_slug,
			"posts_per_page" => -1,
			"post_parent" => $post_id,
		);
	
		$query = new \WP_Query( $query_args );
	
		if( $query->have_posts() ) {
			$classes = "sitemap-section";
	
			if( $depth === 1 ) {
				$stdout .= "<a name='$post_type_slug' class='post-type-link'></a><h$header_level data-posttype='$post_type_slug' class='post-type-title'>" 
					. $post_type_label
					."</h$header_level>";
				$classes .= " top-section";
			}
	
			$stdout .= "<ul id='sitemap-section-$post_type_slug' class='$classes'>";
			$loopData = array();
			while( $query->have_posts() ) {
				$query->the_post();
	
				$loopData[get_the_title()] = "<li class='sitemap-link'><a href='" . get_the_permalink() . "'>" . get_the_title() . "</a>"
					.self::the_child_tree_list( $post_type_slug, $post_type_label, get_the_ID(), $depth + 1 )
					."</li>";
			}
			ksort($loopData);
			$stdout .= implode("", $loopData);
			$stdout .= "</ul>";
		}
		
		$stdout .= "";
		
		return $stdout;
	}	
}
