<?php
define('DEBUG', file_exists('.git'));
define('SCRIPT_ABSPATH', __dir__);
define('IMG_STORE_PATH', __dir__ . '/imgs/');
define('IMG_SRC_PATH', '/feed_parser/imgs/');
include_once "incl/bootstrap.php";

// considering our app is inside WordPress directory
$parts = explode('/', __dir__);
array_pop($parts);
define('WORDPRESS_PATH', implode('/', $parts));
// Set the timezone so times are calculated correctly
date_default_timezone_set('Europe/Berlin');

$link = 'http://www.polizei.bayern.de/fahndung/personen/straftaeter/unbekannt/index.html/243670';

$blogfeed = new BlogFeed('Polizei');
$feed = new Polizei;
$post = new BlogPost;
$post->title = null;
$post->link = $link;
$post->date  = null;
$post->category = null;
$xpath = $feed->get_page_obj($post);
$blogfeed->parse_source_link($post);
$blogfeed->fill_missing_data($xpath, $post);
Utils::d($post);
