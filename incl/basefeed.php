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
			$filename = sprintf("%s/imgs/%s_%s", SCRIPT_ABSPATH, md5($this->base_url),
								md5($imgurl));
			if (!file_exists($imgurl)) {
				Utils::download($imgurl, $filename);
			}
			$imgs[] = sprintf('<img src="%s">', $filename);
		}
		return implode("\n", $imgs);
	}
}

