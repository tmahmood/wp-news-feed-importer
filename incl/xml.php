<?php

class XML
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
		$post = new BlogPost();
		$post->date  = (string) $item->pubDate;
		$post->link  = (string) $item->link;
		$post->title = (string) $item->title;
		$post->category = $this->category;
		return $post;
	}
}

