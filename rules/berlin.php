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
		if (count($txt) == 0) {
			$paras = explode("\n", $h, 3);
			return array_pop($paras);
		}
		return implode("", $txt);
	}

	function get_image($xpath)
	{
		$imgxpath = '//div[@class="html5-section article"]//img';
		$images = $xpath->query($imgxpath);
		if (count($images) == 0) {
			return "";
		}
		$imgs = array();
		foreach ($images as $img){
			$imgs[] = $img->getAttribute('href');
		}
		return $imgs;
	}

	function get_missing_text($xpath, &$post)
	{
		print_r ($post);

	}
}
