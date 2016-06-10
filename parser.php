<?php
include_once "incl/bootstrap.php";

$posts = array();
if (count($argv) > 1) {
	$cls = $argv[1];
	$feed = new BlogFeed($cls);
	$feed->parse();
	file_put_contents("json/$cls.json", json_encode($feed->posts));
} else {
	$ar = array('Berlin', 'Polizei', 'Presse', 'Saarland', 'Zoll', 'Sachsen', 'Brandenburg');
	foreach ($ar as $cls){
		print ("parsing: $cls\n");
		$feed = new BlogFeed($cls);
		$feed->parse();
		file_put_contents("json/$cls.json", json_encode($feed->posts));
	}
}

// Load WordPress
// TODO: CHANGE Path to your WordPress
require_once 'path/to/www/wp-load.php';
require_once ABSPATH . '/wp-admin/includes/taxonomy.php';
// Set the timezone so times are calculated correctly
date_default_timezone_set('Europe/London');

$user_id = 1;
foreach ($feed->posts as $post){
	$id = wp_insert_post(array(
				'post_title'    => $post->title,
				'post_content'  => $post->content,
				'post_date'     => $post->date,
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
