<?php
define('SCRIPT_ABSPATH', __dir__);
include_once "incl/bootstrap.php";

// considering our app is inside WordPress directory
$parts = explode('/', __dir__);
array_pop($parts);
define('WORDPRESS_PATH', implode('/', $parts));
// Set the timezone so times are calculated correctly
date_default_timezone_set('Europe/Berlin');


$posts = array();
if (count($argv) > 1) {
	$cls = $argv[1];
	$feed = new BlogFeed($cls);
	$feed->parse_feed();
	file_put_contents(SCRIPT_ABSPATH . "/json/$cls.json",
					  json_encode($feed->posts));
} else {
	$ar = array('Berlin', 'Polizei', 'Presse', 'Saarland',
				'Zoll', 'Sachsen', 'Brandenburg');
	foreach ($ar as $cls){
		print ("parsing: $cls\n");
		$feed = new BlogFeed($cls);
		$feed->parse_feed();
		file_put_contents(SCRIPT_ABSPATH . "/json/$cls.json",
						  json_encode($feed->posts));
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
require_once WORDPRESS_PATH .'/wp-admin/includes/taxonomy.php';

$user_id = 1;
foreach ($posts as $post){
	$content = "$post->text<br>$post->picture<br><br>Zum Originalartikel: $post->link";
	$id = wp_insert_post(array(
				'post_title'    => $post->title,
				'post_content'  => $content,
				'post_date'     => date('Y-m-d H:i:s'),
				'post_author'   => $user_id,
				'post_type'     => 'post',
				'post_status'   => 'publish',
				));
	if($id) {
		// Set category - create if it doesn't exist yet
		wp_set_post_terms($id, wp_create_category($post->category), 'category');
	} else {
		echo "WARNING: Failed to insert post into WordPress\n";
	}
}
