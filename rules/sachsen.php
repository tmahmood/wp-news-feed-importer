<?php

class Sachsen extends XML
{
	var $feed_url = "https://www.polizei.sachsen.de/de/presse_rss_all.xml";
	var $category = null;
	var $txt_selector = '//a[@name="#mi1"]/following-sibling::p';

	function get_content($xpath)
	{
		$elements = $xpath->query($this->txt_selector)->item(1);
		if ($elements != null) {
			return $elements->nodeValue;
		}
	}

	function get_image($xpath)
	{
		return "";
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

	}
}

