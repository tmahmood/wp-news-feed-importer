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
		$this->set_category_details($post);
		return $post;
	}
}

