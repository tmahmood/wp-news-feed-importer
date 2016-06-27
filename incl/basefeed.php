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

	function get_image($xpath)
	{
		$images = $xpath->query($this->imgs_sel);
		if (count($images) == 0) {
			return "";
		}
		$imgs = array();
		$added = array();
		list($storepath, $imgsrcpath) = Utils::get_base_url($this->base_url);
		if (!file_exists($storepath)) {
			mkdir($storepath, 0777, true);
		}
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
			$filename = md5($imgsrc);
			$filepath = $storepath . $filename;
			$filesrc  = $imgsrcpath . $filename;
			$imgs[] = sprintf('<img src="%s">', $filesrc);
			if ($is_data) {
				$v = explode(',', $imgsrc);
				$imgdata = imagecreatefromstring(array_pop($v));
				file_put_contents($filepath, $imgdata);
			} else {
				$imgcontent = file_get_contents($imgurl);
				if ($imgcontent != null) {
					file_put_contents($filepath, $imgcontent);
				}
			}
		}
		return implode("\n", $imgs);
	}
}

