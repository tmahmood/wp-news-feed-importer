<?php

class Basefeed
{
	function get_image($xpath)
	{
		$images = $xpath->query($this->imgs_sel);
		if (count($images) == 0) {
			return "";
		}
		$imgs = array();
		foreach ($images as $img){
			$imgs[] = $this->base_url . $img->getAttribute('src');
		}
		return implode("\n", $imgs);
	}
}

