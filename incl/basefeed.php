<?php



class Basefeed
{
	var $img_root;

	function is_bad_url($imgurl)
	{
		return isset($this->bad_url) &&
			in_array($imgurl, $this->bad_url);
	}

	function is_full_url($imgsrc)
	{
		return strpos($imgsrc, 'https://') === 0 ||
				strpos($imgsrc, 'http://') === 0;
	}

	function call_custom_image_parsing()
	{
		return isset($this->custom_image_src) && $this->custom_image_src;
	}

	function get_image($xpath, $callback=false)
	{
		$images = $xpath->query($this->imgs_sel);
		if (count($images) == 0) {
			return "";
		}
		$imgs = array();
		$added = array();
		foreach ($images as $imgelm){
			if ($this->call_custom_image_parsing()) {
				$imgsrc = $this->get_image_custom($imgelm);
			} else {
				$imgsrc = $imgelm->getAttribute('src');
			}
			if (in_array($imgsrc, $added)) {
				continue;
			}
			$added[] = $imgsrc;
			$is_data = false;
			if (strpos($imgsrc, 'data:') === 0) {
				$is_data = true;
			} else {
				if ($this->is_bad_url($imgsrc)) {
					continue;
				}
				if ($this->is_full_url($imgsrc)) {
					$imgurl = $imgsrc;
				} else {
					$imgurl = $this->base_url . $imgsrc;
				}
			}
			if ($is_data) {
				$imgs[] = sprintf('<img src="%s">', $imgsrc);
			} else {
				$img = file_get_contents($imgurl);
				if ($img != null) {
					$imgs[] = sprintf('<img src="data:image/jpg;base64,%s">', base64_encode($img));
				}
			}
		}
		return implode("\n", $imgs);
	}
}

