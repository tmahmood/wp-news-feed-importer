<?php

class Saarland extends XML
{
	var $feed_url = "http://www.saarland.de/cps/rde/xchg/SID-C5139897-FEF87F5B/saarland/feed.xsl/rss_polizei.xml";
	var $category = "Saarbrücken (Landespolizeipräsidium Saarland)";
	var $text_cnt = '//div[@class="textchapter_frame"]';

	function get_content($xpath)
	{
		$elments = $xpath->query($this->text_cnt);
		$txt = array();
		foreach ($elments as $ky=>$element){
			$txt[] = $element->nodeValue;
		}
		return implode("", $txt);
	}

	function get_image($xpath)
	{
		$imgxpath = $this->text_cnt . '//img';
		$elms = $xpath->query($imgxpath);
		if (count($elms) == 0) {
			return "";
		}
		$imgs = array();
		$imgs[] = $el;
		foreach ($elms as $elm){
			$imgs[] = $elm->getAttribute('src');
		}
		return implode("\n", $imgs);
	}

}

