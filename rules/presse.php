<?php

class Presse extends XML
{
	var $category = "Polizeimeldungen des Landes Sachsen-Anhalt";
	var $feed_url = "http://www.presse.sachsen-anhalt.de/rss2.php?gruppe=1";
	var $text_cnt = '//p';
	var $imgs_sel = '//img';
	var $base_url = 'http://www.presse.sachsen-anhalt.de/';
	var $bad_url  = array();

	function get_content($xpath)
	{
		$elements = $xpath->query($this->text_cnt);
		$txt = array();
		foreach ($elements as $ky=>$element){
			$txt[] = $element->nodeValue;
		}
		$txt = implode("", $txt);
		return Utils::clean_text($txt);
	}
}
