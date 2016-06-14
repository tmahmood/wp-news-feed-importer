<?php

class Zoll extends XML
{
	var $category = "Zoll Deutschland (Bundesweite Meldungen des Dienstes “Zoll im Fokus”)";
	var $feed_url = "http://www.zoll.de/SiteGlobals/Functions/RSSFeed/DE/RSSNewsfeed/RSSZollImFokus.xml";
	var $text_cnt = 'id("main")/p';

	function get_content($xpath)
	{
		$elements = $xpath->query($this->text_cnt);
		$txt = array();
		foreach ($elements as $ky=>$element){
			$txt[] = $element->nodeValue;
		}
		return implode("", $txt);
	}

	function get_image($xpath)
	{
		$images = $xpath->query('id("main")//img');
		$imgs = array();
		if (count($images) == 0) {
			return "";
		}
		if ($img == null) {
			return '';
		}
		return $img->getAttribute('src');
	}

	function get_missing_date($xpath, &$post)
	{
		$val = $xpath->query('id("main")/div[@class="meta-data"]')
								->item(0)
								->nodeValue;
		$date = explode(',', $val);
		if (count($date) < 1) {
			$post->date = $date[1];
		}
	}

	function get_missing_text($xpath, &$post)
	{
		return '';
	}
}


