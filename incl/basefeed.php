<?php

define('UTF8_TAG',
		'<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>');

class Basefeed
{
	var $img_root;

	/**
	 * downloads content
	 * @return string
	 * @author Tarin Mahmood
	 **/
	protected function download_content($link)
	{
		$content = Utils::download_content($link);
		if(!$this->is_html_file($content, $link)) {
			return false;
		}
		// force html utf8
		$content = str_replace('</head>', UTF8_TAG, $content);
		// check if needs to remove any nodes
		if (property_exists($this, 'replace_elms_child')) {
			$content = str_replace($this->replace_elms_child,
								   $this->replace_with_child,
								   $content);
		}
		return $content;
	}

	/**
	 * get_xpath
	 * @return xpath object
	 * @author Tarin Mahmood
	 **/
	protected function get_xpath($content)
	{
		$doc = new DOMDocument();
		@$doc->loadHTML($content);
		// remove scripts
		while (($r = $doc->getElementsByTagName("script")) && $r->length) {
			$r->item(0)->parentNode->removeChild($r->item(0));
		}
		return new DOMXpath($doc);
	}

	/**
	 * Downloads page, cleans up set
	 * @Return DOMXPath object
	 */
	function get_page_obj($post)
	{
		// download page
		$content = $this->download_content($post->link);
		// return xpath
		return $this->get_xpath($content);
	}

	/**
	 * is_html_file
	 * @return true/false
	 * @author Tarin Mahmood
	 **/
	public function is_html_file($content, $link)
	{
		$tmp_file_name = IMG_SRC_PATH . '/tmp/' . md5($link);
		file_put_contents($tmp_file_name , $content);
		$mtype = mime_content_type($tmp_file_name);
		unlink($tmp_file_name);
		return $mtype == 'text/html';
	}

	/**
	 * is_bad_url, check if the url is in list of bad URLS
	 * @return true/false
	 * @author Tarin Mahmood
	 **/
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
		return isset($this->custom_image_src) &&
				$this->custom_image_src;
	}

	/**
	 * Downloads and save images in page in dis
	 *
	 * @return list of images found
	 * @author Tarin Mahmood
	 */
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
		return $this->download_store_images($images);
	}

	function download_store_images($images)
	{
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
			if (trim($node->nodeValue) == '') {
				continue;
			}
			$txt[] = $node->ownerDocument->saveXML($child);
		}
		$txt = implode(" ", $txt);
		$txt = preg_replace("/[\r\n]+/", "\n", $txt);
		return "<p>$txt</p>";
	}

	protected function is_elm_with_class($node, $nodename)
	{
		return $node->nodeName == $nodename &&
				$node->hasAttribute('class');
	}

	protected function is_elm_with_attr($elm, $attr)
	{
		if($elm->hasAttribute('id')) {
			return $elm->getAttribute('id');
		}
		return false;
	}


	function remove_links($parentnode)
	{
		if ($parentnode == null) {
			return;
		}
		$tobe_removed = array();
		foreach ($parentnode->getElementsByTagName('a') as $link){
			$tobe_removed[] = $link;
		}
		foreach ($tobe_removed as $link){
			$link->parentNode->removeChild($link);
		}
	}

	/**
	 * set_category_details
	 * @return void
	 * @author Tarin Mahmood
	 **/
	public function set_category_details(&$post)
	{
		if (isset($this->parent_category)) {
			$post->parent_category = $this->parent_category;
		}
		if ($post->category_slug == null && $this->category_slug_set($post)) {
			$post->category_slug = $this->category_slug[$post->category];
		}
	}

	function category_slug_set($post)
	{
		return isset($this->category_slug)
				&& array_key_exists($post->category, $this->category_slug);
	}

	/**
	 * text_formatting, place holder fuction
	 * @return void
	 * @author Tarin Mahmood
	 **/
	public function text_formatting($text)
	{
		return $text;
	}

}

