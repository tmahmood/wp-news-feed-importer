<?php

class Zoll extends XML
{
	var $base_url = 'http://www.zoll.de';
	var $category = "Zoll Deutschland (Bundesweite Meldungen des Dienstes “Zoll im Fokus”)";
	var $feed_url = "http://www.zoll.de/SiteGlobals/Functions/RSSFeed/DE/RSSNewsfeed/RSSZollImFokus.xml";
	var $text_cnt = 'id("main")/p';
	var $imgs_sel = 'id("main")//img';
	var $custom_image_src = true;

	function get_content($xpath)
	{
		$elements = $xpath->query($this->text_cnt);
		$txt = array();
		foreach ($elements as $ky=>$element){
			$txt[] = $element->nodeValue;
		}
		$txt = implode("", $txt);
		if (trim($txt) == '') {
			return trim($xpath->query('id("main")')[0]->nodeValue);
		}
		return $txt;
	}

	function get_image_custom($imgelm)
	{
		$src = $imgelm->getAttribute('src');
		$fullurl = str_replace('__blob=wide', '__blob=normal', $src);
		if (!$this->is_full_url($fullurl)) {
			$fullurl = $this->base_url . $fullurl;
		}
		$headers = get_headers($fullurl);
		if (strpos($headers[0], '404') > 0) {
			return $src;
		}
		return $fullurl;
	}

	function get_missing_text($xpath, &$post)
	{
		return '';
	}
}


