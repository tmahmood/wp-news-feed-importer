<?php

define("RDF_SPEC_URL",
	   "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
class RDF extends Basefeed
{
	function get_items()
	{
		if (!is_array($this->feed_url)) {
			return $this->get_items_from_feed($this->feed_url);
		}
		$feeds = array();
		foreach ($this->feed_url as $url){
			$feeds = array_merge($feeds,
						$this->get_items_from_feed($url));
		}
		return $feeds;
	}

	function get_items_from_feed($feed_url)
	{
		$rdf = file_get_contents($feed_url);
		$dom = new DomDocument;
		$dom->loadXml($rdf);
		$xph = new DOMXPath($dom);
		$xph->registerNamespace('rdf', RDF_SPEC_URL);
		$items = $xph->query('//@rdf:about');
		$nodes = array();
		foreach($items as $node) {
			$nodes[] = $node;
		}
		array_shift($nodes);
		return $nodes;
	}

	function parse(&$post, $item)
	{
		$post->link  = (string) $item->value;
		$post->date  = null;
		$post->title = null;
		if (isset($this->category) && !is_array($this->category)) {
			$post->category = $this->category;
		} else {
			$post->category = null;
		}
		$this->set_category_details($post);
		return $post;
	}
}
