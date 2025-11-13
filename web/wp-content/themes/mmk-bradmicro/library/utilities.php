<?php /**
 * Reusable utility functions used across the site
 */

namespace MaddenTheme\Library;

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

  /**
   * Return custom color attributes
   */
  public static function custom_color_attributes( $attributes, $args ) {
    $data = [
      'id' => ( isset( $args['id'] ) && '' != $args['id'] ) ? $args['id'] : false,
    ];
    $classes = ( isset( $args['class'] ) && '' != $args['class'] ) ? explode( ' ', $args['class'] ) :  [];
    $styles = [];

    if ( isset( $args['color'] ) ) {
      if ( isset( $attributes[$args['color']] ) ) {
        $classes[] = "has-{$attributes[$args['color']]}-color";
      } else {
        $custom_key = "custom" . ucfirst( $args['color'] );
        if ( isset( $attributes[$custom_key] ) ) {
          $styles[] = "color:{$attributes[$custom_key]};";
        }
      }
    }
    if ( isset( $args['background_color'] ) ) {
      if ( isset( $attributes[$args['background_color']] ) ) {
        $classes[] = "has-{$attributes[$args['background_color']]}-background-color";
      } else {
        $custom_key = "custom" . ucfirst( $args['background_color'] );
        if ( isset( $attributes[$custom_key] ) ) {
          $styles[] = "background-color:{$attributes[$custom_key]};";
        }
      }
    }

    // Sanitize and add classes
    $sanitized_classes = array_map( 'sanitize_html_class', $classes );
    $data['class'] = implode( ' ', $sanitized_classes );

    // Sanitize and add styles
    if ( isset( $args['style'] ) ) {
      if ( is_array( $args['style'] ) && count( $args['style'] ) > 0 ) {
        foreach ( $args['style'] as $key => $value ) {
          $styles[] = "{$key}:{$value};";
        }
      }
    }
    $data['style'] = implode( '', $styles );

    // Convert data to string
    $html_array = array();
    if ( is_array( $data ) && count( $data ) > 0 ) {
      foreach ( $data as $name => $value ) {
        if ( false === $value || '' === $value ) {
          continue;
        }
        $html_array[] = $name . '="' . esc_attr( $value ) . '"';
      }
    }
    if ( ! empty( $html_array ) ) {
      return implode( ' ', $html_array );
    }
  }

  /**
   * Write to the error log
   */
  public static function write_log( $log ) {
    error_log( print_r( $log, true ) );
  }
}
