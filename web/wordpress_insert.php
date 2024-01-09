<?php
/**
 * A script to take the results of a site scrape run (via the companion script in this package)
 *	and insert the resulting scraped HTML content into a WordPress database as pages or posts.
 *
 * This script should be placed in the web root for the WordPress site.
 *  
 * License: Attribution-NonCommercial-NoDerivs 3.0 Unported (CC BY-NC-ND 3.0)
 *
 * Copyright (c) 2022 Madden Media
 */

ini_set('max_execution_time', 3600);
ini_set('max_input_time', 3600);
set_time_limit(3600);

if (php_sapi_name() === "cli") {
	die("Please run this via a web browser URL.");
}

// constants
$VALID_PROJECT_JSONS = [ "composer.json" ];
$SQL_DATETIME_FORMAT = "Y-m-d H:i:s";
$WP_POSTMETA_ORGINAL_URL_KEY = "original_tpb_url";
$WP_POSTMETA_SEO_TITLE_KEY = "_yoast_wpseo_title";

// vars
$wpRoot = null;
$wpLoadFile = "wp-load.php";
$jsonDataFile = null;
$paramTitleStrip = (isset($_POST["title_strip"])) ? $_POST["title_strip"] : "";
$paramPostMap = (isset($_POST["post_map"])) ? json_decode($_POST["post_map"], JSON_OBJECT_AS_ARRAY) : [];
$doRun = (isset($_POST["run"])) ? true : false;

// attempt to find the load file
$wpRoot = localFindWordPressRoot();
if ( ($wpRoot != null) && (! is_file("{$wpRoot}/{$wpLoadFile}")) ) {
	exit(
		"Error: {$wpRoot}/{$wpLoadFile} was not found. Exiting."."<br/>"
	);
} else if ($wpRoot == null) {
	exit(
		"Error: Could not find a WordPress wp-load.php file. Exiting."."<br/>"
	);
}

//
// the data collection form
//

$self = $_SERVER["PHP_SELF"];
$prettyParamPostMap = (! empty($paramPostMap)) ? json_encode($paramPostMap, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : "";
echo <<<FORM
<!doctype html><html>
<head><style>label { font-weight: bold; }</style></head>
<body>
<h1>Data</h1>
<p>Provide your data for processing and get going. Note that this will take a long time, so consider breaking up your JSON files.</p>
<p><b>Please ensure that your site has "Organize my uploads into month- and year-based folders" unchecked in Media > Settings!</b></p>

<form action="$self" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="run" value="yup" />

	<label for="json_file">JSON data file from scraping</label><br/>
	<input type="file" name="json_file" required><br/><br/>

	<label for="title_strip">Optional title strip (e.g. string added to every page title from site that is unneeded)</label><br/>
	<input size="40" type="text" name="title_strip" placeholder=" | Madden Media" value="$paramTitleStrip" /><br/><br/>

	<label for="post_map">Optional JSON slug to post type map - all entries default to "page" if blank</label><br/>
	<textarea rows="10" cols="40" name="post_map" placeholder='{\n\t"/blog": "post"\n}'>$prettyParamPostMap</textarea><br/><br/>

	<button>Begin Processing</button>
</form>
FORM;

//
// are we running?
//
if ($doRun) {

	// load the json file they gave us
	$jsonDataFile = json_decode(file_get_contents($_FILES["json_file"]["tmp_name"]), JSON_OBJECT_AS_ARRAY);
	if ( ($jsonDataFile == null) || (! $jsonDataFile) ) {
		exit(
			"Error: The provided JSON file was not parsesable. Exiting."."<br/>"
		);
	}

	// Sort the json by slug path
	usort($jsonDataFile, function($a, $b) {
		return strcmp(substr_count($a['slug'], '/'), substr_count($b['slug'], '/'));
	});
	
	// we are good!
	define('WP_USE_THEMES', false);
	require_once("{$wpRoot}/{$wpLoadFile}");
	require_once ABSPATH."wp-admin/includes/media.php";
	require_once ABSPATH."wp-admin/includes/file.php";
	require_once ABSPATH."wp-admin/includes/image.php";

	global $wpdb;

	// start our process dump
	$startTime = new DateTime();
	$counter = 0;
	echo "<br/><hr><code>";
	echo "<br/>"."Start: ".date("r", $startTime->getTimestamp())."<br/>";

	// walk through the data and do the inserting stuff
	foreach ($jsonDataFile as $url => $data) {

		$blogList = [
			"/pages/399/christmas-events-in-the-bradenton-area",
			"/pages/397/refreshed-reimagined-two-ami-resorts-debut-fresh-new-looks",
			"/pages/396/local-color-arte-caffe",
			"/pages/395/thanksgiving-dining-2023",
			"/pages/394/paddleboarding-101",
			"/pages/393/dining-under-the-stars",
			"/pages/387/bradenton-area-markets",
			"/pages/385/5-things-to-know-about-stone-crab-season-in-the-bradenton-area",
			"/pages/383/its-festival-season",
			"/pages/382/mornings-over-easy-best-breakfast-places-in-the-bradenton-area",
			"/pages/380/get-an-underwater-view-of-marine-life-while-staying-dry",
			"/pages/379/local-seafood-is-sustainable-seafood",
			"/pages/378/six-ways-to-eat-like-a-local",
			"/pages/377/toast-of-the-town-the-best-places-to-enjoy-a-locally-crafted-brew-in-the-bradenton-area",
			"/pages/376/enjoy-spring-weather-in-the-bradenton-area",
			"/pages/375/st-patricks-day-in-the-bradenton-area",
			"/pages/374/pi-day-the-bradenton-way",
			"/pages/371/where-to-spot-local-wildlife",
			"/pages/370/making-a-difference-for-our-manatees",
			"/pages/367/linger-lodge",
			"/pages/366/bring-your-best-buds-to-the-bradenton-area",
			"/pages/365/girls-trip-2022",
			"/pages/364/2022-fireworks",
			"/pages/356/top-5-places-to-watch-the-sunset-in-the-bradenton-area",
			"/pages/354/celebrating-fathers-day",
			"/pages/352/local-favorites-2022",
			"/pages/351/mothers-day-2022",
			"/pages/347/planning-your-destination-wedding-in-the-bradenton-area",
			"/pages/341/amazing-bradenton-area-vacation-rentals-on-vrbo",
			"/pages/339/all-about-our-parks-and-preserves",
			"/pages/337/behind-the-scenes-at-village-of-the-arts",
			"/pages/334/behind-the-scenes-at-the-florida-maritime-museum",
			"/pages/330/flying-solo-in-the-bradenton-area",
			"/pages/327/whats-new-at-the-bishop-museum-of-science-nature",
			"/pages/322/how-to-love-it-like-a-local",
			"/pages/321/tips-for-your-best-beach-day-ever",
			"/pages/320/the-many-beaches-of-anna-maria-island",
			"/pages/314/the-finest-of-fine-dining",
			"/pages/313/an-interview-with-a-bradenton-area-food-expert",
			"/pages/310/traveling-with-your-furry-friends",
			"/pages/306/a-captains-tale",
			"/pages/304/rolling-through-the-bradenton-area",
			"/pages/298/date-night-1613677758",
			"/pages/297/have-a-valentines-dinner-to-remember-in-the-bradenton-area",
			"/pages/294/the-best-spots-for-wellness-junkies-in-the-bradenton-area",
			"/pages/293/business-and-the-beach",
			"/pages/292/22-things-to-do-in-2022",
			"/pages/288/share-the-bradenton-area-with-everyone-on-your-list",
			"/pages/287/experience-the-bradenton-area-from-above",
			"/pages/281/nature-as-your-gym",
			"/pages/280/spots-to-explore-by-boat",
			"/pages/279/green-as-pine",
			"/pages/277/behind-the-scenes-at-manatee-village-historical-park",
			"/pages/276/artistic-food-adventures",
			"/pages/275/unique-art-experiences-in-the-bradenton-area",
			"/pages/268/eating-clean",
			"/pages/267/spooky-places-in-the-bradenton-area",
			"/pages/265/the-best-picnic-spots-in-the-bradenton-area",
			"/pages/264/the-best-places-to-dine-outdoors",
			"/pages/263/the-new-way-to-travel",
			"/pages/262/dining-in-cortez-fishing-village",
			"/pages/261/snorkeling-and-scuba-diving-in-the-bradenton-area",
			"/pages/260/fishing-bradenton-area-waters",
			"/pages/259/timeless-cortez",
			"/pages/258/staycation-what-youre-missing-on-ami",
			"/pages/257/kid-friendly-restaurants",
			"/pages/256/road-trip-tips",
			"/pages/255/summer-school-ways-to-make-your-bradenton-area-vacation-educational-and-fun",
			"/pages/254/7-tips-for-managing-a-stress-free-multigenerational-vacation",
			"/pages/252/meet-our-locals",
			"/pages/251/instagram-accounts-to-follow",
			"/pages/250/open-air-activities",
			"/pages/249/notable-nonprofits",
			"/pages/248/black-owned-businesses-in-the-bradenton-area",
			"/pages/246/an-empire-built-on-grapefruit",
			"/pages/245/the-not-so-secret-lives-of-cows",
			"/pages/244/for-the-birds",
			"/pages/243/the-agricultural-heritage-of-the-bradenton-area",
			"/pages/239/visiting-responsibly",
			"/pages/238/missing-the-bradenton-area-youre-virtually-here",
			"/pages/221/the-best-bites-for-the-beach",
			"/pages/220/behind-the-scenes-at-artcenter-manatee",
			"/pages/219/why-you-should-fly-through-srq",
			"/pages/218/sweets-for-your-sweetie",
			"/pages/215/dock-and-dine-best-boat-up-restaurants",
			"/pages/214/catch-a-game-of-americas-favorite-pastime-in-the-bradenton-area",
			"/pages/213/20-things-to-do-in-2020",
			"/pages/212/feed-your-need-for-speed-in-the-bradenton-area",
			"/pages/210/five-local-favorites-for-fresh-seafood",
			"/pages/209/coastal-birds-and-where-to-find-them",
			"/pages/207/date-night",
			"/pages/206/signature-events-festivals-and-more",
			"/pages/204/behind-the-scenes-at-the-bishop-museum-of-science-and-nature",
			"/pages/203/farmers-markets-u-pick-veggies-and-fresh-local-seafood-oh-my",
			"/pages/202/all-about-manatees",
			"/pages/201/unique-eats-in-the-bradenton-area",
			"/pages/192/behind-the-scenes-at-the-manatee-performing-arts-center",
			"/pages/189/great-places-for-live-music",
			"/pages/188/spotting-dolphins-in-bradenton-area-waters",
			"/pages/187/tips-and-tricks-for-traveling-with-the-little-ones",
			"/pages/186/which-beach-is-right-for-you",
			"/pages/185/shopping-on-bridge-street",
			"/pages/184/adrenaline-junkies",
			"/pages/183/the-inside-scoop-on-ice-cream-in-bradenton",
			"/pages/179/10-fast-facts-about-sea-turtles",
			"/pages/178/take-a-bite-out-of-the-bradenton-areas-best-burgers",
			"/pages/177/last-minute-travel-ideas",
			"/pages/176/do-not-forget-the-donuts",
			"/pages/175/road-trip-6-must-see-destinations-not-too-far-from-the-bradenton-area",
			"/pages/165/the-8-most-instagrammable-spots-in-bradenton",
			"/pages/163/pedal-paddle-picnic",
			"/pages/159/bradenton-for-kids",
			"/pages/158/from-our-family-to-your-family-welcome-silver-resorts",
			"/pages/157/get-your-hands-on-history",
			"/pages/150/6-ways-the-riverwalk-can-keep-the-romance-flowing",
			"/pages/149/seven-spalicious-places-to-renew-reflect-reconnect",
			"/pages/146/5-kid-proven-activities-for-family-fun",
			"/pages/143/exploring-the-bradenton-areas-preserves",
			"/pages/142/9-places-to-shop-where-youll-find-more-than-what-you-came-for",
			"/pages/140/5-reasons-to-visit-longboat-key",
			"/pages/138/brunch-like-a-boss",
			"/pages/136/six-spectacular-places-to-get-married-in-the-bradenton-area",
			"/pages/130/a-romantic-getaway-in-the-bradenton-area",
			"/pages/129/the-top-10-things-to-see-and-do-in-bradenton-1534356443",
			"/pages/124/things-you-probably-didnt-know-about-the-bradenton-area",
			"/pages/117/spring-training-2023",
			"/pages/116/get-to-know-the-bradenton-area-breweries",
			"/pages/111/things-to-do-when-the-beach-fatigue-sets-in",
			"/pages/110/your-guide-to-waterfront-dining-on-anna-maria-island",
			"/pages/109/celebrating-the-holidays-in-the-bradenton-area",
			"/pages/72/freshen-up-on-fish-tacos",
			"/pages/71/2023-100-years-of-baseball-in-bradenton",
			"/pages/66/19-things-to-do-in-2019",
			"/pages/64/lay-of-the-land",
			"/pages/63/bradenton-area-river-regatta"
		];

		$log = [];
		$imgLog = [];
		$wpPostInsertData = [];
		$postType = "page";
		$postAuthor = 1;
		$postDate = date($SQL_DATETIME_FORMAT, strtotime($data["last_updated"]));
		$postName = sanitize_title(basename($data["slug"]));
		$postStatus = "ADDED";
		$featuredImage;
		$tags = $data['tags'];
		array_push($tags, "imported");

		if(in_array($data["slug"], $blogList, true)) {

			$postType = "post";
		}

		// we can update any existing post with the post id - to get that, we can
		//    query the post meta table to find that id by our id
		$args = [
			"post_status" => "publish",
			"posts_per_page" => 1,
			"meta_key" => $WP_POSTMETA_ORGINAL_URL_KEY,
			"meta_value" => $url
		];
		$query = new WP_Query($args);
		if ($query->have_posts()) {
			$wpPostInsertData["ID"] = $query->posts[0]->ID;
			$postStatus = "UPDATED";
		}

		// figure out a post type from passed data
		if (! empty($paramPostMap)) {
			foreach ($paramPostMap as $slug => $type) {
				if (strpos($data["slug"], $slug) !== false) {
					$postType = $type;
					break;
				}
			}
		}

		// the scraping did some gutenberg prep to add tag comments. for images, that
		//	means that the tag includes the attachment id in it, which we don't yet 
		//	know. first we will insert the images, get back the ids, and then swap 
		//	them into the tags. 
		foreach ($data["img"] as $url => $imgData) {
			$attachmentId = localUploadOrUpdateImage($url);
			$imgData = maybe_unserialize($imgData);

			if (! is_wp_error($attachmentId)) {

				$newData = substr($data['img'][$url], 2, -1);

				// Split string into key-value pairs
				$pairs = explode('";', $newData);

				// Extract new_src and alt values
				$new_src = substr($pairs[1], strpos($pairs[1], ':"')+3);
				$new_src = parse_url($new_src, PHP_URL_PATH);
				$alt = isset($pairs[3]) ? substr($pairs[3], strpos($pairs[3], ':"') + 3) : '';
				$match_src = $new_src;

				// insert the alt text
				if (! empty($new_src)) {
					update_post_meta($attachmentId, "_wp_attachment_image_alt", $alt);
				}
				// update the original content gutenberg block with the attachment id
				// $data["content"] = str_replace(
				// 	"<!-- wp:image {\"id\":".md5($imgData["new_src"])."} -->",
				// 	"<!-- wp:image {\"id\":{$attachmentId}} -->",
				// 	$data["content"]);
				// update src url 

				$old_url = wp_get_attachment_url($attachmentId);
				$new_url = $new_src;
				
				if($data['featuredImage'] === $match_src) {

					$featuredImage = $new_url;
				}

				$data["content"] = str_replace($new_url, $old_url, $data["content"]);

				$imgLog[] = "[OK] Entry image processed: [{$attachmentId}] {$url}";
			} else {
				$imgLog[] = "[ERROR] Entry image not processed: {$url}";
				$imgLog[] = " - Errors:";
				$imgLog[] = print_r($attachmentId->errors, true);	
			}
		}

		//Grab category ids
		$categoryIds = [];
		if(!empty($data['categories'])){

			$categoryIds = get_category_ids_by_names($data['categories']);
		}

		// Get author id
		$authorId = create_author($data["author"]);

		// DEBUG
		// echo "==============================<br/><br/>";
		// print_r($data["content"]);
		// continue;

		// now build up the rest of the post data to insert
		$wpPostInsertData["post_title"] = $data["title"];
		$wpPostInsertData["post_content"] = $data["content"];
		$wpPostInsertData["post_author"] = $postAuthor;
		$wpPostInsertData["post_date"] = $postDate;
		$wpPostInsertData["post_date_gmt"] = $postDate;
		$wpPostInsertData["post_status"] = "publish";
        $wpPostInsertData["post_category"] = $categoryIds;
		$wpPostInsertData["comment_status"] = "closed";
		$wpPostInsertData["ping_status"] = "closed";
		$wpPostInsertData["post_name"] = $postName;
		$wpPostInsertData["post_modified"] = $postDate;
		$wpPostInsertData["post_modified_gmt"] = $postDate;
		$wpPostInsertData["post_parent"] = "0";
		$wpPostInsertData["menu_order"] = "0";
		$wpPostInsertData["post_type"] = $postType;
		$wpPostInsertData["tags_input"] = $tags;
		$wpPostInsertData["comment_count"] = "0";
		$wpPostInsertData["post_excerpt"] = $data["excerpt"];
		$wpPostInsertData["post_author"] = $authorId;

		// now do the post meta
		$postMeta = [
			$WP_POSTMETA_SEO_TITLE_KEY => str_replace($paramTitleStrip, "", $data["title"])
		];
		$wpPostInsertData["meta_input"] = $postMeta;

		// DEBUG
		// echo "==============================<br/><br/>";
		// print_r($wpPostInsertData);
		// continue;

		// and insert the post
		$pId = wp_insert_post($wpPostInsertData);

		// Add Parent Page if applicable
		if ($postType === 'page' && substr_count($data["slug"], '/') > 1) {
			$segments = explode('/', trim($data["slug"], '/')); 
			$previous_path = $segments[count($segments) - 2];
		
			$parent = get_page_by_path($previous_path);
		
			if ($parent) {
				$update_post = array(
					'ID'          => $pId,
					'post_parent' => $parent->ID,
				);
			} else {    
				array_push($tags, 'missing_slug');
				array_push($tags, $previous_path);
				$update_post = array(
					'ID'         => $pId,
					'tags_input' => $tags,
				);
			}
		
			$pF = wp_update_post($update_post);
			print_r('Updated Post: '. $pF);
		}

		$imageId = get_image_id_by_url(site_url() . $featuredImage);
		if ($imageId !== 0) {
	
            set_post_thumbnail($pId, $imageId);
            echo "Featured thumbnail set successfully.";
        } else {

            echo "Error setting featured thumbnail" . $imageId;
        }

		if (! is_wp_error($pId)) {
			$log[] = "[OK] Entry {$postStatus}: {$data["slug"]}";
			$counter++;
		} else {
			$log[] = "[ERROR] Entry {$postStatus} not processed: {$data["slug"]}";
			$log[] = " - Errors:";
			$log[] = print_r($pId->errors, true);
		}
		// log what we did for this entry out to stdout
		echo implode("<br/>", $log);
		echo "<br/>";
		echo implode("<br/>", $imgLog);
	}

	wp_reset_postdata();
	
	// duration
	$interval = $startTime->diff(new DateTime());

	// end our output
	echo "<br/>"."Finish: ".date("r")."<br/>";
	echo $interval->format("Duration: %H:%I:%S")."<br/>";

	echo "Entries fully processed: ".$counter."<br/>";
	echo "</code>";

}

echo "</body></html>";
exit;

//
// local functions
//

/**
 * Returns the discovered WordPress root relative to where this script is
 * 
 * @return mixed
 */
function localFindWordPressRoot () {
	$dir = dirname(__FILE__);
	do {
		if (file_exists("{$dir}/wp-load.php")) {
			// MAY EXIT THIS BLOCK
			return $dir;
		}
	} while ($dir = realpath("{$dir}/.."));

	return null;
}

/**
 * Sets a featured image from a URL for a post
 *
 * @param string $imageURL The image to download
 * @param string $postId The post id - if not specified, the image will not be tied to the post id
 * @param boolean $makeFeatured Make it the featured image?
 * @return string The attachment ID
 */
function localUploadOrUpdateImage ($imageURL, $postId=0, $makeFeatured=false) {

	global $wpdb;

	$attachmentId = null;

	// let's see if the image is already in the system
	$query = $wpdb->prepare(
		"SELECT ID, guid FROM {$wpdb->posts} WHERE guid LIKE '%s'", 
		"%/".basename($imageURL)
	);
	$attachmentId = $wpdb->get_var($query);

	if ($attachmentId != null) {

		// get the path for the attachment
		$curAttachmentPath = get_attached_file($attachmentId);

		echo "HAD {$attachmentId} | {$curAttachmentPath}<br/>".PHP_EOL;	

		// #safetyfirst
		if ( ($curAttachmentPath === false) || (! file_exists($curAttachmentPath)) ) {
			// MAY EXIT THIS BLOCK
			return $attachmentId;
		}

		// make a temp file of the new image
		$tempImg = tmpfile();
		fwrite($tempImg, file_get_contents($imageURL));
		$meta = stream_get_meta_data($tempImg);
		$tmpImgPath = $meta["uri"];

		// now compare the two images to see if they are the same
		if ( ($tmpImgPath != "") && (! localImagesAreEqual($curAttachmentPath, $tmpImgPath)) ) {
			// they aren't - we need to update the image
			if (copy($tmpImgPath, $curAttachmentPath)) {
				// and get the thumbnails in place
				wp_update_attachment_metadata($attachmentId,
					wp_generate_attachment_metadata($attachmentId, $curAttachmentPath)
				);
			}
		}
		fclose($tempImg);
	} else {
		// add the image anew to the parent post
		$attachmentId = media_sideload_image($imageURL, $postId, null, 'id');
		
		echo "ADDING {$postId} | {$imageURL}<br/>".PHP_EOL;
		
		if ($makeFeatured) {
			set_post_thumbnail($postId, $attachmentId);
		}
	}

	return $attachmentId;
}

/**
 * Compares two images to see if they are the same (danke https://stackoverflow.com/a/30114215)
 *
 * @param string $firstPath The first local image path
 * @param string $secondPath The second local image path
 * @param string $chunkSize The chunk size to use for comparison
 * @return boolean Are they equal or not?
 */
function localImagesAreEqual ($firstPath, $secondPath, $chunkSize=500) {

	// First check if file are not the same size as the fastest method
	if (filesize($firstPath) !== filesize($secondPath)) {
		// MAY EXIT THIS BLOCK
		return false;
	}

	// Compare the first ${chunkSize} bytes
	// This is fast and binary files will most likely be different
	$fp1 = fopen($firstPath, 'r');
	$fp2 = fopen($secondPath, 'r');
	$chunksAreEqual = fread($fp1, $chunkSize) == fread($fp2, $chunkSize);
	fclose($fp1);
	fclose($fp2);

	if (! $chunksAreEqual) {
		// MAY EXIT THIS BLOCK
		return false;
	}

	// Compare hashes
	// SHA1 calculates a bit faster than MD5
	$firstChecksum = sha1_file($firstPath);
	$secondChecksum = sha1_file($secondPath);
	if ($firstChecksum != $secondChecksum) {
		// MAY EXIT THIS BLOCK
		return false;
	}

	return true;
}

/**
 * Get category id's and if category does not exist create it.
 *
 * @param array $categoryNames names of the categories
 * @return array category ids
 */
function get_category_ids_by_names($category_names) {
    $category_ids = array();

    foreach ($category_names as $category_name) {
        // Check if the category exists by name.
        $category = get_term_by('name', $category_name, 'category');

        if ($category) {
            // If the category exists, add its ID to the result array.
            $category_ids[] = $category->term_id;
        } else {
            // If the category doesn't exist, create it and then add its ID to the result array.
            $category_args = array(
                'slug' => sanitize_title($category_name),
                'name' => $category_name,
                'taxonomy' => 'category',
            );

            $new_category = wp_insert_term($category_name, 'category', $category_args);

            if (!is_wp_error($new_category) && isset($new_category['term_id'])) {
                $category_ids[] = $new_category['term_id'];
            } else {
                // If there was an error creating the category, you might handle it accordingly or simply skip it.
            }
        }
    }

    return $category_ids;
}

/**
 * Grabs image by it's url
 *
 * @param array $image_url names of the categories
 * @return int image id 
 */
function get_image_id_by_url($image_url) {
    global $wpdb;
    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )); 
        return $attachment[0]; 
}

/**
 * Create author if does not exist
 *
 * @param string name of author
 * @return int author id
 */
function create_author($author_name) {
    $author = get_user_by('login', $author_name);

    if ($author) {
        return $author->ID;
    } 
    
	$user_data = array(
        'user_login' => $author_name,
        'user_nicename' => sanitize_title($author_name),
        'display_name' => $author_name,
        'role' => 'author',
    );

    $new_author_id = wp_insert_user($user_data);

	if (!is_wp_error($new_author_id)) {

        return $new_author_id;
    } 

	return 0;
}

?>