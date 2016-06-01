<?php

class BlogFeed
{
    var $posts = array();
	var $feed_url = null;

    function __construct($feed)
    {
		$this->feed = new $feed;
    }

	/**
	 * Do parsing
	 */
	function parse()
	{
		$items = $this->feed->get_items();
		if (count($items) == 0) {
			return;
		}
        foreach ($items as $item) {
			$post = new BlogPost;
			$this->feed->parse($post, $item);
			$this->parse_source_link($post);
			$this->posts[] = $post;
			break;
        }
	}

	/**
	 * Download webpage and apply rule to parse data
	 */
	function parse_source_link(&$post)
	{
		$doc = new DOMDocument();
		@$doc->loadHTMLFile($post->link);
		$xpath = new DOMXpath($doc);
		$post->picture = (string) $this->feed->get_image($xpath);
		$post->text  = (string) $this->feed->get_content($xpath);
		$this->fill_missing_data($xpath, $post);
	}

	function fill_missing_data($xpath, &$post)
	{
		if ($post->title == null) {
			$this->feed->get_missing_title($xpath, $post);
		}

		if ($post->date == null) {
			$this->feed->get_missing_date($xpath, $post);
		}

		if ($post->category == null) {
			$this->feed->get_missing_category($xpath, $post);
		}
	}
}

