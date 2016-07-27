<?php

class XML extends BaseFeed
{
	function get_items()
	{
		if (!($x = simplexml_load_file($this->feed_url))) {
			return array();
		}
		return $x->channel->item;
	}

	function parse(&$post, $item)
	{
		$post->date  = (string) $item->pubDate;
		$post->link  = (string) $item->link;
		$post->title = (string) $item->title;
		$post->category = $this->category;
		return $post;
	}

	function get_page_obj($post)
	{
		$doc = new DOMDocument();
		$content = Utils::download_content($post->link);
		@$doc->loadHTML($content);
		// remove scripts
		while (($r = $doc->getElementsByTagName("script")) && $r->length) {
			$r->item(0)->parentNode->removeChild($r->item(0));
		}
		return new DOMXpath($doc);
	}
}

