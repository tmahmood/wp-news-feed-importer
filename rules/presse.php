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
		$textbody = $elements->item(0)->parentNode;
		return $this->_get_inner_html($textbody);
	}

	function ignore_content($node)
	{
		if ($node->nodeName == 'img') {
			return true;
		}
		return false;
	}


	function get_missing_title($xpath, &$post)
	{

	}
}
