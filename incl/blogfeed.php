<?php

class BlogFeed
{
    var $posts = array();

    function __construct($feed)
    {
		$this->feed = new $feed;
    }

	/**
	 * Do parsing
	 */
	function parse_feed()
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
        }
	}

	/**
	 * Download webpage and apply rule to parse data
	 */
	function parse_source_link(&$post)
	{
		if (isset($this->feed->json_data)) {
			return;
		}
		$xpath = $this->feed->get_page_obj($post);
		$post->picture = $this->feed->get_image($xpath);
		$post->text  = (string) $this->feed->get_content($xpath);
		$this->fill_missing_data($xpath, $post);
	}

	/**
	 * Fill up missing information, obj could be either JSON object
	 * or XML object
	 */
	function fill_missing_data($xpath, &$post)
	{
		if ($post->title == null) {
			$this->feed->get_missing_title($xpath, $post);
		}
		if ($post->category == null) {
			$this->feed->get_missing_category($xpath, $post);
		}
		if ($post->text == null) {
			$this->feed->get_missing_text($xpath, $post);
		}
	}
}

