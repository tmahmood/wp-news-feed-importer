<?php

class Zoll extends XML
{
	var $base_url = 'http://www.zoll.de';
	var $category = "Zoll Deutschland (Bundesweite Meldungen des Dienstes “Zoll im Fokus”)";
	var $feed_url = "http://www.zoll.de/SiteGlobals/Functions/RSSFeed/DE/RSSNewsfeed/RSSZollImFokus.xml";
	var $text_cnt = 'id("main")/p';
	var $imgs_sel = array('//span[@class="zoom"]/a', '//dl[@class="photo"]//a');
	var $custom_image_src = true;
	var $parent_category = 504;

	function get_content($xpath)
	{
		$txt = array();
		$textbody = $xpath->query($this->text_cnt)->item(0)->parentNode;
		$this->remove_links($textbody);
		$h1 = $textbody->getElementsByTagName('h1')->item(0);
		$h1->parentNode->removeChild($h1);
		$txt = $this->_get_inner_html($textbody);
		if (trim($txt) == '') {
			$elm = $xpath->query('id("main")');
			return trim($elm[0]->nodeValue);
		}
		return $txt;
	}

	private function is_p_with_a_picture($innerchild)
	{
		$s = $this->is_elm_with_class($innerchild, 'p');
		if ($s) {
			$cls = $innerchild->getAttribute('class');
			return $cls == 'picture rechts' || $cls == 'picture links';
		}
		return false;
	}

	private function is_directurl_span($child)
	{
		if (!($child->nodeName =='span' && $child->hasAttribute('class'))) {
			return false;
		}
		$cls = $child->getAttribute('class');
		if($cls == 'directURL') {
			return true;
		}
	}

	private function is_div_with_class($child)
	{
		if(!($child->nodeName =='div' && $child->hasAttribute('class'))) {
			return false;
		}
		if ($child->getAttribute('class') == 'gallery') {
			return true;
		}
		return false;
	}

	function ignore_content($child)
	{
		if ($this->is_div_with_class($child)) {
			return true;
		}
		if ($this->is_elm_with_class($child, 'p')) {
			if($child->getAttribute('class') == 'navToTop') {
				return true;
			}
		}
		if ($child->nodeName == 'div' && $child->hasChildNodes()) {
			foreach($child->childNodes as $innerchild) {
				if ($this->is_p_with_a_picture($innerchild)) {
					return true;
				}
			}
		}
		if($this->is_directurl_span($child)) {
			return true;
		}
		return false;
	}

	function get_image_custom($imgelm)
	{
		$fullurl = $imgelm->getAttribute('href');
		if (!$this->is_full_url($fullurl)) {
			$fullurl = $this->base_url . $fullurl;
		}
		return $fullurl;
	}

	function get_missing_text($xpath, &$post)
	{
		return '';
	}
}


