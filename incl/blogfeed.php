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
			if($this->parse_source_link($post) === false){
				continue;
			}
			$post->text = $this->feed->text_formatting($post->text);
			$this->posts[] = $post;
        }
	}

	/**
	 * Download webpage and apply rule to parse data
	 */
	function parse_source_link(&$post)
	{
		$xpath = $this->feed->get_page_obj($post);
		if (!is_object($xpath) || $xpath == null) {
			return false;
		}
		if ($post->picture != null) {
			$post->picture = $this->feed->get_image($xpath);
		}
		$post->text  = (string) $this->feed->get_content($xpath);
		$this->fill_missing_data($xpath, $post);
		$this->feed->set_category_details($post);
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

