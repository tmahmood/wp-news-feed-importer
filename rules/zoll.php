<?php

class Zoll extends XML
{
	var $base_url = 'http://www.zoll.de';
	var $category = "Zoll Deutschland (Bundesweite Meldungen des Dienstes “Zoll im Fokus”)";
	var $feed_url = "http://www.zoll.de/SiteGlobals/Functions/RSSFeed/DE/RSSNewsfeed/RSSZollImFokus.xml";
	var $text_cnt = 'id("main")/p';
	var $imgs_sel = 'id("main")//img';

	function get_content($xpath)
	{
		$elements = $xpath->query($this->text_cnt);
		$txt = array();
		foreach ($elements as $ky=>$element){
			$txt[] = $element->nodeValue;
		}
		return implode("", $txt);
	}

	function get_missing_text($xpath, &$post)
	{
		return '';
	}
}


