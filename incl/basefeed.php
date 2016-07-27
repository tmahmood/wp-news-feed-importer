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
		$images = array();
		$count_images = 0;
		if (is_array($this->imgs_sel)) {
			foreach ($this->imgs_sel as $sel){
				$imglist = $xpath->query($sel);
				foreach ($imglist as $img){
					$images[] = $img;
				}
			}
			$count_images = count($images);
		} else {
			$images = $xpath->query($this->imgs_sel);
			$count_images = $images->length;
		}
		if ($count_images == 0) {
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
			$filesrc  = $imgsrcpath. $filename;
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
			$r = exif_imagetype($filepath);
			if ($r == IMAGETYPE_JPEG) {
				$newpath = "$filepath.jpg";
				rename($filepath, $newpath);
				$filepath = $newpath;
			} elseif ($r == IMAGETYPE_PNG) {
				$newpath = "$filepath.png";
				rename($filepath, $newpath);
				$filepath = $newpath;
			}
			$imgs[] = $filepath;
		}
		return $imgs;
	}

	protected function _get_inner_html($node)
	{
		$txt = array();
		$childNodes = $node->childNodes;
		foreach ($childNodes as $ky=>$child){
			if($this->ignore_content($child)) {
				continue;
			}
			$txt[] = $node->ownerDocument->saveXML($child);
		}
		$txt = implode("<br>", $txt);
		return $txt;
	}

	protected function is_elm_with_class($node, $nodename)
	{
		return $node->nodeName == $nodename && $node->hasAttribute('class');
	}

	function remove_links($parentnode)
	{
		$tobe_removed = array();
		foreach ($parentnode->getElementsByTagName('a') as $link){
			$tobe_removed[] = $link;
		}
		foreach ($tobe_removed as $link){
			$link->parentNode->removeChild($link);
		}
	}
}

