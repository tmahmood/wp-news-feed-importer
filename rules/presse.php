<?php

class Presse extends XML
{
	var $category = "Polizeimeldungen des Landes Sachsen-Anhalt";
	var $feed_url = "http://www.presse.sachsen-anhalt.de/rss2.php?gruppe=1";
	var $text_cnt = '//p';

	function get_content($xpath)
	{
		$elements = $xpath->query($this->text_cnt);
		$txt = array();
		foreach ($elements as $ky=>$element){
			$txt[] = $element->nodeValue;
		}
		return implode("\n\n", $txt);
	}

	function get_image($xpath)
	{
		return "";
	}
}
