<?php

class Berlin extends XML
{
	var $base_url = 'http://www.berlin.de';
	var $feed_url = "http://www.berlin.de/polizei/polizeimeldungen/index.php/rss";
	var $category = "Berlin (Polizei)";
	var $parent_category = 42;
	var $text_cnt = '//div[@class="html5-section article"]//div[@class="textile"]/p';
	var $imgs_sel = '//div[@class="html5-section article"]//img';
	var $custom_image_src = true;

	function get_image_custom($img)
	{
		return $img->parentNode->getAttribute('href');
	}

	function get_content($xpath)
	{
		$elements = $xpath->query($this->text_cnt);
		return $this->_get_inner_html($elements->item(0)->parentNode);
	}

	function ignore_content($node)
	{
		return false;
	}


	function get_missing_text($xpath, &$post)
	{
		print_r ($post);
	}
}
