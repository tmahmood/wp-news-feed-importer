<?php
define('DEBUG', file_exists('.git'));
define('SCRIPT_ABSPATH', '/home/mahmood/Projects/leonardos_t/wordpress/feed_parser');

$paths = explode("\n", file_get_contents(SCRIPT_ABSPATH . '/.paths'));
define('IMG_STORE_PATH', __dir__ . $paths[0]);
define('IMG_SRC_PATH', $paths[1]);
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
	$content = sprintf($tmpl, $post->text, $post->link);
	if (post_exists($post->title, $content) !== 0) {
		echo "POST EXISTS\n";
		continue;
	}
	$category_id = category_exists($cat_name);
	if (!$category_id) {
		$category_id = wp_create_category($post->category);
	}
	$id = wp_insert_post(array(
				'post_title'    => $post->title,
				'post_content'  => $content,
				'post_author'   => $user_id,
				'post_type'     => 'post',
				'post_status'   => 'draft',
				'tax_input'		=> array('polizei report'),
				));
	if($id) {
		wp_set_post_terms($id, $category_id, 'category');
	} else {
		echo "WARNING: Failed to insert post into WordPress\n";
	}
	Utils::d($post->picture);
	foreach ($post->picture as $pic){
		Utils::upload_to_wordpress($pic, $id);
	}
}
