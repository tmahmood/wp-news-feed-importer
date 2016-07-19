<?php

class Zoll extends XML
{
	var $base_url = 'http://www.zoll.de';
	var $category = "Zoll Deutschland (Bundesweite Meldungen des Dienstes “Zoll im Fokus”)";
	var $feed_url = "http://www.zoll.de/SiteGlobals/Functions/RSSFeed/DE/RSSNewsfeed/RSSZollImFokus.xml";
	var $text_cnt = 'id("main")/p';
	var $imgs_sel = '//span[@class="zoom"]/a';
	var $custom_image_src = true;

	function get_content($xpath)
	{
		$txt = array();
		$textbody = $xpath->query($this->text_cnt)->item(0)->parentNode;
		$childNodes = $textbody->childNodes;
		foreach ($childNodes as $ky=>$child){
			if($this->should_not_continue($child)) {
				continue;
			}
			$txt[] = $textbody->ownerDocument->saveXML($child);
		}
		$txt = implode("<br>", $txt);
		if (trim($txt) == '') {
			$elm = $xpath->query('id("main")');
			return trim($elm[0]->nodeValue);
		}
		return $txt;
	}

	function should_not_continue($child)
	{
		$skip = false;
		if ($child->nodeName == 'div' && $child->hasChildNodes()) {
			foreach($child->childNodes as $innerchild) {
				if ($innerchild->nodeName == 'p'
					&& $innerchild->hasAttribute('class')) {
					$cls = $innerchild->getAttribute('class');
					if ($cls == 'picture rechts'
						|| $cls == 'picture links') {
						$skip = true;
						break;
					}
				}
			}
			if ($skip) {
				return true;
			}
		}
		if($child->nodeName =='span' && $child->hasAttribute('class')) {
			$cls = $child->getAttribute('class');
			if($cls == 'directURL') {
				return true;
			}
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


