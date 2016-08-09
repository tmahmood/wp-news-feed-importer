<?php

class Polizei extends RDF
{
	var $base_url = 'http://www.polizei.bayern.de';
	var $feed_url = array(
			"http://www.polizei.bayern.de/lka/polizei.rss",
			"http://www.polizei.bayern.de/verwaltungsamt/polizei.rss",
			"http://www.polizei.bayern.de/bepo/polizei.rss",
			"http://www.polizei.bayern.de/muenchen/polizei.rss",
			"http://www.polizei.bayern.de/niederbayern/polizei.rss",
			"http://www.polizei.bayern.de/oberbayern_nord/polizei.rss",
			"http://www.polizei.bayern.de/oberbayern/polizei.rss",
			"http://www.polizei.bayern.de/oberfranken/polizei.rss",
			"http://www.polizei.bayern.de/oberpfalz/polizei.rss",
			"http://www.polizei.bayern.de/schwaben/polizei.rss",
			"http://www.polizei.bayern.de/schwaben_sw/polizei.rss",
			"http://www.polizei.bayern.de/unterfranken/polizei.rss",
			);
	var $category = array(
			"lka" => "München (Bayerisches Landeskriminalamt)",
			"verwaltungsamt" => "München (Polizeiverwaltungsamt)",
			"bepo" => "München (Bayerische Bereitschaftspolizei)",
			"muenchen" => "München (Polizei)",
			"niederbayern" => "Straubing (Polizeipräsidium Niederbayern)",
			"oberbayern_nord" => "Ingolstadt (Polizeipräsidium Oberbayern Nord)",
			"oberbayern" => "Rosenheim (Polizeipräsidiums Oberbayern Süd)",
			"oberfranken" => "Bayreuth (Polizeipräsidium Oberfranken)",
			"oberpfalz" => "Regensburg (Polizeipräsidium Oberpfalz)",
			"schwaben" => "Augsburg (Polizeipräsidium Schwaben Nord)",
			"schwaben_sw" => "Kempten (Polizeipräsidium Schwaben Süd/West)",
			"unterfranken" => "Würzburg (Polizeipräsidiums Unterfranken)",
			);
	var $text_cnt = '';
	var $imgs_sel = '//div[@class="inhaltBilderZoom"]/a';
	var $custom_image_src = true;

	function get_missing_text($xpath, $post)
	{
		$currentnode = $xpath->query('//h1')->item(0)->nextSibling;
		$parent = $currentnode->parentNode;
		$innerhtml = array();
		while ($currentnode) {
			if($this->stop_adding($currentnode)) {
				break;
			}
			if ($this->should_ignore($currentnode)) {
				$currentnode = $currentnode->nextSibling;
				continue;
			}
			$innerhtml[] = $parent->ownerDocument->saveXML($currentnode);
			$currentnode = $currentnode->nextSibling;
		}
		$post->content = trim(implode("", $innerhtml));
	}

	function get_content($xpath)
	{
		$text = array();
		$elm = $xpath->query('//h1')->item(0)->parentNode;
		$this->remove_links($textbody);
		$childNodes = $textbody->childNodes;
		$innerhtml = array();
		$indx = 0;
		foreach ($childNodes as $indx => $child) {
			if ($child->nodeName == 'h1') {
				break;
			}
		}
		++$indx;
		for (;$indx < $childNodes->length; ++$indx){
			$child = $childNodes->item($indx);
			if($this->stop_adding($child)) {
				break;
			}
			if ($this->should_ignore($child)) {
				continue;
			}
			$innerhtml[] = $textbody->ownerDocument->saveXML($child);
		}
		$txt = implode("", $innerhtml);
		return preg_replace('/<!-(.*)->/', '', $txt);
	}


	function should_ignore($child)
	{
		if ($child->nodeName == '#text' ||
			$child->nodeName == '#script' ||
			$child->nodeName == 'img'||
			$child->nodeName == 'a'||
			$child->nodeName == '#style') {
			return true;
		}
		if($child->nodeName == 'table' && trim($child->nodeValue) == '') {
			return true;
		}
		if (method_exists($child, 'getAttribute')) {
			$cls = $child->getAttribute('class');
			if ($cls == 'inhaltBilderZoom') {
				return true;
			}
		}
		return false;
	}

	function stop_adding($child)
	{
		if(method_exists($child, 'getAttribute')) {
			$cls = $child->getAttribute('class');
			if ($cls == 'inhaltFooter') {
				return true;
			}
		}
		return false;
	}

	function get_missing_title($xpath, &$post)
	{
		$post->title = $xpath->query('//h1')->item(0)->nodeValue;
	}

	function get_missing_category($xpath, &$post)
	{
		$segs = explode('/', $post->link);
		$keys = array_keys($this->category);
		foreach ($segs as $seg){
			if (in_array($seg, $keys)) {
				$post->category = $this->category[$seg];
				break;
			}
		}
	}

	function get_image_custom($elm)
	{
		$href = $elm->getAttribute('href');
		preg_match("/'(.[^']*)'/", $href, $match);
		return $match[1];
	}

}
