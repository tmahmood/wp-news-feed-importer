<?php
define('DEBUG', file_exists('.git'));
define('SCRIPT_ABSPATH', __dir__);
define('IMG_STORE_PATH', __dir__ . '/imgs/');
define('IMG_SRC_PATH', __dir__.'/imgs');
include_once "incl/bootstrap.php";

// considering our app is inside WordPress directory
$parts = explode('/', __dir__);
array_pop($parts);
define('WORDPRESS_PATH', implode('/', $parts));
// Set the timezone so times are calculated correctly
date_default_timezone_set('Europe/Berlin');

function test_rdf()
{
	$link = 'http://www.polizei.bayern.de/muenchen/news/presse/aktuell/index.html/246302';
	$blogfeed = new BlogFeed('Polizei');
	$feed = new Polizei;
	$post = new BlogPost;
	$post->title = null;
	$post->link = $link;
	$post->date  = null;
	$post->category = null;
	$xpath = $feed->get_page_obj($post);
	$blogfeed->parse_source_link($post);
	print_r($post);
}

function test_berlin()
{
	$link = 'http://www.berlin.de/polizei/polizeimeldungen/pressemitteilung.504938.php';
	$blogfeed = new BlogFeed('Berlin');
	$feed = new Berlin;
	$post = new BlogPost;
	$post->title = 'sd';
	$post->link = $link;
	$post->date  = 'sd';
	$post->category = "Berlin (Polizei)";
	$xpath = $feed->get_page_obj($post);
	$blogfeed->parse_source_link($post);
	print ($post->text);
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
	echo preg_replace('/<!-(.*)->/', '', $post->text);
}

function test_brandenbur()
{
	$link = 'https://polizei.brandenburg.de/fahndung/einbruch-in-arztpraxis-wer-erkennt-den-m/351879';
	$blogfeed = new BlogFeed('Brandenburg');
	$feed = new Brandenburg;
	$post = new BlogPost;
	$item = json_decode('{"district": "500", "timestamp": 1470828300, "category": "8", "url": "/fahndung/polizei-bittet-bevoelkerung-um-mithilfe/351871", "title": "Polizei bittet Bevölkerung um Mithilfe", "text": "\nAktuell sucht die Kriminalpolizei in Potsdam nach noch drei unbekannten Männern. Diese sind verdächtigt, in der Silvesternacht vom 31.12.2013 auf den 01.101.2014 einen Briefkasten eines Verwaltungsgebäudes mittels Einsatz von Pyrotechnik erheblich beschädigt zu haben.\n\n \n\n \n\n \n\nDie Polizei fragt: Wer  kennt die Männer auf den abgebildeten Fotos und kann Hinweise zu deren Identität oder Aufenthaltsort machen? Ihre Hinweise richten Sie bitte unter der Telefonnummer: 0331 5508- 1224 an die Polizeiinspektion Potsdam oder jede andere Polizeidienstelle. Gerne können sie auch unser Hinweisformular im Internet nutzen. Dieses erreichen Sie unter: www.polizei.brandenburg.de\n\n \n\nTatzeit: 01.01.2014\n", "thumbnail": "/fm/24/thumbnails/PM%201874%20Potsdam%201.jpg.66864.jpg", "images": [ "/fm/24/thumbnails/PM%201874%20Potsdam%201.jpg.66863.jpg", "/fm/24/thumbnails/PM-%201874%20Potsdam.jpg.66871.jpg" ], "e_ort": "Potsdam, Nördliche Innenstadt, Friedrich-Ebert-Straße" }');
	$feed->parse($post, $item);
	print_r ($post);
}

test_rdf();

