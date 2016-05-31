<?php

class Berlin extends XML
{
	var $feed_url = "http://www.berlin.de/polizei/polizeimeldungen/index.php/rss";
	var $category = "Berlin (Polizei)";
	var $text_cnt = '//div[@class="html5-section article"]//div[@class="textile"]/p';

	function get_content($xpath)
	{
		$elments = $xpath->query($this->text_cnt);
		$txt = array();
		foreach ($elments as $ky=>$element){
			$txt[] = $element->nodeValue;
		}
		$h = array_shift($txt);
		assert(strpost($h, 'Nr.') != 0, "check description parsing, Nr. missing");
		return implode("", $txt);
	}

	function get_image($xpath)
	{
		return "";
	}
}
