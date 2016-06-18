<?php

class Berlin extends XML
{
	var $base_url = 'http://www.berlin.de';
	var $feed_url = "http://www.berlin.de/polizei/polizeimeldungen/index.php/rss";
	var $category = "Berlin (Polizei)";
	var $text_cnt = '//div[@class="html5-section article"]//div[@class="textile"]/p';
	var $imgs_sel = '//div[@class="html5-section article"]//img';

	function get_content($xpath)
	{
		$elments = $xpath->query($this->text_cnt);
		$txt = array();
		foreach ($elments as $ky=>$element){
			$txt[] = trim($element->nodeValue);
		}
		$txt = implode('', $txt);
		return Utils::clean_text($txt);
	}

	function get_missing_text($xpath, &$post)
	{
		print_r ($post);
	}
}
