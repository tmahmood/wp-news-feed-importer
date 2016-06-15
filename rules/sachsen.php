<?php

class Sachsen extends XML
{
	var $base_url = "https://www.polizei.sachsen.de/";
	var $feed_url = "https://www.polizei.sachsen.de/de/presse_rss_all.xml";
	var $category = null;
	var $txt_selector = "id('content')/div[3]";
	var $imgs_sel = "//div[@id='content']//img";

	function get_content($xpath)
	{
		$elements = $xpath->query($this->txt_selector)->item(0);
		if ($elements == null) {
			return '';
		}
		return Utils::clean_text($elements->nodeValue);
	}

	function get_missing_category($xpath, &$post)
	{
		$title = trim($post->title);
		$city = explode('-', $title);
		$post->category = trim($city[0]);
		$post->title = trim($post->title);
	}

	function get_missing_text($xpath, &$post)
	{
		print_r ($post);
	}

 	function get_image($xpath)
 	{
		$imgs = parent::get_image($xpath);
		$remove = array(
						'https://www.polizei.sachsen.de/navigation_internet_blau/symbole/blau/vanstrich.gif',
						'https://www.polizei.sachsen.de/navigation_internet_blau/symbole/blau/vanstrich.gif',
						'https://www.polizei.sachsen.de/navigation_internet_blau/symbole/blau/vanstrich_hoch.gif',
				);

		return trim(str_replace($remove, '', $imgs));
 	}
}

