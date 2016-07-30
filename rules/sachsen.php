<?php

class Sachsen extends XML
{
	var $base_url = "https://www.polizei.sachsen.de/de/";
	var $feed_url = "https://www.polizei.sachsen.de/de/presse_rss_all.xml";
	var $category = null;
	var $txt_selector = "id('content')/div[3]";
	var $imgs_sel = "//div[@id='content']//img";
	var $bad_url = array(
		'navigation_internet_blau/symbole/blau/vanstrich.gif',
		'navigation_internet_blau/symbole/blau/vanstrich.gif',
		'navigation_internet_blau/symbole/blau/vanstrich_hoch.gif',
		);
	var $category_slug = array(
		'PD Chemnitz'=> 'pd-chemnitz',
		'PD Leipzig' => 'pd-leipzig',
		'PD Dresden' => 'pd-dresden',
	);

	function get_content($xpath)
	{
		$content_div = $xpath->query($this->txt_selector)->item(0);
		if ($content_div == null) {
			return '';
		}
		return $this->_get_inner_html($content_div);
	}

	function ignore_content($node)
	{
		if ($node->nodeName == 'strong') {
			return true;
		}
		return false;
	}


	function get_missing_category($xpath, &$post)
	{
		$title = trim($post->title);
		$city = explode('-', $title);
		$post->category = trim($city[0]);
		$post->title = $title;
	}

	function get_missing_text($xpath, &$post)
	{
		print_r ($post);
	}

}

