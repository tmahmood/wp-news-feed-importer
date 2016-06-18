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
			$imgsrc = $img->getAttribute('src');
			if (strpos($imgsrc, 'https://') === 0 || strpos($imgsrc, 'http://') === 0) {
				$imgurl = $imgsrc;
			} else {
				$imgurl = $this->base_url . $imgsrc;
			}
			if (isset($this->bad_url) && in_array($imgurl, $this->bad_url)) {
				continue;
			}
			$fname = md5($this->base_url) . '_' . md5($imgurl);
			$filename = sprintf("%s/imgs/%s", SCRIPT_ABSPATH, $fname);
			if (!file_exists($imgurl)) {
				Utils::download($imgurl, $filename);
			}
			$imgs[] = sprintf('<img src="/feed_parser/imgs/%s">', $fname);
		}
		return implode("\n", $imgs);
	}
}

