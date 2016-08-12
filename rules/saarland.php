<?php

class Saarland extends XML
{
	var $feed_url = "http://www.saarland.de/cps/rde/xchg/SID-C5139897-FEF87F5B/saarland/feed.xsl/rss_polizei.xml";
	var $category = "Saarbrücken (Landespolizeipräsidium Saarland)";
	var $text_cnt = '//div[@class="textchapter_frame"]';
	var $imgs_sel = '//div[@class="textchapter_frame"]//img';
	var $base_url = 'http://www.saarland.de/';
	var $is_first = true;

	function get_content($xpath)
	{
		$this->is_first = true;
		$elements = $xpath->query($this->text_cnt);
		return $this->_get_inner_html($elements->item(0)->parentNode);
	}

	function ignore_content($child)
	{
		if ($child->nodeName == 'h1' && $this->is_first) {
			$this->is_first = false;
			return true;
		}

		if ($child->nodeName == 'div' &&
			!($divbtn_id = $this->is_elm_with_attr($child, 'id')) === false) {
			if ($divbtn_id == 'readspeaker_button1') {
				return true;
			}
		}
		return false;
	}
}

