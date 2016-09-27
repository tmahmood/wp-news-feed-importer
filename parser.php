<?php
define('DEBUG', file_exists('.git'));
if (DEBUG) {
	define('SCRIPT_ABSPATH', '/home/mahmood/Projects/leo_t/wordpress/feed_parser');
} else {
	define('SCRIPT_ABSPATH', __dir__);
}
define('IMG_STORE_PATH', __dir__ . '/imgs/');
define('IMG_SRC_PATH', '');
include_once "incl/bootstrap.php";

// considering our app is inside WordPress directory
$parts = explode('/', SCRIPT_ABSPATH);
array_pop($parts);
define('WORDPRESS_PATH', implode('/', $parts));
// Set the timezone so times are calculated correctly
date_default_timezone_set('Europe/Berlin');


$posts = array();
if (count($argv) > 1) {
	$cls = $argv[1];
	$parts = explode(',', $cls);
	if (count($parts) > 1) {
		foreach ($parts as $part){
			$feed = new BlogFeed($part);
			$feed->parse_feed();
			$posts = array_merge($feed->posts, $posts);
		}
	} else {
		$feed = new BlogFeed($cls);
		$feed->parse_feed();
		$posts = $feed->posts;
	}
} else {
	$ar = array('Berlin', 'Polizei', 'Presse', 'Saarland',
				'Zoll', 'Sachsen', 'Brandenburg');
	foreach ($ar as $cls){
		$dt = new \DateTime;
		printf("[%s] - parsing: %s\n", $dt->format('d/m/Y - h:m'), $cls);
		$feed = new BlogFeed($cls);
		$feed->parse_feed();
		$posts = array_merge($posts, $feed->posts);
	}
}

// unregister our autoload fuction so that
// it does not conflict with Wordpresses  autoloader
$functions = spl_autoload_functions();
foreach($functions as $function) {
	spl_autoload_unregister($function);
}

// Load WordPress
require_once WORDPRESS_PATH .'/wp-load.php';
require_once WORDPRESS_PATH .'/wp-admin/includes/post.php';
require_once WORDPRESS_PATH .'/wp-admin/includes/taxonomy.php';
require_once WORDPRESS_PATH .'/wp-admin/includes/file.php';
require_once WORDPRESS_PATH .'/wp-admin/includes/media.php';


$user_id = 9;
$tmpl = '%s<br><br><a href="%s" alt="Zum Originalartikel">Zum Originalartikel</a>';
foreach ($posts as $post){
	print_r ($post);
	$content = sprintf($tmpl, $post->text, $post->link);
	if (post_exists($post->title, $content) !== 0) {
		echo "x";
		continue;
	} else {
		echo ".";
	}
	if(isset($post->category_slug)) {
		$category_id = get_category_by_slug($post->category_slug)->term_id;
	} elseif (isset($post->parent_category)) {
		$category_id = wp_create_category($post->category, $post->parent_category);
	} else {
		$category_id = wp_create_category($post->category);
	}
	$id = wp_insert_post(array(
				'post_title'    => $post->title,
				'post_content'  => $content,
				'post_author'   => $user_id,
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'tax_input'		=> array('polizei report'),
				));
	if($id) {
		wp_set_post_terms($id, $category_id, 'category');
	} else {
		echo "WARNING: Failed to insert post into WordPress\n";
		continue;
	}
	$cnt_pictures = count($post->picture);
	if (!is_array($post->picture) || $cnt_pictures == 0) {
		continue;
	}
	$imgs = array();
	foreach ($post->picture as $ky=>$pic){
		$attachment_id = Utils::upload_to_wordpress($pic, $id);
		if ($attachment_id == null) {
			continue;
		}
		$wp_img = wp_get_attachment_link($attachment_id);
		if ($ky < $cnt_pictures - 1) {
			$wp_img = str_replace('class="', 'class="alignleft ', $wp_img);
		}
		$imgs[] = $wp_img;
	}
	if (count($imgs) == 0) {
		continue;
	}
	$imgs_txt = implode("", $imgs);
	$content = sprintf("%s<br><br>%s", $imgs_txt, $content);
	wp_update_post(array(
		'ID' =>  $id,
		'post_content' => $content,
	));
}
