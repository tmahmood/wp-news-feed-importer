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
		$img = $xpath->query('id("main")//img')->item(0)->getAttribute('src');
		return $img;
	}

	function get_missing_date($xpath, &$post)
	{
		$date = explode(',', $xpath->query('id("main")/div[@class="meta-data"]')->item(0)->nodeValue);
		$post->date = $date[1];
	}


}


