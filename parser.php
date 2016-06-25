<?php
define('DEBUG', file_exists('.git'));
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
	$posts = $feed->posts;
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

if (DEBUG) {
	$tmpl = '%s<br>%s<br><br><a href="%s" alt="Zum Originalartikel">Zum Originalartikel</a>';
	foreach ($posts as $post){
		$content = sprintf($tmpl, $post->picture, $post->text, $post->link);
		print ("$content\n\n");
	}
	exit();
}

// Load WordPress
require_once WORDPRESS_PATH .'/wp-load.php';
require_once WORDPRESS_PATH .'/wp-admin/includes/taxonomy.php';
require_once WORDPRESS_PATH .'/wp-admin/includes/file.php';

$user_id = 1;
$tmpl = '%s<br>%s<br><br><a href="%s" alt="Zum Originalartikel">Zum Originalartikel</a>';
foreach ($posts as $post){
	$content = sprintf($tmpl, $post->picture, $post->text, $post->link);
	if (post_exists($post->title, $content) !== 0) {
		echo "POST EXISTS\n";
		continue;
	}
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
