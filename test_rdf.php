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

function test_rdf()
{
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
	echo $post->content;
}

function test_berlin()
{
	$link = 'http://www.berlin.de/polizei/polizeimeldungen/pressemitteilung.501514.php';
	$blogfeed = new BlogFeed('Berlin');
	$feed = new Berlin;
	$post = new BlogPost;
	$post->title = 'sd';
	$post->link = $link;
	$post->date  = 'sd';
	$post->category = "Berlin (Polizei)";
	$xpath = $feed->get_page_obj($post);
	$blogfeed->parse_source_link($post);
	print_r ($post->picture);
}

function test_zoll()
{
	$link = 'http://www.zoll.de/SharedDocs/Pressemitteilungen/DE/Produktpiraterie/2016/z83_plagiate_h.html';
	// $link = 'http://www.zoll.de/SharedDocs/Pressemitteilungen/DE/Sonstiges/2016/z12_horb_schroeder_stuttgart.html';
	$blogfeed = new BlogFeed('Zoll');
	$feed = new Zoll;
	$post = new BlogPost;
	$post->title = 'title';
	$post->link = $link;
	$post->date = 'd';
	$post->category = "Zoll Deutschland (Bundesweite Meldungen des Dienstes “Zoll im Fokus”)";
	$xpath = $feed->get_page_obj($post);
	$blogfeed->parse_source_link($post);
	Utils::d($post->picture);
}



function test_polizei()
{
	$link = 'http://www.polizei.bayern.de/oberfranken/fahndung/personen/tote/index.html/245358';
	$blogfeed = new BlogFeed('Polizei');
	$feed = new Polizei;
	$post = new BlogPost;
	$post->title = 'title';
	$post->link = $link;
	$post->date = 'd';
	$post->category = "Bayreuth (Polizeipräsidium Oberfranken)";
	$xpath = $feed->get_page_obj($post);
	$blogfeed->parse_source_link($post);
	Utils::d($post->text);
}

test_berlin();

